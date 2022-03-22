<?php
 include 'database.php';
 $conn = connect();
$api_key_value = "tPmAT5Ab3j7F9";
$api_key= $gasSensor = $waterLevel = $phSensor = $tempSensor = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if($api_key == $api_key_value) {
        //gas_sensor  water_level_sensor ph_sensor temp_sensor
        $gasSensor = test_input($_POST["gas_sensor"]);
        $waterLevel = test_input($_POST["water_level_sensor"]);
        $phSensor = test_input($_POST["ph_sensor"]);
        $tempSensor = test_input($_POST["temp_sensor"]);
        $sql = "INSERT INTO sensordata (gasSensor,waterLevel,phSensor,tempSensor)
        VALUES ('" . $gasSensor . "', '" . $waterLevel . "','" . $phSensor . "','" . $tempSensor . "')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $conn->close();
    }
    else {
        echo "Wrong API Key provided.";
    }
}
else {
    echo "No data posted with HTTP POST.";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}