<?php
session_start();
?>
<?php
         include 'database.php';
         $conn = connect();
     if($_SERVER['REQUEST_METHOD'] =="POST"){
           if(isset($_POST['submit']))
           {
               $username = trim($_POST['username']);
               $password = trim($_POST['password']);
               $sql = "SELECT * FROM admin where admin_username ='$username' AND admin_password ='$password'";
               $result = $conn->query($sql) ;
               $num_row = mysqli_num_rows($result);
               if($result-> num_rows > 0)
                {
                    while ($row = $result-> fetch_assoc())
                    {
                       $_SESSION['user_loggedid'] =true;
                       header('location:../php/time_interval.php');  
                    }    
                }
            }
        }  
    ?> 