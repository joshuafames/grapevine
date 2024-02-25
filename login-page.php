<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('classes/DB.php');


	if (isset($_POST['login'])) {
							
		$username = $_POST['username'];
		$password = $_POST['password'];

		if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {

			if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username)) [0]['password'])) {

				echo "Logged In";
				$cstrong = True;
				$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));								
				$user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
				DB::query('INSERT INTO login_tokens(token, user_id) VALUES (:token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));

				setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
				setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
				header('Location: indexfeed.php');

			}else {
				echo "Incorrect Password";
			}

		}else {
			echo "User Not Registered";
		}

	}else if (isset($_POST['login-sm'])) {
		$usernameSM = $_POST['username-sm'];
		$passwordSM = $_POST['password-sm'];

		if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$usernameSM))) {

			if (password_verify($passwordSM, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$usernameSM)) [0]['password'])) {

				echo "Logged In";
				$cstrongSM = True;
				$tokenSM = bin2hex(openssl_random_pseudo_bytes(64, $cstrongSM));								
				$user_idSM = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$usernameSM))[0]['id'];
				DB::query('INSERT INTO login_tokens(token, user_id) VALUES (:token, :user_id)', array(':token'=>sha1($tokenSM), ':user_id'=>$user_idSM));

				setcookie("SNID", $tokenSM, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
				setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
				header('Location: indexfeed.php');

			}else {
				echo "Incorrect Password";
			}

		}else {
			echo "User Not Registered";
		}

	}

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
		<link rel="stylesheet" type="text/css" href="css/mobile.css">
		<link rel="stylesheet" type="text/css" href="css/login-temp.css">
	<!--===============================================================================================-->
</head>
<body>
	<div class="limiter md-sm-only">
		<div class="container">

			<div class="row">
				<div class="col-12">
					<form class="login100-form validate-form-sm" method="post">
					<!-------------------------------------------------------------------->

							<span class="login100-form-title"><div class="logo-icon m-b-30"><img src="./img/core-img/Gv-icon.png" alt="" srcset="" style="height: 40px;"></div>
								LOGIN
							</span>

							<div class="wrap-input100 validate-input-sm" data-validate = "Valid email is required: ex@abc.xyz">
								<input class="input100" type="text" name="username-sm" placeholder="Username">
								<span class="focus-input100"></span>
								<span class="symbol-input100">
									<i class="fa fa-user" aria-hidden="true"></i>
								</span>
							</div>

							<div class="wrap-input100 validate-input-sm" data-validate = "Password is required">
								<input class="input100" type="password" name="password-sm" placeholder="Password">
								<span class="focus-input100"></span>
								<span class="symbol-input100">
									<i class="fa fa-lock" aria-hidden="true"></i>
								</span>
							</div>

							<div class="container-login100-form-btn">
								<button class="login100-form-btn" name="login-sm" type="submit">
									Login
								</button>
							</div>

							<div class="text-center p-t-12">
								<span class="txt1">
									Forgot
								</span>
								<a class="txt2" href="forgot-password.php">
									Username / Password?
								</a>
							</div>

							<div class="text-center p-t-80">
								<a class="txt2" href="create-account.php">
									Create your Account
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
								<p class="txt3 text-white">Join The Vine today.</p><a href="create-account.php" class="signup-link txt1 text-white">Sign Up Here</a>
								<div class="svg-area"><img src="./img/default/undraw_Login_re_4vu2.svg" alt="" srcset="" style="width: 420px;"></div>
							</div>
						</div>
						<div class="col-lg-7">

							<form class="login100-form validate-form" method="post">
								<!-------------------------------------------------------------------->

									<span class="login100-form-title">
										Account Login
									</span>

									<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
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

									<div class="container-login100-form-btn">
										<button class="login100-form-btn" name="login" type="submit">
											Login
										</button>
									</div>

									<div class="text-center p-t-12">
										<span class="txt1">
											Forgot
										</span>
										<a class="txt2" href="forgot-password.php">
											Username / Password?
										</a>
									</div>

									<div class="text-center p-t-80">
										<a class="txt2" href="create-account.php">
											Create your Account
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
	<script type="text/javascript">
		
	</script>

</body>
</html>
