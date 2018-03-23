<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Transfer Tasks</title>
		<link rel="stylesheet" type="text/css" href="registration_styles.css">
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
			<?php
			if(!empty($_POST['recipient'])){
				$email = $_POST['recipient'];
			} else{
				echo "Please choose a recipient";
			}
			$conn = new mysqli("localhost", "student_user","my*password", "abrooks");
			if (mysqli_connect_errno()){
				die('Cannot connect to database: ' . mysqli_connect_error($conn));
			} else{
				if($query = mysqli_prepare($conn,"SELECT netID from profile where email = ?")){
					mysqli_stmt_bind_param($query,"s",$email);
					mysqli_stmt_execute($query);
					mysqli_stmt_bind_result($query,$netID);
					while(mysqli_stmt_fetch($query)){
						$netID = $netID;
					}
					mysqli_stmt_close($query);
				}else{
					die("Error: " . mysqli_error($conn));
				}
				if($query = mysqli_prepare($conn,"UPDATE tasks SET assigned_user = ? WHERE taskID = ?")){
					mysqli_stmt_bind_param($query,"si",$netID,$_SESSION['taskID']);
					mysqli_stmt_execute($query);
					mysqli_stmt_close($query);
					echo "<h3>Successful Transfer</h3><br/>";
					echo "<form id='successful'><br/>";
					echo "<?php include 'menu.php'; ?><br/>";
					echo "The task was successfully transferred.<br/>";
					$to = $email;
					$subject = "Task transferred";
					$msg = "The following task has been transferred to you: ";
					$msg.= $_SESSION['taskdesc'] . " Due: " . $_SESSION['duedate'];
					$body = $msg;
					if(mail($to,$subject,$body)){
						echo "An email has been sent to " . $email;
					}else{
						echo "Email delivery failed. Please notify recipient.";
					}
				}else{
					die("Error: " . mysqli_error($conn));
				}
			}
			mysqli_close($conn);
			?>
			
  </form>

  </div>
</body>
</html>