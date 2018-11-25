<html>
  <head>
    <style>
      input[type="image"] {
          width: 150px;
      }

      form {
          display:inline-block;
      }

      img {
          width: 150px;
      }
    </style>
  </head>
  <body>
    <?php
     $db = mysqli_connect("db1.cs.uakron.edu:3306", "sdg31", "password");

     if(!$db) {
       print "Error - Could not connect to database.";
       exit;
     }

     $er = mysqli_select_db($db, "ISP_sdg31");
     if(!$er) {
       print "Error - could not select database.";
       exit;
     }

     $query = "SELECT Filename FROM Images";
     $result = mysqli_query($db,$query);

     if (!$result) {
       print "Error - the query could not be executed";
       exit;
     }

     $num_fields = mysqli_num_fields($result);

     print "<table><caption> <h2> Images ($num_fields) </h2> </caption>";
     print "<tr align = 'center'>";

     //$row = mysqli_fetch_array($result);
     //$num_fields = mysqli_num_fields($result);

     // Produce the column labels

     // Output the values of the fields in the rows
     for ($row_num = 0; $row_num < $num_rows; $row_num++) {
       print "<tr align = 'center'>";
       $values = array_values($row);
       for ($index = 0; $index < $num_fields; $index++){
         $value = htmlspecialchars($values[2 * $index + 1]);
         $id = htmlspecialchars($values[1]);

         if($index == 1) {
                 //print "<th><form action=\"display.php\" method=\"get\">
                 //<input type=\"text\" style=\"visibility:hidden\" name=\"id\" 
                 //value=" . $value . "> <input type=\"image\" name=\"image\"
                 //src=\"images/" . $value . "\"></input></form></th>";
                 print "<th><img src=\"images/" . $value . "\"></th>";
         }
         else
           print "<th>" . $value . "</th> ";
       }

     print "</tr>";
     $row = mysqli_fetch_array($result);
     }

     print "</table>";
     ?>
  </body>
</html>
