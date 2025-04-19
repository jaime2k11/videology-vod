<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Videos Subidos</title>
        <!-- Video.js Core -->
        <link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet" />
        <script src="https://vjs.zencdn.net/7.15.4/video.min.js"></script>

        <!-- HLS.js (necesario en navegadores que no soportan HLS nativamente) -->
        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

        <!-- videojs-contrib-quality-levels (REQUIRED) -->
        <script src="https://cdn.jsdelivr.net/npm/videojs-contrib-quality-levels@2.0.9/dist/videojs-contrib-quality-levels.min.js"></script>

        <!-- videojs-hls-quality-selector (para selector de calidad) -->
        <script src="https://cdn.jsdelivr.net/npm/videojs-hls-quality-selector@1.1.1/dist/videojs-hls-quality-selector.min.js"></script>
        <style>
            html {
              font-family: Arial, sans-serif;
            }
            .video-container {
                max-width: 720px;
            }
        </style>
    </head>
    <body>
        <h1>Videos Subidos</h1>
        <?php if (!empty($videos)): ?>

            <?php $i=0; foreach ($videos as $video):
                $video_id = "video-".esc($video['base_name']);
                $player = "player_".($i++);
                ?>
                <h2><?= esc($video['base_name']) ?></h2>
                <div class="video-container">
                    <video id="<?=$video_id?>" class="video-js vjs-default-skin" controls preload="auto" width="640" height="360">
                        <source src="<?= $video['url']?>" type="application/x-mpegURL"/>
                        
                    </video>
                </div>
                <p><strong>m3u8 URL:</strong>
                    <a href="<?= $video['url'] ?>" target="_blank">
                        <?= $video['url'] ?>
                    </a>
                </p>
                <script>
                    var <?=$player?> = videojs("<?=$video_id?>", {
                        techOrder: ["html5"],
                        html5: {
                            nativeAudioTracks: false,
                            nativeVideoTracks: false
                        },
                        fluid: true,
                        preload: 'auto'
                    });

                    <?=$player?>.hlsQualitySelector({
                        displayCurrentQuality: true
                    });
                </script>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay videos disponibles.</p>
        <?php endif; ?>

        <a href="<?= base_url('/') ?>">Subir otro video</a>

    </body>
</html>

