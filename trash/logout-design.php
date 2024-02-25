<?php
include('./classes/DB.php');
include('./classes/Login.php');

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
        $myusername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))['0']['username'];
		$myprofilePic = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['profileimg'];
}else {
	$myusername = "Account Name";
	$myprofilePic = "./img/default/undraw_profile_pic_ic5t.png";
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
                                       
                    <!-- Collect the nav links, forms, and other content for toggling --> 
                    
                    <div class="user-area mt-0">
                        <div class="right-corner-nav flex-start v-align">
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="ti-bell"></i></a></div>
                            <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="icon-envelope-o"></i></a><span class="division-border"></span></div>                            
                        </div>
                        <div class="profile-picture ml-20">
                            <a href="profile-page.html"><img src=<?php echo($myprofilePic); ?> alt="" srcset=""></a>
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

<div class="logout-section section section-gradient-bg sign-up-area" style="height: 100vh;">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="main-content text-center" style="margin-top: 100px">
					<div class="section-title">
						<div class="d-block title-icon">
							<div class="icon-container">
								<span class="icon icon-sign-out"></span>
							</div>
						</div>
						<h1 class="text-white title-h1 text-uppercase">Log out of your Account</h1>
					</div>
					<p class="para">
<?php

if (!Login::isLoggedIn()) {
	echo "Not Logged In";
	echo "<br ?>";
	die('<a href="login-page.php" style="color: #fff;">Click Here to login</a>');
}

$isFollowing = False;

if (isset($_POST['confirm'])) {

	if (isset($_POST['alldevices'])) {

		DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid'=>Login::isLoggedIn()));
		echo "Successfully Logged Out";

	}else {

		if (isset($_COOKIE['SNID'])) {
			DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
		}		
		setcookie('SNID', '1', time()-3600);
		setcookie('SNID_', '1', time()-3600);
	}

}

?>						
					</p>			
					<p class="para text-white">Are You Sure You Wish To Logout?</p>
					<form action="logout-design.php" method="post" class="para text-white">
						<input type="checkbox" name="alldevices" value="alldevices"> Log out on all devices<br />
						<input type="submit" name="confirm" value="confirm" class="btn tertiary-button mt-2 mr-4 text-uppercase">
					</form>
				</div>
			</div>
		</div>
							
	</div>
		
</div>
	

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