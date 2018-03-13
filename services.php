<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<title>KSU Student Services | Search Services</title>
<link rel="stylesheet" type="text/css" href="registration_styles.css" media="screen">
<script src="http://code.jquery.com/jquery.min.js"></script>


<script type="text/javascript">
    var xmlReq;
    function processResponse(){
       if(xmlReq.readyState == 4){
           var place = document.getElementById("placeholder");
           place.innerHTML = xmlReq.responseText
      }
    }
   function loadResponse(){

    select = document.getElementById('service_select'); // or in jQuery use: select = this;
    if (!select.value) {
    //If select value isn't valid, return false.
    return false;
    }

     $(document).ready(function() {

       $("form#search").submit(function() {

        var days = new Array();
        $("input:checked").each(function() {
           days.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: "search_result.php",
            dataType: 'html',
            data: 'service_select='+$("#service_select").val()  + '&days=' + days,
            success: function(data){
                $('#placeholder ').html(data)
            }
        });
        return false;
        });
      });

      // create an instance of XMLHttpRequest
      xmlReq = new XMLHttpRequest();
      xmlReq.onreadystatechange = processResponse;
      //call server_side.php
      xmlReq.open("POST", "search_result.php", true);
      //read value from the form
      // encodeURI is used to escaped reserved characters
      parameter = "service_select=" + encodeURI(document.forms["form1"].service_select.value) +  encodeURI("&days : ['Tue', 'Wed']");
      //send headers
      xmlReq.setRequestHeader("Content-type",
                  "application/x-www-form-urlencoded");
      xmlReq.setRequestHeader("Content-length", parameter.length);
      xmlReq.setRequestHeader("Connection", "close");
      //send request with parameters
      xmlReq.send(parameter);
      return false;

   }


</script>
</head >
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

  <h3>Search Services</h3>
  <form form id="search" name="form1" method="post" action="">
    <?php include "menu.php"; ?>
    <?php
      // specify database connection credentials
      $conn = new mysqli("localhost", "student_user","my*password", "abrooks");

      // Query the database for services in our services table
      $query = mysqli_prepare($conn,
        "SELECT service_description FROM services")
          or die("Error: ". mysqli_error($conn));
      mysqli_stmt_execute($query)
        or die("Error. Could not insert into the table." . mysqli_error($conn));

      // Close the connection and the prepared statement
      $result = mysqli_stmt_get_result($query);
      mysqli_close($conn);
      mysqli_stmt_close($query);

      // We're going to display the information as a select menu using PHP to echo HTML
      if (!$result) {
         die("Invalid query: " . mysqli_error($conn));
      } else {
        echo "<fieldset><label>Select by Service:<select id='service_select' name='service_select' required><option value=''>Select Service</option>";
        while($row = mysqli_fetch_array($result)){
          echo "<option>{$row['service_description']}</option>";
        }
        echo "</select><label></fieldset>";
      }
    ?>


    <!-- Filter by available days of the week -->
    <fieldset id="days"><legend>Select Available Days:</legend>
      <label><input type="checkbox" class="days" value="Sun" name="days[]">Sun</label>
      <label><input type="checkbox" class="days" value="Mon" name="days[]">Mon</label>
      <label><input type="checkbox" class="days" value="Tue" name="days[]">Tue</label>
      <label><input type="checkbox" class="days" value="Wed" name="days[]">Wed</label>
      <label><input type="checkbox" class="days" value="Thu" name="days[]">Thu</label>
      <label><input type="checkbox" class="days" value="Fri" name="days[]">Fri</label>
      <label><input type="checkbox" class="days" value="Sat" name="days[]">Sat</label>
    </fieldset>

    <!-- Filter by available hours of the day -->
    <fieldset id="availability">
      <legend>Filter by Availability:</legend>
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
	     <input type="submit" value="Search" onclick="loadResponse()"></script>
	  </fieldset>

    <div id="placeholder"></div>
<!--
    <script>
    var x = document.createElement("FIELDSET");
    x.setAttribute("id","submission");
    var btn = document.createElement("BUTTON");
    btn.setAttribute("type", "submit");
    var t = document.createTextNode("Search");
    btn.appendChild(t);
    x.appendChild(btn);
    document.getElementById("placeholder").appendChild(x);
    document.getElementById("submission").onclick = loadResponse;
    </script>
-->
  </form>
    </div>
  </div>
</body>
</html>
