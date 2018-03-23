<?php 
	session_start();
	$_SESSION['user'] = 'jane234';
	$_SESSION['password'] = 'test*password';
?>
<?xml version="1.0" encoding="ISO-8859-1"?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Transfer task</title>
		<link rel="stylesheet" type="text/css" href="registration_styles.css"/>
		<script type="text/javascript">
			var xmlReq;
			function processResponse(){
				if(xmlReq.readyState == 4){
					var place = document.getElementById("recipients");
					place.innerHTML = xmlReq.responseText;
				}
			}

			function loadResponse(obj){
				var id = document.forms['tasks'].taskID.value;
				xmlReq = new XMLHttpRequest();
				xmlReq.onreadystatechange = processResponse;
				xmlReq.open("POST", "tasks.php", true);
				parameter = "taskID=" + encodeURI(document.forms['tasks'].taskID.value);
				xmlReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				xmlReq.send(parameter);
				return false;
			}
			
			function checkrecipient(){
				var recipients = document.forms['recipients'].recipient.value;
				if(!recipients){
					alert("Please select a person");
					return false;
				}
				else{
					return true;
				}
			}
		</script>
	</head>
	<body>
		<header>
			<h1>KSU Student Services</h1>
		</header>
		<div id="wrapper">
			<nav id="navigation">
				<ul>
					<li><a href="registration.php">Registration</a></li>
					<li><a href="services.php">Search Services</a></li>
				</ul>
			</nav>
			<h3>Select a task to transfer:</h3><br/>
			<form class="tasks" id="tasks">
				<?php include "menu.php";
				$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
				if (mysqli_connect_errno()){
					die('Cannot connect to database: ' . mysqli_connect_error($conn));
				}
				else{
					$query = mysqli_prepare($conn,"SELECT task_description,task_due_date,service_description, taskID FROM tasks INNER JOIN services ON tasks.svcID = services.svcID WHERE assigned_user = ?")
						or die("Error: " . mysqli_error($conn));
					mysqli_stmt_bind_param($query,"s",$_SESSION['user']);
					mysqli_stmt_execute($query)
						or die("Error: ". mysqli_error($conn));
					$result = mysqli_stmt_get_result($query);
					mysqli_close($conn);
					mysqli_stmt_close($query);
				
					if(!$result){
						die("Error: " . mysqli_error($conn));
					}
					else{
						echo "<table>";
						echo "<th></th><th>Task Description</th><th>Due Date</th><th>Service</th></tr>";
						while($row = mysqli_fetch_array($result)){
							echo "<tr><td><input type='radio' name='taskID' value='{$row['taskID']}' onchange='return loadResponse()'/></td><td>{$row['task_description']}</td><td>{$row['task_due_date']}</td><td>{$row['service_description']}</td></tr>";
						}
						echo "</table>";
					}
				}
				?>
			</form><br/>
		</div>
		<h3>Select a recipient:</h3>
			<form class="tasks" action="taskTransfer.php" id="recipients" method="post" onsubmit="return checkrecipient()">
			</form>
		<br/>
	</body>
</html>