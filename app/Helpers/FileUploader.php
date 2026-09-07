<?php
namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileUploader
{
    public static function uploadFile($file, string $path = "images", string $initials = "img"): string
    {
        if (isset($delete_path)) {
            Storage::disk('s3')->delete(substr($delete_path, 48));
        }
        // dd($file);
        $file_path = Storage::disk('s3')
            ->putFile('karmatalks/' . $path ?? 'images/', $file, 'public', $initials . '-' . rand(1, 1000) . sha1(time()) . "." . $file
                    ->getClientOriginalExtension());
        // dd($file_path);

        return Storage::disk('s3')->url($file_path);
    }

    //This function is for image size reduction it converts 5 mb image to almost 800 kb. and it does not has much impact in quality.
    public static function optimizeUploadFile($file, string $path = "images", string $initials = "img"): string
    {
        try {
            $filename = $initials . '-' . rand(1, 1000) . '-' . sha1(time()) . '.' . $file->getClientOriginalExtension();
            $image    = Image::make($file)->encode($file->getClientOriginalExtension(), 50); // 50% quality
            $s3Path   = "$path/$filename";
            Storage::disk('s3')->put($s3Path, (string) $image, 'public');
            return Storage::disk('s3')->url($s3Path);
        } catch (Exception $e) {
            \Log::info('Exception in optimized image upload: ' . $e->getMessage());
            throw new Exception("Failed to upload optimized image. Error: " . $e->getMessage());
        }
    }
}
