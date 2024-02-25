<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Notify.php');
include('./classes/Image.php');
include('./classes/Comment.php');


if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        die('Not logged in');
}

$react = False;
$verified = False;
$isFollowing = False;
$liked = False;

if (Login::isLoggedIn()) {
    if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {

        $username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
        $thisid = $userid;
        $verified = DB::query('SELECT verified FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['verified'];
        $followerid = Login::isLoggedIn();

        $listFollowers = DB::query('SELECT DISTINCT users.* FROM followers, users WHERE followers.user_id = :userid AND users.id = follower_id AND follower_id != 6', array(':userid'=>$userid));
        $listFollowing = DB::query('SELECT DISTINCT users.* FROM followers, users WHERE users.id = followers.user_id AND follower_id = :userid', array(':userid'=>$userid));
        $list = "";

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


        if (isset($_POST['post'])) {
                ini_set('max_execution_time', 300);
                if ($_FILES['postimg']['size'] == 0) {
                        Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                } else {
                        $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                        Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
                }
        }

        if (isset($_POST['deletepost'])) {
                if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                        DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                        DB::query('DELETE FROM post_likes WHERE post_id = :postid', array(':postid'=>$_GET['postid']));
                        echo "Post Deleted";
                }
        }

        $nPosts = DB::query('SELECT COUNT(*) FROM posts WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
        $nFollowers = DB::query('SELECT COUNT(*) FROM followers WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
        $nFollowing = DB::query('SELECT COUNT(*) FROM followers WHERE follower_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
        if (!DB::query('SELECT bio FROM profileinfo WHERE user_id =:userid', array('userid'=>$userid))) {
            $bioEntry = DB::query('SELECT bio FROM profileinfo WHERE user_id =:userid', array('userid'=>$userid));
        }
        if (empty($bioEntry)) {
            $bio = 'Click here to create a bio!';
        }
        $myhandle = DB::query('SELECT handle FROM users WHERE id = :userid', array(':userid'=>$userid))[0]['handle'];
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
                                <li class="nav-item"><a class="nav-link" href="contact.html"><i class="ti-bell"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="contact.html"><i class="ti-settings"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Other Additional nav content-->
                    <div class="user-area mt-0">
                        <div class="right-corner-nav flex-start v-align">
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="ti-bell"></i></a></div>
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="icon-envelope-o"></i></a><span class="division-border"></span></div>                            
                        </div>
                        <div class="profile-picture ml-20" style="border-radius: 50%; border: 2px solid #000;">
                            <img src='<?php echo($myprofilePic); ?>' alt="" srcset="" style="height: 30px; border: 2px solid #fff;">
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
                                    <img src='<?php echo($profilePic); ?>' alt="" srcset="" style="height: 150px;">
                                </div>
                                <div class="floating-edt-i">
                                    <a href="#" class="settings-btn"><i class="ti-settings"></i></a>
                                </div>
                                <div class="info-area">                                    
                                    <div class="profile-id text-center mb-4">
                                        <h4 class="name d-flex justify-content-center">
                                            <?php echo $username; ?>
                                            <span class="<?php if ($verified) { echo 'icon-check-circle';} ?> ml-2" style="color: #7252e9;"></span>
                                        </h4>
                                        <h7 class="handle rs-heading text-black">@<?php echo($myhandle); ?></h7><br/>
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
                                        <form action="profile-page.php?username=<?php echo $username; ?>" method="post">
                                            <?php
                                                $btnSECT = "";
                                                if ($userid != $followerid) {
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
                                                }else {
                                                    echo '<button class="mt-1 btn button follow-btn primary-button gradient-btn" name="follow" style="box-shadow:none;">Edit Bio</button>

                                                        <button class="mt-1 ml-1 btn button dm-button gradient-btn">
                                                            <i class="icon-cog"></i>
                                                        </button>';
                                                }
                                            ?>
                                        </form>
                                    </div>                                    
                                        <div class="bio-section padding-x-35 mt-4">
                                            <h3 class="ra-heading">@<?php echo($myhandle); ?></h3>
                                            <p class="para bio"><?php echo($bio); ?>                                          
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
                                    <a href="#" class="image-fluid" style="background-image: url('./img/cta_bg.png');"></a>
                                </div>
                                <div class="stories-area-alt rounded-container">
                                    <h5 class="ra-heading mb-3">Featured Stories</h5>
                                    <div class="stories owl-carousel owl-theme row">
                                        <div class="single-story d-block ml-20">
                                            <div class="ss-image-body new-item-btn">
                                                <div class="ss-img img-0 d-table border-radius">
                                                    <div class="ss-ni-content">
                                                        <a href="#" class="height-defined">
                                                            <div class="ss-new-item fill m-lr-auto mb-2"><i class="ti-plus"></i></div>
                                                            <p href="#" class="text-white para mb-0">Add to your featured</p>
                                                        </a>                                                            
                                                    </div>                                                   
                                                </div>                                               
                                            </div>
                                        </div>
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
                                            
                                            <div class="sticky-container add-post-section mt-4 rounded-container">
                                                <h5 class="ra-heading mb-3">Post Something</h5>
                                                <div class="add-post-area flex-start">
                                                    <div class="profile-picture">
                                                        <img src='<?php echo($myprofilePic); ?>' alt="Dp" srcset="" style="height: 40px;">
                                                    </div>
                                                    <div class="ap-input-area v-align"><a href="#" data-toggle="modal" data-target="#postCreateForm" class="para mb-0 ml-3" style="color: #7d7d7d;">What's on your mind?</a></div>
                                                    <div class="ap-image-icon v-align"><a href="#" data-toggle="modal" data-target="#postCreateForm" class="ig-btn" style="color: #7d7d7d;"><i class="ti-image"></i></a></div>
                                                </div>
                                                <!--- CREATE POST MODAL --->
                                                <div class="modal fade" id="postCreateForm" role="dialog" tabindex="-1" style="padding-top: 150px;">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                                            <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                                                <h4 class="modal-title">Create Post</h4>
                                                            </div>
                                                            <form action='userprofile.php' method="post" enctype="multipart/form-data">
                                                                <div class="modal-body dg-text modal-border-split c-font">
                                                                    <input type="file" name="postimg">
                                                                    <input type="submit" name="uploadprofileimg" value="Upload Image">
                                                                </div> 
                                                                <div class="modal-body dg-text justify-content-center modal-border-split" style="padding-top: 2rem;">
                                                                    <textarea name="postbody" placeholder="Caption" class="text-center" style="border: none; width: 100%;"></textarea>
                                                                </div>
                                                                <div class="modal-body dg-text justify-content-center">
                                                                    <button class="btn btn-default" name="post" type="submit">Post</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!---- /////////////////// --->
                                            </div>

                                            <div class="myposts" style="margin-bottom: 4rem;">

                                                <!--- POST SETTINGS MODAL --->
                                                <div class="modal fade" id="postSettings" role="dialog" tabindex="-1" style="padding-top: 180px;">                                                    
                                                </div>
                                                <!---- /////////////////// --->

                                                <!--- CREATE-POST MODAL --->
                                                <div class="modal fade" id="postCreateForm" role="dialog" tabindex="-1" style="padding-top: 200px;">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                                            <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                                                <h4 class="modal-title">New Post</h4>
                                                            </div>
                                                            <div class="modal-body dg-text modal-border-split">
                                                                <input type="file" name="">
                                                            </div>

                                                            <div class="modal-body dg-text modal-border-split">                                        
                                                                <form class="add-post-area flex-start form-inline" action="feed.php?username=<?php echo $username; ?>" method="post">
                                                                    <div class="form-group" style="width: 100%">
                                                                        <input type="text" id="exampleInputEmail1" class="form-control center-ph mx-1" name="postbody" placeholder="Caption" style="border-radius: 7px; width: 100%;"></input>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                            <div class="modal-body dg-text justify-content-center">
                                                                    <a href="#" name="post" class="btn v-align" style="color: #7d7d7d;">Post</a>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-default" type="button" data-dismiss="modal"></button>
                                                            </div>                                                    
                                                        </div>
                                                    </div>
                                                </div>
                                                <!---- /////////////////// --->  
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
                                                    $postedPictures = DB::query('SELECT * FROM posts WHERE user_id = :userid AND postimg IS NOT NULL ORDER BY posted_at DESC', array('userid'=>$userid));
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

                            </div>                                    
                        </div>
                    </div>
                </div>

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
        $(document).ready(function() {
                $.ajax({

                    type: "GET",
                    url: "api/userposts",
                    processData : false,
                    contentType: "application/json",
                    data: '',
                    success: function(r) {
                        var res = r
                        var text = res.replace(/'/g, "");
                        var text  = text.replace( /[\r\n]+/gm, "" );
                        var posts = JSON.parse(text);
                        console.log(text)
                        $.each(posts, function(index) {
                                var caption = posts[index].PostBody;
                                var caption = caption.replace(/2>j/g, '"');
                                var caption = caption.replace(/3>j/g, "'");
                                $('.myposts').html(
                                        $('.myposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="#" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-setid="'+posts[index].PostId+'" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image"><img id="post-image" src="'+posts[index].PostImg+'"></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-text c-font cursor-default"><p id="post-caption" class="post-caption"><span id="account-name">'+posts[index].PostedBy+'</span>'+caption+'</p></div><div class="post-time"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
                                );
                                    
                                $('[data-setid]').click(function(){
                                    var formId = $(this).attr('data-setid');

                                    $('#postSettings').html(
                                            $('#post-settings-menu').html() + '<form action="userprofile.php?postid='+formId+'" method="post"><div class="modal-dialog" role="document"><div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;"><div class="modal-header justify-content-center" style="padding: 2rem 1rem"><h4 class="modal-title">Post Options</h4></div><div class="modal-body py-0 px-0 dg-text modal-border-split"><input type="submit" class="modal-btn" name="deletepost" value="Delete This Post"></div><div class="modal-body dg-text modal-border-split"><p class="mb-0">See Fewer Posts Like This</p></div><div class="modal-body dg-text"><p class="mb-0">Share post</p></div><div class="modal-footer justify-content-center"><button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button></div></div></div></form>'
                                    );
                                    $('[data-dismiss]').click(function() {
                                        var formID = " ";
                                    });
                                });

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
                                            var res = JSON.parse(act)
                                            $("[data-id='"+buttonid+"']").toggleClass('active')
                                            $("[data-id='t"+buttonid+"']").html('<p id="reactions" class="likes mb-0 red">'+res.Likes+' likes</p>')
                                            console.log(r)
                                        },
                                        error: function(r) {
                                            console.log(r)
                                        }

                                    }) 

                                })
                        })
                    },
                    error: function(r) {
                        console.log(r)
                    }

                });
        });
    </script>
</body>
</html>