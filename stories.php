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

                </div>
            </nav>
        </div>
    </header>
    <!--================ End Header Area =================-->

    <!--================ Start Main Area =================-->
    <main class="site-main" style="margin-top: 65px;">
        <section>
            <div class="story-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="stories-area mt-100">
                                <div class="row posts-area">
                                    <div class="single-story">
                                        <div class="post-body p-rel">
                                            <div class="profileinfo p-abs flex-start">
                                                <div class="odp-section">
                                                    <img src="./img/people/person_5.jpg">
                                                </div>
                                                <h5 class="text-white fs-14 ml-2 v-align mb-0 c-font">Jakalyn Karr</h5>
                                            </div>                                                
                                            <div class="img" style="background: url('./img/eiliv-sonas-aceron-ZuIDLSz3XLg-unsplash.jpg');">
                                            </div>
                                            <div class="reaction-area">
                                                
                                            </div>
                                            <div class="comment-area flex-start mt-2">
                                                <div class="sdp-section">
                                                    <img src="./img/people/person_6.jpg">
                                                </div>
                                                <div class="comment-input-area ml-3 v-align">
                                                    <form action="comment-page.php?postid=<?php echo($_GET['postid']); ?>" method="post" class="form-inline">
                                                        <div class="input-group">
                                                            <input class="form-control" type="text" name="commentbody" placeholder="Add a comment..."></input>
                                                        </div>
                                                        <button class="btn v-align" type="submit" name="comment">
                                                            <i class="icon-paper-plane-o"></i>
                                                        </button>
                                                    </form>
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
</body>
</html>