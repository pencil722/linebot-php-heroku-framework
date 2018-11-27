<?php

require_once('./LINEBotTiny.php');

$channelAccessToken = getenv('LINE_CHANNEL_ACCESSTOKEN');
$channelSecret = getenv('LINE_CHANNEL_SECRET');

//set db connect
$mysql_connect_str = getenv('CLEARDB_DATABASE_URL');
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$conn = new mysqli($server, $username, $password, $db);
//設定連線使用utf8編碼，才不會有亂碼出現
$conn->set_charset("utf8");

//get message records, no pagination
$query = sprintf("select * from `line_messages_records` order by created_at desc");
try {
    $result = $conn->query($query);
} catch (Exception $exception) {
    error_log("DB process error " . $exception->getMessage());
}


if($result->num_rows > 0){
	//output data of each row
	while($row = $result->fetch_assoc()){
		var_dump($row);
	}
}

