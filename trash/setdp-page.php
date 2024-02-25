<?php
require 'cloudinary/cloudinary-cloudinary_php-0658ab3/autoload.php';
require 'cloudinary/config-cloud.php';
require 'cloudinary/cloudinary-cloudinary_php-0658ab3/src/Helpers.php'; //optional for using the cl_image_tag and cl_video_tag helper methods


?>

<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
        Upload a profile image:
        <input type="file" name="profileimg">
        <input type="submit" name="uploadprofileimg" value="Upload Image">
</form>