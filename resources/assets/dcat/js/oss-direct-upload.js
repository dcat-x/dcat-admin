/**
 * OSS 直传上传组件
 * 支持大文件上传、分片上传、断点续传
 */
class OssDirectUploader {
    constructor(options) {
        this.options = Object.assign({
            container: null,
            fieldName: null,
            maxSize: 500, // MB
            chunkSize: 10, // MB
            accept: '*',
            acceptMimeTypes: null,
            uploadType: 'file',
            uploadDirectory: null, // 自定义上传目录
            token: null,
            stsTokenUrl: null,
            i18n: {
                uploadSuccess: 'Upload successful',
                uploadFailed: 'Upload failed, please try again',
                fileDeleted: 'File deleted',
                linkCopied: 'Link copied to clipboard',
                copyFailed: 'Copy failed, please copy manually: ',
                confirmDelete: 'Are you sure you want to delete this file?',
                noFilePath: 'No file path to copy',
                fileTooLarge: 'File size cannot exceed {size}MB',
                unsupportedType: 'Unsupported file type, only supports: {types}',
                stsError: 'Failed to get upload credentials, please refresh the page'
            },
            onProgress: null,
            onSuccess: null,
            onError: null
        }, options);

        this.ossClient = null;
        this.stsCredentials = null;
        this.ossConfig = null;
        this.uploadDir = null;
        this.currentFile = null;
        this.isUploading = false;
        this.uploadAborted = false;

        this.init();
    }

    /**
     * 初始化
     */
    async init() {
        this.setupElements();
        this.setupEventListeners();
        await this.refreshCredentials();
    }

    /**
     * 设置 DOM 元素
     */
    setupElements() {
        this.container = document.querySelector(this.options.container);
        if (!this.container) {
            console.error('Container not found:', this.options.container);
            return;
        }

        this.uploadArea = this.container.querySelector('.upload-area');
        this.fileInput = this.container.querySelector('.file-input');
        this.progressContainer = this.container.querySelector('.upload-progress');
        this.progressBar = this.container.querySelector('.progress-bar');
        this.uploadInfo = this.container.querySelector('.upload-info');
        this.previewContainer = this.container.querySelector('.upload-preview');
        this.hiddenInput = this.container.querySelector('input[type="hidden"]');
    }

