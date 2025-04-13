<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Videos Subidos</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body>
    <h1>Videos Subidos</h1>
    <?php if (!empty($videos)): ?>
        <?php foreach ($videos as $video): ?>
            <h2><?= esc($video['name']) ?></h2>
            <video id="video-<?= esc($video['name']) ?>" controls></video>
            <script>
                if (Hls.isSupported()) {
                    var video = document.getElementById("video-<?= esc($video['name']) ?>");
                    var hls = new Hls();
                    hls.loadSource("<?= esc($video['url']) ?>");
                    hls.attachMedia(video);
                    hls.on(Hls.Events.MANIFEST_PARSED, function() {
                        video.play();
                    });
                }
            </script>
            <p><a href="<?= esc($video['url']) ?>" target="_blank">Abrir en otra pestaña</a></p>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay videos disponibles.</p>
    <?php endif; ?>

    <a href="<?= base_url('/') ?>">Subir otro video</a>
</body>
</html>

