<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Notify.php');

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        echo 'Not logged in';
}

$activity = "";
$notify = "";
$myusername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))['0']['username'];
$myhandle = DB::query('SELECT handle FROM users WHERE id=:userid', array(':userid'=>$userid))['0']['handle'];
$defaultimg = './img/default/undraw_profile_pic_ic5t.png';

$meimage = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['profileimg'];

if (empty($meimage)) {
        $myprofilePic = "./img/default/undraw_profile_pic_ic5t.png";
}else {
        $myprofilePic = $meimage;
}       

///////////////////////////
        
    if (isset($_GET['handle'])) {
        $hisuserid = DB::query('SELECT id FROM users WHERE handle = :handle', array(':handle'=>$_GET['handle']))[0]['id'];
        $myid = DB::query('SELECT id FROM users WHERE handle = :handle', array(':handle'=>$myhandle))[0]['id'];

        if (isset($_POST['follow'])) {  
            
            if ($hisuserid != $myid) {
                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$hisuserid, ':followerid'=>$myid))) {
                    if ($myid == 6) {
                        DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$hisuserid));
                    }
                    DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$hisuserid, ':followerid'=>$myid));

                    $isFollowing = True;

                    $s = DB::query('SELECT username FROM users WHERE id=:fid', array(':fid'=>$myid))[0]['username']; #the one who is following you
                    DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>3, ':receiver'=>$hisuserid, ':sender'=>$myid, ':extra'=>"", ':postid'=>""));

                } else {
                    echo "Already Following!";
                }                
            }               
        }

        if (isset($_POST['unfollow'])) {            
            if ($hisuserid != $myid) {
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$hisuserid, ':followerid'=>$myid))) {
                    if ($myid == 6) {
                        DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$hisuserid));
                    }
                    DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$hisuserid, ':followerid'=>$myid));
                    $isFollowing = False;
                }                
            }               
        }
    }
///////////////

