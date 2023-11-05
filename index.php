<?php

// header("Content-Type: application/json");

if (isset($_POST['submit'], $_POST['format_type'], $_POST['quality'], $_POST['compression_preset'], $_FILES['file'])) {

    $format_type = escapeshellcmd($_POST['format_type']);
    $compression_preset = escapeshellcmd($_POST['compression_preset']);
    $quality = escapeshellcmd($_POST['quality']);
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
            '17',
            '34',
            '51'
        ]
    ];

    // TODO: refactor
    if (in_array($file["type"], $validation_schema['mimetypes_permited']) && in_array($format_type, $validation_schema['format_types_accepted']) && in_array($compression_preset, $validation_schema['presets_accepted']) && in_array($quality, $validation_schema['quality'])) {

        $file_extension = explode("/", $file["type"])[1];
        $filename = 'videos/' . str_replace(" ", "_", microtime()) . $file_extension;
        $is_file_moved = move_uploaded_file($_FILES['file']['tmp_name'], $filename);

        if (!$is_file_moved) {
            header("HTTP 1.1 500 Internal Server Error", 500);
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
            var blob = new Blob([byteArray], {type: 'audio/$format_type'});
            var url = URL.createObjectURL(blob);
            
            var audio = document.getElementById('audio');
            audio.src = url;
            </script>";

            // echo json_encode($final_file, JSON_UNESCAPED_SLASHES);

            if (feof($p[1])) {
                fclose($p[1]);
                unlink($filename);
            }
        }
    } else {
        header("400 Bad Request", 400);
        echo json_encode(["error" => "Arquivo ou formato de saída inválido"]);
    }
} else {
    echo json_encode(["error" => "Você precisa enviar um arquivo de vídeo."]);
}
