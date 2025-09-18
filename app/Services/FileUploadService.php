<?php

namespace App\Services;

use App\Core\AppConst;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class FileUploadService
{

    /**
     * Upload
     * @param 
     */
    public function upload($files, $folderPath, $uploadConfigName = AppConst::UPLOAD_CONFIG_NAME)
    {
        $arrFileUploaded = [];
        $basePath = Config::get('filesystems.disks.' . $uploadConfigName . '.base_path');
        foreach ($files as $file) {
            $originFileName = urlencode($file->getClientOriginalName());
            $ext = pathinfo($originFileName, PATHINFO_EXTENSION);
            $uploadType = $this->getUploadType($ext);
            $fileName = $this->getFileName($file);
            $fullUrl = $basePath . $folderPath . '/' . $uploadType . "/" . $fileName;
            $file->storeAs($folderPath . '/' . $uploadType, $fileName, $uploadConfigName);
            $fullFilePath = $folderPath. '/' . $uploadType . "/" . $fileName;
            $arrFileUploaded[] = [
                'fileName' => $fileName,
                'path' => $fullFilePath,
                'url' => url($fullUrl),
                'ext' => $ext
            ];
        }

        return $arrFileUploaded;
    }

    public function getUploadType($ext)
    {
        if (in_array($ext, AppConst::FILE_UPLOAD_EXTENSION[AppConst::MEDIA_TYPE_EXCEL])) {
            return AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_EXCEL];
        } else if (in_array($ext, AppConst::FILE_UPLOAD_EXTENSION[AppConst::MEDIA_TYPE_ZIP])) {
            return AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_ZIP];
        } else if (in_array($ext, AppConst::FILE_UPLOAD_EXTENSION[AppConst::MEDIA_TYPE_WORD])) {
            return AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_WORD];
        } else if (in_array($ext, AppConst::FILE_UPLOAD_EXTENSION[AppConst::MEDIA_TYPE_PDF])) {
            return AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_PDF];
        } else if (in_array($ext, AppConst::FILE_UPLOAD_EXTENSION[AppConst::MEDIA_TYPE_IMAGE])) {
            return AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_IMAGE];
        } else if (in_array($ext, AppConst::FILE_UPLOAD_EXTENSION[AppConst::MEDIA_TYPE_VIDEO])) {
            return AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_VIDEO];
        } else if (in_array($ext, AppConst::FILE_UPLOAD_EXTENSION[AppConst::MEDIA_TYPE_AUDIO])) {
            return AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_AUDIO];
        } else {
            return AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_EXCEL];
        }
    }

    // private function getFileName($file){
    //     $originFileName = urlencode($file->getClientOriginalName());
    //     $milliseconds = round(microtime(true) * 1000);
    //     $ext = pathinfo($originFileName, PATHINFO_EXTENSION);
    //     $name = pathinfo($originFileName, PATHINFO_FILENAME);
    //     return Str::customSlug($name) . "_" . $milliseconds . ".$ext";
    // }

    private function getFileName($file)
    {
        $originFileName = urlencode($file->getClientOriginalName());
        $milliseconds = round(microtime(true) * 1000);
        $ext = pathinfo($originFileName, PATHINFO_EXTENSION);
        $name = pathinfo($originFileName, PATHINFO_FILENAME);

        if ($ext == AppConst::FILE_UPLOAD_TYPES[AppConst::MEDIA_TYPE_ZIP]) {
            return $name . ".$ext";
        } else {
            return Str::slug($name) . "_" . $milliseconds . ".$ext";
        }
    }
}
