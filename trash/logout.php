<?php
include('./classes/DB.php');
include('./classes/Login.php');

if (!Login::isLoggedIn()) {
	die("Not Logged In");
}

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

<h1>Log out of your Account</h1>
<p>Are you sure you want to do this?</p>
<form action="logout.php" method="post">
	<input type="checkbox" name="alldevices" value="alldevices"> Logout of all your devices?<br />
	<input type="submit" name="confirm" value="confirm" class="btn tertiary-button mt-2 mr-4 text-uppercase">
</form>
