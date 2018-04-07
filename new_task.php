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
    <script src="new_task.js"></script>
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
            <li><a href="new_task.php">New Task</a></li>
        </ul>
    </nav>
    <h3>Add New Task</h3>
    <form id="newTask" action="new_task_result.php" method="POST" onsubmit="return validate();">
        <?php include 'menu.php'; ?>
        <span class="success"></span>
        <fieldset id="task">
            <label>Your KSU NetID: <input type="text" id="netID" name="netID" placeholder="Enter NetID" required autofocus></label>
            <br><br>
            <?php
                $conn = new mysqli("localhost", "student_user", "my*password", "abrooks");
                $query1 = mysqli_prepare($conn, "SELECT service_description FROM services") or die("Error: ". mysqli_error($conn));
                mysqli_stmt_execute($query1) or die("Error. Could not insert into the table." . mysqli_error($conn));
                $result1 = mysqli_stmt_get_result($query1);
                mysqli_close($conn);
                mysqli_stmt_close($query1);
                if (!$result1) {
                    die("Invalid query: " . mysqli_error($conn));
                } else {
                    echo "<label>Request a Service: <select name='service_select' required><option value=''>Select Service</option>";
                    while($row = mysqli_fetch_array($result1)){
                        echo "<option>{$row['service_description']}</option>";
                    }
                    echo "</select><label>";
                }
            ?>
            <br><br>
            <label>Assign a User (E-Mail): <select name="assign_user" required>
                <option value="">Assign a User</option>
                <?php
                    $conn = new mysqli("localhost", "student_user", "my*password", "abrooks");
                    $query2 = mysqli_prepare($conn, "SELECT email FROM profile") or die("Error: ". mysqli_error($conn));
                    mysqli_stmt_execute($query2) or die("Error. Could not insert into the table." . mysqli_error($conn));
                    $result2 = mysqli_stmt_get_result($query2);
                    mysqli_close($conn);
                    mysqli_stmt_close($query2);
                    if (!$result2) {
                        die("Invalid query: " . mysqli_error($conn));
                    } else {
                        while($row = mysqli_fetch_array($result2)){
                            echo "<option>{$row['email']}</option>";
                        }
                    }
                ?>
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