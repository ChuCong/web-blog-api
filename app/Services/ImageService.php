<?php

namespace App\Services;

use App\Core\ServiceResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class ImageService
{
    public function uploadFromRequest(Request $request, string $fieldName, string $relativePath): ?string
    {
        if (!$request->hasFile($fieldName)) return null;

        $file = $request->file($fieldName);
        $filename = $this->makeFilename($file->getClientOriginalExtension());
        $file->storeAs($relativePath, $filename, 'public');

        return URL::to('/') . "/storage/$relativePath/$filename";
    }

    public function uploadFromFile($file, string $relativePath): string
    {
        $filename = $this->makeFilename($file->getClientOriginalExtension());
        $file->storeAs($relativePath, $filename, 'public');

        return URL::to('/') . "/storage/$relativePath/$filename";
    }

    public function moveFromTemp(string $fileUrl, string $relativePath)
    {
        if ($fileUrl === "") {
            return new ServiceResponse(0, "File url is empty", null);
        }

        $filename = basename($fileUrl);
        $oldPath = "tmp/$relativePath/$filename";
        $newPath = "upload/$relativePath/$filename";

        if (Storage::disk('public')->exists($newPath)) {
            return new ServiceResponse(1, "File was existed", $newPath);
        }

        if (!Storage::disk('public')->move($oldPath, $newPath)) {
            return new ServiceResponse(0, "Move file fail", null);
        };

        // $newPath = URL::to('/') . "/storage/$newPath";
        return new ServiceResponse(1, "save file success", $newPath);
    }

    private function makeFilename(string $ext): string
    {
        return now()->timestamp . '_' . Str::random(5) . '.' . $ext;
    }
}
