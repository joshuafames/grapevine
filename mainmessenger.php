<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    include('classes/DB.php');
    include('classes/Login.php');
    include('classes/Post.php');
    include('classes/Notify.php');
    include('classes/Image.php');
    include('classes/Comment.php');

    $showTimeline = False;
    if (Login::isLoggedIn()) {
            $userid = Login::isLoggedIn();
            $showTimeline = True;

            $myusername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))['0']['username'];
            $meimage = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['profileimg'];
            $myhandle = DB::query('SELECT handle FROM users WHERE id = :userid', array(':userid'=>Login::isLoggedIn()))[0]['handle'];

            if (empty($meimage)) {
                    $myprofilePic = "./img/default/undraw_profile_pic_ic5t.png";
            }else {
                    $myprofilePic = $meimage;
            }
            $defaultimg = 'https://i.imgur.com/K8NcRRz.png';

            if (isset($_POST['comment'])) {
                    Comment::createComment($_POST['commentbody'], $_GET['postid'], $userid);
            }

            if (isset($_POST['post'])) {
                    ini_set('max_execution_time', 300);
                    /*if ($_FILES['postimg']['size'] >= 0) {
                            die("There's an image though!");                            
                            ///$postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                            Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
                    }else { 
                            die("Nope no image");
                            //Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                            
                    }*/

                    if(!empty($_FILES['postimg']['tmp_name']) && file_exists($_FILES['postimg']['tmp_name'])) {
                        //die("There's an image though!"); 
                            $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                            Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
                    }else {
                        //die("Nope no image");
                            Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                    }
            }

            if (isset($_POST['logout'])) {
                if (isset($_COOKIE['SNID'])) {
                    DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
                }       
                setcookie('SNID', '1', time()-3600);
                setcookie('SNID_', '1', time()-3600);
                header('Location: login-page.php');
            }
            $trendingTag = DB::query('SELECT * FROM topics ORDER BY numposts DESC LIMIT 3');
    } else {
            header('Location: login-page.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Grapevine - Messenger</title>
    <link rel="icon" href="./img/core-img/Gv-icon.png">

	<!-- bootstrap.min css -->
	<link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/util.css">

	<!-- FontAwesome -->
    <link rel="stylesheet" href="./vendors/fontawesome/css/all.min.css">

    <!-- Icomooon Icons -->
    <link rel="stylesheet" href="./fonts/icomoon/style.css">

    <!-- Themify Icons -->
    <link rel="stylesheet" href="./css/themify-icons.css">

	<!-- Owl Carousel css -->
	<link rel="stylesheet" href="./vendors/owl-carousel/owl.carousel.min.css">
	<link rel="stylesheet" href="./vendors/owl-carousel/owl.theme.default.min.css">

    <!-- Animate -->
    <link rel="stylesheet" href="css/animate.css">

    <!-- FANCYBOX -->
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">

    <!--- Custom CSS --->
    <link rel="stylesheet" href="./css/addsIn.css">
    <link rel="stylesheet" href="./css/mobile.css">    
    <link rel="stylesheet" href="./scss/style.css">
    
    <!--<link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@700&display=swap" rel="stylesheet">-->

    <script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule="" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

	<!--================ Start Header Area =================-->
	<header class="header_area">
		<div class="main-menu social-site">
			<nav class="navbar fixed paddingNull navbar-expand-lg navbar-light sm-md-border-none">
				<div class="container">
					<!-- Brand and toggle get grouped for better mobile display -->
                    <button class="logo-text border-none md-sm-only profile-pic fs-22" id="nav-toggle">Grapevine</button>
                    <!---<div class="right-menu d-flex">
                        <button class="border-none md-sm-only profile-pic mr-3">
                            <i class="icon-envelope"></i>
                        </button>
                        <button class="border-none md-sm-only profile-pic ml-2" id="nav-toggle">
                            <i class="icon-navicon"></i>
                        </button>
                    </div>        --->                
                    <button class="logo-text border-none lg-only" id="nav-toggle">
                        <img src="./img/core-img/Gv-icon.png" alt="" style="max-width: 40px;" srcset="">
                    </button>
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
                            <a id="act-btn" class="rc-nav-link hover"><i class="far fa-bell"></i></a>
                            <a href="#" id="act-btn" class="rc-nav-link hover notify-btn"><i class="icon-envelope-o"></i></a>
                            <!--<a href="#" class="rc-nav-link"><ion-icon style="font-size: 22px" name="paper-plane-outline"></ion-icon></a><span class="division-border"></span>-->
                        </div>
                        <div class="profile-picture ml-20">
                            <a href="accountprofile.php?handle=<?php echo($myhandle); ?>"><img src="<?php echo($myprofilePic); ?>" alt="" srcset="" style="height: 30px;"></a>
                        </div>
                        <div class="username">
                            <a href="accountprofile.php?handle=<?php echo($myhandle); ?>" class="text-black"><h7><?php echo($myusername); ?></h7></a>
                        </div>                 
                    </div>                    
				</div>
			</nav>
		</div>
	</header>
	<!--================ End Header Area =================-->

	<!--================ Start Main Area =================-->
    <div id="sm-navigation" class="md-sm-only">
        <div class="container">
            <div id="responsive-nav" class="p-4">
                <div class="profile-area">
                    <div class="profile-pic mb-2">
                        <img src="<?php echo($myprofilePic); ?>" style="width: 50px;"></img>
                    </div>
                    <div class="account-info">
                        <p class="mb-0 Uname r-font-tbold"><?php echo($myusername); ?></p>
                        <p class="mb-0 Uhandle">@<?php echo($myhandle); ?></p>
                    </div>
                </div>
                <ul class="sm-nav-ul mt-4">
                    <li><a class="sm-nav-link" href="accountprofile.php?handle=<?php echo($myhandle); ?>">Profile</a></li>
                    <li><a class="sm-nav-link">Bookmarks</a></li>
                    <li><a class="sm-nav-link" href="usersettings.php">Settings and privacy</a></li>
                    <li><a class="sm-nav-link">Help center</a></li>
                    <li><div class="switch-wrap d-flex justify-content-between sm-nav-link px-0">
                        <p>Dark mode</p>
                        <div class="dm-switch">
                            <input type="checkbox" id="default-switch">
                            <label for="default-switch"></label>
                        </div>
                    </div></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="bottom-nav d-none" style="z-index: 99999;">
        <div class="container">
            <div class="w-100">
                <ul class="simplenav navbar">
                    <li><a class="iconic-menu-button" href=""><i class="fa fa-home"></i></a></li>
                    <li><a class="iconic-menu-button" href=""><i class="fa fa-search"></i></a></li>
                    <li><a class="iconic-menu-button" href=""><i class="fa fa-plus"></i></a></li>
                    <li><a class="iconic-menu-button" href=""><i class="fa fa-bell"></i></a></li>
                    <li><a class="iconic-menu-button profile-pic p-0" href=""><img src="./img/people/person_8.jpg"></a></li>
                    
                </ul>
            </div>  
        </div>
    </div>
	<body class="site-main mt">

        <section class="feed-area body p-rel">
            <div class="container">
                <div class="row sm-no-row mx-0">
                    <!--left area-->
                    <div class="col-lg-3 left-profile-area feed">
                        <div class="profile sticky-container">
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
                                            <li class="nav-item d-block active">
                                                <a href="#" class="nav-link d-flex flex-start" style="padding: .5rem .9rem; -webkit-text-fill-color: #555 !important;">
                                                    <span class="iconify" style="font-size: 22px;margin-right: 12px;" data-icon="octicon:home-24" data-inline="false"></span>
                                                    <span>Home</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block">
                                                <a href="explore.php" class="nav-link d-flex flex-start" style="padding: .5rem .9rem;">
                                                    <ion-icon style="font-size: 22px; margin-right: 12px;" name="compass-outline"></ion-icon>
                                                    <span>Explore</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block">
                                                <a href="#" class="nav-link d-flex flex-start">
                                                    <i class="ti-search v-align mr-3"></i>
                                                    <span>Discover</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block">
                                                <a href="messenger.php" class="nav-link c-font-med d-flex flex-start">
                                                    <i class="fa fa-paper-plane v-align mr-3"></i>
                                                    <span>Messenger</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block">
                                                <a href="usersettings.php" class="nav-link  d-flex flex-start">
                                                    <i class="ti-settings v-align mr-3"></i>
                                                    <span>Settings</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block top-divider">
                                                <a href="#" data-toggle="modal" data-target="#logout" class="nav-link d-flex flex-start">
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
                    <div class="col-lg-9 px-0">
                        
                        <div class="mca row sm-no-row mx-0">
                            
                        </div>
                    </div>                    
                </div>
            </div>
            
        </section>
        
	</body>
	<!--================ End Main Area =================-->
	
    <!-- Push Js -->
    <script type="text/javascript" src="push.js"></script>

	<!-- Jquery js file -->
	<script src="./js/jquery-3.2.1.min.js"></script>
	
	<!-- bootstrap js -->
	<script src="./js/bootstrap.min.js"></script>

	<!--OwlCarousel Script-->
	<script src="./vendors/owl-carousel/owl.carousel.min.js"></script>

    <!-- FANCY BOX -->
    <script src="js/jquery.fancybox.min.js"></script>

    <script src="https://code.iconify.design/1/1.0.7/iconify.min.js"></script>

	<!-- Icons-JS -->
	<script src="./js/main.js"></script>

    <!-- Main-JS -->
   
    <script type="text/javascript">
       
        $(document).ready(function() { 
            
            
            
            
        });
    </script>
</body>
</html>
