<?php
/**
 * Created by PhpStorm.
 * User: 111
 * Date: 2018/11/7
 * Time: 上午 07:45
 */

var_dump('this is a db test');

$mysql_connect_str = getenv('CLEARDB_DATABASE_URL');

var_dump($mysql_connect_str);



$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$conn = new mysqli($server, $username, $password, $db);
$sql ="SELECT * FROM `line_messages_records`";
if($result = $conn->query($sql)) {
    foreach($result as $row) {
//...
    }
} else {
    throw new Exception($conn->error);
}

var_dump('db connect finish');