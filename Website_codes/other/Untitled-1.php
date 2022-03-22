<?php
include 'guard.php';
include '../html/sidebar.html'
?>
<!DOCTYPE html>
<html>
    <head>
        <title>View Sensor Data </title>
        <script src ="../js/jquery.js"></script> 
        <script src ="../js/bootstrap.js"> </script> 
        <link  href ="../css/bootstrap.css"   type ="text/css" rel ="stylesheet" >  
        <style>
        </style>
    </head>
    <body>
        <div class="container">
        <form method ="post" action = "../php/Viewdata.php" class ="form-horizontal form-container" >
                <div class="row">
                   <div class="col-lg-6">
                    <h3> Do You Want To CHANGE The Valve STATE</h3>
                   </div>
                   <?php
                        mesay();
                       function mesay(){
                            $prev_val =  file_get_contents('mes.json');
                            $prev_arry = json_decode($prev_val,true);
                            $time_interval = $prev_arry["interval"];
                            $prev_status = $prev_arry["status"];
                            if($prev_status =="OPEN" || $prev_status ==null)
                            {
                  ?> 
                                <div class="col-lg-offset-2">
                                   <input type="radio" id="OPEN" name="valve"  value="OPEN" checked>
                                   <label for="OPEN">OPEN</label><br>
                                   <input type="radio" id="CLOSE" name="valve" value="CLOSE">
                                   <label for="CLOSE">CLOSE</label><br>
                                   <input type="submit" value="SUBMIT" class="btn btn-primary">
                                </div>
                        
                       <?php } 
                        if($prev_status == "CLOSE")
                          {
                       ?>
                              <div class="col-lg-offset-2">
                                   <input type="radio" id="OPEN" name="valve"  value="OPEN"  >
                                   <label for="OPEN">OPEN</label><br>
                                   <input type="radio" id="CLOSE" name="valve" value="CLOSE" checked>
                                   <label for="CLOSE">CLOSE</label><br>
                                   <input type="submit" value="SUBMIT" class="btn btn-primary">
                             </div> 
                       <?php 
                          }
                        }
                       ?>
                       <?php
                          if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                $status = test_input($_POST["valve"]);
                                $prev_val =  file_get_contents('mes.json');
                                $prev_arry = json_decode($prev_val,true);
                                $time_interval = $prev_arry["interval"];
                                if($time_interval == null)
                                  {
                                     $arry_ = array("interval"=>"0.5",'status' =>$status);
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
               </div>
         </form>
           </div>
            <div class="row">
                 <table class="table table-striped table-bordered table-hover table-responsive">
                     <thead>
                         <tr>
                             <th>Gas Content</th>
                             <th>Water Level</th>
                             <th>Ph Value</th>
                             <th>Temprature</th>
                             <th>Date</th>
                         </tr>
                     </thead>
                     <tbody>
                          <?php
                             $i =0;
                             $j =0;
                             $danger_row = array();
                          	 include 'database.php';
                             $conn = connect();
                             $table = mysqli_query($conn, "SELECT gasSensor, waterLevel, phSensor, tempSensor,inputDate FROM sensordata ORDER BY Id DESC"); 
                            while($row = mysqli_fetch_array($table))
                             {
                          ?>
                        <tr id="row<?php echo $i ?>">
                            <td><?php echo $row["gasSensor"]; ?></td>
                            <td><?php echo $row["waterLevel"]; ?></td>
                            <td><?php echo $row["phSensor"]; ?></td>
                            <td><?php echo $row["tempSensor"]; ?></td>
                            <td><?php echo $row["inputDate"]; ?></td>
                        </tr>
                        <?php
                          
                          if($row["phSensor"] <= 6) {
                            $danger_row[$j] = "#row".$i ;
                            $j++;
                          }
                          if($row["tempSensor"] >= 37) {
                            $danger_row[$j] = "#row".$i ;
                            $j++;
                          }
                          if($row["gasSensor"] == "GAS DETECTED") {
                            $danger_row[$j] = "#row".$i ;
                            $j++;
                          }
                          if($row["waterLevel"] <= 80) {
                            $danger_row[$j] = "#row".$i ;
                            $j++;
                          }
                          $i =$i+1;
                          }
                        ?>
                      
                      </tbody>
                 </table>
            </div>
        </div>
    </body>
    <script>
       
        var val =[];
        $(document).ready(function(){
           var radio = <?php echo $status; ?>;
            console.log(radio);
            var val = <?php echo json_encode($danger_row); ?>;
            for(var i=0; i<val.length;i++)
            {
                console.log("mesay");
                $(val[i]).addClass( "danger" );
            }
           
        });
    </script>
</html>