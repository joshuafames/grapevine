
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Notify.php');
include('./classes/Image.php');
include('./classes/Comment.php');

$react = False;
$verified = False;
$isFollowing = False;
$liked = False;
$phoneNumbers = "";
$bio = "";

if (Login::isLoggedIn()) {
    $userid = Login::isLoggedIn();
    if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {

        $username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
        $handle = DB::query('SELECT handle FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['handle'];
        $email = DB::query('SELECT email FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['email'];
        $numberQuery = DB::query('SELECT phonenumber FROM profileinfo WHERE user_id=:userid', array(':userid'=>$userid));
        $bioQuery = DB::query('SELECT bio FROM profileinfo WHERE user_id=:userid', array(':userid'=>$userid));
        
        if(DB::query('SELECT phonenumber FROM profileinfo WHERE user_id=:userid', array(':userid'=>$userid))){
        	$phoneNumbers = $numberQuery[0]['phonenumber'];
        }
        if($bioQuery){
        	$bio = $bioQuery[0]['bio'];
        }

        if (empty($email)) {
            $emailEntry = 'youremail@mail.com" class="width500 form-control mx-4';
        }else {
            $emailEntry = ''.$email.'" class="width500 form-control mx-4 ui-entry';
        }

        if (!$numberQuery) {
            $numbersEntry = '012 345 6789" class="width500 form-control mx-4';
        }else {
            $numbersEntry = ''.$numberQuery[0]['phonenumber'].'" class="width500 form-control mx-4 ui-entry';
        }

        if (empty($bio)) {
            $bioEntry = 'Your Bio" class="width500 form-control mx-4';
        }else {
            $bioEntry = ''.$bio.'" class="width500 form-control mx-4 ui-entry';
        }
        $thisid = $userid;
        $verified = DB::query('SELECT verified FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['verified'];
        $followerid = Login::isLoggedIn();

        $loggedInUserImg = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>$followerid))[0]['profileimg'];
        $defaultimg = './img/default/undraw_profile_pic_ic5t.png';
        $meimage = $loggedInUserImg;


        if (empty($loggedInUserImg)) {
            $myprofilePic = $defaultimg;
        }else {
            $myprofilePic = $loggedInUserImg;
        }

        if ($userid == $followerid) {
            $profilePic = $myprofilePic;
        }elseif (is_null($meimage)) {
            $profilePic = $defaultimg;
        }else {
            $profilePic = $meimage;
        }

        $errorMessage = "";
        $successful = "";

        $nPosts = DB::query('SELECT COUNT(*) FROM posts WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
        $nFollowers = DB::query('SELECT COUNT(*) FROM followers WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
        $nFollowing = DB::query('SELECT COUNT(*) FROM followers WHERE follower_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];

        if (isset($_POST['submitchanges'])) {

            $newName = $_POST['name'];
            $newHandle = $_POST['handle'];
            $newEmail = $_POST['email'];
            $newBio = $_POST['bio'];
            $newNumbers = $_POST['numbers'];
		//print_r($_POST);
            if ($_POST['name']) {
                if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$newName))) {
                    if (strlen($newName) >= 3 && strlen($newName) <= 32) {
                        if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
                            DB::query('UPDATE users SET username = :newname WHERE id = :myid', array(':myid'=>$userid, ':newname'=>$newName));
                        }else {
                            $errorMessage = "Invalid Username";
                        }
                    }
                }else {
                    $errorMessage = "Username Already Exists";
                }
            }
            if ($_POST['handle']) {
		DB::query('UPDATE users SET handle = :newhandle WHERE id=:myid', array(':newhandle'=>$newHandle, ':myid'=>$userid));
            }

            if ($_POST['bio']) {
                #if (strlen($_POST['bio']) >= 1540) {
                    
                    if(DB::query('SELECT * FROM profileinfo WHERE user_id=:myid', array(':myid'=>$userid))){
                    	DB::query('UPDATE profileinfo SET bio = :newbio WHERE user_id = :myid', array(':myid'=>$userid, ':newbio'=>$newBio));
                    }else{
                    	DB::query('INSERT INTO profileinfo VALUES(:myid, :mybio, NULL)', array(':myid'=>$userid, ':mybio'=>$newBio));
                    }
                /*}else {
                    $errorMessage = "Your Bio has a little too many characters, try reducing the number of letters to 140 or less.";
                }*/
            }
            if($_POST['numbers']){
            	if(DB::query('SELECT * FROM profileinfo WHERE user_id=:myid', array(':myid'=>$userid))){
                    	DB::query('UPDATE profileinfo SET phonenumber = :newbio WHERE user_id = :myid', array(':myid'=>$userid, ':newbio'=>$newNumbers));
                    }else{
                    	DB::query('INSERT INTO profileinfo VALUES(:myid, NULL, :NUMS)', array(':myid'=>$userid, ':NUMS'=>$newNumbers));
                    }
            }
            
            
            
        }

        if (isset($_POST['uploadprofileimg'])) {
            Image::uploadImage('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid'=>$userid));
        }
    }else {
        die("Not logged in");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grapevine</title>

    <!-- bootstrap.min css -->
    <link rel="stylesheet" href="./vendor/bootstrap/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="./vendors/fontawesome/css/all.min.css">
    
    <!-- Themify Icons -->
    <link rel="stylesheet" href="./css/themify-icons.css">

    <!-- Icomooon Icons -->
    <link rel="stylesheet" href="./fonts/icomoon/style.css">

    <!-- Owl Carousel css -->
    <link rel="stylesheet" href="./vendors/owl-carousel/owl.carousel.min.css">
    <link rel="stylesheet" href="./vendors/owl-carousel/owl.theme.default.min.css">

    <!--- Custom CSS --->
    <link rel="stylesheet" href="./scss/style.css">
    <link rel="stylesheet" href="./css/addsIn.css">
</head>
<body>

    <!--================ Start Header Area =================-->
    <header class="header_area profile-page">
        <div class="main-menu social-site">
            <nav class="navbar fixed paddingNull navbar-expand-lg navbar-light">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <a class="navbar-brand logo w-150 logo-text" href="index.html">
                        <img src="./img/core-img/Grapevine-alt.png" alt="" srcset="">
                    </a>

                    <!-- Collect the nav links, forms, and other content for toggling --> 
                    <div class="navbar-underscreen offset m-lr-auto" id="navbarSupportedContent">
                        <div>
                            <ul class="nav navbar-nav menu_nav nice_iconic">
                                <li class="nav-item"><a class="nav-link" href="indexfeed.php"><i class="ti-home"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="about.html"><i class="ti-heart"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="portfolio.html"><i class="ti-search"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="contact.html"><i class="ti-settings"></i></a></li>
                                <li class="nav-item active"><a class="nav-link" href="contact.html"><i class="icon-user-o"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Other Additional nav content-->
                    <div class="user-area mt-0">
                        <div class="right-corner-nav flex-start v-align">
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="ti-bell"></i></a></div>
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="icon-envelope-o"></i></a><span class="division-border"></span></div>                            
                        </div>
                        <div class="profile-picture ml-20">
                            <a href="accountprofile.php?handle=<?php echo($handle); ?>"><img src='<?php echo($myprofilePic); ?>' alt="" srcset="" style="height: 30px;"></a>
                        </div>                                      
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!--================ End Header Area =================-->

    <!--================ Start Main Area =================-->
    <div class="limiter settings-page r-font-med">
        <div class="container100" style="padding-top: 50px;">
            <div class="wrap-comment100 mt-4">
                <div class="row">
                    <div class="col-lg-3 bdr-r">

                        <div class="page-heading" style="margin-top: 30px;">
                            <h5 class="ra-heading mb-3" style="margin-left: 30px;">Settings</h5>
                        </div>

                        <ul class="nav-tabs settings-menu py-2" role="tablist" style="width: 100%;">
                            <li class="nav-item">
                                <a href="#tab-1" data-toggle="tab" role="tab" aria-controls="tab-1" aria-selected="true" class="nav-link cn-link active">Edit Profile</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-2" data-toggle="tab" role="tab" aria-controls="tab-2" aria-selected="false" class="nav-link cn-link">Request Verification</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-3" data-toggle="tab" role="tab" aria-controls="tab-3" aria-selected="false" class="nav-link cn-link">Security</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-4" data-toggle="tab" role="tab" aria-controls="tab-4" aria-selected="false" class="nav-link cn-link">Privacy</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-5" data-toggle="tab" role="tab" aria-controls="tab-5" aria-selected="false" class="nav-link cn-link">Notifications</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-6" data-toggle="tab" role="tab" aria-controls="tab-6" aria-selected="false" class="nav-link cn-link">Login Activity</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-7" data-toggle="tab" role="tab" aria-controls="tab-7" aria-selected="false" class="nav-link cn-link">Profile Info</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-9">
                        <div class="tab-content">

                            <div role="tabpanel" id="tab-1" aria-labelledby="tab-1" class="tab-pane show fade tab-1 r-content m-lr-auto active">
                                <form class="form" method="post" action="usersettings.php">
                                    <div class="form-inline mt-4">
                                        <div class="image-section ml-auto">
                                            <img src='<?php echo($myprofilePic); ?>'>
                                        </div>
                                        <div class="r-image width500 mx-4">
                                            <p class="username flex-start c-font mb-0"><?php echo $username; ?>
                                                <?php if ($verified) { echo '<span class="v-align icon-check-circle ml-2" style="color: #007bff;"></span>';} ?>
                                            </p>
                                            <a href="#" data-toggle="modal" data-target="#changeDP" class="username c-font mb-0" style="color: #00acdb">Change Profile Image</a>
                                        </div>
                                    </div>
                                    <div class="name-area mt-4 form-inline">
                                        <p class="input-title mb-0">Name</p>
                                        <input type="text" name="name" placeholder="<?php echo $username; ?>" class="width500 form-control mx-4 ui-entry">
                                    </div>
                                    <div class="mt-4 form-inline">
                                        <p class="input-title mb-0">Handle</p>
                                        <input type="text" name="handle" placeholder="<?php echo $handle; ?>" class="width500 form-control mx-4 ui-entry">
                                    </div>
                                    <div class="mt-4 form-inline">
                                        <p class="input-title mb-0" style="padding-bottom: 35px;">Bio</p>
                                        <textarea type="text" name="bio" placeholder="<?php echo $bioEntry; ?>" class="width500 form-control mx-4 ui-entry" style="height: 70px !important;"></textarea>
                                    </div>
                                    <div class="mt-4 form-inline">
                                        <p class="input-title mb-0">Email</p>
                                        <input type="text" name="email" placeholder="<?php echo $emailEntry; ?>">
                                    </div>
                                    <div class="mt-4 form-inline">
                                        <p class="input-title mb-0">Phone Number</p>
                                        <input type="text" name="numbers" placeholder="<?php echo $numbersEntry; ?>">
                                    </div>

                                    <?php 
                                        if ($errorMessage) {
                                            echo '<h4 class="response-text r-font onn mt-4">'.$errorMessage.'</h4>';
                                        }
                                        if ($successful) {
                                            echo '<h4 class="response-text r-font onn mt-4" style"color : #00db7f;">'.$successful.'</h4>';
                                        }
                                    ?>                                    
                                    <div class="submit-area">
                                        <button class="ss-btn btn button mx-4 my-4" name="submitchanges">Submit</button>
                                    </div>
                                </form>
                            </div>

                            <div role="tabpanel" id="tab-2" aria-labelledby="tab-2" class="tab-pane fade tab-2">
                                <h1>Request Verification</h1>
                            </div>
                            <div role="tabpanel" id="tab-3" aria-labelledby="tab-3" class="tab-pane fade tab-3">
                                <h1>33</h1>
                            </div>
                            <div role="tabpanel" id="tab-4" aria-labelledby="tab-4" class="tab-pane fade tab-4">
                                <h1>44</h1>
                            </div>
                            <div role="tabpanel" id="tab-5" aria-labelledby="tab-5" class="tab-pane fade tab-5">
                                <h1>55</h1>
                            </div>
                            <div role="tabpanel" id="tab-6" aria-labelledby="tab-6" class="tab-pane fade tab-6">
                                <h1>66</h1>
                            </div>
                            <div role="tabpanel" id="tab-7" aria-labelledby="tab-7" class="tab-pane fade tab-7">
                                <h1>77</h1>
                            </div>
                        </div>    

                        <!--- CHANGE PROFILE IMG MODAL --->
                        <div class="modal fade" id="changeDP" role="dialog" tabindex="-1" style="padding-top: 200px;">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                    <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                        <h4 class="modal-title">Change Profile Picture</h4>
                                    </div>
                                    <div class="modal-body dg-text modal-border-split c-font">
                                        <form action='usersettings.php' method="post" enctype="multipart/form-data">
                                                <input type="file" name="profileimg">
                                                <input type="submit" name="uploadprofileimg" value="Upload Image">
                                        </form>
                                    </div>

                                    <div class="modal-body dg-text justify-content-center">
                                            <a href="#" name="post" class="btn v-align c-font" style="color: #c00;">Remove Current Photo</a>
                                    </div>
                                    <div class="modal-footer justify-content-center">
                                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
                                    </div>                                                    
                                </div>
                            </div>
                        </div>
                        <!---- /////////////////// --->                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--================ End Main Area =================-->
    

    <!-- Jquery js file -->
    <script src="./js/jquery-3.2.1.min.js"></script>
    
    <script src="./js/jquery.sticky.js"></script>
    
    <!-- bootstrap js -->
    <script src="./js/bootstrap.min2.js"></script>

    <!--OwlCarousel Script-->
    <script src="./vendors/owl-carousel/owl.carousel.min.js"></script>

    <!-- Main-JS -->
    <script src="./js/main.js"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('.nav-item .cn-link').click(function() {
                    $('.cn-link.active').removeClass('active');
                    $(this).addClass('youuu');
            });
        });
    </script>
</body>
</html>
