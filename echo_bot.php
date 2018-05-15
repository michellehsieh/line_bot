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

$channelAccessToken = 'J7EvT2ky1O5esdL2hiy7+0BD99/vpZu/tqMj0fMu53nHJkOQmQpbM9yUAVKFosdLWOeLMiohJ2LjF5q9nxr3E5AFrQZtvobsMIIV2DcWFkAYpUMi7uv4hAijTmOq/eSXRyt7C8/X21NQ/YwE8wH48gdB04t89/1O/w1cDnyilFU=';
$channelSecret = '74621609c8ce77bb2ed8c8bfaf137bb5';


$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            switch ($message['type']) {
                case 'text':
                    if (strpos($message['text'], '學;') !== false) {
                       learn($client, $event);
                    } else {
                       speak($client, $event);
                    }
                    break;
                default:
                    error_log("Unsupporeted message type: " . $message['type']);
                    break;
            }
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};

function learn($client, $event) {
  $message = $event['message'];
  $source = $event['source'];
  $temp = explode( ';' , $message['text'] );
  $groupId = $source['groupId'];
  $question = $temp[1];
  $answer = $temp[2];

  include('./shared/preSql.php');

  $sql1 ="DELETE FROM user WHERE question='$question'";
  $sql2 ="INSERT INTO user(question, answer, groupId) VALUES ('$question', '$answer', '$groupId')";

  $result1 = mysqli_query($conn, $sql1);
  $result2 = mysqli_query($conn, $sql2);

  if ($result1 && $result2) {
      $client->replyMessage(array(
      'replyToken' => $event['replyToken'],
      'messages' => array(
          array(
              'type' => 'text',
              'text' => '我學會了！'
          )
        )
      ));
  }
  include('./shared/afterSql.php');
}


function speak($client, $event) {
  $message = $event['message'];
  $source = $event['source'];
  $getText = $message['text'];

  include('./shared/preSql.php');
    
  $sql ="SELECT answer FROM user WHERE question = '$getText' limit 1 ";
  $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_row($result, MYSQLI_ASSOC)) {
          $client->replyMessage(array(
              'replyToken' => $event['replyToken'],
              'messages' => array(
                  array(
                      'type' => 'text',
                      'text' => $row['answer']
                  )
              )
          ));
        }
    }
    
  
  include('./shared/afterSql.php');
}
