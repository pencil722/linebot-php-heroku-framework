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
                	if($m_message!="")
                	{
					//get folder file numbers
					$filecount = 0;
					$directory = "images/marriage/";
					if (glob($directory . "*.JPG") != false)
					{
					 $filecount = count(glob($directory . "*.JPG"));
					 echo $filecount;
					}
                		$client->replyMessage(array(
                        'replyToken' => $event['replyToken'],
                        'messages' => array(
                            array(
                                'type' => 'text',
                                'text' => '小呆瓜說： '. $m_message . $filecount
                            ),
                            array(
                                'type' => 'sticker',
                                'packageId' => '1',
                                'stickerId' => '1'
                            ),
                            array(
                                'type' => 'image',
                                'originalContentUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage/marriage_00001.JPG',
                                'previewImageUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage_pre/pre_marriage_00001.JPG'
                            )
                        )
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

function marriagePicture(){
	$pics = array(
		'type' => 'image',
		'originalContentUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage/ori_Image00001.JPG',
		'previewImageUrl' => 'https://linebot-php-test.herokuapp.com/images/marriage/pre_Image00001.JPG'
	);
	return $pics;
}
