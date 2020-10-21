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
	echo '<table border = 1>';
	echo '<tr><td>send</td><td>reply</td></tr>';
	//output data of each row	
	while($row = $result->fetch_assoc()){
		echo '<tr>';
		//var_dump($row);
		$all_content = json_decode($row['all_content'], true);
		if($row['operate_type'] == 1){
			echo '<td>';
//			var_dump($all_content);
			echo '<br>';
			//取出該筆對話的第一筆傳送內容，因為允許一次可以傳送多筆內容
			foreach($all_content as $content){
			    var_dump($content);
				//取得訊息發送者顯示名稱
				$userId = $content['source']['userId'];
				$userProfile = getUserProfile($userId, $channelAccessToken);
				var_dump($userProfile);
				echo $userProfile['displayName'];
				echo $userId;
				if($content['type'] === 'message'){
					//var_dump
					$messageArr = $content['message'];
					if($messageArr['type'] === 'text'){
						echo $messageArr['text'];
						continue;
					}
					if($messageArr['type'] === 'sticker'){
						echo "sticker";
						continue;
					}
				}
				else{
					echo "other content : ".$content['type'];
				}
			}
			echo '</td>';
		}
		if($row['operate_type'] == 2){
			echo '<td></td>';
			echo '<td>';
			var_dump($all_content);
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}

$conn->close();

function getUserProfile($userId, $channelAccessToken){
	$header = array(
            "Content-Type: application/json",
            'Authorization: Bearer ' . $channelAccessToken,
        );

        $context = stream_context_create(array(
            "http" => array(
                "method" => "GET",
                "header" => implode("\r\n", $header),
                "content" => '',
            ),
        ));

        $response = file_get_contents('https://api.line.me/v2/bot/profile/'. $userId, false, $context);
        if (strpos($http_response_header[0], '200') === false) {
            #http_response_code(500);
            error_log("Request failed: " . $response);
        }
        return json_decode($response);
}

