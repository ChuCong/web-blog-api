<?php

namespace App\Services;

use App\Core\CommonUtility;
use App\Core\ServiceResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Upload a file from request using 'file' param and move to /public/uploads.
     */
    public function uploadFile3(Request $request): ?string
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $path = public_path('uploads');

            return $file->move($path, $filename)->getPathname();
        }

        return null;
    }

    /**
     * Upload file to storage/app/public/[folder] using custom name.
     */
    public function uploadFile2(UploadedFile $file, string $folder = 'test'): string
    {
        if (!$file->isValid()) {
            return '';
        }

        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs("public/{$folder}", $filename);

        return Storage::url($path); // returns /storage/test/filename
    }

    /**
     * Upload file with random name into specific folder.
     */
    public function uploadFile(Request $request, string $paramUpload, string $relativePath): ?string
    {
        if (!$request->hasFile($paramUpload)) {
            return null;
        }

        $file = $request->file($paramUpload);
        $milliseconds = round(microtime(true) * 1000);
        $ext = $file->getClientOriginalExtension();
        $randName = CommonUtility::randomCharacter(5);
        $fileName = "{$milliseconds}_{$randName}.{$ext}";

        $file->storeAs($relativePath, $fileName, 'public');

        return App::make('url')->to('/') . "/storage/{$relativePath}/{$fileName}";
    }

    /**
     * Move file from tmp folder to permanent upload folder.
     */
    public function saveToDisk(string $fileUrl, string $relativePath): ServiceResponse
    {
        if (empty($fileUrl)) {
            return new ServiceResponse(0, 'File URL is empty', null);
        }

        $fileName = CommonUtility::getFileName($fileUrl);
        $targetDir = "upload/{$relativePath}";
        $targetPath = "{$targetDir}/{$fileName}";

        if (Storage::disk('public')->exists($targetPath)) {
            return new ServiceResponse(1, 'File already exists', $targetPath);
        }

        // Tạo thư mục nếu chưa tồn tại
        if (!Storage::disk('public')->exists($targetDir)) {
            Storage::disk('public')->makeDirectory($targetDir);
        }

        $sourcePath = "tmp/{$relativePath}/{$fileName}";

        if (!Storage::disk('public')->move($sourcePath, $targetPath)) {
            return new ServiceResponse(0, 'Failed to move file', null);
        }
        return new ServiceResponse(1, 'File saved successfully', $targetPath);
    }

    /**
     * Delete file from storage/public.
     */
    public function delete(string $url): ServiceResponse
    {
        $relativePath = Str::after($url, '/storage/');

        if (!Storage::disk('public')->delete($relativePath)) {
            return new ServiceResponse(0, 'Delete failed', null);
        }

        return new ServiceResponse(1, 'Delete successful', null);
    }
}
