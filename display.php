<html>
  <head>
    <link rel="stylesheet" href="stylesheet.css">

    <style>
      body {
          position: absolute;
          width: 95%;
      }

      .control {
          float: left;
          width: 15%;
          height: 90%;
          margin-left: 0%;
          margin-right: auto;
          margin-top: 0%;
          margin-bottom: auto;
          overflow: hidden;
      }

      .display {
          float: left;
          position: relative;
          width: 80%;
          height: 900%;
          margin-left: auto;
          margin-right: 0%;
          margin-top: auto;
          margin-bottom: auto;
      }
    </style>
    <script>
      function editTags() {
          var editButton = document.getElementById("editButton");
          editButton.outerHTML = "<form method='post'> <input type='input' autofocus value='tags go here'></input><input type='submit' value='Submit'></input></form>"
      }
    </script>
  </head>
<body>
  <div class="control">
    <form action="list_images.php" method="get">
      <input type="text" name="searchString" id="searchString">
      <input type="submit" value="search">
    </form>

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

  print "<ul>";
  $i = 0;
  while($tag_array = mysqli_fetch_array($tags_result)) {
    if($i > 20)
    break;

    $name = $tag_array["Name"];
    $count = $tag_array["Count"];

    print "<li><a href=\"list_images.php?page=get&searchString=$name\">$name</a> $count</li>";
    ++$i;
  }
  print "</ul>";
?>
  </div>
  <div class="display">
<?php
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

   mysqli_close($db);
?>
  <br/>
  <a onclick="editTags()" id="editButton">Edit</a>
</div>
</body>
</html>
