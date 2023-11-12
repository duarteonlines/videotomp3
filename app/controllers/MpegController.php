<?php

namespace app\controllers;

class MpegController
{
    protected function parse_probe_param(string $string, string $param): string
    {
        $parsed_string = array_map(function ($el) use ($param) {
            $el = explode("=", $el);
            if ($el[0] === $param) {
                return $el[1];
            }
        }, explode("\n", $string));

        $parsed_param = array_filter($parsed_string, function ($el) {
            return (bool)$el;
        });

        $parsed_param_as_string = array_values($parsed_param)[0];

        if ($param === "bit_rate") {
            $parsed_param_as_string = substr($parsed_param_as_string, 0, 3) . "K";
        }

        return $parsed_param_as_string;
    }
    public function converter($params)
    {
        $file = $_FILES['file'];
        $file_extension = explode("/", $file["type"])[1];
        $filename = '../app/videos/' . str_replace(" ", "_", microtime()) . ".$file_extension";
        $format_type = "";
        $quality = "";
        $bitrate = "";
        $chanell = "";
        $is_file_moved = move_uploaded_file($file['tmp_name'], $filename);

        if (!$is_file_moved) {
            header("HTTP/1.1 500 Internal Server Error", 500);
            echo json_encode(["error" => "Um erro interno ocorreu. Contate o Suporte."]);
            exit;
        }

        if (property_exists($params, 'default_config') && $params->default_config) {
            $returned = shell_exec("ffprobe -i $filename -show_streams -select_streams a | grep =");
            $format_type = escapeshellcmd($params->format_type);
            $bitrate = $this->parse_probe_param($returned, "bit_rate");
            $chanell = $this->parse_probe_param($returned, "channels");
            $quality = escapeshellcmd($params->quality);
        } else {
            $format_type = escapeshellcmd($params->format_type);
            $quality = escapeshellcmd($params->quality);
            $bitrate = escapeshellcmd($params->bitrate);
            $chanell = escapeshellcmd($params->chanell);
        }

        $pipes = [
            1 => ["pipe", "w"],
            2 => ["file", "stdout.log", "a"]
        ];
        $p = [];

        $cmd = "ffmpeg -i $filename -vn -q:a $quality -ab $bitrate -ac $chanell  -f $format_type pipe:1";
        $process = proc_open($cmd, $pipes, $p);

        if (is_resource($process)) {
            $final_file = "";

            while (!feof($p[1])) {
                $final_file .= base64_encode(stream_get_contents($p[1]));
            }

            fclose($p[1]);
            unlink($filename);

            echo json_encode(["file" => $final_file], JSON_UNESCAPED_SLASHES);
        }
    }
}
