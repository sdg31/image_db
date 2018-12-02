<html>
  <head>
    <link rel="stylesheet" href="stylesheet.css">
  </head>
  <body>
  	Update.php
  <?php
  	$tag_string = $_GET["tags"];
    $image_id = $_GET["id"];
  	$tags = explode(" ", $tag_string);

    // Connect to database
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

    $tag_query =
    "SELECT DISTINCT t.* FROM Images i 
     JOIN Image_tags it ON i.Image_id = it.Image_id 
     JOIN Tags t ON it.Tag_id = t.Tag_id
     WHERE i.Image_id = $image_id";
    $tag_result = mysqli_query($db, $tag_query);

    $tags_str = "";
    if (!$tag_result){
      print "Error: the query could not be executed"; 
      $tags_str = "ERROR";
      exit;
    }

    // Fetch old tags
    $tag_arr = $tag_result->fetch_assoc();

    do {
      // Store old tag ids in string: 
      $tags_str = $tags_str . $tag_arr['Tag_id'] . " ";
    } while ($tag_arr = $tag_result->fetch_assoc());

    // Get old tag ids
    $old_tagid_arr = explode(" ", $tags_str);
    print "<p> $tags_str </p>";


     // Update tags for image
     // FIXME: Delete original ones first
    foreach ($tags as $tag) {

      $query = "SELECT Tag_id FROM Tags WHERE Name = \"$tag\"";
      $result = mysqli_query($db, $query);
      $tag_id = mysqli_fetch_array($result)["Tag_id"];

      $pos = array_search($tag_id, $old_tagid_arr);
      
      // Ignore old tags
      if (!($pos === false)){
        unset($old_tagid_arr[$pos]);
        continue;
      }


      if(mysqli_num_rows($result) == 0) {
        $add_tag_query = "INSERT INTO Tags VALUES(DEFAULT, \"$tag\", 1);";
        $add_tag_result = mysqli_query($db, $add_tag_query);
        $tag_id = mysqli_insert_id($db);

        if(!$add_tag_result) {
          echo "The tag was not added to the database.\n";
          $error = mysqli_error($db);
          print "<p> $error </p>";
        }

      } else { // otherwise, update the tag in the db
           // We can only get the count if we SELECT from the PRIMARY KEY (Tag_id)
        $count_query = "SELECT Count FROM Tags WHERE Tag_id = $tag_id;";
        $count_result = mysqli_query($db, $count_query);
        $count = mysqli_fetch_array($count_result)["Count"];
        ++$count;

        $upd_tag_query = "UPDATE Tags SET Count = $count WHERE Tag_id = $tag_id";
        $upd_tag_result = mysqli_query($db, $upd_tag_query);

        if(!$upd_tag_result) {
          echo "The file was not updated.\n";
          $error = mysqli_error($db);
          print "<p> $error </p>";
        }
      }
      $link_query = "INSERT INTO Image_tags VALUES($image_id, $tag_id);";
      $link_result = mysqli_query($db, $link_query);
    }
    
    mysqli_close($db);

    header('index.html'); 

  ?>
  <a href="index.html">Home</a>

  </body>
</html>