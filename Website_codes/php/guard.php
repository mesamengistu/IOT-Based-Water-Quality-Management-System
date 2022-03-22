<?php
session_start();
if(!isset($_SESSION['user_loggedid']))
{
 header('location:../html/Login.html');
 exit();
}

if(isset($_SESSION['user_loggedid']) != true)
{
 header('location:../html/Login.html');
 exit();

}

//include '../classes/class.password.php';
//include '../classes/class.user.php';

 // $user = new User();