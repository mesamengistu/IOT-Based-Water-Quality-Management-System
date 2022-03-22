<!DOCTYPE html>
<html>
	<head>
		<title>NodeMCU ESP8266 MySQL Database</title>
		<meta charset="utf-8">
		<!-- Script for updating pages without refreshing the page -->
		<script src="../js/jquery.js"></script>
		<script>
		 var mes ={"value1":24.25,"value2":49.54,"value3":1005.14};
			$(document).ready(function() {
				setInterval(function(){get_data()},5000);
				function get_data()
				{
					jQuery.ajax({
						type:"GET",
						url: "read_db.php",
						data:"",
						beforeSend: function() {
						},
						complete: function() {
						},
						success:function(data) {
							$("table").html(data);
						}
					});
				}
			});
		</script>
	</head>
	<body>
		<table>
			
		</table>
	</body>
</html>