    /**
     * 设置事件监听
     */
    setupEventListeners() {
        if (!this.uploadArea || !this.fileInput) return;

        // 点击上传区域触发文件选择
        this.uploadArea.addEventListener('click', () => {
            if (!this.isUploading) {
                this.fileInput.click();
            }
        });

        // 文件选择
        this.fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.handleFileSelect(file);
            }
        });

        // 拖拽上传
        this.uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.uploadArea.classList.add('dragover');
        });

        this.uploadArea.addEventListener('dragleave', () => {
            this.uploadArea.classList.remove('dragover');
        });

        this.uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            this.uploadArea.classList.remove('dragover');

            const file = e.dataTransfer.files[0];
            if (file) {
                this.handleFileSelect(file);
            }
        });

        // 删除文件
        const deleteBtn = this.container.querySelector('.btn-delete');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.handleDelete();
            });
        }

        // 复制 URL
        const copyBtn = this.container.querySelector('.btn-copy-url');
        if (copyBtn) {
            copyBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.handleCopyUrl();
            });
        }

        // 重新上传
        const reuploadBtn = this.container.querySelector('.btn-reupload');
        if (reuploadBtn) {
            reuploadBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.handleReupload();
            });
        }
    }

    /**
     * 刷新 STS 凭证
     */
    async refreshCredentials() {
        try {
            const requestData = {
                type: this.options.uploadType
            };

            // 如果指定了自定义目录，则传递给后端
            if (this.options.uploadDirectory) {
                requestData.directory = this.options.uploadDirectory;
            }

            const response = await fetch(this.options.stsTokenUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.token
                },
                body: JSON.stringify(requestData)
            });

            if (!response.ok) {
                throw new Error(this.options.i18n.stsError);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || this.options.i18n.stsError);
            }

            this.stsCredentials = result.data.credentials;
            this.ossConfig = {
                region: result.data.region,
                bucket: result.data.bucket,
                endpoint: result.data.endpoint,
                cdnDomain: result.data.cdn_domain || null
            };
            this.uploadDir = result.data.upload_dir;

            // 初始化 OSS 客户端
            this.initOssClient();
        } catch (error) {
            console.error('刷新 STS 凭证失败:', error);
            this.showError(this.options.i18n.stsError);
        }
    }

    /**
     * 初始化 OSS 客户端
     */
    initOssClient() {
        if (!window.OSS) {
            console.error('OSS SDK 未加载');
            return;
        }

        this.ossClient = new OSS({
            region: this.ossConfig.region,
            accessKeyId: this.stsCredentials.AccessKeyId,
            accessKeySecret: this.stsCredentials.AccessKeySecret,
            stsToken: this.stsCredentials.SecurityToken,
            bucket: this.ossConfig.bucket
        });
    }

    /**
     * 处理文件选择
     */
    async handleFileSelect(file) {
        // 验证文件
        if (!this.validateFile(file)) {
            return;
        }

        this.currentFile = file;
        this.uploadAborted = false;

        // 开始上传
        await this.uploadFile(file);
    }

    /**
     * 验证文件
     */
    validateFile(file) {
        // 检查文件大小
        const maxSizeBytes = this.options.maxSize * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            this.showError(this.options.i18n.fileTooLarge.replace('{size}', this.options.maxSize));
            return false;
        }

        // 检查文件类型
        if (this.options.accept !== '*') {
            const extension = file.name.split('.').pop().toLowerCase();
            const allowedExtensions = this.options.accept.split(',').map(e => e.trim());
            if (!allowedExtensions.includes(extension)) {
                this.showError(this.options.i18n.unsupportedType.replace('{types}', this.options.accept));
                return false;
            }
        }

        return true;
    }

    /**
     * 上传文件
     */
    async uploadFile(file) {
        this.isUploading = true;
        this.showProgress();

        try {
            // 生成文件名（保持原始文件名）
            const filename = this.generateFilename(file.name);
            const objectKey = this.uploadDir + filename;

            // 检查是否需要分片上传
            const chunkSizeBytes = this.options.chunkSize * 1024 * 1024;
            let result;

            if (file.size < chunkSizeBytes) {
                // 简单上传
                result = await this.simpleUpload(file, objectKey);
            } else {
                // 分片上传
                result = await this.multipartUpload(file, objectKey);
            }

            // 上传成功
            this.handleUploadSuccess(objectKey, file);
        } catch (error) {
            console.error('上传失败:', error);
            this.handleUploadError(error);
        } finally {
            this.isUploading = false;
        }
    }

    /**
     * 简单上传
     */
    async simpleUpload(file, objectKey) {
        return await this.ossClient.put(objectKey, file, {
            progress: (p) => {
                this.updateProgress(p * 100, file.size, file.size * p);
            }
        });
    }

    /**
     * 分片上传
     */
    async multipartUpload(file, objectKey) {
        const chunkSize = this.options.chunkSize * 1024 * 1024;

        return await this.ossClient.multipartUpload(objectKey, file, {
            parallel: 4,
            partSize: chunkSize,
            progress: (p) => {
                this.updateProgress(p * 100, file.size, file.size * p);
            }
        });
    }

    /**
     * 生成文件名（保持原始文件名）
     */
    generateFilename(originalFilename) {
        // 获取文件扩展名
        const extension = originalFilename.split('.').pop();
        const nameWithoutExt = originalFilename.substring(0, originalFilename.lastIndexOf('.'));

        // 添加时间戳确保唯一性，格式：原文件名_时间戳.扩展名
        const timestamp = Date.now();
        return `${nameWithoutExt}_${timestamp}.${extension}`;
    }

    /**
     * 更新上传进度
     */
    updateProgress(percent, totalSize, uploadedSize) {
        if (this.uploadAborted) return;

        const percentInt = Math.floor(percent);

        if (this.progressBar) {
            this.progressBar.style.width = percentInt + '%';
            this.progressBar.setAttribute('aria-valuenow', percentInt);
            this.progressBar.textContent = percentInt + '%';
        }

        if (this.uploadInfo) {
            const uploadedMb = (uploadedSize / 1024 / 1024).toFixed(2);
            const totalMb = (totalSize / 1024 / 1024).toFixed(2);
            this.uploadInfo.querySelector('.filename').textContent = this.currentFile.name;
            this.uploadInfo.querySelector('.upload-speed').textContent =
                `${uploadedMb}MB / ${totalMb}MB`;
        }

        // 触发回调
        if (this.options.onProgress) {
            this.options.onProgress(percentInt, uploadedSize, totalSize);
        }
    }

    /**
     * 上传成功处理
     */
    handleUploadSuccess(objectKey, file) {
        // 更新隐藏字段的值
        if (this.hiddenInput) {
            this.hiddenInput.value = objectKey;
        }

        // 隐藏上传区域和进度条
        if (this.uploadArea) {
            this.uploadArea.style.display = 'none';
        }
        if (this.progressContainer) {
            this.progressContainer.style.display = 'none';
        }

        // 显示预览
        this.showPreview(file.name, objectKey);

        // 触发成功回调
        if (this.options.onSuccess) {
            this.options.onSuccess(objectKey, file);
        }

        // 显示成功消息
        this.showSuccess(this.options.i18n.uploadSuccess);
    }

    /**
     * 上传失败处理
     */
    handleUploadError(error) {
        this.hideProgress();
        this.showError(error.message || this.options.i18n.uploadFailed);

        if (this.options.onError) {
            this.options.onError(error);
        }
    }

    /**
     * 显示进度
     */
    showProgress() {
        if (this.uploadArea) {
            this.uploadArea.style.display = 'none';
        }
        if (this.progressContainer) {
            this.progressContainer.style.display = 'block';
        }
        if (this.previewContainer) {
            this.previewContainer.style.display = 'none';
        }
    }

    /**
     * 隐藏进度
     */
    hideProgress() {
        if (this.progressContainer) {
            this.progressContainer.style.display = 'none';
        }
        if (this.uploadArea) {
            this.uploadArea.style.display = 'flex';
        }
    }

    /**
     * 显示预览
     */
    showPreview(filename, objectKey) {
        if (!this.previewContainer) return;

        this.previewContainer.style.display = 'block';

        // 更新文件名
        const filenameSpan = this.previewContainer.querySelector('.preview-filename');
        if (filenameSpan) {
            filenameSpan.textContent = filename;
            filenameSpan.title = objectKey;
        }

        // 更新文件路径
        const filePathSpan = this.previewContainer.querySelector('.file-path');
        if (filePathSpan) {
            filePathSpan.textContent = objectKey;
            filePathSpan.title = objectKey;
        }
    }

    /**
     * 删除文件
     */
    handleDelete() {
        if (confirm(this.options.i18n.confirmDelete)) {
            if (this.hiddenInput) {
                this.hiddenInput.value = '';
            }

            if (this.previewContainer) {
                this.previewContainer.style.display = 'none';
            }

            if (this.uploadArea) {
                this.uploadArea.style.display = 'flex';
            }

            this.currentFile = null;
            this.fileInput.value = '';

            this.showSuccess(this.options.i18n.fileDeleted);
        }
    }

    /**
     * 复制下载链接
     */
    handleCopyUrl() {
        const filePath = this.hiddenInput ? this.hiddenInput.value : '';
        if (!filePath) {
            this.showError(this.options.i18n.noFilePath);
            return;
        }

        // 构建完整的下载 URL
        const downloadUrl = this.buildDownloadUrl(filePath);

        // 复制到剪贴板
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(downloadUrl).then(() => {
                this.showSuccess(this.options.i18n.linkCopied);
            }).catch(() => {
                this.fallbackCopyToClipboard(downloadUrl);
            });
        } else {
            this.fallbackCopyToClipboard(downloadUrl);
        }
    }

    /**
     * 重新上传
     */
    handleReupload() {
        if (this.fileInput) {
            this.fileInput.click();
        }
    }

    /**
     * 构建下载 URL
     */
    buildDownloadUrl(filePath) {
        // 从 OSS 配置中获取域名
        const ossConfig = this.ossConfig;
        if (!ossConfig || !ossConfig.endpoint) {
            return filePath;
        }

        // 如果有 CDN 域名，使用 CDN 域名
        if (ossConfig.cdnDomain) {
            return `https://${ossConfig.cdnDomain}/${filePath}`;
        }

        // 否则使用 OSS 域名
        return `https://${ossConfig.bucket}.${ossConfig.endpoint}/${filePath}`;
    }

    /**
     * 备用复制方法
     */
    fallbackCopyToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');
            this.showSuccess(this.options.i18n.linkCopied);
        } catch (err) {
            this.showError(this.options.i18n.copyFailed + text);
        }

        document.body.removeChild(textArea);
    }

    /**
     * 显示成功消息
     */
    showSuccess(message) {
        if (typeof Dcat !== 'undefined' && Dcat.success) {
            Dcat.success(message);
        } else {
            alert(message);
        }
    }

    /**
     * 显示错误消息
     */
    showError(message) {
        if (typeof Dcat !== 'undefined' && Dcat.error) {
            Dcat.error(message);
        } else {
            alert(message);
        }
    }
}

// 导出到全局
window.OssDirectUploader = OssDirectUploader;
