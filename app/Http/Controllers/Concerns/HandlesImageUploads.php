<?php


namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


trait HandlesImageUploads {
    /**
     * Store a new image in the given folder on the `public` disk.
     * Returns the relative path (e.g. "brands/abc123.png") or null.
     */
    protected function uploadImage(?UploadedFile $file, string $folder): ?string
    {
        return $file?->store($folder, 'public');
    }

    /**
     * Replace an existing image: delete the old one, store the new one.
     * If no new file is given, returns the old path unchanged.
     */
    protected function replaceImage(?UploadedFile $file, ?string $oldPath, string $folder): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $file->store($folder, 'public');
    }

    /**
     * Delete an image from storage. Safe to call with null.
     */
    protected function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

}