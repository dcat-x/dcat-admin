<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="oss-direct-upload-container" data-field="{{ $column }}" id="container-{{ $id }}">

            {{-- 隐藏字段存储文件路径 --}}
            <input type="hidden" name="{{ $name }}" value="{{ is_array($value) ? ($value[0] ?? '') : $value }}" id="input-{{ $id }}" />

            {{-- 隐藏的文件输入 --}}
            <input type="file" class="file-input" id="file-input-{{ $id }}"
                   @if($acceptMimeTypes) accept="{{ $acceptMimeTypes }}" @endif />

            {{-- 上传区域 --}}
            <div class="upload-area" id="upload-area-{{ $id }}" @if(($value && !is_array($value)) || (is_array($value) && !empty($value[0]))) style="display:none;" @endif>
                <i class="feather icon-upload"></i>
                <p>{{ __('admin.oss_upload.drag_or_click') }}</p>
                <p class="text-muted">
                    @if($help)
                        @if(is_array($help))
                            {{ $help['text'] ?? '' }}
                        @else
                            {{ $help }}
                        @endif
                    @else
                        {{ __('admin.oss_upload.max_size', ['size' => $maxSize]) }}
                        @if($accept !== '*')
                            {{ __('admin.oss_upload.allowed_formats', ['formats' => $accept]) }}
                        @endif
                    @endif
                </p>
            </div>

            {{-- 上传进度 --}}
            <div class="upload-progress" style="display:none;">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%;"
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
                <div class="upload-info">
                    <span class="filename"></span>
                    <span class="upload-speed"></span>
                </div>
            </div>

            {{-- 文件预览 --}}
            <div class="upload-preview" @if(!$value || (is_array($value) && empty($value[0]))) style="display:none;" @endif>
                <div class="preview-item">
                    <div class="file-info">
                        <i class="feather icon-file"></i>
                        <div class="file-details">
                            <span class="preview-filename" title="{{ is_array($value) ? ($value[0] ?? '') : $value }}">
                                {{ is_array($value) ? basename($value[0] ?? '') : basename($value) }}
                            </span>
                            <span class="file-path">{{ is_array($value) ? ($value[0] ?? '') : $value }}</span>
                        </div>
                    </div>
                    <div class="file-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-copy-url" title="{{ __('admin.oss_upload.copy_link') }}">
                            <i class="feather icon-copy"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="{{ __('admin.oss_upload.delete') }}">
                            <i class="feather icon-trash-2"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success btn-reupload" title="{{ __('admin.oss_upload.reupload') }}">
                            <i class="feather icon-upload"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>

{{-- 引入阿里云 OSS JavaScript SDK --}}
<script src="https://gosspublic.alicdn.com/aliyun-oss-sdk-6.18.1.min.js"></script>

{{-- 初始化上传组件 --}}
<script>
(function() {
    const uploader = new OssDirectUploader({
        container: '#container-{{ $id }}',
        fieldName: '{{ $name }}',
        maxSize: {{ $maxSize }},
        chunkSize: {{ $chunkSize }},
        accept: '{{ $accept }}',
        @if($acceptMimeTypes)
        acceptMimeTypes: '{{ $acceptMimeTypes }}',
        @endif
        uploadType: '{{ $uploadType }}',
        @if($uploadDirectory)
        uploadDirectory: '{{ $uploadDirectory }}',
        @endif
        token: '{{ csrf_token() }}',
        stsTokenUrl: '{{ $stsTokenUrl }}',
        i18n: {
            uploadSuccess: '{{ __('admin.oss_upload.success') }}',
            uploadFailed: '{{ __('admin.oss_upload.failed') }}',
            fileDeleted: '{{ __('admin.oss_upload.deleted') }}',
            linkCopied: '{{ __('admin.oss_upload.link_copied') }}',
            copyFailed: '{{ __('admin.oss_upload.copy_failed') }}',
            confirmDelete: '{{ __('admin.oss_upload.confirm_delete') }}',
            noFilePath: '{{ __('admin.oss_upload.no_file_path') }}',
            fileTooLarge: '{{ __('admin.oss_upload.file_too_large') }}',
            unsupportedType: '{{ __('admin.oss_upload.unsupported_type') }}',
            stsError: '{{ __('admin.oss_upload.sts_error') }}'
        },
        onProgress: function(percent, uploaded, total) {
            // console.log('上传进度:', percent + '%');
        },
        onSuccess: function(objectKey, file) {
            console.log('上传成功:', objectKey);
        },
        onError: function(error) {
            console.error('上传失败:', error);
        }
    });
})();
</script>
