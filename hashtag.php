<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Comment.php');
include('./classes/Image.php');
include('./classes/Notify.php');


if (Login::isLoggedIn()) {
        $myuserid = Login::isLoggedIn();
} else {
        die('Not logged in');
}

$react = False;

$username = "";
$isFollowing = False;
$liked = False;

if (isset($_GET['hashtag'])) {
    if (DB::query('SELECT topics FROM posts WHERE FIND_IN_SET(:topic, topics)', array(':topic'=>$_GET['hashtag']))) {
        $hashtagTPC = DB::query('SELECT topics FROM posts WHERE FIND_IN_SET(:topic, topics)', array(':topic'=>$_GET['hashtag']))[0]['topics'];

        $username = $_GET['hashtag'];

        $followerid = Login::isLoggedIn();
        $creatorId = DB::query('SELECT user_id FROM `posts` WHERE FIND_IN_SET(:topic, topics) ORDER BY id ASC LIMIT 1', array(':topic'=>$_GET['hashtag']))[0]['user_id'];
        $tagCreator = DB::query('SELECT username FROM users WHERE id = :creatorid', array(':creatorid'=>$creatorId))[0]['username'];
        $tagCreatorHandle = DB::query('SELECT handle FROM users WHERE id = :creatorid', array(':creatorid'=>$creatorId))[0]['handle'];

        $tagMostlikes = DB::query('SELECT user_id FROM `posts` WHERE FIND_IN_SET(:topic, topics) ORDER BY likes DESC LIMIT 1', array(':topic'=>$_GET['hashtag']))[0]['user_id'];
        $tagMostlikesBy =  DB::query('SELECT handle FROM users WHERE id = :creatorid', array(':creatorid'=>$tagMostlikes))[0]['handle'];


        
        $nPosts = DB::query('SELECT COUNT(*) FROM posts WHERE FIND_IN_SET(:topic, topics)', array(':topic'=>$_GET['hashtag']))[0]['COUNT(*)'];
        $nFollowers =  3;#DB::query('SELECT COUNT(*) FROM followers WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
        $nFollowing = 2;#DB::query('SELECT COUNT(*) FROM followers WHERE follower_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];

        /*
        $listFollowers = DB::query('SELECT DISTINCT users.* FROM followers, users WHERE followers.user_id = :userid AND users.id = follower_id AND follower_id != 6', array(':userid'=>$userid));
        $listFollowing = DB::query('SELECT DISTINCT users.* FROM followers, users WHERE users.id = followers.user_id AND follower_id = :userid', array(':userid'=>$userid));
        $list = "";*/


        # $userid is ID of USER in Page
        # $followerid is YOUR ID as the loggedIn User

        if (isset($_POST['follow'])) {  

            if ($userid != $followerid) {
                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                    if ($followerid == 6) {
                        DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                    }
                    DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));

                    $s = DB::query('SELECT username FROM users WHERE id=:fid', array(':fid'=>$followerid))[0]['username']; #the one who is following you
                    DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>3, ':receiver'=>$userid, ':sender'=>$followerid, ':extra'=>"", ':postid'=>""));

                    ++$nFollowers;
                } else {
                    echo "Already Following!";
                }
                $isFollowing = True;
            }               
        }

        if (isset($_POST['uploadprofileimg'])) {
            Image::uploadImage('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid'=>$userid));
        }


        if (isset($_POST['unfollow'])) {
            if ($userid != $followerid) {
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                    if ($followerid == 6) {
                        DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
                    }
                    DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
                    --$nFollowers;
                }
                $isFollowing = False;
            }               
        }

        /*
        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
            //echo "Already Following!";
            $isFollowing = True;
        }*/

        if (isset($_POST['post'])) {
            $postbody = $_POST['postbody'];
            $loggedInUserId = Login::isLoggedIn();

            if (strlen($postbody) > 160 || strlen($postbody) < 1) {
                die('Incorrect Length');
            }

            if ($loggedInUserId == $userid) {
                DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', \'\')', array(':postbody'=>$postbody, ':userid'=>$userid));
            }else {
                die('Incorrect User!');
            }
            
        }

        if (isset($_GET['postid'])) {
            if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
                DB::query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));

            }else {
                DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
            }
        }
        $loggedInUserImg = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>$followerid))[0]['profileimg'];
        $defaultimg = './img/default/undraw_profile_pic_ic5t.png';
        $meimage = "";

        if (empty($loggedInUserImg)) {
            $myprofilePic = $defaultimg;
        }else {
            $myprofilePic = $loggedInUserImg;
        }

        if (isset($_GET['hashtag'])) {
            $meimage = "./img/people/imagery.jpg";
        }        

        $dbposts = DB::query('SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics) ORDER BY id DESC', array(':topic'=>$_GET['hashtag']));
        
        $posts = "";
        $photos = "";

        //// Posts Variables //////

        $head_forPost = '<div class="post" style="width: 530px; padding-bottom:1rem;">
                                    <div class="post-head flex-start">
                                        <a href="#" class="post-compiler">
                                            <div class="profile-pic story-active">
                                                <img id="profile-picture" style="height: 40px;" src=';
        $image_after =                          '>
                                            </div>
                                            <div class="post-by">
                                                <h5 class="post-by-title">';
        $secondaryHead_forPost =                '</h5>
                                                <p class="location mb-0">Somewhere On Earth</p>
                                            </div>
                                        </a>
                                        <a href="#" data-toggle="modal" data-target="#postSettings" class="post-settings-menu">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>
                                    </div>';
        $postimg =                  '<div class="post-image"><img id="post-image" src=';
        $postimg_aft =              '></div>';
                            
        $reactionArea_close = ' likes</p></div></div>';
        
        $posttext = '<div class="post-text" style="font-family: Poppins-Light;"><p class="post-caption"><span class="account-name">';
        $posttext_close = '</p></div>';
        
        $timestamp_forPost = '<div class="post-time mt-1"><p class="mb-0">';
        //// -------------------------------////

        foreach($dbposts as $p) {

            $reactionArea_forPost = '<a href="comment-page.php?postid='.$p['id'].'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a>
                                <a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a>
                                <a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a>
                            </form>
                        <div class="likes-section mt-2">
                            <p id="reactions" class="likes mb-0 red">';
            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$followerid))) {
                $likeButtonRed = '<div class="post-tags mt-4">
                                                        <form name="reactform" class="reaction-area" id="react" action="profile.php?username=$username&postid='.$p['id'].'" method="post">
                                                            <a href="javascript: submitform()" class="single-reaction"><i class="reaction-icon-size far fa-heart"></i></a>';
                
            }else {
                $likeButtonRed = '<div class="post-tags mt-4">
                                                        <form name="reactform" class="reaction-area" id="react" action="profile.php?username=$username&postid='.$p['id'].'" method="post">
                                                            <a href="javascript: submitform()" class="single-reaction active"><i class="reaction-icon-size far fa-heart"></i></a>';
            }
            

            $posted_hour = DB::query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM posts WHERE id=:postid', array(':postid'=>$p['id']))[0]['hours_since'];
            $posted_minute = DB::query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM posts WHERE id=:postid', array(':postid'=>$p['id']))[0]['minutes_since'];
            $roundDay = 24;
            $posted_day = round($posted_hour/$roundDay);

            if ($posted_hour < 1) {
                    $timeNumber = $posted_minute;
                    $when = " Min Ago";
            }else if ($posted_hour == 1) {
                    $timeNumber = $posted_hour;
                    $when = " Hour Ago";
            }else if ($posted_hour > 24) {
                    $timeNumber = $posted_day;
                    $when = " Days Ago";
            }else {
                $timeNumber = $posted_hour;
                $when = " Hours Ago";
            }

            $since = "$timeNumber$when";
            $DELpost = "";

            $hisImage = DB::query('SELECT profileimg FROM users WHERE id = :userid', array(':userid'=>$p['user_id']))[0]['profileimg'];
            if (empty($hisImage)) {
                $authorDP = $defaultimg;
            }else {
                $authorDP = $hisImage;
            }

            $userSname = DB::query('SELECT username FROM users WHERE id = :userid', array(':userid'=>$p['user_id']))[0]['username'];

            $posts .= $head_forPost.$authorDP.$image_after.$userSname.$secondaryHead_forPost.$postimg.$p['postimg'].$postimg_aft.$likeButtonRed.$reactionArea_forPost.$p['likes'].$reactionArea_close.$posttext.$userSname."</span>".htmlspecialchars($p['body']).$posttext_close.$timestamp_forPost.$since."</p></div></div>";
        }

    }else {
        die("User Not Found");
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
    <link rel="stylesheet" href="./scss/style.css">
    <link rel="stylesheet" href="./css/style.css">
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
                            <img src='<?php echo($myprofilePic); ?>' alt="dp" srcset="" style="height: 30px;">
                        </div>                                      
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!--================ End Header Area =================-->

    <!--================ Start Main Area =================-->
    <main class="site-main">
        <section class="profile-area body">           
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 left-profile-area">
                        <div class="profile fixed">
                            <div class="profile-info left-nav left">
                                <div class="floating-image">
                                    <a style="border-radius: 50%;
                                            height: 150px;
                                            width: 150px;
                                            background: linear-gradient(90deg, #c77efa, #854fee 100%) !important;
                                            display: table;">
                                        <h1 class="text-center text-white" style="display: table-cell;vertical-align: middle;">#</h1>
                                    </a>
                                </div>
                                <div class="floating-edt-i">
                                    <a href="#" class="settings-btn"><i class="ti-settings"></i></a>
                                </div>
                                <div class="info-area" style="margin-top: 5rem;">                                    
                                    <div class="profile-id text-center mb-4">
                                        <h4 class="name d-flex justify-content-center">
                                            #<?php echo $username; ?>
                                            <span class="ml-2" style="color: #7252e9;"></span>
                                        </h4>
                                        <div class="followers-area padding-x-35">
                                            <div class="followers row">
                                                <div class="col-4"><h5 class="ss-heading ra-heading"><?php echo($nPosts); ?><br><span class="ss-content">Posts</span></h5></div>

                                                <div class="col-4">
                                                    <a href="#" data-toggle="modal" data-target="#listFollowers" style="color: initial !important;">
                                                        <h5 class="followers-n ss-heading ra-heading"><?php echo($nFollowers); ?><br><span class="ss-content">Followers</span></h5>   
                                                    </a>                                                    
                                                </div>

                                                <div class="col-4">
                                                    <a href="#" data-toggle="modal" data-target="#listFollowing" style="color: initial !important;">
                                                        <h5 class="following-n ss-heading ra-heading"><?php echo($nFollowing); ?><br><span class="ss-content">Following</span></h5>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <form action="profile-page.php?handle=<?php echo $hashtagTPC; ?>" method="post">
                                            <?php
                                                if ($isFollowing == True) {
                                                    echo '<button class="mt-1 btn button unfollow-btn primary-button gradient-btn" name="unfollow">Unfollow</button>

                                                        <button class="mt-1 ml-1 btn button dm-button gradient-btn">
                                                            <i class="icon-paper-plane-o"></i>
                                                        </button>';
                                                }else {
                                                    echo '<button class="mt-1 btn button follow-btn primary-button gradient-btn" name="follow">Follow</button>

                                                        <button class="mt-1 ml-1 btn button dm-button gradient-btn">
                                                            <i class="icon-paper-plane-o"></i>
                                                        </button>';
                                                }
                                            ?>
                                        </form>
                                    </div>                                    
                                        <div class="bio-section padding-x-35 mt-4">
                                            <h3 class="ra-heading">#<?php echo($username); ?></h3>
                                            <p class="para bio">Most Liked Post is by : @<?php echo($tagMostlikesBy); ?><br />
                                                Most Posted : @Troy<br />
                                                Created By : @<?php echo($tagCreatorHandle); ?>
                                            </p>
                                        </div>
                                </div>                              
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="image-fluid-header">
                                    <a class="image-fluid" style="background: linear-gradient(90deg, #c77efa, #854fee 50%, #6c63ff 100%) !important;display: table;">
                                        <div class="allin c-font-light text-white" style="display: table-cell;vertical-align: middle;">
                                            <h3 class="name d-flex justify-content-center">
                                                #<?php echo $username; ?>
                                                <span class="ml-2" style="color: #7252e9;"></span>
                                            </h3>
                                            <h7 class="handle rs-heading justify-content-center">Creator: <?php echo($tagCreator); ?></h7>
                                        </div>
                                            
                                    </a>
                                </div>
                                <div class="stories-area-alt rounded-container">
                                    <h5 class="ra-heading mb-3">Featured Stories</h5>
                                    <div class="stories owl-carousel owl-theme row">
                                        <div class="single-story d-block ml-20">
                                            <div class="ss-image-body">
                                                <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/about_part_img.png');">
                                                    <div class="ss-ni-content">
                                                        <p class="text-white para mb-0">In My Space</p>
                                                    </div>
                                                </a>                                                    
                                            </div>
                                        </div>
                                        <div class="single-story d-block ml-20">
                                            <div class="ss-image-body">
                                                <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/creative_img.png');">
                                                    <div class="ss-ni-content">
                                                        <p class="text-white para mb-0">Creativity</p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="single-story d-block ml-20">
                                            <div class="ss-image-body">
                                                <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/gallery_item_1.png');">
                                                    <div class="ss-ni-content">
                                                        <p class="text-white para mb-0">All Work No Play</p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="single-story d-block ml-20">
                                            <div class="ss-image-body">
                                                <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/project_1.png');">
                                                    <div class="ss-ni-content">
                                                        <p class="text-white para mb-0">My Project</p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="single-story d-block ml-20">
                                            <div class="ss-image-body">
                                                <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/project_1.png');">
                                                    <div class="ss-ni-content">
                                                        <p class="text-white para mb-0">My Project</p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="single-story d-block ml-20">
                                            <div class="ss-image-body">
                                                <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/project_1.png');">
                                                    <div class="ss-ni-content">
                                                        <p class="text-white para mb-0">My Project</p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="pp-main-section-nav rounded-container mt-4">
                                    <ul class="container-nav m-lr-auto nav nav-tabs">
                                        <li class="nav-item cn-item mr-30p">                                            
                                            <a data-toggle="tab" href="#posts" class="nav-link cn-link d-flex active">
                                                <span class="cn-icon"><i class="ti-layout-media-overlay"></i></span>
                                                <span class="cn-link-aft v-align">Posts</span>
                                            </a>
                                        </li>
                                        <li class="nav-item cn-item">                                            
                                            <a data-toggle="tab" href="#photos" class="nav-link cn-link d-flex">
                                                <span class="cn-icon"><i class="ti-view-grid"></i></span>
                                                <span class="cn-link-aft v-align">Photos</span>
                                            </a>
                                        </li>
                                    </ul>                                        
                                </div>
                                
                                <div class="tab-content main-section">
                                    <div class="posts tab-pane fade row mb-4 active" id="posts">
                                        <div class="col-lg-7">

                                            <div class="posts">
                                                <?php echo $posts; 
                                                    if (empty($posts)) {
                                                        echo "
                                                        <h5 class='c-font-light mb-3 m-lr-auto' style='width:max-content;margin-top:50px;color:#666;'>@".$hashtagTPC." Has No Posts Yet!</h5>
                                                        <div class='Nun-Found justify-content-center mt-4'>
                                                            <img style='max-width: 380px;' src='./img/default/undraw_synchronize_ccxk.svg'>
                                                        </div>";
                                                    }
                                                ?>
                                            </div>
                        
                                        </div>

                                        <div class="col-lg-5">
                                            <div class="sticky-container left-pp-area">
                                                <div class="liked-pages-section rounded-container mt-4">
                                                    <h6 class="lc-section-heading mb-3">Pages that you like</h6>
                                                    <div class="single-liked-page mt-10 flex-start">
                                                        <div class="page-image">
                                                            <a href="#"><img src="./img/people/person_3.jpg" alt="" srcset=""></a>
                                                        </div>
                                                        <a href="#" class="liked-page-name v-align mb-0 ml-10">Football NC</a>
                                                    </div>
                                                    <div class="single-liked-page mt-10 flex-start">
                                                        <div class="page-image">
                                                            <a href="#"><img src="./img/people/person_1.jpg" alt="" srcset=""></a>
                                                        </div>
                                                        <a href="#" class="liked-page-name v-align mb-0 ml-10">Carz.co</a>
                                                    </div>
                                                    <div class="single-liked-page mt-10 flex-start">
                                                        <div class="page-image">
                                                            <a href="#"><img src="./img/people/person_6.jpg" alt="" srcset=""></a>
                                                        </div>
                                                        <a href="#" class="liked-page-name v-align mb-0 ml-10">Desktop Tech</a>
                                                    </div>
                                                </div>
                                                <div class="to-follow-section rounded-container mt-4">
                                                    <h6 class="lc-section-heading mb-3">People to Follow</h6>
                                                    <div class="single-section-item mt-10 flex-start">
                                                        <div class="user-profile-image">
                                                            <a href="#"><img src="./img/people/person_2.jpg" alt="" srcset=""></a>
                                                        </div>
                                                        <a href="" class="v-align single-item-name ml-10">Carlia Johannason</a>                                                    
                                                    </div>
                                                    <div class="single-section-item mt-10 flex-start">
                                                        <div class="user-profile-image">
                                                            <a href="#"><img src="./img/people/person_1.jpg" alt="" srcset=""></a>
                                                        </div>
                                                        <a href="" class="v-align single-item-name ml-10">Marsai Smith</a>                                                    
                                                    </div>
                                                    <div class="single-section-item mt-10 flex-start">
                                                        <div class="user-profile-image">
                                                            <a href="#"><img src="./img/people/person_7.jpg" alt="" srcset=""></a>
                                                        </div>
                                                        <a href="" class="v-align single-item-name ml-10">Karl Johnson</a>                                                    
                                                    </div>
                                                </div>
                                                <div class="sub-section footer mt-30">
                                                    <div class="d-block">
                                                        <p class="seo-link mb-0">Dreams &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved |</p>
                                                        <a href="#" class="seo-link">Report A Problem<span>|</span></a>
                                                        <a href="#" class="seo-link">Privacy<span>|</span></a>
                                                        <a href="#" class="seo-link">Terms & Policies<span>|</span></a>
                                                        <a href="#" class="seo-link">Help</a>
                                                        <div class="seo-link">Icons made by <a href="https://www.flaticon.com/authors/roundicons" title="Roundicons">Roundicons</a> from <a href="https://www.flaticon.com/" title="Flaticon"> www.flaticon.com</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="photos tab-pane fade row mb-4" id="photos">
                                        <div class="image-gallery mt-4">
                                            <div class="images">
                                                <div class="row">
                                                     <?php 
                                                    $postedPictures = DB::query('SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics) AND postimg IS NOT NULL ORDER BY id DESC', array(':topic'=>$_GET['hashtag']));
                                                    $photos = "";

                                                    foreach ($postedPictures as $p) {

                                                        ////        Photo Variables         ////
                                                        $openDivs ='<div class="single-img-item"><div class="item-content"><div class="item-img">';
                                                        $imageDiv = '<a href="comment-page.php?postid='.$p['id'].'" class="img" style="background-image: url(';
                                                        $imageDiv_close = ');"></a>';
                                                        $closingDivs = '</div></div></div>';
                                                        //// -------------------------------////

                                                        if (empty($p['postimg'])) {
                                                            echo "";
                                                        }else {
                                                            echo '<div class="col-md-4">'.$openDivs.$imageDiv.$p['postimg'].$imageDiv_close.$closingDivs.'</div>';
                                                        }            
                                                    }
                                                    ?> 
                                                    <div class="col-md-4">
                                                        <div class="single-img-item">
                                                            <div class="item-img">
                                                                <a href="#" class="img" style="background-image: url('./img/gallery_item_2.png');"></a>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="single-img-item">
                                                            <div class="item-img">
                                                                <a href="#" class="img" style="background-image: url('./img/experiance_img.png');"></a>
                                                            </div>                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">                                                    
                                                        <div class="single-img-item">
                                                            <div class="item-img">
                                                                <a href="#" class="img" style="background-image: url('./img/project_3.png');"></a>
                                                            </div>
                                                        </div>                                                  
                                                    </div>
                                                    <div class="col-md-4">                                                    
                                                        <div class="single-img-item">
                                                            <div class="item-img">
                                                                <a href="#" class="img" style="background-image: url('./img/project_3.png');"></a>
                                                            </div>
                                                        </div>                                                  
                                                    </div>
                                                    <div class="col-md-4">                                                    
                                                        <div class="single-img-item">
                                                            <div class="item-img">
                                                                <a href="#" class="img" style="background-image: url('./img/project_3.png');"></a>
                                                            </div>
                                                        </div>                                                  
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- ############ MODALS ################ -->
                                                <!--- POST SETTINGS MODAL --->

                                                <div class="modal fade" id="postSettings" role="dialog" tabindex="-1" style="padding-top: 180px;">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                                            <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                                                <h4 class="modal-title">Post Options</h4>
                                                            </div>
                                                            <div class="modal-body dg-text modal-border-split">
                                                                    <a href="#" class="mb-0 dg-text">Delete This Post</a>
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
                                                <!---- /////////////////// --->

                                                <!--- LIST FOLLOWERS MODAL --->

                                                <div class="modal fade" id="listFollowers" role="dialog" tabindex="-1" style="padding-top: 100px;">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                                            <div class="modal-header justify-content-center modal-border-split" style="padding: 2rem 1rem">
                                                                <h4 class="modal-title">Followers</h4>
                                                            </div>
                                                            <div class="modal-body dg-text" style="text-align: initial;">
                                                                <?php 
                                                                    foreach ($listFollowers as $lf) {

                                                                        $hisdp = Post::dpCheck($lf['id']);
            
                                                                        $Firss = '<div class="single-notify-item flex-start mt-3">
                                                                                    <div class="s-dp margin-r-15">
                                                                                        <img src="';///IMAGE///
                                                                        $Second = '"></div><div class="v-align notify-body flex-start"><a class="c-font-light text-black mr-1" href="#">';///NAME///

                                                                        $Third = '</a></div><div class="call-to ml-auto">';

                                                                        if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$lf['id'], ':followerid'=>$followerid))) {

                                                                            $actionBtn = '<form action="profile-page.php?username='.$lf['username'].'" method="post"><button class="btn sub-follow-btn padding-x-25" type="submit">Follow</button></form></div></div>';
                                                                        }else {
                                                                            $actionBtn = '<form action="profile-page.php?username='.$lf['username'].'" method="post"><button class="btn sub-following-btn" type="submit">Following</button></form></div></div>';
                                                                        }

                                                                        echo($Firss.$hisdp.$Second.$lf['username'].$Third.$actionBtn.$list);
                                                                    }
                                                                ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-default" type="button" data-dismiss="modal"></button>
                                                            </div>                                                    
                                                        </div>
                                                    </div>
                                                </div>
                                                <!---- /////////////////// --->

                                                <!--- LIST FOLLOWING MODAL --->

                                                <div class="modal fade" id="listFollowing" role="dialog" tabindex="-1" style="padding-top: 100px;">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                                            <div class="modal-header justify-content-center modal-border-split" style="padding: 2rem 1rem">
                                                                <h4 class="modal-title">Following</h4>
                                                            </div>
                                                            <div class="modal-body dg-text" style="text-align: initial;">
                                                                <?php 
                                                                    foreach ($listFollowing as $lf) {

                                                                        $hisdp = Post::dpCheck($lf['id']);
            
                                                                        $Firss = '<div class="single-notify-item flex-start mt-3">
                                                                                    <div class="s-dp margin-r-15">
                                                                                        <img src="';///IMAGE///
                                                                        $Second = '"></div><div class="v-align notify-body flex-start"><a class="c-font-light text-black mr-1" href="#">';///NAME///

                                                                        $Third = '</a></div><div class="call-to ml-auto">';

                                                                        if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$lf['id'], ':followerid'=>$followerid))) {

                                                                            $actionBtn = '<form action="profile-page.php?username='.$lf['username'].'" method="post"><button class="btn sub-follow-btn padding-x-25" type="submit">Follow</button></form></div></div>';
                                                                        }else {
                                                                            $actionBtn = '<form action="profile-page.php?username='.$lf['username'].'&&unfollow=yes" method="post"><button class="btn sub-following-btn" type="submit">Following</button></form></div></div>';
                                                                        }

                                                                        echo($Firss.$hisdp.$Second.$lf['username'].$Third.$actionBtn.$list);
                                                                    }
                                                                ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-default" type="button" data-dismiss="modal"></button>
                                                            </div>                                                    
                                                        </div>
                                                    </div>
                                                </div>
                                                <!---- /////////////////// --->

                                                <!--- CHANGE PROFILE IMG MODAL --->
                                                <div class="modal fade" id="changeDP" role="dialog" tabindex="-1" style="padding-top: 200px;">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                                            <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                                                <h4 class="modal-title">Change Profile Picture</h4>
                                                            </div>
                                                            <div class="modal-body dg-text modal-border-split c-font">
                                                                <form action='profile-page.php?username=<?php echo($username); ?>' method="post" enctype="multipart/form-data">
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
                                                
                                <!--- ######################################## --->
                            </div>                                    
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>
    <!--================ End Main Area =================-->
    

    <!-- Jquery js file -->
    <script src="./js/jquery-3.2.1.min.js"></script>
    
    <script src="./js/jquery.sticky.js"></script>
    
    <!-- bootstrap js -->
    <script src="./js/bootstrap.min.js"></script>

    <!--OwlCarousel Script-->
    <script src="./vendors/owl-carousel/owl.carousel.min.js"></script>

    <!-- Main-JS -->
    <script src="./js/main.js"></script>

    <script type="text/javascript">
        $(".site-main .stories-area-alt .owl-carousel").owlCarousel({
            loop: false,
            autoplay: false,
            dots: false,
            nav: false,
            responsive:{
                0: {
                    items: 1
                },
                544: {
                    items: 6
                }
            }
        });
    </script>
</body>
</html>