<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Extra.php');
include('./classes/Comment.php');
include('./classes/Post.php');
include('./classes/Notify.php');

include('./classes/Image.php');

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        echo 'Not logged in';
}
$react = "";
$myusername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))['0']['username'];
$meimage = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['profileimg'];

if (is_null($meimage)) {
        $myprofilePic = "./img/default/undraw_profile_pic_ic5t.png";
}else {
        $myprofilePic = $meimage;
}

$postid = "";
$verified = False;
$isFollowing = False;
if (isset($_GET['postid'])) {
    if (DB::query('SELECT * FROM posts WHERE id=:postid', array(':postid'=>$_GET['postid']))) {

                $postid = DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$_GET['postid']))[0]['id'];
                $pImage = DB::query('SELECT postimg FROM posts WHERE id=:postid', array(':postid'=>$_GET['postid']))[0]['postimg'];
                if (is_null($pImage)) {
                        $postImage = "./img/default/undraw_page_not_found_su7k.svg";
                        $addXtraStyling = True;
                }else {
                        $postImage = $pImage;
                        $addXtraStyling = False;
                }

                $authorId = DB::query('SELECT user_id FROM posts WHERE id=:postid', array(':postid'=>$_GET['postid']))[0]['user_id'];

                $authorName = DB::query('SELECT username FROM users WHERE id=:authorid', array(':authorid'=>$authorId))[0]['username'];
                $authorIMG = DB::query('SELECT profileimg FROM users WHERE id=:authorid', array(':authorid'=>$authorId))[0]['profileimg'];

                if (is_null($authorIMG)) {
                        $authorDp = "./img/default/undraw_profile_pic_ic5t.png";
                }else {
                        $authorDp = $authorIMG;
                }

                $postBody = DB::query('SELECT body FROM posts WHERE id=:postid', array(':postid'=>$_GET['postid']))[0]['body'];
                        $caption = Post::link_add($postBody);
                        $caption = Extra::emojiAdd($caption, "22");                   
                        $caption = preg_replace("/\r\n|\r|\n/", '<br/> ', $caption);
                        $caption = preg_replace("/8ccc1o/", '<br/> ', $caption);

                $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$authorName))[0]['verified'];
                $followerid = Login::isLoggedIn();

                $post = DB::query('SELECT * FROM posts WHERE id=:postid', array(':postid'=>$_GET['postid']))[0];


                $posted_hour = DB::query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM posts WHERE id=:postid', array(':postid'=>$postid))[0]['hours_since'];

                $posted_minute = DB::query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM posts WHERE id=:postid', array(':postid'=>$postid))[0]['minutes_since'];

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

                if (isset($_POST['comment'])) {
                    Comment::createComment($_POST['commentbody'], $_GET['postid'], $userid);
                    DB::query('INSERT INTO notifications(type, receiver, sender, extra, postid, time) VALUES (:type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>4, ':receiver'=>$authorId, ':sender'=>$userid, "extra"=>'', ':postid'=>$postid));
                }
                if (isset($_GET['react']) == True) {
                    Post::likePost($postid, $userid);
                }
                if (isset($_GET['cid'])) {
                    $commentid = $_GET['cid'];
                    Comment::likeComment($userid, $commentid, $postid);
                }
                $post = DB::query('SELECT * FROM posts WHERE id=:postid', array(':postid'=>$_GET['postid']))[0];

        $single_com = Comment::displayDesComments($postid, $userid); 

    }else {
        die('User not found!');
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
                                <li class="nav-item"><a class="nav-link" href="contact.html"><i class="icon-user-o"></i></a></li>
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
                            <a href="profile-page.html"><img src="<?php echo($myprofilePic); ?>" alt="" srcset=""></a>
                        </div>                                      
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!--================ Start Main Area =================-->

    <div class="limiter">
        <div class="container100" style="padding-top: 50px;">
            <div class="wrap-comment100 mt-4">
                <div class="row">
                    <div class="col-lg-7">
                        <!-- <div class="postimg"><img src="<?php //echo($postImage); ?>" id="postimg" class="postPage-img" <?php //if ($addXtraStyling == True) { echo 'style="height: 550px;padding: 50px;"';} ?> ></div> -->  

                        <?php
                            if (empty($pImage)) {
                                echo '<div class="postimg"><img src="';
                                echo './img/default/undraw_page_not_found_su7k.svg';
                                echo '" id="postimg" class="postPage-img" style="height: 550px;padding: 50px;"></div>';
                            }else {
                                echo '<div class="postimg"><img src="';
                                echo($postImage);
                                echo '" id="postimg" class="postPage-img"></div>';
                            }
                        ?>                      
                    </div>
                    <div class="col-lg-5">
                        <div class="postArea">
                            <div class="postAuthor">
                                <div class="postPageHeader">
                                    <a href="" class="profile-pic">
                                        <img id="profile-picture" src="<?php echo($authorDp); ?>" style="height: 50px;width:50px;">
                                    </a>
                                    <div class="postAuthorNameSec">
                                        <h5 class="postAuthorName"><?php echo($authorName); ?></h5>
                                    </div>
                                    <a href="#" class="post-settings-menu"><i class="icon-ellipsis-v"></i></a>
                                </div>
                            </div>
                            <div class="comments-section">
                                <?php
                                    if (empty($postBody)) {
                                        echo '';
                                    }else {
                                        echo '<div class="single-comment">
                                                <div class="commentContainer mycomment">
                                                    <a href="" class="commenter-dp">
                                                        <img src="';
                                                        echo($authorDp);
                                                        echo '">';
                                              echo '</a>
                                                    <div class="theComment ml-2">
                                                        <p class="mb-2"><span class="commenterName">';
                                                        echo($authorName);
                                                        echo '</span>';
                                                        echo($caption);
                                                        echo'</p>';
                                              echo '</div>
                                                    
                                                </div>
                                              </div>';
                                    }

                                    ///// Comments

                                    if (empty($single_com)) {
                                        if(empty($postBody)) {
                                            echo '<div class="nunFound-img">
                                                    <img src="./img/default/undraw_not_found_60pq.svg" style="width: 190px;">
                                                    <h3 class="para-light nf-heading m-lr-auto">Be the first to comment</h3>
                                                </div>';
                                        }
                                    }else {
                                        echo($single_com);
                                    }
                                ?>
                                
                            </div>
                            <div class="post-tags">
                                <form name="reactform" class="reaction-area" id="react" action="comment-page.php?react=True&postid=<?php echo($postid); ?>" method="post">
                                    <?php
                                     if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postid, ':userid'=>$followerid))) {

                                        echo '<button type="submit" name="like" class="single-reaction react-btn"><i class="reaction-icon-size far fa-heart"></i></button>';
                                    }else {
                                        echo '<button type="submit" name="unlike" class="single-reaction react-btn active"><i class="reaction-icon-size far fa-heart"></i></button>';
                                    }
                                    
                                    ?>
                                    <a href="" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a>
                                    <a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a>
                                    <a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o" style="margin-right: 0;"></i></a>
                                </form>
                                <div class="likes-section mt-2">
                                    <p id="reactions" class="likes mb-0 red"><?php echo($post['likes']); ?> likes</p>
                                </div>
                                <div class="post-time mt-1"><p class="mb-0"><?php echo($since); ?></p></div>
                            </div>
                            <div class="commentInputArea py-2">
                                <form action="comment-page.php?postid=<?php echo($_GET['postid']); ?>" method="post" class="form-inline">
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="commentbody" placeholder="Add your comment..."></input>
                                    </div>
                                    <button class="btn v-align" type="submit" name="comment">Post</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--================ End Main Area =================-->
    

    <!-- Jquery js file -->
    <script src="./js/jquery-3.2.1.min.js"></script>
    
    <script src="./js/jquery.sticky.js"></script>
    <!-- Main-JS -->
    <script src="js/main.js"></script>
    
    <!-- bootstrap js -->
    <script src="./js/bootstrap.min.js"></script>

    <!--OwlCarousel Script-->
    <script src="./vendors/owl-carousel/owl.carousel.min.js"></script>

    <!-- Main-JS -->
    <script type="text/javascript"> 
        function submitform() {   document.reactform.submit(); } 
        ////
        $allLeftCompo = 280;

        $height = $('.postimg').height();
        $(".wrap-comment100").css("height", $height);

        var nheight = $height - $allLeftCompo;
        $(".comments-section").css("height", nheight);
        $(".nunFound-img").css("height", nheight);
   </script>
</body>
</html>
