<?php
/**
 * Created by PhpStorm.
 * User: 111
 * Date: 2018/11/8
 * Time: 上午 12:50
 */

$log_path = './logs';
$date = date('Ymd', strtotime('+8 hour'));
$log_file_name = sprintf("%s/%s.log", $log_path, $date);

var_dump($log_file_name);

$file = fopen($log_file_name, 'a+');

fwrite($file, 'this is a test');

fclose($file);