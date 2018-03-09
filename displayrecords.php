<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">
    var xmlReq;
    function processResponse(){
       if(xmlReq.readyState == 4){
           var place = document.getElementById("placeholder");
           place.innerHTML = xmlReq.responseText
      }
    }

   function loadResponse(){
      // create an instance of XMLHttpRequest
      xmlReq = new XMLHttpRequest();
      xmlReq.onreadystatechange = processResponse;

      //call server_side.php
      xmlReq.open("POST", "search_result.php", true);

      //read value from the form
      // encodeURI is used to escaped reserved characters
      parameter = "keyword=" + encodeURI(document.forms["form1"].keyword.value);

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
</head>
   <body>
      <form method="POST" name="form1" action="" onsubmit="return loadResponse();">
         Search by job title: <input type="text" name="keyword">
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
           echo "<fieldset><label>Select by Service:<select name='service_select' required><option value=''>Select Service</option>";
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

         <input type="button" value="GO"
             onclick="loadResponse()">


      </form>
      <div id="placeholder"></div>
   </body>
</html>
