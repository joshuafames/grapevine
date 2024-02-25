<?php
include('classes/DB.php');
include('classes/Extra.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (isset($_POST['createaccount'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	echo("WORKING");
	if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
		echo("USER DOES NOT EXIST YET");
		if (strlen($username) >= 3 && strlen($username) <= 32) {

			if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

				if (strlen($password) >= 6 && strlen($password) <= 60) {
					if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

						if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
							echo("WE ARE ALL GOOD!!");
							DB::query('INSERT INTO users(username, password, email) VALUES (:username, :password, :email)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));

							$uid = DB::query('SELECT id FROM users WHERE username = :username', array(':username'=>$username))[0]['id'];
							DB::query('INSERT INTO profileinfo VALUES (:uid, NULL, NULL)', array(':uid'=>$uid));
							
							echo "Success!";
							$cstrong = True;
								$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));								
								$user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
								DB::query('INSERT INTO login_tokens(token, user_id) VALUES (:token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));

								setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
								setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

								header('Location: '.'welcome-setup.php');
							echo "Logged In";
						} else {
							echo "Email Already In Use";
						}
					}else {
						echo "Invalid Email";
					}
				} else {
					echo "Your Password Must Have More Than 6 Characters";
				}

			}else {
				echo "Invalid Username";
			}			
		}
	}else {
		echo 'User Already Exists';
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Grapevine Sign - Up</title>
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
		<link rel="stylesheet" type="text/css" href="css/mobile.css">
		<link rel="stylesheet" type="text/css" href="css/login-temp.css">
	<!--===============================================================================================-->
</head>
<body>
	<div class="limiter md-sm-only">
		<div class="navbar justify-content-center">
			<span class="close-icon"><i class="fa fa-close"></i></span>
			<div class="logo-icon"><img src="./img/core-img/Gv-icon.png" alt="" srcset="" style="height: 40px;width: 40px;"></div>
		</div>
		<div class="container">
				<div class="row">
					<div class="col-12">

						<form class="login100-form validate-form" method="post" action="create-account.php">
							

							<!-------------------------------------------------------------------->

								<span class="login100-form-title">
									Join The Grapevine!
								</span>

								<div class="wrap-input100 validate-input" data-validate = "Valid username is required: ex@abc.xyz">
									<input class="input100" type="text" name="name" placeholder="Name">
									<span class="focus-input100"></span>
									<span class="symbol-input100">
										<i class="fa fa-user" aria-hidden="true"></i>
									</span>
								</div>

								<div class="wrap-input100 validate-input" data-validate = "Valid username is required: ex@abc.xyz">
									<input class="input100" type="text" name="username" placeholder="Username">
									<span class="focus-input100"></span>
									<span class="symbol-input100">
										<i class="fa fa-user" aria-hidden="true"></i>
									</span>
								</div>

								<div class="wrap-input100 validate-input" data-validate = "Password is required">
									<input class="input100" type="password" name="password" placeholder="Password">
									<span class="focus-input100"></span>
									<span class="symbol-input100">
										<i class="fa fa-lock" aria-hidden="true"></i>
									</span>
								</div>

								<div class="wrap-input100 validate-input" data-validate = "Valid email is required">
									<input class="input100" type="text" name="email" placeholder="email">
									<span class="focus-input100"></span>
									<span class="symbol-input100">
										<i class="fa fa-envelope" aria-hidden="true"></i>
									</span>
								</div>

								<div class="container-login100-form-btn">
									<button class="login100-form-btn" name="createaccount" type="submit">
										Sign Up
									</button>
								</div>
								<div class="container-login100-form-btn">
									<button class="login100-form-btn" name="jscreateaccount" type="submit" style="background: #2d87ff !important;">
										Google
									</button>
								</div>

								<div class="text-center p-t-50">
									<a class="txt2" href="login-page.php">
										Log in to your Account
										<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
									</a>
								</div>
						</form>
					</div>							
				</div>
				
		</div>
	</div>
	<div class="limiter lg-only">
		<div class="container-login100">

				<div class="wrap-login100">
					<div class="row">
						<div class="col-lg-5">
							<div class="bg-color h-100 br-left p-l-55 p-r-75 p-t-50 p-b-55" style="background:linear-gradient(45deg, #ba54ce, #ea477c 50%, #ff7f42 100%)">
								<div class="logo-icon m-b-30"><img src="./img/core-img/Grapevine.png" alt="" srcset="" style="height: 40px;"></div>
								<p class="txt3 text-white">Don't have an account yet?</p><a href="create-account.php" class="signup-link txt1 text-white">Sign Up Here</a>
								<div class="svg-area"><img src="./img/default/undraw_Login_re_4vu2.svg" alt="" srcset="" style="width: 420px;"></div>
							</div>
						</div>
						<div class="col-lg-7">

							<form class="login100-form validate-form" id="signin" method="post" action="create-account.php">
								

								<!-------------------------------------------------------------------->

									<span class="login100-form-title">
										Join The Grapevine!
									</span>

									<div class="wrap-input100 validate-input" data-validate = "Valid username is required: ex@abc.xyz">
										<input class="input100" type="text" name="username" id="username" placeholder="Username">
										<span class="focus-input100"></span>
										<span class="symbol-input100">
											<i class="fa fa-user" aria-hidden="true"></i>
										</span>
									</div>

									<div class="wrap-input100 validate-input" data-validate = "Password is required">
										<input class="input100" type="password" name="password" id="password" placeholder="Password">
										<span class="focus-input100"></span>
										<span class="symbol-input100">
											<i class="fa fa-lock" aria-hidden="true"></i>
										</span>
									</div>

									<div class="wrap-input100 validate-input" data-validate = "Valid email is required">
										<input class="input100" type="text" name="email" id="email" placeholder="email">
										<span class="focus-input100"></span>
										<span class="symbol-input100">
											<i class="fa fa-envelope" aria-hidden="true"></i>
										</span>
									</div>

									<div class="container-login100-form-btn">
										<button class="login100-form-btn" name="createaccount" type="submit">
											Sign Up
										</button>
									</div>
									<div class="container-login100-form-btn">
										<button class="login100-form-btn" name="jscreateaccount" type="submit" style="background: #2d87ff !important;">
											Google
										</button>
									</div>

									<div class="text-center p-t-50">
										<a class="txt2" href="login-page.php">
											Log in to your Account
											<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
										</a>
									</div>
							</form>
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
	<!--<script type="text/javascript">
	$(document).ready(function () {
		$("#signin").submit(function(event) {
			event.preventDefault();

			// Get form data
			var formData = {
			    username: $("#username").val(),
			    email: $("#email").val(),
			    password: $("#password").val()
			};
			
			$.ajax({
			    type: "POST",
			    url: "api/signup", // Replace with the actual URL to your PHP processing script
			    data: formData,
			    dataType: "json", // Expects JSON response from the server
			    encode: true,
			    success: function(response) {
				// Handle the response from the server
				console.log(response);
				// Redirect to success.html on success
				window.location.href = "welcome-setup.php";

				//$("#responseMessage").text(response.message);
			    },
			    error: function(xhr, status, error) {
				console.log(xhr.responseText);
				//$("#responseMessage").text("An error occurred. Please try again later.");
			    }
			});
		});
	}); !-->
	

	</script>

</body>
</html>
