<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Comment.php');
include('./classes/Image.php');
include('./classes/Notify.php');
include('./classes/Extra.php');


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

            $bioEntry = DB::query('SELECT bio FROM profileinfo WHERE user_id =:userid', array('userid'=>$userid))[0]['bio'];
            if (empty($bioEntry)) {
                if ($userid == $myuserid ) {
                    $bio = 'Click here to create a bio!';
                }else {
                    $bio = '';
                }
                
            }else {
                $textContent = $bioEntry;
                  $textContent = Post::link_add($textContent);
                  $textContent = Extra::emojiAdd($textContent, "18");
                  $textContent = preg_replace("/\r\n|\r|\n/", '<br/> ', $textContent);
                  $textContent = preg_replace("/8ccc1o/", '<br/> ', $textContent);

                $bio = $textContent;
            }
            
            if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                $isFollowing = True;
            }

            if (isset($_POST['uploadprofileimg'])) {
                Image::uploadImage('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid'=>$userid));
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
        die('Not logged in');
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

    <!-- FANCYBOX -->
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="css/util.css">

    <!--- Custom CSS --->
    <link rel="stylesheet" href="./scss/style.css">
    <link rel="stylesheet" href="./css/mobile.css">  
    <link rel="stylesheet" href="./css/addsIn.css">
