<?php
require_once('./shared/SqlConnection.php');
// 開始SQL
$conn = mysqli_connect( $DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
if (mysqli_connect_errno()) {
    $client->replyMessage(array(
        'replyToken' => $event['replyToken'],
        'messages' => array(
            array(
                'type' => 'text',
                'text' => mysqli_connect_error()
            )
        )
    ));
    exit();
}
// 設定連線編碼
mysqli_query( $conn, "SET NAMES 'utf8'");

// 設定時區
mysqli_query( $conn, "SET GLOBAL time_zone = 'Asia/Taipei'");
date_default_timezone_set("Asia/Taipei");