$defaultimg = 'https://i.imgur.com/K8NcRRz.png';
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Grapevine</title>

	<!-- bootstrap.min css -->
	<link rel="stylesheet" href="./css/bootstrap.min.css">

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
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./scss/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@700&display=swap" rel="stylesheet">
</head>
<body>

	<!--================ Start Header Area =================-->
	<header class="header_area">
		<div class="main-menu social-site">
			<nav class="navbar fixed paddingNull navbar-expand-lg navbar-light">
				<div class="container">
					<!-- Brand and toggle get grouped for better mobile display -->
                    <a class="navbar-brand logo w-150 logo-text" href="index.html">
                        <img src="./img/core-img/Grapevine-alt.png" alt="" srcset="">
                    </a>   
                    <div class="search-bar">
                        <form action="#">
                            <div class="input-group">
                                <i class="ti-search input-icon"></i>
                                <input
                                  type="search" 
                                  name="" id="" 
                                    class="form-control"
                                    placeholder="Search"                                            
                                >
                            </div>                            
                        </form>
                    </div>                    
                    <!-- Collect the nav links, forms, and other content for toggling --> 
                    
                    <div class="user-area mt-0">
                        <div class="right-corner-nav flex-start v-align">
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="ti-bell"></i></a></div>
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="icon-envelope-o"></i></a><span class="division-border"></span></div>                            
                        </div>
                        <div class="profile-picture ml-20">
                            <a href="userprofile.php"><img src="<?php echo($myprofilePic); ?>" alt="" srcset=""></a>
                        </div>
                        <div class="username">
                            <a href="userprofile.php" style="color: initial;"><h7><?php echo($myusername); ?></h7></a>
                        </div>                 
                    </div>                    
				</div>
			</nav>
		</div>
	</header>
	<!--================ End Header Area =================-->

	<!--================ Start Main Area =================-->
	<body class="site-main mt">

        <section class="feed-area body" style="min-height: 100vh;">
            <div class="container">
                <div class="row">
                    <!--left area-->
                    <div class="col-lg-3 left-profile-area feed">
                        <div class="profile fixed">
                            <div class="profile-info left-nav left">
                                <div class="floating-image">
                                    <img src="<?php echo($myprofilePic); ?>" alt="" srcset="" style="height: 100px;">
                                </div>
                                <div class="info-area">
                                    <div class="profile-id text-center mb-4">
                                        <h5 class="name"><?php echo($myusername); ?></h5>
                                        <h7 class="handle" style= "color: #000;">@<?php echo($myhandle); ?></h7>
                                    </div>                                    
                                    <div class="nav-section vertical-navbar">
                                        <ul class="nav navbar iconic-vertical">
                                            <li class="nav-item d-block">
                                                <a href="indexfeed.php" class="nav-link d-flex flex-start" style="padding: .5rem .9rem;">
                                                    <span style="font-size: 22px;" class="iconify mr-3" data-icon="octicon:home-24" data-inline="false"></span>
                                                    <span>Home</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block">
                                                <a href="explore.php" class="nav-link d-flex flex-start">
                                                    <i class="ti-heart v-align mr-3"></i>
                                                    <span>Explore</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block">
                                                <a href="#" class="nav-link d-flex flex-start">
                                                    <i class="ti-search v-align mr-3"></i>
                                                    <span>Popular Hashtags</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block active">
                                                <a href="#" class="nav-link d-flex flex-start">
                                                    <i class="ti-bell v-align mr-3"></i>
                                                    <span>Notifications</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block">
                                                <a href="#" class="nav-link  d-flex flex-start">
                                                    <i class="ti-settings v-align mr-3"></i>
                                                    <span>Settings</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block top-divider">
                                                <a href="#" class="nav-link d-flex flex-start">
                                                    <i class="icon-sign-out v-align mr-3"></i>
                                                    <span>Log Out</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>                              
                            </div>
                        </div>
                    </div>
                    <!--Post area-->
                    <div class="col-lg-9">
                        <div class="mca row">
                            <div class="col-lg-7">

                                <div class="activity rounded-container mt-4" style="margin-bottom: 3.5rem;">
                                    <h5 class="ra-heading mb-4" style="color: #000;">Activity</h5>

                                    <?php 

                                        if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid))) {
                                                $notifications = DB::query('SELECT DISTINCT * FROM notifications WHERE receiver=:userid ORDER BY `time` DESC', array(':userid'=>$userid));

                                                foreach ($notifications as $n) {
                                                    $sName = DB::query('SELECT username FROM users WHERE id=:sid', array(':sid'=>$n['sender']))[0]['username'];
                                                    $sHandle = DB::query('SELECT handle FROM users WHERE id=:sid', array(':sid'=>$n['sender']))[0]['handle'];
                                                    $profileimg = DB::query('SELECT profileimg FROM users WHERE id=:sid', array(':sid'=>$n['sender']))[0]['profileimg'];

                                                    if (empty($profileimg)) {
                                                        $sDp = $defaultimg;
                                                    }else {
                                                        $sDp = $profileimg;
                                                    }
                                                    $Firss = '<div class="single-notify-item flex-start mt-3">
                                                                <div class="s-dp margin-r-15">
                                                                    <img style="width:40px;" src="';///IMAGE///
                                                    $Second = '"></div><div class="v-align notify-body flex-start"><p class="c-font-light pr-4 mb-0"><a class="c-font-med text-black mr-1" href="profile-page.php?handle="'.$sHandle.'">';///NAME///
                                                    $Third = '</a>';//// NOTIFICATION
                                                    $Fourth = '</p></div><div class="call-to ml-auto"><a href="#"><img src="';/////Post Link Img
                                                    $Fifth = '" style="height: 40px;"></a></div></div>';

                                                    if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$n['id'], ':followerid'=>$userid))) {
                                                        $typeThree = '</a></div><div class="call-to ml-auto"><form action="activity-page.php?username='.$sName.'" method="post"><button class="btn sub-following-btn" type="submit">Following</button></form></div></div>';
                                                    }else {
                                                        $typeThree = '</a></div><div class="call-to ml-auto"><form action="activity-page.php?username='.$sName.'" method="post"><button class="btn sub-follow-btn" type="submit">Follow</button></form></div></div>';
                                                    }
                                                    

                                                    $posted_hour = DB::query('SELECT timestampdiff(HOUR, `time`, now() ) as hours_since FROM notifications WHERE id=:id', array(':id'=>$n['id']))[0]['hours_since'];
                                                    $posted_minute = DB::query('SELECT timestampdiff(MINUTE, `time`, now() ) as minutes_since FROM notifications WHERE id=:id', array(':id'=>$n['id']))[0]['minutes_since'];
                                                    $roundDay = 24;
                                                    $posted_day = round($posted_hour/$roundDay);

                                                    if ($posted_hour < 1) {
                                                        $timeNumber = $posted_minute;
                                                        $when = "min";
                                                        }else if ($posted_hour == 1) {
                                                                $timeNumber = $posted_hour;
                                                                $when = "hr";
                                                        }else if ($posted_hour > 24) {
                                                                $timeNumber = $posted_day;
                                                                $when = "d";
                                                        }else {
                                                            $timeNumber = $posted_hour;
                                                            $when = "hrs ago";
                                                    }

                                                    $since = "$timeNumber $when";

                                                    if ($n['type'] == 1) {

                                                            if ($n['extra'] == "") {
                                                                    echo "New Notification";

                                                            }else {
                                                                    $extra = json_decode($n['extra']);
                                                                    echo $Firss.$sDp.$Second.$sName.$Third.'  mentioned you in a post - '.$Fourth.$Fifth.'';
                                                                    ///$notify .= $Firss.$sDp.$Second.$sName.$Third.'  mentioned you in a post'.$Fourth.$Fifth."<br/> - ".$extra->postbody.'';
                                                            }
                                                    
                                                    }elseif ($n['type'] == 2) {
                                                            $postlinkid = $n['post_id'];
                                                            $postlinkimg = DB::query('SELECT postimg FROM posts WHERE id=:plid', array(':plid'=>$postlinkid))[0]['postimg'];
                                                            echo($Firss.$sDp.$Second.$sName.$Third.' liked your post!'."<br/><span style='color:#b7b7b7;font-size: 12px;'>".$since."</span>".$Fourth.$postlinkimg.$Fifth);
                                                            ///$notify .= $sName.' liked your post!';
                                                    }elseif ($n['type'] == 3) {
                                                            echo($Firss.$sDp.$Second.$sName.$Third.' just started following you!'."<br/><span style='color:#b7b7b7;font-size: 13px;font-family:Poppins-Medium;'>".$since."</span>".$typeThree);
                                                    }
                                              }
                                        }

                                    /*<div class="single-notify-item flex-start mt-3">
                                        <div class="s-dp margin-r-15">
                                            <img src="./img/people/person_8.jpg">
                                        </div>
                                        <div class="v-align notify-body flex-start">
                                            <a class="c-font-med text-black mr-1" href="#"><!-- SENDER NAME -->Jaden</a><a class="c-font-light text-black" href="#">liked your post</a>
                                        </div>
                                        <div class="call-to ml-auto">
                                            <!-- LIKED IMAGE OR FOLLOW BUTTON IF FOLLOWED -->
                                            <a href="#"><img src="./img/people/imagery.jpg" style="height: 40px;"></a>
                                        </div>
                                    </div>
                                    <div class="single-notify-item flex-start mt-3">
                                        <div class="s-dp margin-r-15">
                                            <img src="./img/people/person_8.jpg">
                                        </div>
                                        <div class="v-align notify-body flex-start">
                                            <a class="c-font-med text-black mr-1" href="#"><!-- SENDER NAME -->Taylor Matt</a><a class="c-font-light text-black" href="#">liked your post</a>
                                        </div>
                                        <div class="call-to ml-auto">
                                            <!-- LIKED IMAGE OR FOLLOW BUTTON IF FOLLOWED -->
                                            <a href="#"><img src="./img/people/imagery.jpg" style="height: 40px;"></a>
                                        </div>
                                    </div>
                                    <div class="single-notify-item flex-start mt-3">
                                        <div class="s-dp margin-r-15">
                                            <img src="./img/people/person_8.jpg">
                                        </div>
                                        <div class="v-align notify-body flex-start">
                                            <a class="c-font-med text-black mr-1" href="#"><!-- SENDER NAME -->Jason Trae</a><a class="c-font-light text-black" href="#">started following you</a>
                                        </div>
                                        <div class="call-to ml-auto">
                                            <!-- LIKED IMAGE OR FOLLOW BUTTON IF FOLLOWED -->
                                            <a href="#"><button class="btn sub-follow-btn" type="button">Follow</button></a>
                                        </div>
                                    </div> */

                                    ?>
                                    
                                </div>

                            </div>

                            <div class="col-lg-5">
                                <div class="sticky-container left-pp-area">
                                    <div class="trending-tags-section rounded-container mt-4">
                                        <h6 class="lc-section-heading mb-3">Stories</h6>
                                        <div class="single-section-item mt-10 flex-start">
                                            <div class="icon">
                                                <i class="fa fa-hashtag hashtag-icon"></i>
                                            </div>
                                            <a href="#" class="liked-page-name v-align mb-0 ml-10">Football NC</a>
                                        </div>
                                        <div class="single-section-item mt-10 flex-start">
                                            <div class="icon">
                                                <i class="fa fa-hashtag hashtag-icon"></i>
                                            </div>
                                            <a href="#" class="liked-page-name v-align mb-0 ml-10">BlackLivesMatter</a>
                                        </div>
                                        <div class="single-section-item mt-10 flex-start">
                                            <div class="icon">
                                                <i class="fa fa-hashtag hashtag-icon"></i>
                                            </div>
                                            <a href="#" class="liked-page-name v-align mb-0 ml-10">4th_Industrial_Revolution</a>
                                        </div>
                                        <div class="single-section-item mt-10 flex-start">
                                            <div class="icon">
                                                <i class="fa fa-hashtag hashtag-icon"></i>
                                            </div>
                                            <a href="#" class="liked-page-name v-align mb-0 ml-10">Football NC</a>
                                        </div>
                                        <div class="single-section-item mt-10 flex-start">
                                            <div class="icon">
                                                <i class="fa fa-hashtag hashtag-icon"></i>
                                            </div>
                                            <a href="#" class="liked-page-name v-align mb-0 ml-10">BlackLivesMatter</a>
                                        </div>
                                        <div class="single-section-item mt-10 flex-start">
                                            <div class="icon">
                                                <i class="fa fa-hashtag hashtag-icon"></i>
                                            </div>
                                            <a href="#" class="liked-page-name v-align mb-0 ml-10">4th_Industrial_Revolution</a>
                                        </div>
                                        <div class="single-section-item mt-10 flex-start">
                                            <div class="icon">
                                                <i class="fa fa-hashtag hashtag-icon"></i>
                                            </div>
                                            <a href="#" class="liked-page-name v-align mb-0 ml-10">BlackLivesMatter</a>
                                        </div>
                                        <div class="single-section-item mt-10 flex-start">
                                            <div class="icon">
                                                <i class="fa fa-hashtag hashtag-icon"></i>
                                            </div>
                                            <a href="#" class="liked-page-name v-align mb-0 ml-10">4th_Industrial_Revolution</a>
                                        </div>
                                    </div>
                                    <div class="sub-section footer mt-30">
                                        <div class="d-block">
                                            <p class="seo-link mb-0">Dreams &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved |</p>
                                            <a href="#" class="seo-link">Report A Problem<span>|</span></a>
                                            <a href="#" class="seo-link">Privacy<span>|</span></a>
                                            <a href="#" class="seo-link">Terms & Policies<span>|</span></a>
                                            <a href="#" class="seo-link">Help</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                            
                    
                </div>
            </div>
        </section>

	</body>
	<!--================ End Main Area =================-->
	

	<!-- Jquery js file -->
	<script src="./js/jquery-3.2.1.min.js"></script>
	
	<!-- bootstrap js -->
	<script src="./js/bootstrap.min.js"></script>

	<!--OwlCarousel Script-->
	<script src="./vendors/owl-carousel/owl.carousel.min.js"></script>

    <script src="https://code.iconify.design/1/1.0.7/iconify.min.js"></script>

	<!-- Main-JS -->
	<script src="./js/main.js"></script>
</body>
</html>