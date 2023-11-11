<?php

namespace app\controllers;

class MpegController
{
    public function converter($params)
    {
        $format_type = escapeshellcmd($params->format_type);
        $quality = escapeshellcmd($params->quality);
        $bitrate = escapeshellcmd($params->bitrate);
        $chanell = escapeshellcmd($params->chanell);
        $file = $_FILES['file'];
        $validation_schema = [
            'format_types_accepted' => [
                "mp3",
                "wav"
            ],
            'mimetypes_permited' => [
                "video/mp4"
            ],
            "bitrate" => [
                "64K",
                "128K",
                "192K",
                "320K"
            ],
            'quality' => [
                "0",
                "3",
                "6",
                "9"
            ],
            "chanells" => [
                "1",
                "2"
            ]
        ];

        if (in_array($file["type"], $validation_schema['mimetypes_permited']) && in_array($chanell, $validation_schema['chanells']) && in_array($bitrate, $validation_schema['bitrate']) && in_array($format_type, $validation_schema['format_types_accepted']) && in_array($quality, $validation_schema['quality'])) {
            $file_extension = explode("/", $file["type"])[1];
            $filename = '../app/videos/' . str_replace(" ", "_", microtime()) . ".$file_extension";
            $is_file_moved = move_uploaded_file($file['tmp_name'], $filename);
            
            if (!$is_file_moved) {
                header("HTTP/1.1 500 Internal Server Error", true, 500);
                echo json_encode(["error" => "Um erro interno ocorreu. Contate o Suporte."]);
                exit;
            }

            $pipes = [
                1 => ["pipe", "w"],
                2 => ["file", "stdout.log", "a"]
            ];
            $p = [];

            $process = proc_open("ffmpeg -i $filename -vn -q:a $quality -ab $bitrate -ac $chanell -f $format_type pipe:1", $pipes, $p);

            if (is_resource($process)) {
                $final_file = "";

                while (!feof($p[1])) {
                    $final_file .= base64_encode(stream_get_contents($p[1]));
                }

                if (feof($p[1])) {
                    fclose($p[1]);
                    unlink($filename);
                }

                echo json_encode(["cacheable" => true, "file" => $final_file], JSON_UNESCAPED_SLASHES);
            }
        } else {
            header("HTTP/1.1 400 Bad Request", true, 400);
            echo json_encode(["error" => "Arquivo ou formato de saída inválido"]);
            exit;
        }
    }
}
