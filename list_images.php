<html>
  <head>
    <link rel="stylesheet" href="stylesheet.css">

    <style>
      html {
          margin: 1em;
      }
      
      input[type="image"] {
          width: 150px;
      }

      form {
          display:inline-block;
      }

      table {
          width: 100%;
          padding: 5px;
      }

      td, tr {
          padding: 5px;     
      }
      
      .grid-container {
          display: grid;
          grid-gap: 10px; 
          grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));  
     }

      body {
          position: absolute;
          width: 95%;
      }

      .control {
          float: left;
          width: 5cm;
          height: 90%;
          margin-left: 0%;
          margin-right: auto;
          margin-top: 0%;
          margin-bottom: auto;
          overflow: hidden;
      }

      .list {
          float: left;
          position: relative;
          width: 70%;
          height: 900%;
          margin-left: auto;
          margin-right: 0%;
          margin-top: auto;
          margin-bottom: auto;
      }
    </style>

    <script>
      function orderGrid() {
          var clientWidth = document.getElementById("grid-container").clientWidth;

          var numElements = Math.floor(clientWidth / 160);
          
          var autos = "";
          for(var i = 0; i < numElements; ++i) {
              autos += "auto ";
          }
          
          document.getElementById("grid-container").style.gridTemplateColumns = autos;
      }
    </script>
  </head>
  <body onload="orderGrid()">
    <div class="control">
      <a href="index.html">Home</a>
      <a href="list_images.php?page=get&searchString=">Browse</a>
      <a href="help.html">Help</a>
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

       $query = "SELECT Name, Count FROM Tags ORDER BY Count DESC;";
       $result = mysqli_query($db, $query);
       $tag_array = mysqli_fetch_array($result);

       // list the top 20 tags
       print "<ul>";
       $i = 0;
       while($tag_array = mysqli_fetch_array($result)) {
         if($i > 20)
           break;

         $name = $tag_array["Name"];
         $count = $tag_array["Count"];

         print "<li><a href=\"list_images.php?page=get&searchString=$name\">$name</a> $count</li>";
         ++$i;
       }
       print "</ul>";

       mysqli_close($db);
       ?>
    </div>
    
    <div class="list">
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

     $query = NULL;
     $search_string = $_GET["searchString"];
     $tags = explode(" ", $search_string);

     // If the user searched for nothing, display everything.
     if(empty($search_string)) {
       $query = "SELECT Filename, Image_id FROM Images";
     } else {
       $tag_ids = [];
       foreach($tags as $tag) {
         $tag_name_query = "SELECT Tag_id FROM Tags WHERE Name = \"$tag\";";
         $tag_name_result = mysqli_query($db, $tag_name_query);

         $new_tag = mysqli_fetch_array($tag_name_result)["Tag_id"];
         // FIXME: this excludes non-existing tags, it shouldn't
         // proposed solution: tag ids start at 1, and invalids = 0
         if($new_tag)
           $tag_ids[] = $new_tag;
         else 
           $tag_ids[] = 0;
       }

       $image_id_query = "SELECT i.Image_id FROM Image_tags it
                          INNER JOIN Images i ON i.Image_id IN
                          (SELECT Image_id from Image_tags
                          WHERE Tag_id IN (" . implode(',',$tag_ids) . ") GROUP BY Image_id
                          HAVING COUNT(*) = " . count($tag_ids) . ");";


       $image_id_result = mysqli_query($db, $image_id_query);

       if(!$image_id_result) {
         print "Nothing here.<br/>";
         $error = mysqli_error($db);
         print "<p> $error </p>";
       } else {
         $image_ids = [];

         // Get each ID of the matching images
         while($row = mysqli_fetch_array($image_id_result))
             $image_ids[] = $row["Image_id"];

         // Find all filenames
         $query = "SELECT Filename, Image_id FROM Images
                   WHERE Image_id IN (" . implode(',',$image_ids) . ");";
       }
     }

     $result = mysqli_query($db, $query);
     if (!$result) {
       print "Nothing here.<br/>";
       exit;
     }

     print "<div class='grid-container'>";

     // Display each image as a link to tde display page.
    foreach ($result as $row) {
       $id = $row["Image_id"];
       $filename = $row["Filename"];
       print "<span class='thumb'><form action=\"display.php\" method=\"get\">
       <input type=\"hidden\" name=\"id\"
       value=" . $id . "> </input> <input type=\"image\" name=\"image\"
       src=\"images/" . $filename . "\"></input></form></span>";
     }

     print "</div>";

     mysqli_close($db);
    ?>
    </div>
  </body>
</html>
