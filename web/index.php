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
                                    array(
                                        'type' => 'sticker',
                                        'packageId' => '11539',
                                        'stickerId' => '52114111'
                                    )
                                );
                                break;
                        }

                        $client->replyMessage(
                            array(
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

                            )
                        );
                    }
                    break;
                case 'sticker' :
                    $client->replyMessage(
                        array(
                            'replyToken' => $event['replyToken'],
                            'messages' => array(
                                array(
                                    'type' => 'sticker',
                                    'packageId' => $message['packageId'],
                                    'stickerId' => $message['stickerId']
                                )
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
        'text' => "宴客地點 : 新天地\n宴客時間 ： 12/2(日) 中午"
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
        'title' => '新天地旗艦店',
        'address' => '406台中市北屯區崇德五路345號',
        'latitude' => 24.179194,
        'longitude' => 120.684127

    );
    return array($map, $location);
}

function loveSticker(){
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
