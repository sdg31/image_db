<html>

  <body>
    
    <?php
     $target_dir = "images/";
     $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
     $upload_ok = 1;
     $image_filetype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

     // Check if the image is real or fake.
     if(isset($_POST["submit"])) {
       $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
       if($check !== false) {
         echo "File is an image: " . $check["mime"] . ".";
         $upload_ok = 1;
       } else {
         echo "File is not an image.";
         $upload_ok = 0;
       }
     }

     if($upload_ok == 0) {
       echo "File was not uploaded.";
     } else {
       if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
         echo "The file " . basename($_FILES["fileToUpload"]["tmp_name"]) . " has been uploaded.";
       } else {
         echo "There was an error uploading the file.";
       }
     }
     ?>
  </body>

</html>
