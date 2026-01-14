<?php

namespace Dcat\Admin\Form\Field;

use Illuminate\Support\Facades\URL;

/**
 * Private bucket multiple image upload field.
 *
 * Automatically generates proxy URL for image preview in private buckets.
 *
 * Usage:
 * $form->privateMultipleImage('images')->disk('oss-private');
 */
class PrivateMultipleImage extends MultipleImage
{
    /**
     * Current disk name.
     */
    protected string $diskName = '';

    /**
     * Set storage disk (override parent method to record disk name).
     *
     * @param  string  $disk
     * @return $this
     */
    public function disk($disk)
    {
        $this->diskName = $disk;

        return parent::disk($disk);
    }

    /**
     * Get file access URL (override parent method).
     *
     * @param  string  $path
     * @return string
     */
    public function objectUrl($path): string
    {
        if (URL::isValidUrl($path)) {
            return $path;
        }

        // For private disk, use proxy route to generate URL
        $privateDisk = config('admin.upload.oss.private_disk', 'oss-private');
        if ($this->diskName === $privateDisk) {
            return admin_url('dcat-api/oss/proxy/'.ltrim($path, '/'));
        }

        return $this->getStorage()->url($path);
    }
}
