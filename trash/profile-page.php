<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Comment.php');
include('./classes/Image.php');
include('./classes/Notify.php');


if (Login::isLoggedIn()) {
        $myuserid = Login::isLoggedIn();


    $react = False;

    $username = "";
    $verified = False;
    $isFollowing = False;
    $liked = False;

    if (isset($_GET['handle'])) {
        if (DB::query('SELECT handle FROM users WHERE handle = :handle', array(':handle'=>$_GET['handle']))) {
            $myhandle = DB::query('SELECT handle FROM users WHERE handle = :handle', array(':handle'=>$_GET['handle']))[0]['handle'];

            $username = DB::query('SELECT username FROM users WHERE handle = :handle', array(':handle'=>$myhandle))[0]['username'];
            $userid = DB::query('SELECT id FROM users WHERE handle = :handle', array(':handle'=>$myhandle))[0]['id'];
            $thisid = $userid;
            $verified = DB::query('SELECT verified FROM users WHERE handle = :handle', array(':handle'=>$myhandle))[0]['verified'];
            $followerid = Login::isLoggedIn();

            $nPosts = DB::query('SELECT COUNT(*) FROM posts WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
            $nFollowers = DB::query('SELECT COUNT(*) FROM followers WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
            $nFollowing = DB::query('SELECT COUNT(*) FROM followers WHERE follower_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];

            $listFollowers = DB::query('SELECT DISTINCT users.* FROM followers, users WHERE followers.user_id = :userid AND users.id = follower_id AND follower_id != 6', array(':userid'=>$userid));
            $listFollowing = DB::query('SELECT DISTINCT users.* FROM followers, users WHERE users.id = followers.user_id AND follower_id = :userid', array(':userid'=>$userid));
            $list = "";

            if (!DB::query('SELECT bio FROM profileinfo WHERE user_id =:userid', array('userid'=>$userid))) {
                $bioEntry = DB::query('SELECT bio FROM profileinfo WHERE user_id =:userid', array('userid'=>$userid));
            }
            if (empty($bioEntry)) {
                $bio = 'Click here to create a bio!';
            }

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


            if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                //echo "Already Following!";
                $isFollowing = True;
            }

            if (isset($_POST['post'])) {
                $postbody = $_POST['postbody'];
                $loggedInUserId = Login::isLoggedIn();

                if (strlen($postbody) > 160 || strlen($postbody) < 1) {
                    die('Incorrect Length');
                }

                if ($loggedInUserId == $userid) {
                    $postbody = preg_replace("/\r\n|\r|\n/", '<br/> ', $postbody);
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
            $meimage = DB::query('SELECT profileimg FROM users WHERE handle = :handle', array(':handle'=>$myhandle))[0]['profileimg'];


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
            $posts = "";
            $photos = "";

        }else {
            die("User Not Found");
        }
    }

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

    <!--- Magnific Popup CSS --->
    <link rel="stylesheet" type="text/css" href="./css/magnific-popup.css">

    <!-- FANCYBOX -->
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">

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
                                <li class="nav-item"><a class="nav-link" href="contact.html"><i class="ti-bell"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Other Additional nav content-->
                    <div class="user-area mt-0">
                        <div class="right-corner-nav flex-start v-align">
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="ti-bell"></i></a></div>
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><img style="width: 20px;" src="./vendors/ionicons-5.1.2/paper-plane-outline.svg"></a><span class="division-border"></span></div>                            
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
        <section class="profile-area" style="background: #f4f4f4 !important;">           
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 left-profile-area">
                        <div class="profile fixed">
                            <div class="profile-info left-nav left">
                                <div class="floating-image">
                                    <a href="#"  data-toggle="modal" data-target="#changeDP" ><img src='<?php echo($profilePic); ?>' alt="" srcset=""></a>
                                </div>
                                <?php 
                                    if ($userid != $followerid) {
                                        echo " ";
                                    }else {
                                        echo '<div class="floating-edt-i"><a href="#" class="settings-btn"><i class="ti-settings"></i></a></div>';
                                    }
                                ?>
                                <div class="info-area">
                                    <div class="profile-id text-center mb-4">
                                        <h4 class="name d-flex justify-content-center">
                                            <?php echo $username ?>
                                            <span class="<?php if ($verified) { echo 'icon-check-circle';} ?> ml-2" style="color: #007bff;"></span>
                                        </h4>
                                        <h7 class="handle rs-heading text-black mb-4">@<?php echo($myhandle); ?></h7><br/>
                                        <form style="margin-top: 30px;" action="profile-page.php?handle=<?php echo $myhandle; ?>" method="post">
                                            <?php
                                                $btnSECT = "";
                                                if ($userid != $followerid) {
                                                    if ($isFollowing == True) {
                                                        echo '<button class="mt-1 btn button unfollow-btn gradient-btn" name="unfollow">Following</button>

                                                            <button class="mt-1 ml-1 btn button dm-button gradient-btn">
                                                                <img style="width: 20px;" src="./vendors/ionicons-5.1.2/paper-plane-outline.svg">
                                                            </button>';
                                                    }else {
                                                        echo '<button class="mt-1 btn button follow-btn gradient-btn" name="follow">Follow</button>

                                                            <button class="mt-1 ml-1 btn button dm-button gradient-btn">
                                                                <img style="width: 20px;" src="./vendors/ionicons-5.1.2/paper-plane-outline.svg">
                                                            </button>';
                                                    }
                                                }else {
                                                        echo '<a href="usersettings.php">
                                                                <button class="mt-1 btn button unfollow-btn gradient-btn" name="unfollow">Edit Profile</button></a>

                                                            <button class="mt-1 ml-1 btn button dm-button gradient-btn">
                                                                <i class="icon-cog"></i>
                                                            </button>';
                                                    }
                                            ?>
                                        </form>
                                        <div class="followers-area padding-x-35">
                                            <div class="followers row">
                                                <div class="col-4"><h5 class="ss-heading ra-heading"><?php echo($nPosts); ?><br><span class="ss-content" style="color: #555 !important;">Posts</span></h5></div>

                                                <div class="col-4">
                                                    <a href="#" data-toggle="modal" data-target="#listFollowers" style="color: initial !important;">
                                                        <h5 class="followers-n ss-heading ra-heading"><?php echo($nFollowers); ?><br><span class="ss-content" style="color: #555 !important;">Followers</span></h5>   
                                                    </a>                                                    
                                                </div>

                                                <div class="col-4">
                                                    <a href="#" data-toggle="modal" data-target="#listFollowing" style="color: initial !important;">
                                                        <h5 class="following-n ss-heading ra-heading"><?php echo($nFollowing); ?><br><span class="ss-content" style="color: #555 !important;">Following</span></h5>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>                                    
                                        <div class="bio-section padding-x-35 mt-4">
                                            <h3 class="ra-heading">@<?php echo($myhandle); ?></h3>
                                            <p class="para bio">Photographer | Cinematographer | Photoshop Artist <br />
                                                Follow me on<br />
                                                twitter: @jasminedababy<br />
                                                instagram: @daddysgirl_jazzy<br />
                                                facebook: Jasmine Fames                                             
                                            </p>
                                        </div>
                                </div>                              
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-12" style="margin-top: 4rem;">
                                <div class="stories-area col featured rounded-container carousel" style="background: url('./img/banner_img.png'); padding: 2rem;">
                                    <div class="area-heading flex-start relative mb-1 mt-1">
                                        <h5 class="lc-section-heading mb-4 text-white">Featured Stories</h5>                               
                                    </div>
                                    <div class="stories owl-carousel owl-theme row">
                                        <?php
                                            if ($userid == $followerid) {
                                                echo '<div class="item">
                                                        <div class="single-story">
                                                            <div class="s-content">
                                                                <div class="s-new"><a href="#" class="s-new-item-icon" style="background: none !important;"><i class="ti-plus"></i></a></div>
                                                                <div class="s-title"><p class="para text-size-15">New</p></div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }else {
                                                echo "";
                                            }
                                        ?>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="./img/about_part_img.png" class="s-image" id="altimea"><img src="./img/about_part_img.png" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a class="s-image" id="many-popup"><img src="./img/creative_img.png" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="./img/people/person_7.jpg" data-fancybox="hisid" class="s-image"><img src="./img/people/person_7.jpg" alt="" srcset=""></a>
                                                    <a href="./img/people/person_1.jpg" data-fancybox="hisid" class="s-image d-none"><img src="./img/people/person_7.jpg" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="./img/people/person_3.jpg" data-fancybox="333" class="s-image"><img src="./img/people/person_3.jpg" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>                                
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="#" class="s-image"><img src="./img/about_part_img.png" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="#" class="s-image"><img src="./img/creative_img.png" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="#" class="s-image"><img src="./img/people/person_7.jpg" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="#" class="s-image"><img src="./img/people/person_3.jpg" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="#" class="s-image"><img src="./img/people/person_7.jpg" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="single-story">
                                                <div class="s-content">
                                                    <a href="#" class="s-image"><img src="./img/people/person_3.jpg" alt="" srcset=""></a>
                                                    <div class="s-title"><p class="para">2K19</p></div>
                                                </div>
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
                                            
                                            <?php 
                                                if ($myuserid != $userid) {
                                                    $addPostStyle = 'none !important;';
                                                }else {
                                                    $addPostStyle = '';
                                                }
                                            ?>
                                            <div class="sticky-container add-post-section mt-4 rounded-container" style="display: <?php echo($addPostStyle); ?>">
                                                <h5 class="ra-heading mb-3">Post Something</h5>
                                                <form class="add-post-area flex-start form-inline" action="profile-page.php?handle=<?php echo $username; ?>" method="post">
                                                    <div class="profile-picture">
                                                        <img src='<?php echo($myprofilePic); ?>' alt="" srcset="">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" id="exampleInputEmail1" class="form-control mx-1" name="postbody" placeholder="What's on your mind?" style="border-radius: 7px; width: 350px"></input>
                                                    </div>                                                    
                                                    <button type="submit" name="post" class="btn v-align">Post</button>
                                                </form>
                                            </div>

                                            <div class="timelineposts">
                                                <?php /* echo $posts; 
                                                    if (empty($posts)) {
                                                        echo "
                                                        <h5 class='c-font-light mb-3 m-lr-auto' style='width:max-content;margin-top:50px;color:#666;'>@".$myhandle." Has No Posts Yet!</h5>
                                                        <div class='Nun-Found justify-content-center mt-4'>
                                                            <img style='max-width: 380px;' src='./img/default/undraw_synchronize_ccxk.svg'>
                                                        </div>";
                                                    }*/
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
                                                                <a class="img" data-fancybox="gal" href="./img/gallery_item_2.png" style="background-image: url('./img/gallery_item_2.png');"></a>
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

                                                            $actionBtn = '<form action="profile-page.php?handle='.$lf['handle'].'" method="post"><button class="btn sub-follow-btn padding-x-25" type="submit">Follow</button></form></div></div>';
                                                        }else {
                                                            $actionBtn = '<form action="profile-page.php?handle='.$lf['handle'].'" method="post"><button class="btn sub-following-btn" type="submit">Following</button></form></div></div>';
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

                                                            $actionBtn = '<form action="profile-page.php?handle='.$lf['handle'].'" method="post"><button class="btn sub-follow-btn padding-x-25" type="submit">Follow</button></form></div></div>';
                                                        }else {
                                                            $actionBtn = '<form action="profile-page.php?handle='.$lf['handle'].'&&unfollow=yes" method="post"><button class="btn sub-following-btn" type="submit">Following</button></form></div></div>';
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
                                                <form action='profile-page.php?handle=<?php echo($myhandle); ?>' method="post" enctype="multipart/form-data">
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
    <script src="./js/jquery-3.3.1.min.js"></script>
    
    <script src="./js/jquery.sticky.js"></script>

    <!--- Magnific Popup JS --->
    <script src="./js/jquery.magnific-popup.min.js"></script>

    <!-- bootstrap js -->
    <script src="./js/bootstrap.min.js"></script>    

    <!--OwlCarousel Script-->
    <script src="./vendors/owl-carousel/owl.carousel.min.js"></script>

    <!-- FANCY BOX -->
    <script src="js/jquery.fancybox.min.js"></script>

    <!-- Main-JS -->
    <script src="./js/main.js"></script>

    <script type="text/javascript">

        var start = 5;
        var working = false;
        $(window).scroll(function() {
                if ($(this).scrollTop() + 1 >= $('body').height() - $(window).height()) {
                        if (working == false) {
                                working = true;
                                $.ajax({

                                        type: "GET",
                                        url: "api/profileposts?handle=<?php echo $myhandle; ?>&start="+start,
                                        processData: false,
                                        contentType: "application/json",
                                        data: '',
                                        success: function(r) {
                                                var posts = JSON.parse(r)
                                                $.each(posts, function(index) {
                                                        var caption = posts[index].PostBody;
                                                        var caption = caption.replace(/2>j/g, '"');
                                                        var caption = caption.replace(/3>j/g, "'");
                                                        if (posts[index].PostImg == "") {
                                                            $('.timelineposts').html(
                                                                    $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="#" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image mainPost"><div class="post-text c-font-med cursor-default" style="height:100%;display:table;"><p id="post-caption" class="post-caption text-black niCap"><span id="account-name">@'+posts[index].HisHandle+'</span>'+caption+'</p></div></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-time c-font-med" style="color: #2e3236 !important;"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
                                                            );
                                                        }else {
                                                            $('.timelineposts').html(
                                                                    $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="#" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image"><img id="post-image" src="'+posts[index].PostImg+'"></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-text c-font-alt cursor-default"><p id="post-caption" class="post-caption text-black"><span id="account-name" class="text-black">'+posts[index].PostedBy+'</span>'+caption+'</p></div><div class="post-time c-font-med" style="color: #2e3236 !important;"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
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

                                                $('.postimg').each(function() {
                                                        this.src=$(this).attr('data-tempsrc')
                                                        this.onload = function() {
                                                                this.style.opacity = '1';
                                                        }
                                                })

                                                scrollToAnchor(location.hash)

                                                start+=5;
                                                setTimeout(function() {
                                                        working = false;
                                                }, 4000)

                                        },
                                        error: function(r) {
                                                console.log(r)
                                        }

                                });
                        }
                }
        })

        function scrollToAnchor(aid){
        try {
        var aTag = $(aid);
            $('html,body').animate({scrollTop: aTag.offset().top},'slow');
            } catch (error) {
                    console.log(error)
            }
        }

            $(document).ready(function() {

                $("#fancybox").fancybox({
                    autoPlay: true,
                    loop: true,
                });

                    $('#many-popup').magnificPopup({
                        items: [
                          {
                            src: './img/blog/blog_1.png',
                            type : 'image'
                          },
                          {
                            src: './img/blog/blog_2.png',
                            type : 'image'
                          },
                          {
                            src: './img/blog/blog_4.png',
                            type : 'image'
                          },
                          {
                            src: './img/blog/blog_3.png',
                            type : 'image'
                          },
                          {
                            src: './img/blog/single_blog_3.png',
                            type : 'image'
                          }
                        ],
                        gallery: {
                          enabled: true
                        },
                        type: 'image', // this is default type
                    });

                    $.ajax({

                            type: "GET",
                            url: "api/profileposts?handle=<?php echo $myhandle;?>&start=0",
                            processData: false,
                            contentType: "application/json",
                            data: '',
                            success: function(r) {
                                            var posts = JSON.parse(r)
                                            console.log(r)
                                            $.each(posts, function(index) {
                                                var caption = posts[index].PostBody;
                                            var caption = caption.replace(/2>j/g, '"');
                                            var caption = caption.replace(/3>j/g, "'");

                                            if (posts[index].PostImg == "") {
                                                $('.timelineposts').html(
                                                        $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="#" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image mainPost"><div class="post-text c-font-med cursor-default" style="height:100%;display:table;"><p id="post-caption" class="post-caption text-black niCap"><span id="account-name">@'+posts[index].HisHandle+'</span>'+caption+'</p></div></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-time c-font-med" style="color: #2e3236 !important;"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
                                                );
                                            }else {
                                                $('.timelineposts').html(
                                                        $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="#" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image"><img id="post-image" src="'+posts[index].PostImg+'"></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-text c-font-alt cursor-default"><p id="post-caption" class="post-caption text-black"><span id="account-name" class="text-black">'+posts[index].PostedBy+'</span>'+caption+'</p></div><div class="post-time c-font-med" style="color: #2e3236 !important;"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
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

                                    $('.postimg').each(function() {
                                            this.src=$(this).attr('data-tempsrc')
                                            this.onload = function() {
                                                    this.style.opacity = '1';
                                            }
                                    })

                                    scrollToAnchor(location.hash)

                            },
                            error: function(r) {
                                    console.log(r)
                            }

                    });

            });

    </script>
</body>
</html>