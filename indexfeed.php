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
	<title>Grapevine</title>
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
                                                <a href="#" class="nav-link d-flex c-font-med flex-start" style="padding: .5rem .9rem; -webkit-text-fill-color: #555 !important;">
                                                    <span class="iconify" style="font-size: 22px;margin-right: 12px;" data-icon="octicon:home-fill-24" data-inline="false"></span>
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
                                                <a href="messenger.php" class="nav-link d-flex flex-start">
                                                    <i class="far fa-paper-plane v-align mr-3"></i>
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
                        <div class="stories-area rounded-container carousel">
                            <div class="area-heading flex-start">
                                <h5 class="ra-heading mb-3 text-primary-grey lg-only">Stories</h5>
                                <div class="ml-auto section-left-button lg-only flex-start">
                                    <a href="#" class="watch-all-btn">
                                        <div class="iconic-button mr-2">
                                            <i class="fa fa-play"></i>
                                        </div>
                                        <a class="para mb-0 ra-heading text-primary-grey">Watch All</a>
                                    </a>
                                </div>                                    
                            </div>
                            <div class="stories owl-carousel owl-theme row">
                                <div class="item">
                                    <div class="single-story">
                                        <div class="s-content" style="margin-left: 5px;">
                                            <div class="s-new">
                                                <a href="#" class="s-new-item-icon" style="background:url('<?php echo($myprofilePic) ?>'); background-size: cover !important;">
                                                    <i class="ti-plus"></i>
                                                </a>
                                            </div>
                                            <div class="s-title"><p class="para text-size-15">New</p></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    $sCreators = DB::query('SELECT DISTINCT stories.user_id FROM stories, users, followers WHERE stories.user_id = followers.user_id AND followers.follower_id = :myid', array(':myid'=>$userid));
                                    #this will output the IDs of people you follow

                                    foreach ($sCreators as $sids) { ##### MEANING FOR EACH PERSON DO THIS....
                                            $subCC = $sids['user_id'];

                                            ### STARTING DIVS
                                            echo '<div class="item"><div class="single-story"><div class="s-content">';

                                            $singleSPERSON = DB::query('SELECT user_id, content FROM stories WHERE user_id = :hisid', array(':hisid'=>$subCC));
                                            
                                            $a = 1;
                                            foreach ($singleSPERSON as $ssp) {

                                                    $hisname = DB::query('SELECT handle FROM users WHERE users.id = :hisid', array(':hisid'=>$ssp['user_id']))[0]['handle'];
                                                    $hisDP = DB::query('SELECT profileimg FROM users WHERE id = :hisid', array(':hisid'=>$ssp['user_id']))[0]['profileimg'];

                                                    echo '<a href="'.$ssp['content'].'" data-fancybox="'.$ssp['user_id'].'" class="s-image numb'.$a++.'" style="display:none"><img src="'.$hisDP.'" alt="" srcset=""></a>';
                                            }

                                            ### CLOSING DIVS
                                            echo '<div class="s-title"><p class="para">'.$hisname.'</p></div></div></div></div>';
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="mca row sm-no-row mx-0">
                            <div class="col-lg-7 px-0">
                                                    
                                <div class="sticky-container add-post-section lg-only mt-4 rounded-container">
                                    <a href="#" data-toggle="modal" data-target="#postCreateForm" class="add-post-area flex-start">
                                        <div class="circular-plus-icon">
                                            <span class="ti-plus"></span>
                                        </div>
                                        <h5 class="lc-section-heading v-align ml-10 mb-0 text-primary-grey">Post Something Fresh!</h5>
                                    </a>
                                </div>
                                <div class="timelineposts" style="margin-bottom: 4rem;">
                                    <?php
                                        $followingposts = DB::query('SELECT DISTINCT posts.*, users.`username` FROM users, posts, followers
                                            WHERE (posts.user_id = followers.user_id
                                            OR posts.user_id = :userid)
                                            AND users.id = posts.user_id
                                            AND follower_id = :userid
                                            ORDER BY posts.posted_at DESC;', array(':userid'=>$userid, ':userid'=>$userid));

                                        if (empty($followingposts)) {
                                            echo '<div class="err-nun-found mt-4" style="width:100%;">
                                                    <img src="./img/default/undraw_snap_the_moment_oyn6.svg" style="width: 400px;" class="m-lr-auto d-block">
                                                </div>';
                                        }
                                    ?>
                                </div>
                            </div>

                            <!--- POST SETTINGS MODAL --->

                            <div class="modal fade" id="postSettings" role="dialog" tabindex="-1" style="padding-top: 180px;">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                        <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                            <h4 class="modal-title">Post Options</h4>
                                        </div>
                                        <div class="modal-body dg-text modal-border-split">
                                                <p class="mb-0">Hide This Post</p>
                                        </div>
                                        <div class="modal-body dg-text modal-border-split">
                                                <p class="mb-0">See Fewer Posts Like This</p>
                                        </div>
                                        <div class="modal-body dg-text">
                                                <p class="mb-0">Share post</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-default" type="button" data-dismiss="modal"></button>
                                        </div>                                                    
                                    </div>
                                </div>
                            </div>

                            <!--- POST SETTINGS MODAL --->

                            <div class="modal fade" id="logout" role="dialog" tabindex="-1" style="padding-top: 180px;">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                        <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                            <h4 class="modal-title para">logging out?</h4>
                                        </div>
                                        <form class="modal-body dg-text p-0" action="indexfeed.php" method="post">
                                                <button type="submit" name="logout" style="color: Blue !important; background: transparent;border: none !important;" class="r-font btn-approve w-100 p-3">Yes, Continue</button>
                                        </form>
                                        <div class="modal-footer justify-content-center p-0">
                                            <button class="btn btn-default p-3 w-100 r-font btn-cancel" type="button" data-dismiss="modal" style="color: Red !important;">Cancel</button>
                                        </div>                                                    
                                    </div>
                                </div>
                            </div>
                            <!--- SHARE-POST MODAL --->
                            <div class="modal fade" id="postShare" role="dialog" tabindex="-1" style="padding-top: 30px;"></div>
                            <!--- UPCLOSE-POST MODAL --->

                            <div class="modal fade" id="upclosePost" role="dialog" tabindex="-1" style="padding-top: 200px;">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                        <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                            <h4 class="modal-title">Post Options</h4>
                                        </div>
                                        <div class="modal-body dg-text modal-border-split">
                                                <p class="mb-0">Hide This Post</p>
                                        </div>
                                        <div class="modal-body dg-text modal-border-split">
                                                <p class="mb-0">See Fewer Posts Like This</p>
                                        </div>
                                        <div class="modal-body dg-text">
                                                <p class="mb-0">Share post</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-default" type="button" data-dismiss="modal"></button>
                                        </div>                                                    
                                    </div>
                                </div>
                            </div>
                            <!--- CREATE POST MODAL --->
                            <div class="modal fade" id="postCreateForm" role="dialog" tabindex="-1" style="padding-top: 30px;">
                                <form action='indexfeed.php' method="post" enctype="multipart/form-data" class="modal-dialog" role="document" style="max-width: 600px;">
                                    <div class="modal-content c-font-light" style="border-radius: 1.5rem;">
                                        <div class="modal-header p-b-10">
                                            <button class="btn btn-default py-0" type="button" data-dismiss="modal"><i class="ti-close"></i></button>
                                        </div>
                                        <div class="mydp-section flex-start p-t-15 p-b-15 px-3">
                                            <div class="dp-area">
                                                <img class="dp" style="width: 50px;height: 50px;" src="<?php echo($myprofilePic); ?>">
                                            </div>
                                            <div class="input-area ml-3 m-t-11 w-100">
                                                <textarea name="postbody" placeholder="What's Up?" class="w-100 postCaptionEntry fs-18" style="border: none;min-height: 2rem; max-height: 10rem;"></textarea>
                                                <div class="postimagery p-rel">
                                                    <div id="img-preview" class="modal-border-split pb-3">
                                                    </div>
                                                    <div class="remove-img-btn d-none" id="removeButton">
                                                        <span class="ti-close"></span>
                                                    </div>
                                                </div>                                                    

                                                    <!--<div class="modal-body dg-text justify-content-center modal-border-split" style="padding-top: 2rem;">
                                                        <textarea name="postbody" placeholder="Caption" class="caption-space text-center" style="border: none; width: 100%;"></textarea>
                                                    </div>
                                                    <div class="modal-body dg-text justify-content-center">
                                                        <button class="btn btn-default" name="post" type="submit">Post</button>
                                                    </div>-->
                                                
                                                <div class="action-seaction flex-start pt-3">
                                                    <div class="post-nav v-align g-text c-font">
                                                        <input type="file" name="postimg" accept="image/*" id="choose-postimg" class="p-abs d-none">
                                                        <label for="choose-postimg" class="fi-btn">
                                                            <ion-icon name="image-outline" class="ion-15"></ion-icon>
                                                        </label>
                                                        <a class="fi-btn ml-3"><ion-icon class="ion-15" name="videocam-outline"></ion-icon></a>
                                                        <a class="fi-btn ml-3"><ion-icon class="ion-15" name="happy-outline"></ion-icon></a>
                                                        <a class="fi-btn ml-3"><ion-icon class="ion-15" name="pricetags-outline"></ion-icon></a>
                                                        
                                                    </div>
                                                    <div class="post-btn ml-auto">
                                                        <button name="post" type="submit" class="circular-plus-icon border-none grad-btn"><span class="ti-arrow-right"></span></button>
                                                    </div>
                                                </div>
                                            </div>                                           
                                        </div>
                                        
                                    </div>
                                </form>
                            </div>
                            <!--- CREATE POST MODAL --->
                            <div class="modal fade" id="postCreateFormE" role="dialog" tabindex="-1" style="padding-top: 30px;">
                                <form action='indexfeed.php' method="post" enctype="multipart/form-data" class="modal-dialog" role="document" style="max-width: 600px;">
                                    <div class="modal-content c-font-light" style="border-radius: 1.5rem;">
                                        <div class="modal-header p-b-10">
                                            <button class="btn btn-default py-0" type="button" data-dismiss="modal"><i class="ti-close"></i></button>
                                        </div>

                                        <div class="modal-body dg-text justify-content-center modal-border-split" style="padding-top: 2rem;">
                                            <textarea name="postbody" placeholder="Caption" class="caption-space text-center" style="border: none; width: 100%;"></textarea>
                                        </div>
                                        <div class="modal-body justify-content-center modal-border-split">
                                            <input type="file" name="postimg">
                                        </div>
                                        <div class="modal-body dg-text justify-content-center">
                                            <button class="btn btn-default" name="post" type="submit">Post</button>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="post-btn ml-auto">
                                                <button name="post" type="submit" class="circular-plus-icon border-none grad-btn"><span class="ti-arrow-right"></span></button>
                                            </div>
                                        </div>                                            
                                        
                                    </div>
                                </form>
                            </div>

                            <div class="col-lg-5 lg-only">
                                <div class="sticky-container left-pp-area">
                                    <div class="trending-tags-section rounded-container mt-4 px-0">
                                        <h6 class="lc-section-heading grey mb-3 py-2 px-4">Trending Hashtags</h6>

                                        <?php

                                            foreach ($trendingTag as $tt) {
                                                $divs = '<div class="single-section-item  px-4 flex-start">
                                                <div class="icon">
                                                    <i class="fa fa-hashtag hashtag-icon"></i>
                                                </div>
                                                <a href="hashtag.php?hashtag='; ///LINK
                                                $two = '" class="liked-page-name w-100 v-align mb-0 ml-10">';
                                                $three = '</a></div>';

                                                echo $divs.$tt['topic'].$two.$tt['topic'].$three;
                                            }
                                        ?>
                                    </div>
                                    <div class="to-follow-section px-0 rounded-container mt-4">
                                        <h6 class="lc-section-heading grey py-2 px-4 mb-3">People to Follow</h6>
                                    <?php 
                                        $peopleToFollow = DB::query('SELECT DISTINCT users.username,users.profileimg, users.handle FROM users, followers WHERE followers.follower_id != :userid AND user_id != :userid AND users.id = followers.user_id ORDER BY RAND() LIMIT 3', array(':userid'=>$userid));



                                        foreach ($peopleToFollow as $ptf) {

                                            $FstLine = '<div class="single-section-item px-4 flex-start">';
                                            $SndLine = '<div class="user-profile-image">';
                                            $TrdLine = '<a href="#"><img style="height: 40px;" src=';
                                            
                                            $Tclose = '></a>';
                                            $FthLine = '</div><a href="';
                                            
                                            $profileLink = 'accountprofile.php?handle=';

                                            $FTopen = '" class="v-align w-100 single-item-name ml-10">';
                                            $FTclose = '</a></div>';

                                            $profilePic = DB::query('SELECT profileimg FROM users WHERE username=:username', array(':username'=>$ptf['username']))[0]['profileimg'];
                                            if (is_null($profilePic)) {
                                                $dp = "./img/default/undraw_profile_pic_ic5t.png";
                                            }else {
                                                $dp = $ptf['profileimg'];
                                            }
                                            echo $FstLine.$SndLine.$TrdLine.$dp.$Tclose.$FthLine.$profileLink.$ptf['handle'].$FTopen.$ptf['username'].$FTclose;
                                        }
                                    ?>
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
            <div class="activity-tab fade" style="display: none;">
                <div class="activity rounded-container dark-shadow mt-0">
                    <h5 class="ra-heading mb-2 pb-2" style="color: #000;">Activity</h5>
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
                                    $Second = '"></div><div class="v-align notify-body flex-start"><p class="para text-primary-grey pr-4 mb-0"><a class="c-font-med text-dark-grey mr-1" href="accountprofile.php?handle='.$sHandle.'">';///NAME///
                                    $Third = '</a>';//// NOTIFICATION
                                    $Fourth = '</p></div><div class="call-to ml-auto"><a href="#"><img src="';/////Post Link Img
                                    $Fifth = '" style="height: 40px;"></a></div></div>';

                                    if (!DB::query('SELECT * FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$n['sender'], ':followerid'=>$userid))) {
                                        $typeThree = '</a></div><div class="call-to ml-auto"><form action="activity-page.php?username='.$sName.'" method="post"><button class="btn sub-follow-btn" type="button"><i class="icon-user-plus"></i></button></form></div></div>';
                                    }else {
                                        $typeThree = '</a></div><div class="call-to ml-auto"><form action="activity-page.php?username='.$sName.'" method="post"><button class="btn sub-following-btn" type="submit"><i class="icon-user"></i></button></form></div></div>';
                                    }
                                    

                                    $posted_hour = DB::query('SELECT timestampdiff(HOUR, `time`, now() ) as hours_since FROM notifications WHERE id=:id', array(':id'=>$n['id']))[0]['hours_since'];
                                    $posted_minute = DB::query('SELECT timestampdiff(MINUTE, `time`, now() ) as minutes_since FROM notifications WHERE id=:id', array(':id'=>$n['id']))[0]['minutes_since'];
                                    $roundDay = 24;
                                    $posted_day = round($posted_hour/$roundDay);

                                    if ($posted_hour < 1) {
                                        $timeNumber = $posted_minute;
                                        $when = "min";
                                        }else if ($posted_hour === 1) {
                                                $timeNumber = $posted_hour;
                                                $when = "hr";
                                        }else if ($posted_hour > 24) {
                                                $timeNumber = $posted_day;
                                                $when = "dys";
                                        }else {
                                            $timeNumber = $posted_hour;
                                            $when = "hrs";
                                    }

                                    $since = "$timeNumber $when";

                                    if ($n['type'] == 1) {

                                            if ($n['extra'] == "") {
                                                    echo "New Notification";

                                            }else {
                                                    $extra = json_decode($n['extra']);
                                                    $commented = $extra->postbody;

                                                    if (strlen($commented)>35){
                                                        $commented=substr($commented,0,30).'...';
                                                    }
                                                    echo $Firss.$sDp.$Second.$sName.$Third.'  mentioned you in a post <br><span style="font-family:ProductSans-Ital !important;">"'.$commented.'"</span>'.'<br/><span class="not-timestamp">'.$since."</span>".$Fourth.$Fifth.'';
                                                    ///$notify .= $Firss.$sDp.$Second.$sName.$Third.'  mentioned you in a post'.$Fourth.$Fifth."<br/> - ".$extra->postbody.'';
                                            }
                                    #Mention
                                    }elseif ($n['type'] == 2) {
                                            $postlinkid = $n['post_id'];
                                            $postlinkimg = DB::query('SELECT postimg FROM posts WHERE id=:plid', array(':plid'=>$postlinkid))[0]['postimg'];
                                            echo($Firss.$sDp.$Second.$sName.$Third.' liked your post!'.'<br/><span class="not-timestamp">'.$since."</span>".$Fourth.$postlinkimg.$Fifth);
                                            ///$notify .= $sName.' liked your post!';
                                    #Like
                                    }elseif ($n['type'] == 3) {
                                            echo($Firss.$sDp.$Second.$sName.$Third.' just started following you!'.'<br/><span class="not-timestamp">'.$since."</span>".$typeThree);
                                    #Follow
                                    }elseif ($n['type'] == 4) {
                                            echo($Firss.$sDp.$Second.$sName.$Third.' commented on your post!'.'<br/><span class="not-timestamp">'.$since."</span>".$typeThree);
                                    #Comment
                                    }elseif ($n['type'] == 5) {
                                            $uid = DB::query('SELECT user_id FROM posts WHERE id = :pid', array(':pid'=>$n['post_id']))[0]['user_id'];
                                            $who = DB::query('SELECT username FROM users WHERE id = :uid', array(':uid'=>$uid))[0]['username'];
                                            echo($Firss.$sDp.$Second.$sName.$Third.' liked your comment on '.$who."'s post!".'<br/><span class="not-timestamp">'.$since."</span>".$typeThree);
                                    }elseif ($n['type'] == 6) {
                                            $uid = DB::query('SELECT user_id FROM posts WHERE id = :pid', array(':pid'=>$n['post_id']))[0]['user_id'];
                                            $who = DB::query('SELECT username FROM users WHERE id = :uid', array(':uid'=>$uid))[0]['username'];
                                            echo($Firss.$sDp.$Second.$sName.$Third.' mention you in a comment on '.$who."'s post!".'<br/><span class="not-timestamp">'.$since."</span>".$typeThree);
                                    }
                            }
                        } 
                    ?>
                    <div class="single-notify-item flex-start mt-3">
                        <div class="s-dp margin-r-15">
                            <img src="./img/people/person_8.jpg">
                        </div>
                        <div class="v-align notify-body flex-start">
                            <a class="c-font-med text-black mr-1" href="#"><!-- SENDER NAME -->Jason Trae</a><a class="c-font-light text-black" href="#">started following you</a>
                        </div>
                        <div class="call-to ml-auto">
                            <!-- LIKED IMAGE OR FOLLOW BUTTON IF FOLLOWED -->
                            <a href="#"><button class="btn sub-follow-btn" type="button"><i class="icon-user-plus"></i></button></a>
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
    <script src="apiget.js"></script>
    <!--<script type="text/javascript">
        $(document).ready(function() {
                $.ajax({

                    type: "GET",
                    url: "api/feedposts",
                    processData : false,
                    contentType: "application/json",
                    data: '',
                    success: function(r) {
                        var res = r
                        var text = res.replace(/'/g, "");
                        var text  = text.replace( /[\r\n]+/gm, "" );
                        var posts = JSON.parse(text);
                        
                        $.each(posts, function(index) {
                                var caption = posts[index].PostBody;
                                var caption = caption.replace(/2>j/g, '"');
                                var caption = caption.replace(/3>j/g, "'");

                                var ptc = posts[index].ptc;
                                if (ptc == "share") {
                                    $('.timelineposts').html(
                                        $('.timelineposts').html() + '<blockquote><div class="post"><div class="postsharedby mb-1 flex-start" style="border-bottom: 1px solid #e5e5e5;"><p class="pt-3 pb-2 px-4 r-font-med mb-0" style="color: #9b9b9b;"><span class="mr-2"><i class="fa fa-paper-plane"></i></span>'+posts[index].sharedby+' shared</p></div><div class="post-head flex-start"><a href="accountprofile.php?handle='+posts[index].HisHandle+'" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5></div></a><a href="#" class="post-settings-menu" data-postid="'+posts[index].PostId+'" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image mainPost"><div class="post-text r-font-med cursor-default" style="height:100%;display:table;"><p id="post-caption" class="post-caption text-black niCap"><span id="account-name">@'+posts[index].HisHandle+'</span>'+caption+'</p></div></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+' likeButton animated" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="#" data-toggle="modal" data-target="#postShare" data-postid="'+posts[index].PostId+'" class="'+posts[index].Sicon+' single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-time r-font-med"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
                                    );
                                }else if (ptc == "wordpost") {
                                    $('.timelineposts').html(
                                            $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="accountprofile.php?handle='+posts[index].HisHandle+'" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image mainPost"><div class="post-text r-font-med cursor-default" style="height:100%;display:table;"><p id="post-caption" class="post-caption text-black niCap"><span id="account-name">@'+posts[index].HisHandle+'</span>'+caption+'</p></div></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+' likeButton animated" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="#" data-toggle="modal" data-target="#postShare" data-postid="'+posts[index].PostId+'" class="'+posts[index].Sicon+' single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-time r-font-med"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
                                    );
                                }else {
                                    $('.timelineposts').html(
                                        $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="accountprofile.php?handle='+posts[index].HisHandle+'" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location r-font mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image"><img id="post-image" src="'+posts[index].PostImg+'"></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="#" data-toggle="modal" data-target="#postShare" data-postid="'+posts[index].PostId+'" class="'+posts[index].Sicon+' single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-text c-font-alt cursor-default"><p id="post-caption" class="post-caption r-font text-black"><span id="account-name" class="text-black">'+posts[index].PostedBy+'</span>'+caption+'</p></div><div class="post-time r-font-med"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
                                    );
                                };
                                

                                $('[data-id]').click(function() {
                                    var buttonid = $(this).attr('data-id');

                                    $.ajax({

                                        type: "POST",
                                        url: "api/likes?id=" + $(this).attr('data-id'),
                                        processData : false,
                                        contentType: "application/json",
                                        data: '', 
                                        success: function(r) {
                                            act = r
                                            var res = JSON.parse(r);

                                            $("[data-id='t"+buttonid+"']").html('<p id="reactions" class="likes mb-0 red">'+res.Likes+' likes</p>');

                                            $("[data-id='"+buttonid+"']").toggleClass('active');

                                            var timeoutHandler = null;
                                            $("[data-id='"+buttonid+"']").addClass('tada');

                                            if (timeoutHandler) clearTimeout(timeoutHandler);

                                            timeoutHandler = setTimeout(function() {
                                                $("[data-id='"+buttonid+"']").removeClass('tada');
                                            }, 1000);

                                            console.log(r)
                                        },
                                        error: function(r) {
                                            console.log(r)
                                        }

                                    }) 

                                });

                                $('[data-postid]').click(function() {
                                    var postid = $(this).attr('data-postid');

                                    $('#postShare').html(
                                            $('#postShare').html() + '<div class="modal-dialog" role="document"><div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;"><div class="modal-header justify-content-center" style="padding: 2rem 1rem"><h4 class="modal-title">Share Post</h4></div><a href="createpost.php?type=snq&postid='+postid+'" class="modal-body dg-text modal-border-split"><h5 class="my-2 c-font">Share to your followers</h5><p class="description">The post will be seen by your followers and it will appear in your profile page</p></a><a href="#" class="modal-body dg-text modal-border-split"><h5 class="my-2 c-font">Share as Quote</h5><p class="description">Quote this post and say something about it.</p></a><a href="#" class="modal-body dg-text"><h5 class="my-2 c-font">Share as link</h5><p class="description">Share this post to other social media platforms.</p></a><div class="modal-footer"><button class="btn btn-default" type="button" data-dismiss="modal"></button></div></div></div>'
                                    );
                                    $('[data-dismiss]').click(function() {
                                        var postid = " ";
                                    });
                                });
                        })
                    },
                    error: function(r) {
                        console.log(r)
                    }

                });
        });        
    </script>-->
    <script type="text/javascript">
        $('.notify-btn').click(function(){
            Push.create("New Follower!", {
                body: "Kevin started following you",
                icon: './img/core-img/gv-icon.png',
                timeout: 8000,
                onClick: function () {
                    window.focus();
                    this.close();
                }
            });
        });

        var autoExpand = function (field) {

            // Reset field height
            field.style.height = 'inherit';

            // Get the computed styles for the element
            var computed = window.getComputedStyle(field);

            // Calculate the height
            var height = parseInt(computed.getPropertyValue('border-top-width'), 10)
                         + parseInt(computed.getPropertyValue('padding-top'), 10)
                         + field.scrollHeight
                         + parseInt(computed.getPropertyValue('padding-bottom'), 10)
                         + parseInt(computed.getPropertyValue('border-bottom-width'), 10);

            field.style.height = height + 'px';

        };

        document.addEventListener('input', function (event) {
            if (event.target.tagName.toLowerCase() !== 'textarea') return;
            autoExpand(event.target);
        }, false);

        $(document).ready(function() { 
            $('#act-btn').click(function() {
                $('.activity-tab').toggleClass('d-block');
                $('#act-btn').toggleClass('onn');
                $('.fa-bell').toggleClass('fw-6 text-white');
                $('.activity-tab').toggleClass('show');
            })
        })
    </script>
</body>
</html>
