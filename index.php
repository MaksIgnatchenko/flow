<?php
if (isset($_POST['code'])) {
    $code = $_POST['code'];
    $filename = 'run.php';
    $file = fopen($filename, "w");
    fwrite($file, $code);
    fclose($file);
    $descriptorspec = array(
        0 => array("pipe", "r"),  // stdin - канал, из которого дочерний процесс будет читать
        1 => array("pipe", "w"),  // stdout - канал, в который дочерний процесс будет записывать
        2 => array("pipe", "w") // stderr - файл для записи
    );
    $cmd = "php run.php";
    $env = ['a' => "Variable"];
    $process = proc_open($cmd, $descriptorspec, $pipes, null, null);
    if (is_resource($process)) {
        fwrite($pipes[0], '10');
        fclose($pipes[0]);
        echo stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        echo stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        proc_close($process);
    }
    $result = unlink($filename);
} else {
    require 'view.html';
}
