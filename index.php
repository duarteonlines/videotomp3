<?php

// header("Content-Type: application/json");

if (isset($_FILES)) {
    move_uploaded_file($_FILES['file']['tmp_name'], 'videos/teste.mp4');

    $pipes = [
        1 => ["pipe", "w"],
        2 => ["file", "error.log", "a"]
    ];
    $p = [];
    $process = proc_open('ffmpeg -i videos/teste.mp4 -preset ultrafast -threads 24 -f wav pipe:1', $pipes, $p);

    if (is_resource($process)) {
        $final_file = "data:audio/wav;base64,";
        $offset = 1048;

        while (!feof($p[1])) {
            $final_file .= base64_encode(stream_get_contents($p[1], $offset));
            $offset += 1000;
        }

        // echo json_encode($final_file, JSON_UNESCAPED_SLASHES);
        echo "<audio controls='controls' autobuffer='autobuffer' autoplay='autoplay'>
        <source src='$final_file' />
    </audio>";
        fclose($p[1]);
    }
} else {
    echo json_encode(["error" => "Você precisa enviar um arquivo de vídeo."]);
}
