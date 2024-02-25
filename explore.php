<?php
    include('./classes/DB.php');
    include('./classes/Login.php');
    include('./classes/Post.php');
    include('./classes/Notify.php');
    include('./classes/Image.php');
    include('./classes/Comment.php');

    $showTimeline = False;
    if (Login::isLoggedIn()) {
            $userid = Login::isLoggedIn();
            $showTimeline = True;

            $myusername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))['0']['username'];
            $meimage = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['profileimg'];
            $myhandle = DB::query('SELECT handle FROM users WHERE id = :userid', array(':userid'=>$userid))[0]['handle'];

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
                    if ($_FILES['postimg']['size'] == 0) {
                            Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                    } else {
                            $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                            Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
                    }
            }

            $trendingTag = DB::query('SELECT * FROM topics ORDER BY numposts DESC LIMIT 3');
            #$exploreQuery = DB::query('SELECT * FROM ( SELECT posts.*, comments.post_id FROM `posts`, `comments` WHERE postimg IS NOT NULL AND comments.post_id = posts.id ORDER BY RAND() ) explore');

    } else {
            die('Not logged in');
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
    <link rel="stylesheet" href="./css/addsIn.css">
    
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
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><ion-icon name="paper-plane-outline"></ion-icon></a><span class="division-border"></span></div>                            
                        </div>
                        <div class="profile-picture ml-20">
                            <a href="userprofile.php"><img src="<?php echo($myprofilePic); ?>" alt="" srcset="" style="height: 30px;"></a>
                        </div>
                        <div class="username">
                            <a href="userprofile.php" class="text-black"><h7><?php echo($myusername); ?></h7></a>
                        </div>                 
                    </div>                    
                </div>
            </nav>
        </div>
    </header>
	<!--================ End Header Area =================-->

	<!--================ Start Main Area =================-->
	<body class="explore-page site-main" style="margin-top: 3rem;">

        <section class="feed-area body">
            <div class="container">
                <div class="row">
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
                                            <li class="nav-item d-block">
                                                <a href="indexfeed.php" class="nav-link d-flex flex-start" style="padding: .5rem .9rem;">
                                                    <span class="iconify" style="font-size: 22px;margin-right: 12px;" data-icon="octicon:home-24" data-inline="false"></span>
                                                    <span>Home</span>
                                                </a>
                                            </li>
                                            <li class="nav-item d-block active">
                                                <a href="explore.php" class="nav-link d-flex flex-start c-font-med" style="padding: .5rem .9rem; background: none !important; -webkit-text-fill-color: #555 !important;">
                                                    <ion-icon style="font-size: 22px; margin-right: 12px;" name="compass"></ion-icon>
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
                                                <a href="activity-page.php" class="nav-link d-flex flex-start">
                                                    <i class="ti-bell v-align mr-3"></i>
                                                    <span>Activity</span>
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
                    <div class="col-lg-9">
                        <div class="stories-area-alt rounded-container">
                            <h5 class="ra-heading mb-3">Top Stories</h5>
                            <div class="stories owl-carousel owl-theme row">
                                
                                <div class="single-story d-block ml-20">
                                    <div class="ss-image-body">
                                        <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/about_part_img.png');">
                                            <div class="ss-ni-content">
                                                <div class="text-white para mb-0 d-flex flex-start justify-content-center">
                                                    <div class="dp-area"><img src="./img/post/person_1.jpg"></div>
                                                    <p class="v-align mb-0">@kevinfeigi</p>
                                                </div>
                                            </div>
                                        </a>                                                    
                                    </div>
                                </div>
                                <div class="single-story d-block ml-20">
                                    <div class="ss-image-body">
                                        <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/creative_img.png');">
                                            <div class="ss-ni-content">
                                                <div class="text-white para mb-0 d-flex flex-start justify-content-center">
                                                    <div class="dp-area"><img src="./img/post/post_8.png"></div>
                                                    <p class="v-align mb-0">@makeitup</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="single-story d-block ml-20">
                                    <div class="ss-image-body">
                                        <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/gallery_item_1.png');">
                                            <div class="ss-ni-content">
                                                <div class="text-white para mb-0 d-flex flex-start justify-content-center">
                                                    <div class="dp-area"><img src="./img/post/person_4.jpg"></div>
                                                    <p class="v-align mb-0">@mannie</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="single-story d-block ml-20">
                                    <div class="ss-image-body">
                                        <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/post/monika-grabkowska-TAj4X5-eRqE-unsplash.jpg');">
                                            <div class="ss-ni-content">
                                                <div class="text-white para mb-0 d-flex flex-start justify-content-center">
                                                    <div class="dp-area"><img src="./img/post/person_3.jpg"></div>
                                                    <p class="v-align mb-0">@yokiho</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="single-story d-block ml-20">
                                    <div class="ss-image-body">
                                        <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/project_1.png');">
                                            <div class="ss-ni-content">
                                                <div class="text-white para mb-0 d-flex flex-start justify-content-center">
                                                    <div class="dp-area"><img src="./img/post/person_2.jpg"></div>
                                                    <p class="v-align mb-0">@youtube</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="single-story d-block ml-20">
                                    <div class="ss-image-body">
                                        <a href="" class="ss-img d-table border-radius" style="background-image: url('./img/post/eiliv-sonas-aceron-ZuIDLSz3XLg-unsplash.jpg');">
                                            <div class="ss-ni-content">
                                                <div class="text-white para mb-0 d-flex flex-start justify-content-center">
                                                    <div class="dp-area"><img src="./img/post/person_5.jpg"></div>
                                                    <p class="v-align mb-0">@stevebiko</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="image-gallery" style="margin-top: 4rem;">
                                    
                            <div class="images">
                                <div class="row">
                                    
                                    <div class="col-md-4">
                                     <?php
                                        $i = 0;
                                        $exploreQuery = DB::query('SELECT * FROM posts WHERE postimg IS NOT NULL');
                                        foreach ($exploreQuery as $star) {
                                            if (!empty($star['postimg'])) {
                                                $tree['"img'.++$i.'"'] = $star['postimg'];
                                            }
                                        }

                                        foreach (array_slice($tree, 0, 3) as $imgX) {

                                            $openD = '<div class="single-img-item"><div class="item-img"><a href=';
                                            $pLink = '"comment-page.php?postid=" ';
                                            $twoD = 'class="exp-img" style="display: inline-block;width: 100%;"><img src=';
                                            $photo = '"'.$imgX.'" style="width: 100%;">';
                                            $threeD = '</a></div></div>';

                                            echo ''.$openD.$pLink.$twoD.$photo.$threeD;
                                        } 
                                        
                                    ?>                                           
                                    </div>

                                    <div class="col-md-4">
                                     <?php
                                        foreach (array_slice($tree, 3, 3) as $imgX) {

                                            $openD = '<div class="single-img-item"><div class="item-img"><a href=';
                                            $pLink = '"comment-page.php?postid=" ';
                                            $twoD = 'class="exp-img" style="display: inline-block;width: 100%;"><img src=';
                                            $photo = '"'.$imgX.'" style="width: 100%;">';
                                            $threeD = '</a></div></div>';

                                            echo ''.$openD.$pLink.$twoD.$photo.$threeD;
                                        }
                                    ?>                                           
                                    </div>

                                    <div class="col-md-4">
                                    <?php
                                        foreach (array_slice($tree, 6, 3) as $imgX) {

                                            $openD = '<div class="single-img-item"><div class="item-img"><a href=';
                                            $pLink = '"comment-page.php?postid=" ';
                                            $twoD = 'class="exp-img" style="display: inline-block;width: 100%;"><img src=';
                                            $photo = '"'.$imgX.'" style="width: 100%;">';
                                            $threeD = '</a></div></div>';

                                            echo ''.$openD.$pLink.$twoD.$photo.$threeD;
                                        }
                                    ?>                                           
                                    </div>
                                    
                                    <!--<div class="col-md-4">
                                        <div class="single-img-item">
                                            <div class="item-img">
                                                <a href="#" class="exp-img" style="display: inline-block;width: 100%;">
                                                    <img src="./img/post/emma-matthews-digital-content-production-O_CLjxjzN3M-unsplash.jpg" style="width: 100%;">
                                                </a>
                                            </div>
                                        </div> 
                                        <div class="single-img-item">
                                            <div class="item-img">
                                                <a href="#" class="exp-img" style="display: inline-block;width: 100%;">
                                                    <img src="./img/post/img_6.jpg" style="width: 100%;">
                                                </a>
                                            </div>
                                        </div>                                         
                                    </div>
                                    <div class="col-md-4">
                                        <div class="single-img-item">
                                            <div class="item-img">
                                                <a href="#" class="exp-img" style="display: inline-block;width: 100%;">
                                                    <img src="./img/post/dilyara-garifullina-7au0RszbLNc-unsplash.jpg" style="width: 100%;">
                                                </a>
                                            </div>
                                        </div> 
                                        <div class="single-img-item">
                                            <div class="item-img">
                                                <a href="#" class="exp-img" style="display: inline-block;width: 100%;">
                                                    <img src="./img/gallery_item_2.png" style="width: 100%;">
                                                </a>
                                            </div>
                                        </div>                                         
                                    </div>-->
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
    <script src="./vendor/bootstrap/js/popper.js"></script>
	<script src="./vendor/bootstrap/js/bootstrap.min.js"></script>

	<!--OwlCarousel Script-->
	<script src="./vendors/owl-carousel/owl.carousel.min.js"></script>

    <script src="https://code.iconify.design/1/1.0.7/iconify.min.js"></script>

	<!-- Icons-JS -->
	<script src="./js/main.js"></script>
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>

    <!-- Main-JS -->
</body>
</html>