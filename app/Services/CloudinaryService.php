<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;
    protected $uploadApi;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);

        $this->uploadApi = new UploadApi();
    }

    /**
     * Upload file to Cloudinary
     * 
     * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\File $file
     * @param string $folder (e.g., 'products', 'brands', 'promotions')
     * @return string Public URL
     */
    public function upload($file, $folder = 'uploads')
    {
        try {
            $result = $this->uploadApi->upload($file->getRealPath(), [
                'folder' => 'shop-quan-ao/' . $folder,
                'resource_type' => 'auto',
                'quality' => 'auto:good',
                'transformation' => [
                    'quality' => 'auto:good',
                    'fetch_format' => 'auto',
                ],
            ]);

            return $result['secure_url'];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage());
            throw new \Exception('Failed to upload image to Cloudinary: ' . $e->getMessage());
        }
    }

    /**
     * Upload from URL
     * 
     * @param string $url
     * @param string $folder
     * @return string Public URL
     */
    public function uploadFromUrl($url, $folder = 'uploads')
    {
        try {
            $result = $this->uploadApi->upload($url, [
                'folder' => 'shop-quan-ao/' . $folder,
                'resource_type' => 'auto',
            ]);

            return $result['secure_url'];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload from URL failed: ' . $e->getMessage());
            throw new \Exception('Failed to upload image from URL: ' . $e->getMessage());
        }
    }

    /**
     * Delete file from Cloudinary
     * 
     * @param string $publicId (e.g., 'shop-quan-ao/products/abc123')
     * @return bool
     */
    public function delete($publicId)
    {
        try {
            $this->uploadApi->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract public_id from Cloudinary URL
     * 
     * @param string $url
     * @return string|null
     */
    public function getPublicIdFromUrl($url)
    {
        // URL format: https://res.cloudinary.com/{cloud_name}/image/upload/v{version}/{public_id}.{format}
        if (preg_match('/\/v\d+\/(.+)\.\w+$/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Delete file from Cloudinary by URL
     * 
     * @param string $url
     * @return bool
     */
    public function deleteByUrl($url)
    {
        $publicId = $this->getPublicIdFromUrl($url);
        if ($publicId) {
            return $this->delete($publicId);
        }
        return false;
    }
}
