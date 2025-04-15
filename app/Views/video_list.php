<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Videos Subidos</title>
    <link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/7.15.4/video.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        select[id^=quality-select] {
    padding: 5px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: white;
    cursor: pointer;
}
    </style>
</head>
<body>
    <h1>Videos Subidos</h1>
    <?php if (!empty($videos)): ?>

        <?php $count=0; foreach ($videos as $video): ?>
            <h2><?= esc($video['url']) ?></h2>
            <video id="video-<?= esc($video['base_name']) ?>" class="video-js vjs-default-skin" controls preload="auto" width="640" height="360">
                <?php foreach(['360p', '480p', '720p', '1080p'] as $res){
                    if(isset($video['resolutions'][$res])){?>
                        <source src="<?= $video['resolutions'][$res]?>" type="application/x-mpegURL" data-res="<?=$res?>"/>
                <?php }}?>
                
            </video>
            <div id="quality-container-<?= esc($video['base_name']) ?>">
                <select id="quality-selector-<?= esc($video['base_name']) ?>">
                    <?php foreach(['360p', '480p', '720p', '1080p'] as $res){
                        if(isset($video['resolutions'][$res])){?>
                            <option value="<?=$res;?>"><?=$res;?></option>
                    <?php    }
                    }?>

                    <!-- Add more quality options -->
                </select>
            </div>
            <p><strong>URL (m3u8 base):</strong>
                <a href="<?= $video['url'] ?>" target="_blank">
                    <?= $video['url'] ?>
                </a>
            </p>
            <script>
                var player_<?=$count;?> = videojs("video-<?= esc($video['base_name']) ?>");

                // Handle quality change when selecting an option
                $("#quality-selector-<?= esc($video['base_name']) ?>").change(function() {
                    var selectedQuality = $(this).val();
                    var selectedSource = $('#video-<?= esc($video['base_name']) ?> source[data-res="' + selectedQuality + '"]').attr('src');
                    // ✅ Guarda la posición actual antes de cambiar de calidad
                    var currentTime = player_<?= $count; ?>.currentTime();
                    var isPaused = player_<?= $count; ?>.paused();                    
                    // Change the video source to the selected quality
                    player_<?=$count;?>.src({
                        type: 'application/x-mpegURL',
                        src: selectedSource
                    });
                    
                    // ✅ Espera a que el nuevo video cargue para restaurar el tiempo
                    player_<?= $count; ?>.on('loadedmetadata', function () {
                        player_<?= $count; ?>.currentTime(currentTime);
                        if (!isPaused) {
                            player_<?= $count; ?>.play();
                        }else{
                            player_<?= $count++; ?>.pause();
                        }
                    }); });
            </script>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay videos disponibles.</p>
    <?php endif; ?>

    <a href="<?= base_url('/') ?>">Subir otro video</a>
</body>
</html>

