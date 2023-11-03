<?php

// header("Content-Type: application/json");

if (isset($_FILES)) {
    $filename = str_replace(" ", "_", microtime());
    move_uploaded_file($_FILES['file']['tmp_name'], "videos/$filename.mp4");

    $pipes = [
        1 => ["pipe", "w"],
        2 => ["file", "stdout.log", "a"]
    ];
    $p = [];
    $process = proc_open("ffmpeg -i videos/$filename.mp4 -preset ultrafast -threads 24 -q:a 0 -map a -f mp3 pipe:1", $pipes, $p);

    if (is_resource($process)) {
        $final_file = "";

        while (!feof($p[1])) {
            $final_file .= base64_encode(stream_get_contents($p[1]));
        }

        echo "
        <iframe width='500' height='500' id='audio'></iframe>
        <script>
        var base64String = '$final_file';
        var byteCharacters = atob(base64String);
        var byteNumbers = new Array(byteCharacters.length);
        for (var i = 0; i < byteCharacters.length; i++) {
           byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        var byteArray = new Uint8Array(byteNumbers);
        var blob = new Blob([byteArray], {type: 'audio/mp3'});
        var url = URL.createObjectURL(blob);
        
        var audio = document.getElementById('audio');
        audio.src = url;
        </script>";
        
        if (feof($p[1])) {
            fclose($p[1]);
            unlink("videos/$filename.mp4");
        }
    }
} else {
    echo json_encode(["error" => "Você precisa enviar um arquivo de vídeo."]);
}
