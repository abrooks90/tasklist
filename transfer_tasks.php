<?php
@session_start(); //start session

//see http://phpsec.org/projects/guide/4.html
if (!isset($_SESSION['authenticated'])) {
    session_regenerate_id();
    $_SESSION['authenticated'] = 0;
}
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

			function loadResponse(){
				var id = document.forms['tasks'].taskID.value;
				xmlReq = new XMLHttpRequest();
				xmlReq.onreadystatechange = processResponse;
				xmlReq.open("POST", "get_recipients.php", true);
				parameter = "taskID=" + encodeURI(document.forms['tasks'].taskID.value);
				xmlReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				xmlReq.send(parameter);
				return false;
			}
			
			function loadResponse2(){
				if(confirm("Are you sure you want to mark this task as complete?")){
					document.forms["tasks"].action = "mark_complete.php";
					document.forms["tasks"].submit();
				}else{
					alert("Incomplete");
					return false;
				}
			}
			
			function checkrecipient(){
				var recipients = document.forms['tasks'].recipient.value;
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
			<?php include "side_nav.php"; ?>
			<h3>Transfer a Task:</h3>

			<form class="tasks" action="transfer.php" id="tasks" method="post" onsubmit="return checkrecipient()">
				<?php include "menu.php";
				// Check to see if there's a valid session. If not, display link to login page.
				if (!isset($_SESSION['authenticated']) OR !$_SESSION['authenticated'] == 1) {
				    echo "ERROR: To use this web site, you need to have valid credentials.  <a href='login.php'>Log in here.</a>";
				}
				else {
					$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
					if (mysqli_connect_errno()){
						die('Cannot connect to database: ' . mysqli_connect_error($conn));
					}
					else{
						$query = mysqli_prepare($conn,"SELECT profileID FROM profile where netID = ?");
						mysqli_stmt_bind_param($query,"s",$_SESSION['user']);
						mysqli_stmt_execute($query);
						mysqli_stmt_bind_result($query,$profID);
						$result = mysqli_stmt_get_result($query);
						while($row=mysqli_fetch_array($result)){
							$profileID = $row['profileID'];
						}
						mysqli_stmt_close($query);
						$query = mysqli_prepare($conn,"SELECT task_description,task_due_date,service_description, taskID FROM tasks INNER JOIN services ON tasks.svcID = services.svcID INNER JOIN profile on tasks.assigned_user = profile.profileID WHERE assigned_user = ? AND complete = 'N'")
							or die("Error: " . mysqli_error($conn));
						mysqli_stmt_bind_param($query,"i",$profileID);
						mysqli_stmt_execute($query)
							or die("Error: ". mysqli_error($conn));
						$result = mysqli_stmt_get_result($query);
						mysqli_close($conn);
						mysqli_stmt_close($query);
	
						if(!$result){
							die("Error: " . mysqli_error($conn));
						}
						else{
							echo "<div class='trow'><div class='tcell'>Task description</div><div class='tcell'>Due date</div><div class='tcell'>Service</div><div class='tcell'>Transfer task</div><div class='tcell'>Mark complete</div></div>";
							while($row = mysqli_fetch_array($result)){
								echo "<div class='trow'><div class='tcell'>{$row['task_description']}</div><div class='tcell'>{$row['task_due_date']}</div><div class='tcell'>{$row['service_description']}</div><div class='tcell'><input type='radio' name='taskID' value='{$row['taskID']}' onchange='return loadResponse()'/></div><div class='tcell'><input type='radio' name='markcomplete' value='{$row['taskID']}' onchange='return loadResponse2()'/></div></div>";
							}
						}
					}
				?>
				<br/>
				<div id="recipients"></div>
			</form>
		</div>
	<!-- Closing tag for our previous statement that checks for a valid session -->
	<?php } ?>
	</body>
</html>
