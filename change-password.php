<?php
include('./classes/DB.php');
include('./classes/Login.php');

$tokenIsValid = False;
if (Login::isLoggedIn()){

	if(isset($_POST['changepassword'])) {

		$oldpassword = $_POST['oldpassword'];
		$newpassword = $_POST['newpassword'];		
		$newpasswordrepeat = $_POST['newpasswordrepeat'];
		$userid = Login::isLoggedIn();

		if (password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id=:userid', array(':userid'=>$userid)) [0]['password'])) {

			if ($newpassword == $newpasswordrepeat) {

				if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {

					DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$userid));
					echo "Password Changed Successfully!";

				}else {

				}

			}else {
				echo "Passwords Don't Match";
			}

		}else {
			echo "Incorrect Password";
		}

	}

}else {
	if (isset($_GET['token'])) {

		$token = $_GET['token'];

		if (DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))) {
			$userid = DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			$tokenIsValid = True;

			if(isset($_POST['changepassword'])) {

				$newpassword = $_POST['newpassword'];		
				$newpasswordrepeat = $_POST['newpasswordrepeat'];

					if ($newpassword == $newpasswordrepeat) {

						if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {

							DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$userid));
							echo "Password Changed Successfully!";
							DB::query('DELETE FROM password_tokens WHERE user_id=:userid', array(':userid'=>$userid));

						}

					}else {
						echo "Passwords Don't Match";
					}
			}

		}else {
			die ('Token Invalid');
		}
		
}else {
	die ("Not Logged In") ;
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
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-l-85 p-r-85 p-t-55 p-b-55">
				<form class="login100-form validate-form flex-sb flex-w" method="post" action="<?php if (!$tokenIsValid) { echo 'change-password.php'; } else { echo 'change-password.php?token='.$token.''; } ?>">

					<span class="login100-form-title p-b-32">
						Change Password
					</span>

					
<?php if (!$tokenIsValid) { echo '<span class="txt1 p-b-11">Current Password</span><div class="wrap-input100 validate-input m-b-36" data-validate = "New Password"><input class="input100" type="password" name="oldpassword" value="" placeholder=""><span class="focus-input100"></span></div>'; } ?>


					<span class="txt1 p-b-11">
						New Password
					</span>
					<div class="wrap-input100 validate-input m-b-36" data-validate = "New Password">
						<input class="input100" type="password" name="newpassword" value="" placeholder="">
						<span class="focus-input100"></span>
					</div>

					<span class="txt1 p-b-11">
						New Password Again
					</span>
					<div class="wrap-input100 validate-input m-b-36" data-validate = "New Password">
						<input class="input100" type="password" name="newpasswordrepeat" value="" placeholder="">
						<span class="focus-input100"></span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" name="changepassword" value="Change Password">
							Change Password
						</button>
					</div>

					<div class="justify-content-center mt-4">
					</div>

				</form>
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