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
$sql = "SELECT * FROM `line_messages_records`";
if ($result = $conn->query($sql)) {
    foreach ($result as $row) {
        var_dump($row);
//...
    }
} else {
    var_dump('no result');
    throw new Exception($conn->error);
}


var_dump('db connect finish');

var_dump("\n\n\n");

//write data to db
$request_data = array(
    "events" => array(
        array(
            "replyToken" => "0f3779fba3b349968c5d07db31eab56f",
            "type" => "message",
            "timestamp" => 1462629479859,
            "source" => array(
                "type" => "user",
                "userId" => "U4af4980629..."
            ),
            "message" => array(
                "id" => "325708",
                "type" => "text",
                "text" => "Hello, world"
            )
        ),
        array(
            "replyToken" => "8cf9239d56244f4197887e939187e19e",
            "type" => "follow",
            "timestamp" => 1462629479859,
            "source" => array(
                "type" => "user",
                "userId" => "U4af4980629..."
            )
        )
    )
);
var_dump($request_data);

$request_str = json_encode($request_data);


var_dump($request_str);

$now = date('Y-m-d H:i:s');
var_dump($now);
$content = '001';

$query = sprintf("INSERT INTO `line_messages_records` (`all_content`, `created_at`) VALUES ('%s', '%s', NULL)", $content, $now);

if ($result = $conn->query($sql)) {
    var_dump($result);
} else {
    var_dump('insert failed');
    throw new Exception($conn->error);
}


var_dump('write finish');