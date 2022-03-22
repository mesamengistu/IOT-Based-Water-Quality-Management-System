<?php
    function connect(){
   $conn = new mysqli("localhost","root","","WQM") ;
   // echo "et is in database connct class";
   return $conn;
    }
?>