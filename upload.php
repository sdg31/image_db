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

     if($upload_ok == 0) {
       echo "File was not uploaded.\n";
     } else {
       $filename = basename($_FILES["fileToUpload"]["name"]);
       if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
         $query = "INSERT INTO Images VALUES(DEFAULT, \"$filename\");";
         $result = mysqli_query($db, $query);
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

     mysqli_close($db);
     ?>

    <a href="index.html">Home</a>
    <a href="upload.html">Upload another image.</a>
  </body>
</html>
