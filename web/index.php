<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

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

$client = new LINEBotTiny($channelAccessToken, $channelSecret);

//save input data to DB
$now = date('Y-m-d H:i:s', strtotime('+8 hour'));
$request_data = $client->parseEvents();
//json_encode 加上JSON_UNESCAPED_UNICODE，讓它不要把中文字轉成unicode
$query = sprintf("INSERT INTO `line_messages_records` (`all_content`, `created_at`, `operate_type`)
 VALUES ('%s', '%s', 1)", json_encode($request_data, JSON_UNESCAPED_UNICODE), $now);
try {
    $conn->query($query);
} catch (Exception $exception) {
    error_log("DB process error " . $exception->getMessage());
}

foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            switch ($message['type']) {
                case 'text':
                    $m_message = $message['text'];
                    if ($m_message != "") {
                        switch ($m_message) {
//                            case '婚紗' :
//                            case '婚紗照' :
//                            case '照片' :
                            case (preg_match("/(.?)+照片(.?)+/i", $m_message) ? $m_message : !$m_message):
                            case (preg_match("/(.?)+婚紗(.?)+/i", $m_message) ? $m_message : !$m_message):
                            case (preg_match("/(.?)+pic(.?)+/i", $m_message) ? $m_message : !$m_message):
                                $return_message = marriagePicture();
                                break;
//                            case '地圖' :
//                            case '地點' :
//                            case '餐廳地圖' :
//                            case '餐廳地點' :
//                            case '宴客場地' :
                            case (preg_match("/(.?)+地點(.?)+/i", $m_message) ? $m_message : !$m_message):
                            case (preg_match("/(.?)+地圖(.?)+/i", $m_message) ? $m_message : !$m_message):
                            case (preg_match("/(.?)+map(.?)+/i", $m_message) ? $m_message : !$m_message):
                            case (preg_match("/(.?)+場地(.?)+/i", $m_message) ? $m_message : !$m_message):
                            case (preg_match("/(.?)+餐廳(.?)+/i", $m_message) ? $m_message : !$m_message):
                                $return_message = marriageMap();
                                break;
//                            case '宴客資訊' :
                            case '活動訊息' :
                            case (preg_match("/(.?)+資訊(.?)+/i", $m_message) ? $m_message : !$m_message):
                            case (preg_match("/(.?)+時間(.?)+/i", $m_message) ? $m_message : !$m_message):
                                $return_message = marriageInfo();
                                break;
//                            case '交通資訊' :
//                            case '交通方式' :
//                            case '交通' :
                            case (preg_match("/(.?)+交通(.?)+/i", $m_message) ? $m_message : !$m_message):
                                $return_message = restaurantDirection();
                                break;
                            case (preg_match("/(.?)+喜帖(.?)+/i", $m_message) ? $m_message : !$m_message):
                                $return_message = inviteLetter();
                                break;
                            case (preg_match("/(.?)+恭喜(.?)+/i", $m_message) ? $m_message : !$m_message):
                            case (preg_match("/(.?)+祝福(.?)+/i", $m_message) ? $m_message : !$m_message):
                                $return_message = thanksSticker();
                                break;
                            default :
                                //回貼圖
                                $return_message = array(
                                    array(
                                        'type' => 'sticker',
                                        'packageId' => '11539',
                                        'stickerId' => '52114111'
                                    )
                                );
                                $return_message = loveSticker();
                                break;
                        }
                    }
                    break;
                case 'sticker' :
                    $client->replyMessage(
                        $return_message = array(
                            array(
                                'type' => 'sticker',
                                'packageId' => $message['packageId'],
                                'stickerId' => $message['stickerId']
                            )
                        )
                    );
                    break;

            }
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
    if (empty($return_message)) {
        continue;
    }
    $now = date('Y-m-d H:i:s', strtotime('+8 hour'));
    $query = sprintf("INSERT INTO `line_messages_records` (`all_content`, `created_at`, `operate_type`) VALUES ('%s', '%s', 2)", json_encode($return_message), $now);
    try {
        $conn->query($query);
    } catch (Exception $exception) {
        error_log("DB process error " . $exception->getMessage());
    }

    $client->replyMessage(
        array(
            'replyToken' => $event['replyToken'],
            'messages' => $return_message

//            'messages' => array(
//                array(
//                    'type' => 'text',
//                    'text' => '小呆瓜說： ' . $m_message . $filecount
//                ),
//                array(
//                    'type' => 'sticker',
//                    'packageId' => '1',
//                    'stickerId' => '1'
//                ),
//                array(
//                    'type' => 'image',
//                    'originalContentUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage/marriage_00001.JPG',
//                    'previewImageUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage_pre/pre_marriage_00001.JPG'
//                )
//            )

        )
    );
};
$conn->close();

