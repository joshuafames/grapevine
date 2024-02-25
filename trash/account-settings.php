<?php
include('./classes/DB.php');
include('./classes/Login.php');

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        die('Not logged in');
}

if (isset($_POST['update'])) {
	if(!DB::query('SELECT * FROM profileinfo WHERE user_id = :userid', array(':userid'=>$userid))) {
		DB::query('UPDATE profileinfo SET `bio` = :bio WHERE user_id=:userid', array(':userid'=>$userid));
		DB::query('UPDATE profileinfo SET `handle` = :handle WHERE user_id=:userid', array(':userid'=>$userid));
	}else {
		DB::query('INSERT INTO profileinfo VALUES (\'\', :bio, :handle, NULL, :userid)', array(':bio'=>$_POST['bio'], ':handle'=>$_POST['handle'], ':userid'=>$userid));
	}	
}
$username = "troy._113";

if (preg_match('/[\'\/~`\!@#\$%\^&\*\(\)\-\+=\{\}\[\]\|;:"\<\>,\?\\\]/', $username)) {
	echo($username);
	echo " there are some nonsense stuff in there bro!";
}else {
	if (preg_match("/\\s/", $username)) {
		echo "there are spaces in that string";
	}else {
		if (preg_match("/[A-Z]/", $username)) {
			echo "Uppercase texts dont go in me";
		}else {
			echo $username;
			echo " entering because there arent any wrongs";
		}			
	}
	
}


?>

<h1>Account settings</h1>
<form action="account-settings.php" method="post">
	<input type="text" name="bio" placeholder="Bio">
	<input type="handle" name="handle" placeholder="handle">
	<input type="submit" name="update" value="update">
</form>