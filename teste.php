<!DOCTYPE html>

<head>
    <title></title>
</head>

<body>

    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="file"><span>Filename:</span></label>
        <input type="file" required name="file" id="file" />
        <br />
        <label for="format">Selecione o formato de saida:</label>
        <select name="format_type" id="format" required>
            <option value="mp3" selected>MP3</option>
            <option value="wav">WAV</option>
        </select>
        <br />
        <label for="compression_level">Nível de compressão:</label>
        <select name="compression_preset" id="compression_level" required>
            <option value="slow">Muito bom</option>
            <option value="medium" selected>Normal</option>
            <option value="ultrafast">Ruim</option>
        </select>
        <br />
        <label for="quality">Qualidade:</label>
        <select name="quality" id="quality" required>
            <option value="51">Péssima</option>
            <option value="34" selected>Ruim</option>
            <option value="17">Normal</option>
            <option value="0">Muito boa</option>
        </select>
        <br />
        <input type="submit" name="submit" value="Submit" />
    </form>

</body>

</html>