function marriagePicture()
{
    //get folder file numbers
    $filecount = 0;
    $directory = "images/marriage/";
    if (glob($directory . "*.JPG") != false) {
        $filecount = count(glob($directory . "*.JPG"));
    }
    $target = rand(1, $filecount);
    $target_pic_url = sprintf('https://linebot-php-test.herokuapp.com/images/marriage/marriage_%05d.JPG', $target);
    $pic_url = array(
        'type' => 'text',
        'text' => $target_pic_url
    );
//    $pics = array(
//        'type' => 'image',
//        'originalContentUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage/marriage_00001.JPG',
//        'previewImageUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage/marriage_00001.JPG'
//    );
    $pics = array(
        'type' => 'image',
        'originalContentUrl' => $target_pic_url,
        'previewImageUrl' => $target_pic_url
    );

//    $return = array($pics, $pic_url);
    $return = array($pics);
    return $return;
}

function marriageInfo()
{
    $info = array(
        'type' => 'text',
        'text' => "宴客地點 : 新天地\n宴客時間 ： 12/2(日) 中午 12:00"
    );
    return array($info);
}

function marriageMap()
{
    $map = array(
        'type' => 'text',
        'text' => 'https://www.google.com.tw/maps/place/%E6%96%B0%E5%A4%A9%E5%9C%B0%E9%A4%90%E9%A3%B2%E9%9B%86%E5%9C%98-%E6%97%97%E8%89%A6%E5%BA%97/@24.1791019,120.6819703,17z/data=!3m1!4b1!4m5!3m4!1s0x346917c15c7763e9:0xa1cc7f52b77c3f88!8m2!3d24.179097!4d120.684159?hl=zh-TW&authuser=0'
    );
    $location = array(
        'type' => 'location',
        'title' => '新天地台中旗艦店',
        'address' => '406台中市北屯區崇德五路345號',
        'latitude' => 24.179194,
        'longitude' => 120.684127

    );

//    $return = array($map, $location);
    $return = array($location);

    return $return;
}

function loveSticker()
{
    $sticker_list = array(
        array(
            'type' => 'sticker',
            'packageId' => '11539',
            'stickerId' => '52114111'
        ),
        array(
            'type' => 'sticker',
            'packageId' => '1',
            'stickerId' => '1'
        )
    );
    $sticker = $sticker_list[0];
    return array($sticker);
}

function thanksSticker()
{
    $sticker_list = array(
        array(
            'type' => 'sticker',
            'packageId' => '2',
            'stickerId' => '41'
        ),
        array(
            'type' => 'sticker',
            'packageId' => '1',
            'stickerId' => '407'
        ),
        array(
            'type' => 'sticker',
            'packageId' => '11537',
            'stickerId' => '52002739'
        )
    );
    $sticker = $sticker_list[0];
    return array($sticker);
}

function restaurantDirection()
{
    $target_pic_url = 'https://linebot-php-test.herokuapp.com/images/direction_map.jpg';
    $direction = array(
        'type' => 'image',
        'originalContentUrl' => $target_pic_url,
        'previewImageUrl' => $target_pic_url
    );
    return array($direction);
}

function inviteLetter()
{
//    $temp = array(
//        'type' => 'text',
//        'text' => 'https://www.google.com.tw/maps/place/%E6%96%B0%E5%A4%A9%E5%9C%B0%E9%A4%90%E9%A3%B2%E9%9B%86%E5%9C%98-%E6%97%97%E8%89%A6%E5%BA%97/@24.1791019,120.6819703,17z/data=!3m1!4b1!4m5!3m4!1s0x346917c15c7763e9:0xa1cc7f52b77c3f88!8m2!3d24.179097!4d120.684159?hl=zh-TW&authuser=0'
//    );
    $target_pic_url = 'https://linebot-php-test.herokuapp.com/images/invite_letter.jpg';
    $invite_letter = array(
        'type' => 'image',
        'originalContentUrl' => $target_pic_url,
        'previewImageUrl' => $target_pic_url
    );

    $return = array($invite_letter);

    return $return;
}

function same_message($message)
{
    $same_message = array(
        'type' => 'text',
        'text' => $message
    );
    $return = array($same_message);

    return $return;
}
