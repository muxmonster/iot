<?php
session_start();
/**
Get parameter from Arduion Mega 2560 and Ethernet Sh
**/
$node_id = $_GET["nodeid"];
$temperature_a=$_GET["temp_a"];
$humidity_a=$_GET["hum_a"];
$smoke_a=$_GET["sm_a"];
$temperature_b=$_GET["temp_b"];
$humidity_b=$_GET["hum_b"];
$smoke_b=$_GET["sm_b"];
$temperature_station=$_GET["temp_s"];
$temperature_station_b=$_GET["temp_s_b"];
$distance_ultra_a = $_GET["distance_a"]; //Dinstace ultrasonic Station A

$avg_temp_before;
$avg_temp_now;
$avg_temp_now_b;
$h_now;
$h_now_b;
$send_noti_now;
//========= Config default values.============
$limit_temp = 28;
$token = 'xxxx'; // <-- Token Line Application is alert Server room status.
$time_noti_one = '0';
$time_noti_two = '6';
$time_noti_three = '12';
$time_noti_four = '18';

/**============================================
Please CHANGE IP_HOST,USERID,PASSWORD,DATABASENAME in below
=============================================**/
$link = mysqli_connect("IP_HOST","USERID","PASSWORD","DATABASENAME");
mysqli_set_charset($link,"utf8");
if (!$link) {
   echo "Error: Unable to connect to HOSxP". PHP_EOL;
   echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
   exit;
}
// Temperature Station A //
$sql_temp = "SELECT HOUR(temp_date_time) as h_dt, AVG(temp) as avg_temp ";
$sql_temp .= "FROM server_room WHERE temp_station = 'A' AND ";
$sql_temp .= "temp_date_time BETWEEN DATE_ADD(NOW(), INTERVAL - 1 DAY) AND ";
$sql_temp .= "DATE_ADD(NOW(), INTERVAL 1 DAY) AND ";
$sql_temp .= "HOUR(temp_date_time) = HOUR(NOW()) - 1 GROUP BY HOUR(temp_date_time)";
$result = mysqli_query($link,$sql_temp);

$avg_temp_before = mysqli_fetch_assoc($result);

$sql_temp = "SELECT HOUR(temp_date_time) as h_dt, AVG(temp) as avg_temp ";
$sql_temp .= "FROM server_room WHERE temp_station = 'A' AND ";
$sql_temp .= "temp_date_time BETWEEN DATE_ADD(NOW(), INTERVAL - 1 DAY) AND ";
$sql_temp .= "DATE_ADD(NOW(), INTERVAL 1 DAY) AND ";
$sql_temp .= "HOUR(temp_date_time) = HOUR(NOW()) GROUP BY HOUR(temp_date_time)";
$result = mysqli_query($link,$sql_temp);

$avg_temp_now = mysqli_fetch_assoc($result);
$h_now = $avg_temp_now["h_dt"];
$sql_temp = "SELECT HOUR(temp_date_time) as h_dt, AVG(temp) as avg_temp ";
$sql_temp .= "FROM server_room WHERE temp_station = 'B' AND ";
$sql_temp .= "temp_date_time BETWEEN DATE_ADD(NOW(), INTERVAL - 1 DAY) AND ";
$sql_temp .= "DATE_ADD(NOW(), INTERVAL 1 DAY) AND ";
$sql_temp .= "HOUR(temp_date_time) = HOUR(NOW()) GROUP BY HOUR(temp_date_time)";
$result = mysqli_query($link,$sql_temp);

$avg_temp_now_b = mysqli_fetch_assoc($result);
//$h_now_b = $avg_temp_now_b["h_dt"];
/////////////////////////////////////
function send_line_notify($message, $token)
{
  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
  curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt( $ch, CURLOPT_POST, 1);
  curl_setopt( $ch, CURLOPT_POSTFIELDS, "message=$message");
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
  $headers = array( "Content-type: application/x-www-form-urlencoded", "Authorization: Bearer $token", );
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec( $ch );
  curl_close( $ch );

  return $result;
}

