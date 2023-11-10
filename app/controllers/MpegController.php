<?php   
namespace app\controllers;

class MpegController{

    public function converter($params){
        
        $format_type = escapeshellcmd($params->format_type);
        $compression_preset = escapeshellcmd($params->compression_preset);
        $quality = escapeshellcmd($params->quality);

        $file = $_FILES['file'];
        $validation_schema = [
            'format_types_accepted' => [
                "mp3",
                "wav"
            ],
            'mimetypes_permited' => [
                "video/mp4"
            ],
            'presets_accepted' => [
                'slow',
                'medium',
                'ultrafast'
            ],
            'quality' => [
                "51",
                "45",
                "34",
                "23",
                "15",
                "10",
                "0"
            ]
        ];        
    
        if (in_array($file["type"], $validation_schema['mimetypes_permited']) && in_array($format_type, $validation_schema['format_types_accepted']) && in_array($compression_preset, $validation_schema['presets_accepted']) && in_array($quality, $validation_schema['quality'])) {
            
            $file_extension = explode("/", $file["type"])[1];
            $filename = '../app/videos/' . str_replace(" ", "_", microtime()) . ".$file_extension";
            $is_file_moved = move_uploaded_file($file['tmp_name'], $filename);
    
            if (!$is_file_moved) {
                // header("HTTP 1.1 500 Internal Server Error", 500);
                echo json_encode(["error" => "Um erro interno ocorreu. Contate o Suporte."]);
                die();
            }
    
            $pipes = [
                1 => ["pipe", "w"],
                2 => ["file", "stdout.log", "a"]
            ];
            $p = [];
    
            $process = proc_open("ffmpeg -i $filename -preset $compression_preset -crf $quality -threads 24 -q:a 0 -map a -f $format_type pipe:1", $pipes, $p);
    
            if (is_resource($process)) {
                $final_file = "";
    
                while (!feof($p[1])) {
                    $final_file .= base64_encode(stream_get_contents($p[1]));
                }
    
                echo json_encode(["cacheable" => true, "file" => $final_file], JSON_UNESCAPED_SLASHES);
    
                if (feof($p[1])) {
                    fclose($p[1]);
                    unlink($filename);
                }
            }
        } else {
            // header("400 Bad Request", 400);
            echo json_encode(["error" => "Arquivo ou formato de saída inválido"]);
        }
    }
}   