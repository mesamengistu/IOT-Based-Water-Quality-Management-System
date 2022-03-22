<?php

include 'guard.php';
include '../html/sidebar.html';
include '../html/timeInterval.htm';
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $time_interval = test_input($_POST["time_interval"]);
    $prev_val =  file_get_contents('mes.json');
    $prev_arry = json_decode($prev_val,true);
    $status = $prev_arry["status"];
    if($status ==null)
    {
        $arry_ = array("interval"=>$time_interval,'status' =>"OPEN");
        $encode_ =json_encode($arry_);
        file_put_contents('mes.json',$encode_);
    }
   else{
         $arry_ = array("interval"=>$time_interval,'status' =>$status);
         $encode_ = json_encode($arry_);
        file_put_contents('mes.json',$encode_); 
    }  
}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>