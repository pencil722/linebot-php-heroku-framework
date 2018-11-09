<?php
/**
 * Created by PhpStorm.
 * User: 111
 * Date: 2018/11/9
 * Time: 下午 10:53
 */

//get all jpg picture
//
$filecount = 0;
$directory = "images/marriage/";
if (glob($directory . "*.JPG") != false) {
    $filecount = count(glob($directory . "*.JPG"));
}

foreach (glob($directory. "*.txt") as $filename) {
    echo "$filename \n";
    $target_pic_url = sprintf('https://linebot-php-test.herokuapp.com/images/marriage/%s', $filename);
    echo "<img src = '$target_pic_url'></img>";
}

var_dump('123');



//$target = rand(1, $filecount);
//$target_pic_url = sprintf('https://linebot-php-test.herokuapp.com/images/marriage/marriage_%05d.JPG', $target);
//$pic_url = array(
//    'type' => 'text',
//    'text' => $target_pic_url
//);