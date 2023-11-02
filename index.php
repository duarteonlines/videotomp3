<?php

header("Content-Type: application/json");

if (isset($_POST["file"])) {

    $file = $_POST["file"];

    $pipes = [
        1 => ["pipe", "w"],
        2 => ["file", "error.txt", "a"]
    ];
    $p = [];
    $process = proc_open("ffmpeg -i teste.mp4 -preset ultrafast -threads 24 -f mp3 pipe:1", $pipes, $p);
    
    if (is_resource($process)) {
        $final_file = "data:audio/mpeg;base64,";
        $offset = 2048;
    
        while (!feof($p[1])) {
            $final_file .= base64_encode(stream_get_contents($p[1], $offset));
            $offset += 1000;
        }
        
        echo $final_file;
        fclose($p[1]);
    }
} else {
    echo json_encode(["error" => "Você precisa enviar um arquivo de vídeo."]);
}