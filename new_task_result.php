<?php
session_start();
include 'menu.php';
if (!isset($_SESSION['user'])) {
	exit;
}
$conn = new mysqli("localhost", 'student_user', 'my*password', "abrooks");
if (mysqli_connect_errno()) {
	die('Cannot connect to database: ' . mysqli_connect_error($conn));
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!empty($_POST['taskDescription'])) {
		$netID = $_SESSION['user'];
		$email = "";
		$taskDescription = $_POST['taskDescription'];
		$serviceSelected = $_POST['service_select'];
		$query1 = mysqli_prepare($conn, "SELECT svcID FROM services WHERE service_description=?");
		mysqli_stmt_bind_param($query1, "s", $serviceSelected);
        mysqli_stmt_execute($query1) or die("Error. Could not select from the table." . mysqli_error($conn));
        $result = mysqli_stmt_get_result($query1);
        mysqli_stmt_close($query1);
        if (!$result) {
            die("Invalid query: " . mysqli_error($conn));
        } else {
            while ($row = mysqli_fetch_array($result)) {
                $serviceSelected = $row['svcID'];
            }
        }
        $query2 = mysqli_prepare($conn, "SELECT email FROM profile WHERE netid=?");
        mysqli_stmt_bind_param($query2, "s", $netID);
        mysqli_stmt_execute($query2) or die("Error. Could not select from the table." . mysqli_error($conn));
        $result2 = mysqli_stmt_get_result($query2);
        mysqli_stmt_close($query2);
        if (!$result2) {
            die("Invalid query: " . mysqli_error($conn));
        } else {
            while ($row = mysqli_fetch_array($result2)) {
                $email = $row['email'];
            }
        }
        $assignedUser = $_POST['assign_user'];
        $query3 = mysqli_prepare($conn, "SELECT profileID FROM profile WHERE email=?");
        mysqli_stmt_bind_param($query3, "s", $assignedUser);
        mysqli_stmt_execute($query3) or die("Error. Could not select from the table." . mysqli_error($conn));
        $result3 = mysqli_stmt_get_result($query3);
        mysqli_stmt_close($query3);
        if (!$result3) {
            die("Invalid query: " . mysqli_error($conn));
        } else {
            while ($row = mysqli_fetch_array($result3)) {
                $assignedUser = $row['profileID'];
            }
        }
		$deadline = $_POST['taskDeadline'];
        echo "<br> $netID | $email | $taskDescription | $serviceSelected | $assignedUser | $deadline";
        $enum = 'N';
        $query4 = mysqli_prepare($conn, "INSERT INTO tasks(requester_email, task_description, task_due_date, svcID, profileID, complete) VALUES (?, ?, ?, ?, ?, ?)") or die("Error: " . mysqli_error($conn));
        mysqli_stmt_bind_param($query4, "sssiis", $email, $taskDescription, $deadline, $serviceSelected, $assignedUser, $enum);
        mysqli_stmt_execute($query4) or die("Error. Could not insert into the table." . mysqli_error($conn));
        mysqli_stmt_close($query4);
        mysqli_close($conn);
	}
} else {
	exit;
}