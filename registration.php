<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>KSU Student Services | Registration</title>
<link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
<script src="http://code.jquery.com/jquery.min.js"></script>
<script>
function formValidation(){
  // Check the number of checkboxes that are checked and assign them to variable n.
  var n = $( ".service:checkbox:checked" ).length;
  // If n is equal to 0, alert the user and don't allow form submission.
  if(n==0){
    alert("Please select at least one available service!")
    return false;
  }

  // If n is equal to 0, alert the user and don't allow form submission.
  n = $( ".availability:checkbox:checked" ).length;
  if(n==0){
    alert("Please select your availability!")
    return false;
  }

  n = $( ".days:checkbox:checked" ).length;
  if(n==0){
    alert("Please select your availability!")
    return false;
  }
}
</script>
</head>

<body>
  <?php
    // specify database connection credentials
    $conn = new mysqli("localhost", "student_user","my*password", "abrooks");

    // Query the database to create checkboxes for available services.
    $query = mysqli_prepare($conn,
      "SELECT service_description FROM services")
        or die("Error: ". mysqli_error($conn));
    mysqli_stmt_execute($query)
      or die("Error. Could not insert into the table." . mysqli_error($conn));

    $result = mysqli_stmt_get_result($query);
    mysqli_close($conn);
    mysqli_stmt_close($query);
  ?>

  <header>
    <h1>KSU Student Services</h1>
  </header>

  <!-- Using a div to format the form -->
  <div id="wrapper">
  <?php include "side_nav.php"; ?>


  <h3>Student Services Registration </h3>
  <!--Trigger the "formValidation" javascript for input validation on checkboxes-->
  <form id="registration" method="post" action="form_result.php" onsubmit="return formValidation(this)">
    <?php include "menu.php"; ?>
    <fieldset id="contact">
    <!--use the "pattern" and "title" fields to notify the end user of invalid input-->
    <!--NetID consists of alphanumerical input. Uppercase and lowercase and 0-9. Input should at least be 3 characters.-->
    <label>KSU NetID:<input type="text" pattern="[A-Za-z0-9]{3,}" title="Please enter your alphanumerical NetID." name="netId" required placeholder="Enter NetID"></label><br>
    <!--First and last name are uppercase/lowercase with at least 3 letters.-->
    <label>First Name:<input type="text" pattern="[A-Za-z]{3,}" title="Please enter a name with at least three letters." name="firstName" required placeholder="Enter First Name"></label><br>
    <label>Last Name:<input type="text" pattern="[A-Za-z]{3,}" title="Please enter a name with at least three letters." name="lastName" required placeholder="Enter Last Name"></label><br>
    <!--HTML5 has a built in pattern match for email-->
    <label>Email:<input type="email" name="email" required placeholder="Enter a valid Email"></label><br>
    </fieldset>


    <?php
    // Creating checkboxes from resultant query
    if (!$result) {
       die("Invalid query: " . mysqli_error($conn));
    } else {
      echo "<fieldset id='service'><legend>Select Available Services:</legend>";
      while($row = mysqli_fetch_array($result)){
        echo " <label><input type='checkbox' class='service' value='{$row['service_description']}' name='services[]'>{$row['service_description']}</label>";
      }
      echo "</fieldset>";
    }
    ?>

    <!-- Select days of the week for availability -->
    <fieldset id="days"><legend>Select Available Days:</legend>
    <label><input type="checkbox" class="days" value="Sun" name="days[]">Sun</label>
    <label><input type="checkbox" class="days" value="Mon" name="days[]">Mon</label>
    <label><input type="checkbox" class="days" value="Tue" name="days[]">Tue</label>
    <label><input type="checkbox" class="days" value="Wed" name="days[]">Wed</label>
    <label><input type="checkbox" class="days" value="Thu" name="days[]">Thu</label>
    <label><input type="checkbox" class="days" value="Fri" name="days[]">Fri</label>
    <label><input type="checkbox" class="days" value="Sat" name="days[]">Sat</label>
    </fieldset>

    <!-- Select hours of the day for availability -->
    <fieldset id="availability">
    <legend>Select Available Times:</legend>
    <label><input type="checkbox" class="availability" value="8AM-9AM" name="time[]">8AM-9AM</label>
    <label><input type="checkbox" class="availability" value="9AM-10AM" name="time[]">9AM-10AM</label>
    <label><input type="checkbox" class="availability" value="11AM-12PM" name="time[]">11AM-12PM</label>
    <label><input type="checkbox" class="availability" value="12PM-1PM" name="time[]">12PM-1PM</label>
    <br>
    <label><input type="checkbox" class="availability" value="1PM-2PM" name="time[]">1PM-2PM</label>
    <label><input type="checkbox" class="availability" value="2PM-3PM" name="time[]">2PM-3PM</label>
    <label><input type="checkbox" class="availability" value="3PM-4PM" name="time[]">3PM-4PM</label>
    <label><input type="checkbox" class="availability" value="4PM-5PM" name="time[]">4PM-5PM</label>
    </fieldset>

    <fieldset id="submission">
    <label>Email Confirmation?<input type="checkbox" name="emailConfirmation"></label>
    <input type="submit" value="Submit Registration" />
    </fieldset>
  </form>

  </div>
</body>
</html>
