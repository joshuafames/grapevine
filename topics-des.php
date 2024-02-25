<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');


if (isset($_GET['topic'])) {

	if (DB::query('SELECT topics FROM posts WHERE FIND_IN_SET(:topic, topics)', array(':topic'=>$_GET['topic']))) {
		
		$posts = DB::query('SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics)', array(':topic'=>$_GET['topic']));

		foreach ($posts as $post) {
			echo $post['body']."<hr />";
		}

	}

}else {
	die('No Topic Identified');
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
                            <a href="profile-page.php?username=<?php echo($myusername) ?>"><img src=<?php echo($myprofilePic); ?> alt="" srcset=""></a>
                        </div>
                        <div class="username">
                            <h7><?php echo($myusername); ?></h7>
                        </div>                 
                    </div>                    
				</div>
			</nav>
		</div>
	</header>
	<!--================ End Header Area =================-->

	<!--================ Start Main Area =================-->
	<body class="site-main mt">

        <section class="feed-area body">
            <div class="container">
                <div class="row">
                    <!--left area-->
                    <div class="col-lg-3 left-profile-area feed">
                        <div class="profile sticky-container">
                            <div class="profile-info left-nav left">
                                <div class="floating-image">
                                    <img src="" alt="" srcset="" style="height: 100px;">
                                </div>
                                <div class="info-area">
                                    <div class="profile-id text-center mb-4">
                                        <h5 class="name"><?php echo('My usernamee'); ?></h5>
                                        <h7 class="handle">@famesdababy</h7>
                                    </div>                                    
                                    <div class="nav-section vertical-navbar">
                                        <ul class="nav navbar iconic-vertical">
                                            <li class="nav-item d-block active">
                                                <a href="#" class="nav-link d-flex flex-start">
                                                    <i class="ti-home v-align mr-3"></i>
                                                    <span>Home</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block">
                                                <a href="#" class="nav-link d-flex flex-start">
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
                                            <li class="nav-item d-block">
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
                                                <a href="logout-design.php" class="nav-link d-flex flex-start">
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
                        <div class="stories-area rounded-container carousel">
                            <div class="area-heading flex-start">
                                <h5 class="ra-heading mb-3">Stories</h5>
                                <div class="ml-auto section-left-button">
                                    <a href="#" class="watch-all-btn flex-start">
                                        <div class="iconic-button mr-2">
                                            <i class="fa fa-play"></i>
                                        </div>
                                        <p class="para mb-0 ra-heading">Watch All</p>
                                    </a>
                                </div>                                    
                            </div>
                            <div class="stories owl-carousel owl-theme row">
                                <div class="item">
                                    <div class="single-story">
                                        <div class="s-content">
                                            <div class="s-new"><a href="#" class="s-new-item-icon" style="background:url(<?php echo($myprofilePic) ?>) !important; background-size: cover !important;"><i class="ti-plus"></i></a></div>
                                            <div class="s-title"><p class="para text-size-15">New</p></div>
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
                        <div class="mca row">
                            <div class="col-lg-7">
                                                    
                                <div class="sticky-container add-post-section mt-4 rounded-container">
                                    <h5 class="ra-heading mb-3">Post Something</h5>
                                    <div class="add-post-area flex-start">
                                        <div class="profile-picture">
                                            <img src=<?php echo('myprofilePic'); ?> alt="" srcset="">
                                        </div>
                                        <div class="ap-input-area v-align"><p class="para mb-0 ml-3">What's on your mind?</p></div>
                                        <div class="ap-image-icon v-align"><i class="ti-image"></i></div>
                                    </div>
                                </div>
<div class="posts" style="margin-bottom: 4rem;">

<?php

		$followingposts = DB::query('SELECT posts.*, users.`username` FROM users, posts, followers
		WHERE posts.user_id = followers.user_id
		AND users.id = posts.user_id
		AND follower_id = :userid
		ORDER BY posts.posted_at DESC;', array(':userid'=>$userid));

		$feedposts = $followingposts;
		        $posts = "";
		                $head_forPost = '<div class="post" style="width: 530px; padding-bottom:1rem;">
		                                            <div class="post-head flex-start">
		                                                <a href="#" class="post-compiler">
		                                                    <div class="profile-pic story-active">
		                                                        <img id="profile-picture" style="height: 40px;" src=';
		                $image_after =                          'alt="">
		                                                    </div>
		                                                    <div class="post-by">
		                                                        <h5 class="post-by-title">';
		                $secondaryHead_forPost =                '</h5>
		                                                        <p class="location mb-0">Somewhere On Earth</p>
		                                                    </div>
		                                                </a>
		                                                <a href="#" class="post-settings-menu">
		                                                    <i class="fa fa-ellipsis-v"></i>
		                                                </a>
		                                            </div>';
		                $postimg ='<div class="post-image"><img id="post-image" src=';
		                $postimg_aft = '></div>';

		                $reactionArea_close = ' likes</p></div></div>';
		                
		                $posttext = '<div class="post-text" style="font-family: Poppins-Light;"><p class="post-caption"><span class="account-name">';
		                $posttext_close = '</p></div>';
		                
		                $timestamp_forPost = '<div class="post-time mt-1"><p class="mb-0">';

		$NowTime = date('Y-m-d H:i:s');

		foreach($feedposts as $post) {

		        $reactionArea_forPost = '
		                                                                <a href="comment-page.php?postid='.$post['id'].'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a>
		                                                                <a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a>
		                                                                <a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a>
		                                                            </form>
		                                                            <div class="likes-section mt-2">
		                                                                <p id="reactions" class="likes mb-0 red">';

		        $posted_hour = DB::query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['hours_since'];
		            $posted_minute = DB::query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['minutes_since'];
		            $roundDay = 24;
		            $posted_day = round($posted_hour/$roundDay);

		            if ($posted_hour < 1) {
		                    $timeNumber = $posted_minute;
		                    $when = " Minutes Ago";
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

		            $since = "$timeNumber $when";

		        if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid))) {
		                $likeButtonRed = '<div class="post-tags mt-4">
		                                        <form name="reactform" class="reaction-area" id="react" action="feed.php?postid='.$post['id'].'" method="post">
		                                                <button type="submit" name="like" class="single-reaction react-btn"><i class="reaction-icon-size far fa-heart"></i></button>';
		                        
		        }else {
		                $likeButtonRed = '<div class="post-tags mt-4">
		                                        <form name="reactform" class="reaction-area" id="react" action="feed.php?postid='.$post['id'].'" method="post">
		                                                <button type="submit" name="unlike" class="single-reaction react-btn active"><i class="reaction-icon-size far fa-heart"></i></button>';
		        }
		        $profileIMG = DB::query('SELECT profileimg FROM users WHERE id=:authorid', array(':authorid'=>$post['user_id']))[0]['profileimg'];
		        if (is_null($profileIMG)) {
		                $profilePic = $defaultimg;
		        }else {
		                $profilePic = $profileIMG;
		        }

		        $posts .= $head_forPost.$profilePic.$image_after.$post['username'].$secondaryHead_forPost.$postimg.$post['postimg'].$postimg_aft.$likeButtonRed.$reactionArea_forPost.$post['likes'].$reactionArea_close.$posttext.$post['username']."</span>".htmlspecialchars($post['body']).$posttext_close.$timestamp_forPost.$since."</p></div></div>";
		} 
        echo($posts);


