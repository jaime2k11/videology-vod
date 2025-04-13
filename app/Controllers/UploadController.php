<?php

namespace App\Controllers;

use Aws\S3\S3Client;
use CodeIgniter\Controller;

class UploadController extends Controller
{
    private $s3;

    public function __construct()
    {
        $this->s3 = new S3Client([
            'region'  => getenv('AWS_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => getenv('AWS_ACCESS_KEY_ID'),
                'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function test(){
        return "Funciona correctamente desde CLI 🚀";
    }

    public function index()
    {
        return view('upload_form');
    }

    public function upload()
    {
        $file = $this->request->getFile('video');
        
        if (!$file->isValid()) {
            return redirect()->to('/')->with('error', 'Archivo no válido.');
        }

        $filename = $file->getRandomName();
        $filePath = $file->getTempName();

        // Subir archivo a S3
        $bucket = getenv('AWS_BUCKET');
        try {
            $this->s3->putObject([
                'Bucket' => $bucket,
                'Key'    => 'videos/' . $filename,
                'SourceFile' => $filePath
            ]);
        } catch (\Exception $e) {
            return redirect()->to('/')->with('error', 'Error al subir: ' . $e->getMessage());
        }

        return redirect()->to('/videos')->with('success', 'Video subido con éxito.');
    }

    public function list()
    {
        $bucket = getenv('AWS_OUTPUT_BUCKET');
        try {
            $result = $this->s3->listObjects([
                'Bucket' => $bucket,
                'Prefix' => 'videos/'
            ]);

            $videos = [];
            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    if (strpos($object['Key'], '.m3u8') !== false) {
                        $videoName = explode('/', $object['Key']);
                        $videoName = end($videoName);
                        $cloudfrontUrl = getenv('CLOUDFRONT_URL');
                        $videoPath = $object['Key']; // Ruta del video en S3 (sin el bucket)
                        // Construye la URL final con CloudFront
                        $videoUrl = rtrim($cloudfrontUrl, '/') . '/' . ltrim($videoPath, '/');

                        $videos[] = [
                            'name' => $videoName,
                            'url' => $videoUrl
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->to('/')->with('error', 'Error al listar videos: ' . $e->getMessage());
        }

        return view('video_list', ['videos' => $videos]);
    }
}

