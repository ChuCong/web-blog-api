<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class AwsService
{
    public function __construct(){}

    public function uploadFileToS3($fileZip) {
        $bucket = env('AWS_BUCKET');
        $s3 = App::make('aws')->createClient('s3');
        // logger()->info($bucket);
        // logger()->info(basename($fileZip));

        $s3->putObject(array(
            'Bucket'     => $bucket,
            'Key'        => basename($fileZip),
            'SourceFile' => $fileZip,
        ));
    }
    // public function uploadFileToS3($fileZip) {
    //     $accessKey = env('AWS_ACCESS_KEY');
    //     $secretKey = env('AWS_SECRET_KEY');
    //     $bucket = env('AWS_BUCKET');

    //     $credentials = new \Aws\Credentials\Credentials($accessKey, $secretKey);

    //     $options = [
    //         'version'=>'latest',
    //         'region' => 'hn',
    //         'signature_version' => 'v4',
    //         'credentials' => $credentials,
    //         'endpoint' => 'https://ss-hn-1.bizflycloud.vn'
    //     ];

    //     $s3Client = new \Aws\S3\S3Client($options); 

    //     $buckets = $s3Client->listBuckets();

    //     logger()->info("Owner ID: %s\n" . $buckets['Owner']['ID']);
    //     logger()->info("Owner DisplayName: %s\n\n" . $buckets['Owner']['DisplayName']);

    //     logger()->info("Bucket Name\t\tCreation Date\n");
    //     foreach ($buckets['Buckets'] as $bucket) {
    //         logger()->info($bucket['Name']."\t\t". $bucket['CreationDate']."\n");
    //     }

    //     $uploader = new \Aws\S3\MultipartUploader($s3Client, $fileZip, [
    //         'bucket' => $bucket,
    //         'key'    => '',
    //     ]);

    //     $result = $uploader->upload();
    //     logger()->info("Upload complete: {$result['ObjectURL']}\n");

    //     //extract file

    //     return $result;
    // }
}
