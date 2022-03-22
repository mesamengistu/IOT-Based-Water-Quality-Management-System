<?php
include 'database.php';
$conn = connect();
$sql = "DELETE *FROM sensordata";
if ($sql->$conn()) {
   echo "<script>STORAGE SUCCESSFULLY CLEARD</script>";
}
?>