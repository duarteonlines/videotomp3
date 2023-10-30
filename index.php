<?php

header("Content-Type: application/json");

$pipes = [
    1 => ["pipe", "w"],
    2 => ["file", "log.txt", "a"]
];
$p = [];
$process = proc_open("ffmpeg -i teste.mp4 -preset ultrafast -threads 24 -f avi pipe:1", $pipes, $p);

if (is_resource($process)) {
    $final_file = "data:video/x-msvideo;base64,";
    $offset = 2048;

    while (!feof($p[1])) {
        $final_file .= base64_encode(stream_get_contents($p[1], $offset));
        $offset += 1000;
    }
    
    echo $final_file;
    fclose($p[1]);
}