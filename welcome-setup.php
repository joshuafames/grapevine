<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Image.php');
include('./classes/Post.php');
include('./classes/Comment.php');

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
        $myusername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))['0']['username'];
		$myprofilePic = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['profileimg'];
		$responsee = "";
		if (isset($_POST['submitthis'])) {
			$handle = $_POST['handle'];
			
			if (strlen($handle) >= 3 && strlen($handle)) {
				if (!DB::query('SELECT handle FROM users WHERE handle=:handle', array(':handle'=>$handle))) {

					if (preg_match('/[\'\/~`\!@#\$%\^&\*\(\)\-\+=\{\}\[\]\|;:"\<\>,\?\\\]/', $handle)) {
						$responsee = "Invalid handle due to special characters";
					}else {
						if (preg_match("/\\s/", $handle)) {
							$responsee = "Invalid handle";
						}else {
							if (preg_match("/[A-Z]/", $handle)) {
								$responsee = "Invalid handle";

							}else {

								DB::query('UPDATE users SET handle = :handle WHERE id = :userid', array(':handle'=>$handle, ':userid'=>$userid));
								Image::uploadImage('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid'=>$userid));								

								header('Location: '.'indexfeed.php');
							}			
						}
						
					}
				}else {
					$responsee = "Handle already exists";
				}
			}

								
		}
}else {
	$myusername = "Account Name";
	$myprofilePic = "./img/default/undraw_profile_pic_ic5t.png";
}

$defaultDP = "./img/default/undraw_profile_pic_ic5t.png";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Grapevine</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->	
		<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<!--===============================================================================================-->	
		<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<!--===============================================================================================-->	
		<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
	<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="css/util.css">
		<link rel="stylesheet" type="text/css" href="css/log-in.css">
	<!--===============================================================================================-->
	<!-- Themify Icons -->
    <link rel="stylesheet" href="./css/themify-icons.css">
    <!-- Icomooon Icons -->
    <link rel="stylesheet" href="./fonts/icomoon/style.css">

    <!--- Custom CSS --->
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    
    <link rel="stylesheet" href="./scss/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@700&display=swap" rel="stylesheet">
</head>

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
                            <a><img src="<?php echo($defaultDP); ?>" alt="" srcset=""></a>
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

<body>	
	<div class="limiter" style="margin-top: 20px;">
		<div class="container-login100">

				<div class="wrap-login100">
					<div class="row">
						<div class="col-lg-5">
							<div class="bg-color h-100 br-left p-l-55 p-r-75 p-t-50 p-b-55" style="background: linear-gradient(90deg, #c77efa, #854fee 50%, #6c63ff 100%);">
								<div class="logo-icon m-b-30"><img src="./img/core-img/Grapevine.png" alt="" srcset="" style="height: 40px;"></div>
								<p class="txt3 text-white">Account Sign In Was Successful! <br/><span style="font-family: Raleway-SemiBold;">Welcome to Grapevine <?php echo($myusername); ?> </span></p>
								<div class="svg-area"><img src="./img/default/undraw_Mobile_application_mr4r2.png" alt="" srcset="" style="width: 420px;"></div>
							</div>
						</div>
						<div class="col-lg-7">

							<form class="login100-form validate-form flex-sb flex-w p-l-35 p-r-75 p-t-55 p-b-55" method="post" style="height: 550px;" action="welcome-setup.php">

								<span class="login100-form-title">Welcome!</span>
								<span class="p-b-11" style="color: #888;font-size: 14px; font-family: Raleway-medium;">Let others get to know you better! You can edit these later.</span>

								<span class="p-t-20 m-t-10 m-b-5" style="font-family: Raleway-semiBold; color: #555;">Add an avatar and a username</span>

								<div class="profile-edit-area flex-start" style="width: 100%;">
									<a href="#" data-toggle="modal" data-target="#addDP" class="dp-section mr-auto text-center" style="background: url('<?php echo($defaultDP); ?>');">
										<div class="iconic">
											<i class="ti-plus"></i>
											<span class="txt1 addDp">Add dp</span>
											<span class="txt1 replaceDP" style=" display: none;">Change dp</span>
										</div>
										<input class="theDP" type="file" name="profileimg" hidden>
										
									</a>
									<div class="account-info v-align">
										<div class="wrapit">
											<div class="wrap-input100 validate-input m-b-10" data-validate = "GV handle is required">
												<input class="input100" style="padding-left: 35px;" type="text" name="handle" placeholder="your_username">
												<p style="position: absolute;top: 8px;font-size: 22px;left: 11px;">@</p>
												<span class="focus-input100"></span>
											</div>
										</div>
									</div>										
								</div>

								<div class="container-login100-form-btn" style="margin-top: 70px;">
									<button class="login100-form-btn" name="submitthis">
										Continue
									</button>
									<p class="response-text v-align" style="display: block!important;"><?php echo($responsee); ?></p>
									<p class="response-text v-align fade" id="error-response"><?php echo($responsee); ?></p>
								</div>
							</form>
						</div>							
					</div>				
				</div>

				<div class="modal fade" id="addDP" role="dialog" tabindex="-1" style="padding-top: 35px;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;">
                            <div class="modal-header justify-content-center" style="padding: 2rem 1rem">
                                <h4 class="modal-title">Upload Image</h4>
                            </div>

                            <div class="inmodal-container">
                            	<div class="inmodal-wrapper"
                            	style="height: 350px;
                            			position: relative;
		                            	width: 100%;
		                            	background: #fff;
		                            	display: flex;
		                            	align-items: center;
		                            	justify-content: center;
		                            	overflow: hidden;">
                            		<div class="inmodal-image"
                            		style="position: absolute;
                            			   height: 100%;
                            			   width: 100%;
                            			   display: flex;
                            			   align-items: center;
                            			   justify-content: center;
                            			   ">

                            			<img src="" alt=""
                            			style="width: 100%;
                            				   height: 100%;
                            				   object-fit: cover;">
                            		</div>
                            		<div class="inmodal-content">
                            			<div class="inmodal-icon" style="font-size: 100px;color: #9658fe;"><i class="icon-cloud-upload"></i></div>
                            			<div class="inmodal-text" style="font-size: 20px;color: #5B5B7B;font-weight: 500;">No file chosen, yet!</div>
                            		</div>
                            		<!---<div id="cancel-btn"
                            			style="position: absolute;
                            				   right: 15px;
                            				   top: 15px;
                            				   font-size: 20px;
                            				   cursor: pointer;
                            				   color: #9658fe;">
                            			<i class="icon-times"></i>
                            		</div>--->
                            		<div class="file-name text-white"
                            			style="position: absolute;
                            				   bottom: 0px;
                            				   height: 55px;
                            				   width: 100%;
                            				   padding: 12px 0;
                            				   font-size: 18px;
                            				   background: #e0e0e05e">
                            			Filename here
                            		</div>
                            	</div>
                            	<input id="default-btn" type="file" name="profileimg" hidden>
                            	<button onclick="defaultBtnActive()" id="custom-btn"
                            		style="margin-top: 0px;
                            			   width: 100%;
                            			   height: 55px;
                            			   display: block;
                            			   border: none;
                            			   color: #fff;
                            			   background: #9658fe;">
                            		Choose a file
                            	</button>
                            </div>
                            
                            <div class="modal-footer justify-content-center">
                                <button class="btn btn-default" type="button" data-dismiss="modal">Done</button>
                            </div>                                                    
                        </div>
                    </div>
                </div>
				
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/log-in.js"></script>

</body>
</html>
