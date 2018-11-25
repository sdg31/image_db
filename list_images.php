<html>
  <head>
    <style>
      input[type="image"] {
          width: 150px;
      }

      form {
          display:inline-block;
      }

      table, td, tr {
          padding: 5px;          
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
       print "Error - tde query could not be executed";
       exit;
     }

     $num_fields = mysqli_num_fields($result);

     print "<table><tr>";

     // Display each image as a link to tde display page.
     foreach ($result as $row) {
       $filename = $row["Filename"];
       print "<td><form action=\"display.php\" metdod=\"get\">
       <input type=\"hidden\" name=\"id\"
       value=" . $filename . "> <input type=\"image\" name=\"image\"
       src=\"images/" . $filename . "\"></input></form></td>";
     }

     print "</tr></table>";
     ?>
  </body>
</html>
