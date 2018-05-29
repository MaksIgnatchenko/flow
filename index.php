<?php
$tests = [
    [[5, 5], [10]],
    [[10, 10], [20]],
    [[20, 20], [40]]
];

$descriptorspec = [
    0 => ["pipe", "r"],  // stdin
    1 => ["pipe", "w"],  // stdout
    2 => ["pipe", "w"] // stderr
];

$interpreter = 'php -r ';
$wrapScript = '$data = json_decode($argv[1]);
               foreach ($data as $arg) {
                   $arguments[] = $arg;
               }';
$userCode = $_POST['code'];
$postCode = '$result = call_user_func_array("solve", $arguments);
             echo json_encode($result);';

$mark = false;

if (isset($_POST['code'])) {
    foreach($tests as $test) {
        $data = json_encode($test[0]);
        $params = ' ' . $data;
        $cmd = $interpreter . '\'' . $wrapScript . $userCode . $postCode . '\'' . $params;
        $process = proc_open($cmd, $descriptorspec, $pipes, null, null);
        if (is_resource($process)) {
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

