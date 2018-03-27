<!-- This file returns search results from the submission of services.php -->

<?php
  // Checks to see if the submit button was clicked.
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate that the search included a service selection.
        if($_POST['service_select'] != ""){
          $days = explode(',', $_POST['days']);
          $times = explode(',', $_POST['time']);
          $service = $_POST['service_select'];

          // specify database connection credentials
          $conn = new mysqli("localhost", "student_user","my*password", "abrooks");

          // Pass the connection information and sql query to our function
          function servicesQuery($conn,$sql){
            // Use the variables we passed to our function in the Query
            $query = mysqli_prepare($conn,$sql)
              or die("Error: ". mysqli_error($conn));
            mysqli_stmt_execute($query)
              or die("Error. Could not insert into the table." . mysqli_error($conn));

            // Assign returned array to the variable "result"
            $result = mysqli_stmt_get_result($query);

            // Close the connection and the prepared statement.
            mysqli_stmt_close($query);
            mysqli_close($conn);

            // If statement to check if the filters returned anything with our Query.
            if(mysqli_num_rows($result) != 0){
            // Display our results as a table
            echo "<table id='searchTable'>";
            echo "<tr>
                <th>KSU NetID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Posted</th>
                <th>Available Days</th>
                <th>Available Hours</th>
                <th>Service</th>
                </tr>";
            // Loop through the query results(results are returned as an array).
            while($row = mysqli_fetch_array($result)){
              echo "<tr><td>{$row['netid']}</td><td>{$row['fname']} {$row['lname']}</td><td>{$row['email']}</td><td>{$row['post_date']}</td>
                <td>{$row['avail_days']}</td><td>{$row['avail_hours']}</td><td>{$row['service_description']}</td></tr>";
            }
            echo "</table>";
          }else{
            echo "Your search returned no results. Better luck next time!";
          }
          }

          if(!empty($_POST['time']) && !empty($_POST['days'])){
            // Run this query if time and day filters are selected.
              $sql = "SELECT netid, fname, lname, email, post_date, avail_days, avail_hours, service_description
                FROM profile
                INNER JOIN services_offered ON profile.profileID=services_offered.profileID
                INNER JOIN services ON services.svcID=services_offered.svcID
                WHERE (service_description LIKE '%$service%') AND (avail_hours LIKE '%";
              $times = implode("%' OR avail_hours LIKE '%", $times);
              $sql = $sql . $times . "%') AND (avail_days LIKE '%";
              $days = implode("%' OR avail_days LIKE '%", $days);
              $sql = $sql . $days . "%');";

              // Calls out function to query the database. Connection information and the SQL query are passed as variables.
              servicesQuery($conn, $sql);

          }elseif(!empty($_POST['time']) && empty($_POST['days'])){
            // Run this query if times are selected but not days.
              $sql = "SELECT netid, fname, lname, email, post_date, avail_days, avail_hours, service_description
                FROM profile
                INNER JOIN services_offered ON profile.profileID=services_offered.profileID
                INNER JOIN services ON services.svcID=services_offered.svcID
                WHERE (service_description LIKE '%$service%') AND (avail_hours LIKE '%";
              $times = implode("%' OR avail_hours LIKE '%", $times);
              $sql = $sql . $times . "%');";

              // Calls out function to query the database. Connection information and the SQL query are passed as variables.
              servicesQuery($conn, $sql);

          }elseif(empty($_POST['time']) && !empty($_POST['days'])){
            // Run this query if days are selected by times aren't
              $sql = "SELECT netid, fname, lname, email, post_date, avail_days, avail_hours, service_description
                FROM profile
                INNER JOIN services_offered ON profile.profileID=services_offered.profileID
                INNER JOIN services ON services.svcID=services_offered.svcID
                WHERE (service_description LIKE '%$service%') AND (avail_days LIKE '%";
              $days = implode("%' OR avail_days LIKE '%", $days);
              $sql = $sql . $days . "%');";

              // Calls out function to query the database. Connection information and the SQL query are passed as variables.
              servicesQuery($conn, $sql);
          }else{
            // Our default selection if no filters are applied. We display all available profiles for the selected service.
            $sql = "SELECT netid, fname, lname, email, post_date, avail_days, avail_hours, service_description
              FROM profile
              INNER JOIN services_offered ON profile.profileID=services_offered.profileID
              INNER JOIN services ON services.svcID=services_offered.svcID
              WHERE service_description LIKE '%$service%'";

              servicesQuery($conn, $sql);
          }

        }else{
          echo "You didn't search by service. Please click the back button and select a service.";
        }
    }
?>
