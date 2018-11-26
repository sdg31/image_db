<html>
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

   $id = $_GET["id"];

   // Get the filename.
   $filename_query = "SELECT Filename FROM Images WHERE Image_id = $id;";
   $filename_result = mysqli_query($db, $filename_query);

   if(!$filename_result) {
     print "Error: the query could not be executed.";
     exit;
   }

   // Get the actual filename string and display the image.
   $image = mysqli_fetch_array($filename_result);
   print "<img src=\"images/$image[0]\">";

   // Get the tags
   $tags_query = "SELECT Name, Count FROM Image_tags it
                  INNER JOIN Tags t ON t.Tag_id = it.Tag_id
                  WHERE it.Image_id = $id;";
   $tags_result = mysqli_query($db, $tags_query);

   if(!$tags_result) {
     echo "Tag could not be linked to file.\n";
     $error = mysqli_error($db);
     print "<p> $error </p>";
   }

  print "<br />";

   while($row = mysqli_fetch_array($tags_result)) {
     printf("%s %s ", $row["Name"], $row["Count"]);
   }

   mysqli_close($db);
?>
</body>
</html>