?>
            
</div>
                        </div>

                            <div class="col-lg-5">
                                <div class="sticky-container left-pp-area">
                                    <div class="trending-tags-section rounded-container mt-4">
                                        <h6 class="lc-section-heading mb-3">Trending Hashtags</h6>
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
                                    </div>
                                    <div class="to-follow-section rounded-container mt-4">
                                        <h6 class="lc-section-heading mb-3">People to Follow</h6>
<?php 
    $peopleToFollow = DB::query('SELECT username, profileimg FROM users, followers WHERE followers.follower_id != :userid AND users.id != :userid  LIMIT 3', array(':userid'=>$userid));

    foreach ($peopleToFollow as $ptf) {

        $FstLine = '<div class="single-section-item mt-10 flex-start">';
        $SndLine = '<div class="user-profile-image">';
        $TrdLine = '<a href="#"><img src=';
        
        $Tclose = '></a>';
        $FthLine = '</div><a href="';
        
        $profileLink = 'profile-page.php?username=';

        $FTopen = '" class="v-align single-item-name ml-10">';
        $FTclose = '</a></div>';

        $profilePic = DB::query('SELECT profileimg FROM users WHERE username=:username', array(':username'=>$ptf['username']))[0]['profileimg'];
        if (is_null($profilePic)) {
            $dp = "./img/default/undraw_profile_pic_ic5t.png";
        }else {
            $dp = $ptf['profileimg'];
        }
        echo $FstLine.$SndLine.$TrdLine.$dp.$Tclose.$FthLine.$profileLink.$ptf['username'].$FTopen.$ptf['username'].$FTclose;
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
        </section>

	</body>
	<!--================ End Main Area =================-->
	

	<!-- Jquery js file -->
	<script src="./js/jquery-3.2.1.min.js"></script>
	
	<!-- bootstrap js -->
	<script src="./js/bootstrap.min.js"></script>

	<!--OwlCarousel Script-->
	<script src="./vendors/owl-carousel/owl.carousel.min.js"></script>

	<!-- Main-JS -->
	<script src="./js/main.js"></script>
</body>
</html>