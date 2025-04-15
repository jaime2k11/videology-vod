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
        $cloudfrontUrl = rtrim(getenv('CLOUDFRONT_URL'), '/');
        $videos = [];
        try {
            $result = $this->s3->listObjects([
                'Bucket' => $bucket,
                'Prefix' => 'videos/'
            ]);

            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $key = $object['Key'];

                    if (str_ends_with($key, '.m3u8')) {
                        $parts = explode('/', $key);
                        $baseId = $parts[1]; // carpeta base del video
                        $filename = basename($key); // e.g., 1743617263_65850cafbd986115ec24_720p.m3u8

                        // Separar resolución al final del nombre antes de la extensión
                        $filenameWithoutExt = str_replace('.m3u8', '', $filename);
                        $underscorePos = strrpos($filenameWithoutExt, '_');
                        $baseName = substr($filenameWithoutExt, 0, $underscorePos);
                        $resolution = substr($filenameWithoutExt, $underscorePos + 1);

                        // URL final
                        $videoUrl = rtrim($cloudfrontUrl, '/') . '/' . ltrim($key, '/');

                        // Agrupar resoluciones bajo el mismo video
                        if(in_array($resolution, ['360p', '480p', '720p', '1080p'])){
                            $videos[$baseId]['resolutions'][$resolution] = $videoUrl;
                        }
                        else{
                            $videos[$baseId]['base_name'] = $baseName;
                            $videos[$baseId]['url'] = $videoUrl;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->to('/')->with('error', 'Error al listar videos: ' . $e->getMessage());
        }

        return view('video_list', ['videos' => $videos]);
    }
}

