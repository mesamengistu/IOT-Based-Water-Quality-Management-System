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
            <div class="row">
                <form method ="post" action = "../php/Viewdata.php" class ="form-horizontal form-container" >
                   <div class="col-lg-6">
                    <h3> Do You Want To CHANGE The Valve STATE</h3>
                   </div>
                   <div class="col-lg-offset-2" style ="margin-bottom:20px;">
                            <input type="radio" id="OPEN" name="valve"  value="OPEN" checked>
                            <label for="OPEN">OPEN</label><br>
                            <input type="radio" id="CLOSE" name="valve" value="CLOSE" >
                            <label for="CLOSE">CLOSE</label><br>
                            <input type="submit" value="SUBMIT" class="btn btn-primary">
                    </div> 
                       <?php
                          $prev_val =  file_get_contents('mes.json');
                          $prev_arry = json_decode($prev_val,true);
                          $time_interval = $prev_arry["interval"];
                          $prev_status = $prev_arry["status"];
                          $java_status = $prev_status;
                          if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                $status = test_input($_POST["valve"]);
                                $java_status = $status;
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
                 </form>
         </div>
            <div class="row">
                 <table class="table table-striped table-bordered table-hover table-responsive">
                     <thead>
                         <tr>
                             <th>Turbudity(NTU)</th>
                             <th>Water Level(V)</th>
                             <th>Ph Value</th>
                             <th>Temprature(°C)</th>
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
                            <td>
                            <?php 
                                  $gasSensor =  $row["gasSensor"];
                                    if($gasSensor < 2.5){
                                      $ntu = 3000;
                                    }
                                    elseif ($gasSensor > 4.2 ) {
                                      $ntu = 0.6;
                                    }
                                    else{
                                      $ntu = intval(-1120.4*($gasSensor*$gasSensor)+5742.3*$gasSensor-4352.9); 
                                    }
                                     echo $ntu." NTU";
                             ?>
                            </td>
                            <td
                            ><?php echo $row["waterLevel"];?></td>
                            <td><?php echo $row["phSensor"]; ?></td>
                            <td><?php echo $row["tempSensor"]; ?></td>
                            <td><?php echo $row["inputDate"]; ?></td>
                        </tr>
                        <?php
                          
                          if(($row["phSensor"] <= 6.5)||($row["phSensor"] >= 8.5)) {
                            $danger_row[$j] = "#row".$i;
                            $j++;
                          }
                          if(($row["tempSensor"] <=10)||($row["tempSensor"] >= 23)) {
                            $danger_row[$j] = "#row".$i ;
                            $j++;
                          }
                          if($ntu > 5) {
                            $danger_row[$j] = "#row".$i ;
                            $j++;
                          }
                          /*if($row["waterLevel"] >= 4) {
                            $danger_row[$j] = "#row".$i ;
                            $j++;
                          }*/
                          $i =$i+1;
                          }
                        ?>
                      
                      </tbody>
                 </table>
            </div>
        </div>
        <div class = "col-lg-offset-3 col-lg-6">
              <a href = "" >
                  <button type ="submit" name ="clear" class =" col-lg-12 btn btn-success">CLEAR PREVIOUS WATER QUALITY DATA</button>
              </a> 
         </div><br>
    </body>
    <script>
        var val =[];
        $(document).ready(function(){
           var radio = <?php echo $java_status; ?>;
             $(radio).prop("checked",true);
            var val = <?php echo json_encode($danger_row); ?>;
            for(var i=0; i<val.length;i++)
            {
              $(val[i]).addClass( "danger" );
            }
           
        });
    </script>
</html>