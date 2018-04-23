<?php
	@session_start(); //start session

	//see http://phpsec.org/projects/guide/4.html
	if (!isset($_SESSION['authenticated'])) {
		session_regenerate_id();
		$_SESSION['authenticated'] = 0;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KSU Student Services | New Task</title>
    <link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script>
      $(function () {

        $('.service_select').on('change', function() {
            var selected = $(this).val();
            var dataString = "selected="+selected;
            $.ajax({
                type: "POST",
                url: "assign_user.php",
                data: dataString,
                success: function(result) {
                    if (result=="<option value=''>Assign a user</option>") {
                        $('#assignUser').html("<option value=''>Assign a user</option>")
                    } else {
                        $('#assignUser').html("<option value=''>Assign a user</option>").append(result);
                    }
                }
            });
        });

        $('form').on('submit', function (e) {

          e.preventDefault();

          $.ajax({
            type: 'POST',
            url: 'new_task_result.php',
            data: $('form').serialize(),
            success: function () {
              $(".success").html('Form submitted successfully!');
            }
          });
        });
      });
    </script>
</head>
<body>
    <header>
	    <h1>KSU Student Services</h1>
    </header>
    <div id="wrapper">
    <?php include 'side_nav.php';?>
    <h3>Add New Task</h3>
    <form id="newTask" action="new_task_result.php" method="POST">
        <?php include 'menu.php';?>
        <h4><?php
if (!isset($_SESSION['user'])) {
	echo "ERROR: To use this web site you need to have valid credentials.  <a href='login.php'>Log in here.</a>";
	exit;
}
?></h4>
        <span class="success"></span>
        <fieldset id="task">
            <label>Your KSU NetID is: <input type="text" id="netID" name="netID" placeholder="<?php echo $_SESSION['user']; ?>" disabled></label>
            <br><br>

<?php
$conn = new mysqli("localhost", "student_user", "my*password", "abrooks");
$query1 = mysqli_prepare($conn, "SELECT svcID, service_description FROM services") or die("Error: " . mysqli_error($conn));
mysqli_stmt_execute($query1) or die("Error. Could not insert into the table." . mysqli_error($conn));
$result1 = mysqli_stmt_get_result($query1);
mysqli_stmt_close($query1);
if (!$result1) {
	die("Invalid query: " . mysqli_error($conn));
} else {
    echo "<label>Request a Service: <select class='service_select' name='service_select' required>";
    echo "<option value=''>Select a service</option>";
	while ($row = mysqli_fetch_array($result1)) {
		echo "<option>{$row['service_description']}</option>";
	}
	echo "</select><label>";
}
?>
            <br><br>
            <label>Assign a User (E-Mail): <select id="assignUser" name="assign_user" required>
            <option value="">Assign a user</option>

            </select></label>
            <br><br>
            <label>Task Deadline: <input type="date" name="taskDeadline" required></label>
            <br><br>
            <label>Task Description: <textarea id="taskDescription" name="taskDescription" cols="40" rows="7" maxlength="140" title="Please enter a task description." required placeholder="Enter task description..."></textarea></label>
            <br><br>
            <button type="submit" id="btnSubmit">Add New Task</button>
        </fieldset>
    </form>
    </div>
</body>
</html>
