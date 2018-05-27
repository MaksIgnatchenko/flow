<?php
$tests = [
    [[5, 5], [10]],
    [[10, 10], [20]],
    [[20, 20], [40]]
];

$mark = false;

if (isset($_POST['code'])) {
    foreach($tests as $test) {
        $data = serialize($test[0]);
        $interpreter = 'php -r ';
        $preCode = ' $stream = fopen("php://stdin", "r");';
        $preCode .= ' $input = stream_get_contents($stream);';
        $preCode .= ' $args = unserialize($input);';
        $preCode .= ' $a = $args[0];';
        $preCode .= ' $b = $args[1];';
        $postCode = ' $result = solve($a, $b);';
        $postCode .= ' echo json_encode($result);';
        $cmd = $interpreter . '\'' . $preCode . $_POST['code'] . $postCode . '\'';
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin - канал, из которого дочерний процесс будет читать
            1 => array("pipe", "w"),  // stdout - канал, в который дочерний процесс будет записывать
            2 => array("pipe", "w") // stderr - файл для записи
        );
        $process = proc_open($cmd, $descriptorspec, $pipes, null, null);
        if (is_resource($process)) {
            fwrite($pipes[0], $data);
            fclose($pipes[0]);
            $result = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            echo stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            proc_close($process);
        }
        $result = json_decode($result);
        if (!($test[1][0] === $result)) {
            $mark = true;
        }
    }
    if ($mark) {
        echo "Неверно";
    } else {
        echo "Все правильно";
    }
} else {
    require 'view.html';

}
