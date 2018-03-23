<?php session_start(); ?>
<html>
	<head>
		<title>Task List</title>
  </head>
  <body>
		<?php include "menu.php";
			$conn = new mysqli("localhost", $_SESSION['user'],$_SESSION['password'], "abrooks");
			if (mysqli_connect_errno()){
				die('Cannot connect to database: ' . mysqli_connect_error($conn));
			}
			else{
				if(!empty($_POST['taskID']){
					echo $_POST['taskID'];
					/*$query = mysqli_prepare($conn,"SELECT profileID FROM profile where netID = ?");
					mysqli_stmt_bind_param($query,"s",$_SESSION['user']);
					mysqli_stmt_execute($query);
					mysqli_stmt_bind_result($query,$profID);
					$result = mysqli_stmt_get_result($query);
					while($row=mysqli_fetch_array($result)){
						$profileID = $row['profileID'];
					}
					mysqli_stmt_close($query);
					$query = mysqli_prepare($conn,"SELECT task_description,task_due_date,service_description, taskID FROM tasks INNER JOIN services ON tasks.svcID = services.svcID INNER JOIN profile on tasks.assigned_user = profile.profileID WHERE assigned_user = ?")
						or die("Error: " . mysqli_error($conn));
					mysqli_stmt_bind_param($query,"i",$profileID);
					mysqli_stmt_execute($query)
						or die("Error: ". mysqli_error($conn));
					$result = mysqli_stmt_get_result($query);
					mysqli_close($conn);
					mysqli_stmt_close($query);
					if(!$result){
						die("Error: " . mysqli_error($conn));
					}else{
						echo "<table>";
						echo "<th></th><th>Task Description</th><th>Due Date</th><th>Service</th><th>Mark Complete</th></tr>";
						while($row = mysqli_fetch_array($result)){
							echo "<tr><td><input type='radio' name='taskID' value='{$row['taskID']}' onchange='return loadResponse()'/></td><td>{$row['task_description']}</td><td>{$row['task_due_date']}</td><td>{$row['service_description']}</td><td><input type='checkbox' name='completetask' value='{$row['taskID']}'/></td></tr>";
						}
						echo "</table>";
					}*/
				}else{
					echo "Please select task";
				}
			}
		?>
	</body>
</html>