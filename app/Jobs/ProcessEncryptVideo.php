<?php

namespace App\Jobs;

use App\Core\CommonUtility;
use App\Models\EncryptMediaProcess;
use App\Models\Setting;
use App\Services\AwsService;
use App\Services\EncryptMediaProcessService;
use App\Services\MediaService;
use App\Services\SettingService;
use App\Services\WhisperService;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessEncryptVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    const FILE_ENCRYPT_NAME = '1080p.m3u8';

    private $pathFile;
    private $lesson;
    private EncryptMediaProcessService $encryptMediaProcessService;
    private AwsService $awsService;
    private MediaService $mediaService;
    private WhisperService $whisperService;
    private settingService $settingService;
    private $videoConfig;
    private $watermark;
    private $storage;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 18000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pathFile, $lesson)
    {
        $this->pathFile = $pathFile;
        $this->lesson = $lesson;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        EncryptMediaProcessService $encryptMediaProcessService,
        AwsService $awsService,
        MediaService $mediaService,
        WhisperService $whisperService,
        SettingService $settingService
    ) {
        try {
            $this->encryptMediaProcessService = $encryptMediaProcessService;
            $this->awsService = $awsService;
            $this->mediaService = $mediaService;
            $this->whisperService = $whisperService;
            $this->settingService = $settingService;
            $this->detailHandle();
        } catch (Exception $exception) {
            logger()->error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            $this->saveError($exception->getMessage());
        }
    }

    private function detailHandle()
    {
        $this->removeOldProcessInfo();
        $this->saveStart(EncryptMediaProcess::STATUS_START);
        $this->getSettingVideo();
        $fileInfo = $this->moveFileToLessonFolder($this->pathFile);
        if (!$fileInfo) {
            throw new Exception('move file to lesson folder fail.');
        }
        $this->saveEnd(EncryptMediaProcess::STATUS_START);

        $videoPath = $fileInfo['folder'] . '/' . $fileInfo['file_name'];
        $outputVideoPath = $fileInfo['folder'] . '/watermarked_' . $fileInfo['file_name'];

        // 1. Export audio từ video trước
        if ($this->videoConfig['auto_transcript']) {
            $media = $this->lesson['medias'][0] ?? null;
            $currentFileName = basename($videoPath);
            $skipTranscript = false;
            if (
                $media &&
                isset($media['src']) &&
                basename($media['src']) === $currentFileName &&
                !empty($media['vi_sub']) && file_exists(public_path('storage/' . $media['vi_sub'])) &&
                !empty($media['en_sub']) && file_exists(public_path('storage/' . $media['en_sub']))
            ) {
                $skipTranscript = true;
                Log::info("Skip export audio & transcript: transcript files already exist for this video.");
            }

            $this->saveStart(EncryptMediaProcess::STATUS_ADD_TRANSCRIPT);
            if (!$skipTranscript) {
                $audioPath = $this->exportAudioFromVideo($videoPath, $fileInfo['folder']);
                $transcriptData = $this->generate($audioPath, $fileInfo['folder']);
                Log::info($transcriptData);
                $this->saveTranscriptPath($transcriptData);
            }
            $this->saveEnd(EncryptMediaProcess::STATUS_ADD_TRANSCRIPT);
        }

        // 2. Thêm watermark
        if ($this->videoConfig['use_watermark']) {
            $this->saveStart(EncryptMediaProcess::STATUS_ADD_WATER_MARK);
            $this->addWatermarkToVideo($videoPath, $outputVideoPath);
            $this->saveWatermarkPath($outputVideoPath);
            $this->saveEnd(EncryptMediaProcess::STATUS_ADD_WATER_MARK);
        }

        // 3. Encrypt video với file đã watermark
        $this->saveStart(EncryptMediaProcess::STATUS_ENCRYPTING);
        $fileInfo['file_name'] = 'watermarked_' . $fileInfo['file_name'];
        $encryptResult = $this->encryptVideo($fileInfo);
        if ($encryptResult) {
            // $this->deleteSourceFile($fileInfo);
            $pathEncryptVideoFolder = $this->getFolderPathOfEncryptVideo($fileInfo);
            if ($pathEncryptVideoFolder) {
                $this->saveEncryptLink($pathEncryptVideoFolder);
            } else {
                throw new Exception('folder path of encypt video doesnt exist.');
            }
        } else {
            throw new Exception('encrypt video fail.');
        }
        $this->saveEnd(EncryptMediaProcess::STATUS_ENCRYPTING);

        if ($this->videoConfig['store_on_s3']) {
            $this->saveStart(EncryptMediaProcess::STATUS_PUSH_TO_STORAGE);
            $this->removeOldMediaFolderOnS3();
            $this->uploadToS3($pathEncryptVideoFolder);
            // $this->removeEncryptFolder($pathEncryptVideoFolder);
            $s3FileUrl = $this->getS3Path($fileInfo);
            $this->saveS3Url($s3FileUrl);
            $this->saveStart(EncryptMediaProcess::STATUS_PUSH_TO_STORAGE);
        }
        $this->saveStart(EncryptMediaProcess::STATUS_COMPLETE);
    }

    private function exportAudioFromVideo($videoPath, $outputFolder)
    {
        $audioPath = $outputFolder . '/' . pathinfo($videoPath, PATHINFO_FILENAME) . '.mp3';
        $cmdAudio = "ffmpeg -i \"$videoPath\" -q:a 0 -map a \"$audioPath\" -y";
        exec($cmdAudio, $outputAudio, $returnAudio);
        if ($returnAudio !== 0) {
            throw new Exception('Export audio failed: ' . implode("\n", $outputAudio));
        }
        return $audioPath;
    }

    private function addWatermarkToVideo($videoPath, $outputVideoPath)
    {
        $videoConfig = $this->videoConfig;
        $watermark = $this->watermark;
        $type = $videoConfig['watermark_type'] ?? 'text';
        Log::info('type: ' . $type);

        if ($type === 'text') {
            $text = $watermark['text']['text'] ?? 'MSD';
            $position = $watermark['text']['position'] ?? 'bottom-right';
            $x = $watermark['text']['x'] ?? 20;
            $y = $watermark['text']['y'] ?? 20;
            $fontColor = $watermark['text']['fontcolor'] ?? '#F27619';
            $fontSize = $watermark['text']['fontsize'] ?? 72;

            // Xử lý vị trí cho drawtext
            switch ($position) {
                case 'top-left':
                    $drawX = $x;
                    $drawY = $y;
                    break;
                case 'top-right':
                    $drawX = "(w-text_w)-$x";
                    $drawY = $y;
                    break;
                case 'bottom-left':
                    $drawX = $x;
                    $drawY = "(h-text_h)-$y";
                    break;
                case 'bottom-right':
                default:
                    $drawX = "(w-text_w)-$x";
                    $drawY = "(h-text_h)-$y";
                    break;
            }

            $cmd = "ffmpeg -i \"$videoPath\" -vf \"drawtext=text='$text':fontcolor=$fontColor:fontsize=$fontSize:x=$drawX:y=$drawY:box=1\" -codec:a copy \"$outputVideoPath\" -y";
        } elseif ($type === 'image') {
            $imageUrl = $watermark['image']['image_url'] ?? '';
            $position = $watermark['image']['position'] ?? 'top-left';
            $x = $watermark['image']['x'] ?? 10;
            $y = $watermark['image']['y'] ?? 10;

            Log::info('imageUrl: ' . $imageUrl);

            // Nếu là URL thì tải về file tạm
            if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $imagePath = sys_get_temp_dir() . '/' . basename(parse_url($imageUrl, PHP_URL_PATH));
                file_put_contents($imagePath, file_get_contents($imageUrl));
            } else {
                $imagePath = $imageUrl;
            }

            // Xử lý vị trí cho overlay
            switch ($position) {
                case 'top-left':
                    $overlayX = $x;
                    $overlayY = $y;
                    break;
                case 'top-right':
                    $overlayX = "main_w-overlay_w-$x";
                    $overlayY = $y;
                    break;
                case 'bottom-left':
                    $overlayX = $x;
                    $overlayY = "main_h-overlay_h-$y";
                    break;
                case 'bottom-right':
                default:
                    $overlayX = "main_w-overlay_w-$x";
                    $overlayY = "main_h-overlay_h-$y";
                    break;
            }

            $cmd = "ffmpeg -i \"$videoPath\" -i \"$imagePath\" -filter_complex \"overlay=$overlayX:$overlayY\" -codec:a copy \"$outputVideoPath\" -y";
        } else {
            throw new Exception('Invalid watermark type');
        }

        exec($cmd, $output, $returnVar);
        if ($returnVar !== 0) {
            Log::error($output);
            throw new Exception('Add watermark failed: ' . json_encode($output));
        }
    }

    private function getSettingVideo() {
        $videoConfig = $this->settingService->findByKey(Setting::VIDEO_CONFIG_KEY);
        $this->videoConfig = json_decode($videoConfig->value, true);
        $watermark = $this->settingService->findByKey(Setting::WATERMARK_KEY);
        $this->watermark = json_decode($watermark->value, true);
        $storage = $this->settingService->findByKey(Setting::STORAGE_KEY);
        $this->storage = json_decode($storage->value, true);
    }

    private function removeOldProcessInfo()
    {
        $this->encryptMediaProcessService->deleteEncryptVideoProcess($this->lesson['id']);
    }
    
    private function removeOldMediaFolderOnS3()
    {
        $this->removeMediaOnS3($this->lesson['medias']);
    }

    private function removeMediaOnS3($medias)
    {
        if ($medias && count($medias) > 0) {
            $mediaS3Url = $medias[0]['s3_url'];
            if ($mediaS3Url && !empty($mediaS3Url)) {
                // https://hanquocnori-test.s3.ap-southeast-1.amazonaws.com/700/1628156894204_PWuIx_dash.mpd
                logger()->info('start remove media on s3 from link: ' . $mediaS3Url);

                // $bucket = config('aws.bucket');
                $storage = $this->storage;
                $bucket = $storage['s3']['bucket'];

                $parrentPath = dirname($mediaS3Url);
                $nameParentFolder = basename($parrentPath);
                logger()->info('remove folder: ' . $nameParentFolder);
                //  aws s3 rm s3://bucket-name/example --recursive
                $process = new Process(['aws', 's3', 'rm', 's3://' . $bucket . '/' . $nameParentFolder, '--recursive']);
                $process->setTimeout(18000);
                $process->setPTY(true);

                // $process->run();
                $process->start();
                $process->wait();
                logger()->info($process->getExitCodeText());
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                } else {
                }
                logger()->info('end remove old media on s3 folder');
            }
        }
    }

    private function moveFileToLessonFolder($fileUrl)
    {
        $lessonId = $this->lesson['id'];
        $fileName = CommonUtility::getFileName($fileUrl);
        $oldPath = "upload/lessons/" . $fileName;
        $folder = "upload/lessons/" . $lessonId;
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        $newPath = $folder . '/' . $fileName;
        logger()->info('old path: ' . $oldPath);
        logger()->info('new path: ' . $newPath);
        if (Storage::disk('public')->copy($oldPath, $newPath)) {
            $folderPath = public_path("/storage/upload/lessons/" . $lessonId);
            $path = public_path("/storage/upload/lessons/" . $lessonId . "/" . $fileName);
            exec('chmod -R 777 ' . $folderPath);
            exec('chmod 777 ' . $path);
            logger()->info('file video path: ' . $path);
            return [
                'folder' => $folderPath,
                'file_name' => $fileName,
                'folder_name' => $lessonId,
                'parrent_folder' => public_path("/storage/upload/lessons")
            ];
        }
        return null;
    }

    private function encryptVideo($fileInfo)
    {
        logger()->info('start encrypt video');
        $encryptFilePath = config('app.encrypt_file_path');
        logger()->info('file encript: ' . $encryptFilePath);

        copy($encryptFilePath, $fileInfo['folder'] . '/hls.sh');

        //encrypt video
        // perl transcode.pl $FILENAME
        // $process = new Process(['perl', 'transcode.pl', $fileInfo['file_name']], $fileInfo['folder']);

        //HiepTH updated 30/11
        //bash hls.sh $FILENAME
        $process = new Process(['bash', 'hls.sh', $fileInfo['file_name']], $fileInfo['folder']);
        //HiepTH end updated
        $process->setTimeout(18000);
        $process->setPTY(true);

        // $process->run();
        $process->start();
        $process->wait();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
        }

        logger()->info('complete encrypt video');
        return true;
    }


    private function deleteSourceFile($fileInfo)
    {
        logger()->info('remove file: ' . $fileInfo['folder'] . '/' . $fileInfo['file_name']);
        $process = new Process(['rm', $fileInfo['file_name']], $fileInfo['folder']);
        $process->setTimeout(1800);
        $process->setPTY(true);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
        }

        logger()->info('end remove file');
    }

    private function removeEncryptFolder($pathEncryptVideoFolder)
    {
        logger()->info('remove folder: ' . $pathEncryptVideoFolder);
        $process = new Process(['rm', '-rf', $pathEncryptVideoFolder]);
        $process->setTimeout(1800);
        $process->setPTY(true);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
        }
    }

    private function getFolderPathOfEncryptVideo($fileInfo)
    {
        $path = $fileInfo['parrent_folder'] . '/' . $fileInfo['folder_name'] 
            . '/' . pathinfo($fileInfo['file_name'], PATHINFO_FILENAME);
        if (!File::exists($path)) {
            return null;
        }
        return $path;
    }

    private function uploadToS3($pathFolderVideo)
    {
        $storage = $this->storage;
        logger()->info('start upload folder to s3 by aws cli:  ' . $pathFolderVideo);
        // $bucket = config('aws.bucket');
        $bucket = $storage['s3']['bucket'];
        logger()->info('bucket name:  ' . $bucket);
        // aws s3 cp local_folder_path s3://bucket_name/ --recursive
        $process = new Process([
            'aws',
            's3',
            'cp',
            $pathFolderVideo,
            's3://' . $bucket . '/' . basename($pathFolderVideo),
            '--recursive'
        ]);
        $process->setTimeout(18000);
        $process->setPTY(true);

        // $process->run();
        $process->start();
        $process->wait();

        logger()->info($process->getExitCodeText());
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
        }

        logger()->info('end upload to s3 folder');
    }

    private function getS3Path($fileInfo)
    {
        $storage = $this->storage;
        // $awsObjectUrlParent = config('aws.aws_object_url');
        $awsObjectUrlParent = $storage['s3']['endpoint'];
        // return ['folder' => $folderPath, 'file_name' => $fileName, 'folder_name' => $lessonId,
        //         'parrent_folder' => public_path("/storage/upload/lessons")];
        // https://hanquocnori-test.s3.ap-southeast-1.amazonaws.com/588/1627320623484_tnBX0_dash.mpd
        $folderName = $fileInfo['folder_name'];
        $sourceFileName = pathinfo($fileInfo['file_name'], PATHINFO_FILENAME);
        // $encryptFile = $sourceFileName . '_dash.mpd';

        //HiepTH updated 30/11
        // $encryptFile = '1080p.m3u8';
        $encryptFile = self::FILE_ENCRYPT_NAME;
        //HiepTH end updated

        return $awsObjectUrlParent . '/' . $folderName . '/' . $sourceFileName . '/' . $encryptFile;
    }

    private function saveStart($status)
    {
        $start = Carbon::now();
        $mediaId = $this->lesson['medias'][0]['id'];
        $this->encryptMediaProcessService->saveStartStatus(
            $this->lesson['id'],
            $start,
            $status,
            'start handle encrypt video and upload to s3',
            $mediaId
        );
    }

    private function saveEnd($status)
    {
        $end = Carbon::now();
        $this->encryptMediaProcessService->saveEndStatus(
            $this->lesson['id'],
            $status,
            $end
        );
    }

    private function saveError($message, $pid = null)
    {
        $start = Carbon::now();
        $mediaId = $this->lesson['medias'][0]['id'];
        $this->encryptMediaProcessService->saveError(
            $this->lesson['id'],
            EncryptMediaProcess::STATUS_ERROR,
            $mediaId,
            $message,
            $start,
            $pid
        );
    }

    private function saveTranscriptPath($transcriptData)
    {
        $mediaId = $this->lesson['medias'][0]['id'];
        $viPath = $transcriptData['vi_sub'];
        $enPath = $transcriptData['en_sub'];
        $audio = $transcriptData['audio'];

        $storagePrefix = public_path('storage') . '/';
        if (str_starts_with($viPath, $storagePrefix)) {
            $viRelativeFolder = substr($viPath, strlen($storagePrefix));
        } else {
            $viRelativeFolder = $viPath;
        }

        if (str_starts_with($enPath, $storagePrefix)) {
            $enRelativeFolder = substr($enPath, strlen($storagePrefix));
        } else {
            $enRelativeFolder = $enPath;
        }

        if (str_starts_with($audio, $storagePrefix)) {
            $audioRelativeFolder = substr($audio, strlen($storagePrefix));
        } else {
            $audioRelativeFolder = $audio;
        }

        $data = [
            'id' => $mediaId,
            'vi_sub' => $viRelativeFolder,
            'en_sub' => $enRelativeFolder,
            'audio' => $audioRelativeFolder
        ];
        $this->mediaService->updateOrCreate($data);
    }

    private function saveWatermarkPath($watermarkFile)
    {
        $mediaId = $this->lesson['medias'][0]['id'];
        $storagePrefix = public_path('storage') . '/';
        if (str_starts_with($watermarkFile, $storagePrefix)) {
            $watermarkRelativeFolder = substr($watermarkFile, strlen($storagePrefix));
        } else {
            $watermarkRelativeFolder = $watermarkFile;
        }

        $data = [
            'id' => $mediaId,
            'watermark' => $watermarkRelativeFolder
        ];
        $this->mediaService->updateOrCreate($data);
    }

    private function saveEncryptLink($folder)
    {
        $mediaId = $this->lesson['medias'][0]['id'];

        $storagePrefix = public_path('storage') . '/';
        if (str_starts_with($folder, $storagePrefix)) {
            $relativeFolder = substr($folder, strlen($storagePrefix));
        } else {
            $relativeFolder = $folder;
        }

        $data = [
            'id' => $mediaId,
            'source_encrypt_folder' => $relativeFolder,
            'source_encrypt_file_path' => $relativeFolder . '/' . self::FILE_ENCRYPT_NAME
        ];
        $this->mediaService->updateOrCreate($data);
    }

    private function saveS3Url($s3Url)
    {
        $mediaId = $this->lesson['medias'][0]['id'];
        $data = ['id' => $mediaId, 's3_url' => $s3Url];
        $this->mediaService->updateOrCreate($data);
    }

    public function generate($audioPath, $outputFolder)
    {
        Log::info("Get vi script");
        $viResult = $this->whisperService->transcribe($audioPath, 'vi');
        $this->whisperService->generateVtt($viResult['segments'], $outputFolder . '/sub_vi.vtt');

        Log::info("Get en script");
        $enSegments = [];
        foreach ($viResult['segments'] as $seg) {
            $enText = $this->whisperService->translateTextToEn($seg['text']);
            $enSegments[] = [
                'start' => $seg['start'],
                'end' => $seg['end'],
                'text' => $enText,
            ];
        }
        $this->whisperService->generateVtt($enSegments, $outputFolder . '/sub_en.vtt');

        return [
            'vi_sub' => $outputFolder . '/sub_vi.vtt',
            'en_sub' => $outputFolder . '/sub_en.vtt',
            'audio' => $audioPath
        ];
    }
}
