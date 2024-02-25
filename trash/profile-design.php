<?php
include('./classes/DB.php');
include('./classes/Login.php');

$username = "";
$verified = False;
$isFollowing = False;

if (isset($_GET['username'])) {
	if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {

		$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
		$userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
		$verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
		$followerid = Login::isLoggedIn();


		if (isset($_POST['follow'])) {	

			if ($userid != $followerid) {
				if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
					if ($followerid == 6) {
						DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
					}
					DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
				} else {
					echo "Already Following!";
				}
				$isFollowing = True;
			}				
		}


		if (isset($_POST['unfollow'])) {			
			if ($userid != $followerid) {
				if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
					if ($followerid == 6) {
						DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
					}
					DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
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

			if ($LoggedInUserId == $userid) {
				DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody'=>$postbody, ':userid'=>$userid));
			}else {
				die('Incorrect User!');
			}
			
		}

		$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
		$posts = "";
		$head_forPost = '<div class="post" style="width: 500px; padding-bottom:1rem;">
                                    <div class="post-head flex-start">
                                        <a href="#" class="post-compiler">
                                            <div class="profile-pic story-active">
                                            	<img id="profile-picture" src="./img/people/person_1.jpg" alt="">
                                            </div>
                                            <div class="post-by">
                                                <h5 class="post-by-title">';
        $secondaryHead_forPost = '</h5>
                                                <p class="location mb-0">Somewhere On Earth</p>
                                            </div>
                                        </a>
                                        <a href="#" class="post-settings-menu">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>
                                    </div>
                                    <div class="post-text para" style="color: #000;">';
        $reactionArea_forPost = '</div><div class="post-tags mt-4">                                        
                                                    <div class="reaction-area">
                                                        <a href="#" class="single-reaction active"><i class="reaction-icon-size far fa-heart"></i></a>
                                                        <a href="" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a>
                                                        <a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a>
                                                        <a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a>
                                                    </div>
                                                    <div class="likes-section mt-2">
                                                        <p id="reactions" class="likes mb-0 red"> -';
        $timestamp_forPost = ' likes</p></div></div>
        						<div class="post-time mt-1"><p class="mb-0">';

		foreach($dbposts as $p) {
$posts .= $head_forPost.$userid.$secondaryHead_forPost.htmlspecialchars($p['body']).$reactionArea_forPost.$timestamp_forPost.$p['posted_at']."</p></div></div></div><br />";
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


<h1><?php echo $username ?>'s Profile <?php if ($verified) { echo '- Verified';}; ?></h1>
<form action="profile-design.php?username=<?php echo $username; ?>" method="post">
	<?php 
		if ($userid != $followerid) {
			if ($isFollowing) {
				echo '<input type="submit" name="unfollow" value="Unfollow">';
			}else {
				echo '<input type="submit" name="follow" value="Follow">';
			}
		}		

	?>
</form>

<form action="profile.php?username=<?php echo $username; ?>" method="post">
	<textarea name="postbody" rows="8" cols="80"></textarea>
	<input type="submit" name="post" value="Post">
</form>


	<div class="posts">
		<?php echo $posts; ?>
	</div>

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