<?php
session_start();
if (!isset($_SESSION['user'])) {
	exit;
}
$conn = new mysqli("localhost", "student_user", "my*password", "abrooks");
$service =  $_POST['selected'];
$sql = mysqli_prepare($conn, "SELECT email FROM profile INNER JOIN services_offered ON profile.profileID=services_offered.profileID INNER JOIN services ON services.svcID=services_offered.svcID WHERE service_description=?") or die("Error: " . mysqli_error($conn));
mysqli_stmt_bind_param($sql, "s", $service);
mysqli_stmt_execute($sql) or die("Error. Could not select from the table." . mysqli_error($conn));
$result = mysqli_stmt_get_result($sql);
mysqli_stmt_close($sql);
if (!$result) {
	die("Invalid query: " . mysqli_error($conn));
} else {
	if (mysqli_num_rows($result) != 0) {
		while ($row = mysqli_fetch_array($result)) {
			echo "<option>{$row['email']}</option>";
			
		}
		
	} else {
		echo "<option value=''>Assign a user</option>";
	}	
}