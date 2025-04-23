<h1>Subir Video</h1>
<form id="upload-form">
    <input type="file" name="video" accept="video/*" required>
    <button type="submit">Subir</button>
</form>

<progress id="progress-bar" value="0" max="100" style="width: 300px; display: none;"></progress>
<p id="progress-text"></p>
<div id="video-url" style="margin-top: 10px; display: none;"></div>

<a href="<?= base_url('/videos') ?>">Ver videos subidos</a>

<script>
document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const fileInput = form.querySelector('input[type="file"]');
    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append('video', file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= base_url('/upload') ?>', true);

    // Mostrar progreso
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            const complete_message = percent == 100 ? '. No cierre esta ventana hasta que el video termine de procesarse...' : '';
            document.getElementById('progress-bar').style.display = 'block';
            document.getElementById('progress-bar').value = percent;
            document.getElementById('progress-text').textContent = `${percent}% subido${complete_message}`;
        }
    };

    xhr.onload = function() {
        document.getElementById("video-url").style.display = 'block';
        if (xhr.status === 200) {
            try {
                const res = JSON.parse(xhr.responseText);
                if (res.url) {
                    document.getElementById("video-url").innerHTML =
                        `Video subido. Quedará disponible en la siguiente URL en algunos instantes: <a href="${res.url}" target="_blank">${res.url}</a>`;
                } else {
                    document.getElementById("video-url").innerText = "Subido, pero no se recibió URL.";
                }
            } catch (err) {
                document.getElementById("video-url").innerText = "Error procesando respuesta.";
            }
        } else {
            document.getElementById("video-url").innerText = "Error al subir el video.";
        }
    };

    xhr.onerror = function() {
        alert('Error en la conexión al subir el archivo.');
    };

    xhr.send(formData);
});
</script>