//if ($temperature_a > $limit_temp)
if ($avg_temp_now["avg_temp"] > $limit_temp)
{
    $text = 'Temperature Server room is => '.$avg_temp_now["avg_temp"];
    send_line_notify($text, $token);
    //$text = 'Distance: '.$distance_ultra_a;
    //send_line_notify($text, $token);
}
switch ($node_Id) {
    case(0):
        if ($distance_ultra_a > 4)
        {
         $sql_temp = "insert into aggressive_status (agg_status,distance,agg_datetime) value ('1','".$distance_ultra_a."',now())";
         $result = mysqli_query($link,$sql_temp);
        }
        else{
         $sql_temp = "insert into aggressive_status (agg_status,distance,agg_datetime) value ('0','".$distance_ultra_a."',now())";
         $result = mysqli_query($link,$sql_temp);
        }
         $sql_temp = "insert into server_room (temp_date_time,temp,humidity,temp_station,smoke_value,sm_station,node_id) value (now(),'".$temperature_a."','".$humidity_a."','".$temperature_station."','".$smoke_a."','".$temperature_station."','".$node_id."')";
         $result = mysqli_query($link,$sql_temp);
         $sql_temp = "insert into server_room (temp_date_time,temp,humidity,temp_station,smoke_value,sm_station,node_id) value (now(),'".$temperature_b."','".$humidity_b."','".$temperature_station_b."','".$smoke_b."','".$temperature_station_b."','".$node_id."')";
         $result = mysqli_query($link,$sql_temp);
    break;

}
/* $sql_temp = "insert into cc (temp_date_time,temp,humidity,temp_station,sm_station) value (now(),'".$temperature_a."','".$humidity_a."','".$temperature_station."')";
 $result = mysqli_query($link,$sql_temp);
 $sql_temp = "insert into cc (temp_date_time,temp,humidity,temp_station,sm_station) value (now(),'".$temperature_b."','".$humidity_b."','".$temperature_station_b."')";
 $result = mysqli_query($link,$sql_temp);
*/

//// Notify Status at 06.00 AM && 00.00 PM ////
if($h_now == $time_noti_one)
{
    $sql_temp = "SELECT notify FROM notify_status WHERE DATE(noti_date_time) = DATE(NOW()) ";
    $sql_temp .= " AND h_time = '".$h_now."'";
    $result = mysqli_query($link,$sql_temp);
    $send_noti_now = mysqli_fetch_assoc($result);

    if ($send_noti_now["notify"] == 0)
    {
        $text = "Average Temperature (c)\nStation A: ".$avg_temp_now["avg_temp"];
        $text .= "\nStation B: ".$avg_temp_now_b["avg_temp"];
        send_line_notify($text, $token);
         $sql_temp = "INSERT INTO notify_status (noti_date_time,h_time,notify) VALUE ";
         $sql_temp .= "(NOW(),HOUR(NOW()),'1')";
         $result = mysqli_query($link,$sql_temp);
    }
}

if($h_now == $time_noti_two)
{
    $sql_temp = "SELECT notify FROM notify_status WHERE DATE(noti_date_time) = DATE(NOW()) ";
    $sql_temp .= " AND h_time = '".$h_now."'";
    $result = mysqli_query($link,$sql_temp);
    $send_noti_now = mysqli_fetch_assoc($result);

    if ($send_noti_now["notify"] == 0)
    {
        $text = "Average Temperature (c)\nStation A: ".$avg_temp_now["avg_temp"];
        $text .= "\nStation B: ".$avg_temp_now_b["avg_temp"];
        send_line_notify($text, $token);
         $sql_temp = "INSERT INTO notify_status (noti_date_time,h_time,notify) VALUE ";
         $sql_temp .= "(NOW(),HOUR(NOW()),'1')";
         $result = mysqli_query($link,$sql_temp);
    }
}

if($h_now == $time_noti_three)
{
    $sql_temp = "SELECT notify FROM notify_status WHERE DATE(noti_date_time) = DATE(NOW()) ";
    $sql_temp .= " AND h_time = '".$h_now."'";
    $result = mysqli_query($link,$sql_temp);
    $send_noti_now = mysqli_fetch_assoc($result);

    if ($send_noti_now["notify"] == 0)
    {
        $text = "Average Temperature (c)\nStation A: ".$avg_temp_now["avg_temp"];
        $text .= "\nStation B: ".$avg_temp_now_b["avg_temp"];
        send_line_notify($text, $token);
         $sql_temp = "INSERT INTO notify_status (noti_date_time,h_time,notify) VALUE ";
         $sql_temp .= "(NOW(),HOUR(NOW()),'1')";
         $result = mysqli_query($link,$sql_temp);
    }
}

if($h_now == $time_noti_four)
{
    $sql_temp = "SELECT notify FROM notify_status WHERE DATE(noti_date_time) = DATE(NOW()) ";
    $sql_temp .= " AND h_time = '".$h_now."'";
    $result = mysqli_query($link,$sql_temp);
    $send_noti_now = mysqli_fetch_assoc($result);

    if ($send_noti_now["notify"] == 0)
    {
        $text = "Average Temperature (c)\nStation A: ".$avg_temp_now["avg_temp"];
        $text .= "\nStation B: ".$avg_temp_now_b["avg_temp"];
        send_line_notify($text, $token);
         $sql_temp = "INSERT INTO notify_status (noti_date_time,h_time,notify) VALUE ";
         $sql_temp .= "(NOW(),HOUR(NOW()),'1')";
         $result = mysqli_query($link,$sql_temp);
    }
}
//////////////////////

session_destroy();
?>
