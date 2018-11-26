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

     $query = NULL;
     $search_string = $_POST["searchString"];
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
       }

       $image_id_query = "SELECT Image_id FROM Image_tags it
                          INNER JOIN Images i ON i.Image_id = it.Image_id 
                          WHERE ";

       $and_clause = "";
       for($i = 0; $i < count($tag_ids); ++$i) {
         $and_clause .= "it.Tag_id = $tag_ids[$i]";
         if($i != (count($tag_ids) - 1))
           $and_clause .= " AND ";
       }

       $image_id_query .= $and_clause .= ";";

       printf("image_id_query: %s <br/>", $image_id_query); 

       $image_id_result = mysqli_query($db, $image_id_query);

       if(!$image_id_result) {
         print "Nothing here.<br/>";
         $error = mysqli_error($db);
         print "<p> $error </p>";
       } else {
         $image_ids = mysqli_fetch_array($image_id_result);

         foreach($image_ids as $individual_id)
           printf("id1: %s <br/>", $individual_id);
  
         $query = "SELECT Filename, Image_id FROM Images
                   WHERE Image_id IN $image_ids;";
       }
     }

     $result = mysqli_query($db, $query);
     if (!$result) {
       print "Error - the query could not be executed";
       exit;
     }

     print "<table><tr>";

     // Display each image as a link to tde display page.
    foreach ($result as $row) {
       $id = $row["Image_id"];
       $filename = $row["Filename"];
       print "<td><form action=\"display.php\" metdod=\"get\">
       <input type=\"hidden\" name=\"id\"
       value=" . $id . "> <input type=\"image\" name=\"image\"
       src=\"images/" . $filename . "\"></input></form></td>";
     }

     print "</tr></table>";

     mysqli_close($db);
     ?>
  </body>
</html>
