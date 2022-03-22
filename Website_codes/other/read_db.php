<!DOCTYPE html>
<html>
	<head>
		<style>
			table {
				border-collapse: collapse;
				width: 100%;
				color: #1f5380;
				font-family: monospace;
				font-size: 20px;
				text-align: left;
			} 
			th {
				background-color: #1f5380;
				color: white;
			}
			tr:nth-child(even) {background-color: #f2f2f2}
		</style>
	</head>
	<?php
		$hostname = "localhost";	
		$username = "root";	
		$password = "";	
		$dbname = "andu";
		$conn = mysqli_connect($hostname, $username, $password, $dbname);
		if (!$conn) {
			die("Connection failed !!!");
		} 
	?>
	<body>
		<table>
			<tr>
				<th>gasSensor</th> 
				<th>waterLevel</th> 
				<th>phSensor</th>
				<th>tempSensor</th>
			</tr>
			<?php
				$table = mysqli_query($conn, "SELECT gasSensor, waterLevel, phSensor, tempSensor FROM SensorData"); 
				while($row = mysqli_fetch_array($table))
				{
			?>
			<tr>
				<td><?php echo $row["gasSensor"]; ?></td>
				<td><?php echo $row["waterLevel"]; ?></td>
				<td><?php echo $row["phSensor"]; ?></td>
				<td><?php echo $row["tempSensor"]; ?></td>
			</tr>
			<?php
				}
			?>
		</table>
	</body>
</html>