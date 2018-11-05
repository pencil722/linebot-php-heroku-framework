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

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            switch ($message['type']) {
                case 'text':
                    $m_message = $message['text'];
                    if ($m_message != "") {
                        switch ($m_message) {
                            case '婚紗' :
                            case '婚紗照' :
                            case '照片' :
                                $return_message = marriagePicture();
                                break;
                            case '地圖' :
                            case '地點' :
                            case '餐廳地圖' :
                            case '餐廳地點' :
                            case '宴客場地' :
                                $return_message = marriageMap();
                                break;
                            case '宴客資訊' :
                            case '活動訊息' :
                                $return_message = marriageInfo();
                                break;
                            default :
                                //回貼圖
                                $return_message = array(
                                    'type' => 'sticker',
                                    'packageId' => '1',
                                    'stickerId' => '1'
                                );
                                break;
                        }

                        $client->replyMessage(array(
                            'replyToken' => $event['replyToken'],
                            'messages' => $return_message


//                        'messages' => array(
//                            array(
//                                'type' => 'text',
//                                'text' => '小呆瓜說： '. $m_message . $filecount
//                            ),
//                            array(
//                                'type' => 'sticker',
//                                'packageId' => '1',
//                                'stickerId' => '1'
//                            ),
//                            array(
//                                'type' => 'image',
//                                'originalContentUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage/marriage_00001.JPG',
//                                'previewImageUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage_pre/pre_marriage_00001.JPG'
//                            )
//                        )

                        ));
                    }
                    break;

            }
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};

function marriagePicture()
{
    //get folder file numbers
    $filecount = 0;
    $directory = "images/marriage/";
    if (glob($directory . "*.JPG") != false) {
        $filecount = count(glob($directory . "*.JPG"));
    }
    $target = rand(1,$filecount);
    $target_pic_url = sprintf('https://linebot-php-test.herokuapp.com/images/marriage/marriage_%05d.JPG',$target);
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
    return array($pics, $pic_url);
}

function marriageInfo()
{
    $info = array(
        'type' => 'text',
        'text' => '宴客地點 : 新天地'
    );
    return array($info);
}

function marriageMap()
{
    $map = array(
        'type' => 'text',
        'text' => '宴客地圖 : 新天地'
    );
    return array($map);
}