</head>
<body>

    <!--================ Start Header Area =================-->
    <header class="header_area profile-page">
        <div class="main-menu social-site">
            <nav class="navbar sm-md-displaced fixed paddingNull navbar-expand-lg navbar-light">
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
                                <li class="nav-item"><a class="nav-link" href="about.html"><i class="ti-search"></i></a></li>
                                <li class="nav-item d-lg-none"><a class="nav-link" href="portfolio.html"><i class="ti-plus"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="portfolio.html"><i class="ti-heart"></i></a></li>
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
    <main class="site-main account">
        <section class="profile-area" style="background: #f4f4f4 !important;">           
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="account-header w-100 p-rel">
                            <!--<div class="backimg" style="background: url('<?php echo($profilePic); ?>');">-->
                                <div class="backimg" style="background: url('./img/unorganised/lukas-hellebrand.jpg');">
                            </div>
                            <div class="short-info bg-white">
                                <div class="profileimg-section w-fit">
                                    <div class="image-container w-fit">
                                        <img src='<?php echo($profilePic) ?>' alt="" srcset="">
                                    </div>
                                </div>
                                <div class="info sm-padding p-rel">
                                    <div class="info-container">
                                        <div class="account">
                                            <h6 class="username c-font-bol sm-heading mb-0"><?php echo $username ?></h6>
                                            <p class="handle">@<?php echo($myhandle); ?></p>
                                            
                                        </div>
                                        <div class="profile-call-to-action">
                                            <?php
                                                $btnSECT = "";
                                                if ($userid != $followerid) {
                                                    if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                                        //// if following is true

                                                        echo '<button class="mt-1 text-white btn button d-none follow-btn gradient-btn" name="follow" data-followid="'.$myhandle.'">Follow</button>

                                                        <button class="mt-1 btn button unfollow-btn show gradient-btn" name="unfollow" data-unfollowid="'.$myhandle.'" style="color:#212529;background:transparent!important;">Following</button>

                                                        <button class="mt-1 ml-1 btn button dm-button gradient-btn" style="background:transparent!important;">
                                                                <img style="width: 20px;" src="./vendors/ionicons-5.1.2/paper-plane-outline.svg">
                                                        </button>';

                                                    }else {
                                                        echo '<button class="mt-1 btn button show follow-btn gradient-btn" name="follow" data-followid="'.$myhandle.'">Follow</button>

                                                        <button class="mt-1 btn button d-none unfollow-btn gradient-btn" style="background:transparent!important;" name="unfollow" data-unfollowid="'.$myhandle.'">Following</button>

                                                        <button class="mt-1 ml-1 btn button dm-button gradient-btn" style="background:transparent!important;">
                                                                <img style="width: 20px;" src="./vendors/ionicons-5.1.2/paper-plane-outline.svg">
                                                        </button>';
                                                    }                                                    
                                                    
                                                }else {
                                                        echo '<a href="usersettings.php">
                                                                <button class="mt-1 btn button unfollow-btn gradient-btn" name="unfollow" style="color:#212529;background:transparent!important;">Edit Profile</button></a>

                                                            <a href="usersettings.php"><button class="mt-1 ml-1 btn button dm-button gradient-btn" style="background:transparent!important;">
                                                                <i class="icon-cog"></i>
                                                            </button></a>';
                                                    }
                                            ?>
                                        </div>
                                            
                                    </div>
                                        
                                    <div class="interaction-info mt-4">
                                        <div class="followers-area">
                                            <div class="followers flex-start">
                                                <h6 class="ss-heading c-font fs-18">
                                                    <?php echo($nPosts); ?>
                                                    <span class="ss-content r-font-reg" style="color: #444 !important;">Posts</span>
                                                </h6>

                                                <a href="#" class="m-l-40" data-toggle="modal" data-target="#listFollowers" style="color: initial !important;">
                                                    <h6 class="followers-n ss-heading c-font fs-18">
                                                        <?php echo($nFollowers); ?>
                                                        <span class="ss-content r-font-reg" style="color: #444 !important;">Followers</span>
                                                    </h6>   
                                                </a>

                                                <a href="#" class="m-l-40" data-toggle="modal" data-target="#listFollowing" style="color: initial !important;">
                                                    <h6 class="following-n ss-heading c-font fs-18">
                                                        <?php echo($nFollowing); ?>
                                                        <span class="ss-content r-font-reg" style="color: #444 !important;">Following</span></h6>
                                                </a>
                                            </div>
                                        </div>
                                    </div>                                       
                                </div>
                            </div>
                        </div>
                        <div class="second-section row">
                            <div class="col-lg-7">
                                <div class="about-account rounded-container mt-4">
                                    <div class="section-header py-4">
                                        <h5 class="ra-heading text-primary-grey">About</h5>
                                    </div>
                                    <div class="about-info" style="margin-left: 4.3rem;">
                                        <div class="bio-entry-section mb-4">
                                            <p class="r-font text-primary-grey"><!--The Verge Original photography and videography from The Verge, which covers the future of #technology, #science, #art and #culture.<br>Visit the verge.com-->
                                                <?php if (empty($bio)) { echo"No bio"; }
                                                else { echo($bio);
                                                    if(strlen($bio)>1000){
                                                        $bio=substr($bio,0,1000).'... more';
                                                    }
                                                } ?></p>
                                        </div>
                                        <div class="socials">
                                            <div class="facebook flex-start">
                                                <span class="m-r-15 fs-25 icon-facebook-square"></span>
                                                <h6 class="handle"><?php echo($username); ?></h6>
                                            </div>
                                            <div class="twitter flex-start">
                                                <span class="m-r-15 fs-25 icon-twitter-square"></span>
                                                <h6 class="handle">@<?php echo($myhandle); ?></h6>
                                            </div>
                                            <div class="instagram flex-start">
                                                <span class="m-r-15 fs-25 icon-instagram"></span>
                                                <h6>@<?php echo($myhandle); ?>_sa</h6>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="stories-area default rounded-container carousel" style="margin-top:1.5rem !important;padding: 2.3rem;">
                                    <div class="area-heading flex-start relative mb-1 mt-1">
                                        <h5 class="ra-heading mb-4" style="font-family: 'Poppins-Bold';">Featured Stories</h5>                               
                                    </div>
                                    <div class="stories owl-carousel owl-theme row">
                                        <?php
                                            if ($userid == $followerid) {
                                                echo '<div class="item">
                                                        <div class="single-story mr-3">
                                                            <div class="s-content">
                                                                <div class="s-new"><a href="#" class="s-new-item-icon" style="background: none !important;"><i class="ti-plus"></i></a></div>
                                                                <div class="s-title"><p class="para text-size-15">New</p></div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }else {
                                                echo "";
                                            }

                                            ##################

                                            #GET IDs OF PEOPLE WHO HAVE POSTED A STORY THAT I FOLLOW
                                            /* 
                                                $sCreators DB::query('SELECT user_id FROM stories, users, followers WHERE stories.user_id = followers.user_id AND followers.follower_id = :myid', array(':myid'=>$userid)): i.e.g of result
                                            */

                                            /*GET STORIES LINKS FOR EACH OF THE FOLLOWED PEOPLE

                                                foreach ($sCreators as $ssc) {
                                                    1.$content  =  SELECT Story_Content FROM stories WHERE user_id = $ssc[id]
                                                    2.foreach $content
                                                        `stylize
                                                }
                                             
                                            */

                                            $sCreators = DB::query('SELECT DISTINCT stories.user_id FROM stories, users, followers WHERE stories.user_id = followers.user_id AND followers.follower_id = :myid', array(':myid'=>$followerid));
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

                                                            echo '<a href="'.$ssp['content'].'" data-fancybox="'.$ssp['user_id'].'" class="s-image numb'.$a++.'" style="display:none"><img src="'.$hisDP.'"></a>';
                                                    }

                                                    ### CLOSING DIVS
                                                    echo '<div class="s-title"><p class="para">'.$hisname.'</p></div></div></div></div>';
                                            }
                                        ?>
                                        
                                    </div>
                                </div>
                            </div>    
                        </div>
                                
                    </div>
                    <div class="col-lg-3 left-profile-area mt-4">
                        <div class="profile sticky-container left-pp-area" style="width: 290px !important;">
                            <div class="profile-info mt-0 left-nav left">
                                <div class="info-area mt-0">
                                    <div class="photos3x3 rounded-container mt-4">
                                        <h6 class="lc-section-heading grey py-2 mb-3">Photos</h6>
                                        <div class="pics flex-start">
                                            <div class="image">
                                                <img src="./img/post/post_5.png" style="border-top-left-radius: 7px;">
                                            </div>
                                            <div class="image px-2">
                                                <img src="./img/post/post_7.png">
                                            </div>
                                            <div class="image">
                                                <img src="./img/post/post_8.png" style="border-top-right-radius: 7px;">
                                            </div>
                                        </div>
                                        <div class="pics flex-start pt-2">
                                            <div class="image">
                                                <img src="./img/post/post_9.png" style="border-bottom-left-radius: 7px;">
                                            </div>
                                            <div class="image px-2">
                                                <img src="./img/post/post_10.png">
                                            </div>
                                            <div class="image">
                                                <img src="./img/post/emma-matthews-digital-content-production-O_CLjxjzN3M-unsplash.jpg" style="border-bottom-right-radius: 7px;">
                                            </div>
                                        </div>
                                </div>
                                </div>                              
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-12" style="margin-top: 1.5rem;">
                                
                                

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
                                    <div id="posts" class="posts tab-pane fade row mb-4 show active">
                                        <div class="col-lg-7">
                                            
                                            <?php 
                                                if ($myuserid != $userid) {
                                                    $addPostStyle = 'none !important;';
                                                }else {
                                                    $addPostStyle = ' ;';
                                                }
                                            ?>
                                            <div class="sticky-container add-post-section mt-4 rounded-container" style="display: none;">
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

                                                    $posts = DB::query('SELECT posts.*, users.`username`, users.`handle` FROM users, posts
                                                    WHERE users.id = posts.user_id
                                                    AND users.id = :userid
                                                    ORDER BY posts.posted_at DESC', array(':userid'=>$userid));
                                                    if (empty($posts)) {
                                                        echo '<div class="err-nun-found mt-4" style="width:100%;">
                                                                <img src="./img/default/undraw_snap_the_moment_oyn6.svg" style="width: 400px;" class="m-lr-auto d-block">
                                                            </div>';
                                                    }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-5">
                                            <div class="sticky-container left-pp-area">
                                                <div class="liked-pages-section rounded-container mt-4">
                                                    <h6 class="lc-section-heading grey py-2 mb-3">Pages that you like</h6>
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
                                                    <h6 class="lc-section-heading grey py-2 mb-3">People to Follow</h6>
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
                                    <div id="photos" class="photos tab-pane fade row mb-4">
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

                                <!--- POST MODAL --->

                                <div class="modal fade" id="postFull" role="dialog" tabindex="-1" style="padding-top: 110px;">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content thesharedpost c-font-light" style="border-radius: 1.5rem;">
                                            
                                            <blockquote>
                                                <div class="post" style="display: initial;">
                                                    <div class="post-head flex-start">
                                                        <a href="#" class="post-compiler">
                                                            <div class="profile-pic story-active">
                                                                <img id="profile-picture" src="./img/people/person_2.jpg" alt="" style="height:40px;">
                                                            </div>
                                                            <div class="post-by">
                                                                <h5 class="post-by-title">Carlia Martin</h5>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <div class="post-image mainPost">
                                                        <div class="post-text c-font-med cursor-default" style="height:100%;display:table;">
                                                            <p id="post-caption" class="post-caption text-black niCap">
                                                                <span id="account-name">@carlia_m</span>Life is all about loving those who care about you :)
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="post-tags mt-4">
                                                        
                                                        <div class="likes-section mt-2" data-id="t'+posts[index].PostId+'">
                                                            <p id="reactions" class="likes mb-0 red">451 likes</p>
                                                        </div>
                                                    </div>
                                                    <div class="post-time c-font-med">
                                                        <p>30 Nov 2020</p>
                                                    </div>
                                                </div>
                                            </blockquote>


                                        </div>
                                    </div>
                                </div>

                                <!--- LIST FOLLOWERS MODAL --->

                                <div class="modal fade" id="listFollowers" role="dialog" tabindex="-1" style="padding-top: 0px;">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                            <div class="modal-header justify-content-center modal-border-split" style="padding: 1.5rem 1rem">
                                                <h4 class="modal-title">Followers</h4>
                                            </div>
                                            <div class="modal-body dg-text" style="text-align: initial; max-height: 460px; overflow-y: scroll;">
                                                <?php 
                                                    foreach ($listFollowers as $lf) {

                                                        $hisdp = Post::dpCheck($lf['id']);

                                                        $Firss = '<div class="single-notify-item flex-start mt-3">
                                                                    <div class="s-dp margin-r-15">
                                                                        <img src="';///IMAGE///
                                                        $Second = '"></div><div class="v-align notify-body flex-start"><a class="c-font-light text-black mr-1" href="accountprofile.php?handle='.$lf['handle'].'">';///NAME///

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

                                <div class="modal fade" id="listFollowing" role="dialog" tabindex="-1" style="padding-top: 0px;">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                                            <div class="modal-header justify-content-center modal-border-split" style="padding: 1.5rem 1rem">
                                                <h4 class="modal-title">Following</h4>
                                            </div>
                                            <div class="modal-body dg-text" style="text-align: initial; max-height: 460px; overflow-y: scroll;">
                                                <?php 
                                                    foreach ($listFollowing as $lf) {

                                                        $hisdp = Post::dpCheck($lf['id']);

                                                        $Firss = '<div class="single-notify-item flex-start mt-3">
                                                                    <div class="s-dp margin-r-15">
                                                                        <img src="';///IMAGE///
                                                        $Second = '"></div><div class="v-align notify-body flex-start"><a class="c-font-light text-black mr-1" href="accountprofile.php?handle='.$lf['handle'].'">';///NAME///

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
                                                <form action='accountprofile.php?handle=<?php echo($myhandle); ?>' method="post" enctype="multipart/form-data">
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

    <!-- FANCY BOX -->
    <script src="js/jquery.fancybox.min.js"></script>

    <!-- Main-JS -->
    <script src="./js/main.js"></script>

    <script type="text/javascript">

        $(".site-main .stories-area .owl-carousel").owlCarousel({
            loop: false,
            autoplay: false,
            dots: false,
            nav: false,
            responsive:{
                0: {
                    items: 5
                },
                993: {
                    items: 2
                },
                1138: {
                    items: 3
                },
                1150: {
                    items: 3
                },
                1174: {
                    items: 4
                }

            }
        });

        var start = 5;
        var working = false;

        function scrollToAnchor(aid){
        try {
        var aTag = $(aid);
            $('html,body').animate({scrollTop: aTag.offset().top},'slow');
            } catch (error) {
                    console.log(error)
            }
        }

            $(document).ready(function() {

                    ///FOLLOW & UNFOLLOW
                    $('[data-followid]').click(function() {

                        hishandle = $(this).attr('data-followid');
                        $.ajax ({
                                type: "POST",
                                url: "api/follow?handle=" + $(this).attr('data-followid'),
                                processData : false,
                                contentType: "application/json",
                                data: '', 
                                success: function(r) {
                                    act = r
                                    var t = JSON.parse(r)
                                    $("[data-followid='"+hishandle+"']").removeClass('show')
                                    $("[data-followid='"+hishandle+"']").addClass('d-none')
                                    $('.unfollow-btn').removeClass('d-none')
                                    $('.unfollow-btn').addClass('show')
                                    $('.followers-n').html(''+t.nf+'<span class="ss-content r-font-reg" style="color: #444 !important;">Followers</span>')
                                },
                                error: function(r) {
                                    console.log(r)
                                }

                        }) 
                    });

                    $('[data-unfollowid]').click(function() {

                        hishandle = $(this).attr('data-unfollowid');
                        $.ajax ({
                                type: "POST",
                                url: "api/unfollow?handle=" + $(this).attr('data-unfollowid'),
                                processData : false,
                                contentType: "application/json",
                                data: '', 
                                success: function(r) {
                                    act = r
                                    var t = JSON.parse(r)
                                    $("[data-unfollowid='"+hishandle+"']").removeClass('show')
                                    $("[data-unfollowid='"+hishandle+"']").addClass('d-none')

                                    $('.follow-btn').removeClass('d-none')
                                    $('.follow-btn').addClass('show')
                                    $('.followers-n').html(''+t.nofollowers+'<span class="ss-content r-font-reg" style="color: #444 !important;">Followers</span>')
                                },
                                error: function(r) {
                                    console.log(r)
                                }

                        }) 
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
                                                    $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="#" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image mainPost"><div class="post-text c-font-med cursor-default" style="height:100%;display:table;"><p id="post-caption" class="post-caption text-black niCap"><span id="account-name">@'+posts[index].HisHandle+'</span>'+caption+'</p></div></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="#" data-toggle="modal" data-target="#postFull" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href=#"" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-time c-font-med"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
                                            );
                                        }else {
                                            $('.timelineposts').html(
                                                    $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="#" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image"><img id="post-image" src="'+posts[index].PostImg+'"></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-text c-font-alt cursor-default"><p id="post-caption" class="post-caption text-black"><span id="account-name" class="text-black">'+posts[index].PostedBy+'</span>'+caption+'</p></div><div class="post-time c-font-med"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
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
