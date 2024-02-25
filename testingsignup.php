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

	if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {

		if (strlen($username) >= 3 && strlen($username) <= 32) {

			if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

				if (strlen($password) >= 6 && strlen($password) <= 60) {
					if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

						if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {

							DB::query('INSERT INTO users(username, password, email) VALUES (:username, :password, :email)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));

							$uid = DB::query('SELECT id FROM users WHERE username = :username', array(':username'=>$username))[0]['id'];
							DB::query('INSERT INTO profileinfo VALUES (:uid, \'\', \'\')', array(':uid'=>$uid));
							
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
<form method="post" action="testingsignup.php">

<input type="password" placeholder="password" name="password">
<input type="email" placeholder="email" name="email">
<input type="username" placeholder="username" name="username">
<input type="submit" name="createaccount">

</form>
