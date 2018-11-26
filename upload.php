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

     // Upload the image.
     $target_dir = "images/";
     $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
     $upload_ok = 1;
     $image_filetype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

     // Check if the image is real or fake.
     if(isset($_POST["submit"])) {
       $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
       if($check !== false) {
         echo "File is an image: " . $check["mime"] . ".\n";
         $upload_ok = 1;
       } else {
         echo "File is not an image.\n";
         $upload_ok = 0;
       }
     }

     $image_id = 0;

     if($upload_ok == 0) {
       echo "File was not uploaded.\n";
     } else {
       $filename = basename($_FILES["fileToUpload"]["name"]);
       if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
         $query = "INSERT INTO Images VALUES(DEFAULT, \"$filename\");";
         $result = mysqli_query($db, $query);
         $image_id = mysqli_insert_id($db);
         if($result) {
           echo "The file $filename has been uploaded.\n";
         } else {
           echo "The file was uploaded but not added to the database.\n";
           $error = mysqli_error($db);
           print "<p> $error </p>";
         }
       } else {
         echo "There was an error uploading the file.\n";
       }
     }

     // Get the tags for the image.
     $tag_string = $_POST["tags"];
     if($tag_string != "") {
       $tags = explode(" ", $tag_string);

       // Add each tag to the Tag table
       foreach($tags as $tag) {
         // check if the tag already exists
         $query = "SELECT Tag_id FROM Tags WHERE Name = \"$tag\"";
         $result = mysqli_query($db, $query);
         $tag_id = mysqli_fetch_array($result)["Tag_id"];
  
         // If there were no results add the tag to db
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

           $upd_tag_query = "UPDATE Tags SET Count = $count;";
           $upd_tag_result = mysqli_query($db, $upd_tag_query);

           if(!$upd_tag_result) {
             echo "The file was not updated.\n";
             $error = mysqli_error($db);
             print "<p> $error </p>";
           }
         }

         // Link this tag to the image.
         $link_query = "INSERT INTO Image_tags VALUES($image_id, $tag_id);";
         $link_result = mysqli_query($db, $link_query);

         if(!$link_result) {
             echo "Tag could not be linked to file.\n";
             $error = mysqli_error($db);
             print "<p> $error </p>";
         }
       }

     }

     mysqli_close($db);
     ?>

    <a href="index.html">Home</a>
    <a href="upload.html">Upload another image.</a>
  </body>
</html>
