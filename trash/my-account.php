<?php
include('./classes/DB.php');
include('./classes/Login.php');

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        die('Not logged in');
}

if (isset($_POST['uploadprofileimg'])) {
        ini_set('max_execution_time', 300);
        $image = base64_encode(file_get_contents($_FILES['profileimg']['tmp_name']));

        $options = array('http'=>array(
                'method'=>"POST",
                'header'=>"Authorization: Bearer 22534c7089622d5104cec242e1de2b1e7ade5060\n".
                "Content-Type: application/x-www-form-urlencoded",
                'content'=>$image
        ));

        $context = stream_context_create($options);

        $imgurURL = "https://api.imgur.com/3/image";

        if ($_FILES['profileimg']['size'] > 10240000) {
                die('Image too big, must be 10MB or less!');
        }

        $response = file_get_contents($imgurURL, false, $context);
        $response = json_decode($response);
        DB::query("UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':profileimg'=>$response->data->link, ':userid'=>$userid));
        ini_set('max_execution_time', 300);

        
}

 $exploreQuery = DB::query('SELECT * FROM ( SELECT posts.*, comments.post_id FROM `posts`, `comments` WHERE postimg IS NOT NULL AND comments.post_id = posts.id ORDER BY RAND() ) explore');
 $e = DB::query('SELECT COUNT(*)  AS temp FROM ( SELECT posts.*, comments.post_id FROM `posts`, `comments` WHERE postimg IS NOT NULL AND comments.post_id = posts.id ORDER BY RAND() ) explore')[0]['temp'];

$num = $e/3;

$res = DB::query('SELECT * FROM ( SELECT posts.*, comments.post_id FROM `posts`, `comments` WHERE postimg IS NOT NULL AND comments.post_id = posts.id ORDER BY RAND() ) explore LIMIT 3');
$res2 = DB::query('SELECT * FROM ( SELECT posts.*, comments.post_id FROM `posts`, `comments` WHERE postimg IS NOT NULL AND comments.post_id = posts.id ORDER BY RAND() ) explore LIMIT 3 OFFSET 3');

/*

$dret = DB::query('SELECT DISTINCT users.`id` FROM followers, users WHERE users.`id` = followers.`user_id` AND follower_id = :userid', array(':userid'=>$userid));

foreach ($dret as $dre) {
        foreach ($dre as $m) {
                $tree = DB::query('SELECT id FROM users WHERE id != :mid', array(':mid'=>$m));
                foreach ($tree as $k) {
                        foreach ($k as $trap) {
                                $names = DB::query('SELECT username FROM users WHERE id = :mid', array(':mid'=>$trap));
                                foreach ($names as $listname) {
                                        $keyname = $listname['username'];
                                        echo($keyname);
                                        echo "<br/>";
                                        
                                }
                        }
                }
        }                
}*/

$sCreators = DB::query('SELECT DISTINCT stories.user_id FROM stories, users, followers WHERE stories.user_id = followers.user_id AND followers.follower_id = :myid', array(':myid'=>$userid));

foreach ($sCreators as $sids) { ##### MEANING FOR EACH PERSON DO THIS....
        $subCC = $sids['user_id'];
        $singleSPERSON = DB::query('SELECT user_id, content FROM stories WHERE user_id = :hisid', array(':hisid'=>$subCC));
        
        echo "<br/><br/>";

        echo "PERSON<br>";
        
        foreach ($singleSPERSON as $ssp) { #### MEANING GROUP OF STORY CONTENTS PER PERSON
                $scontent = $ssp;

                /*foreach ($scontent as $image) {
                        echo "image : ".$image."<br>";
                }*/
                $name = DB::query('SELECT username FROM users WHERE users.id = :hisid', array(':hisid'=>$scontent['user_id']))[0]['username'];
                
                echo "This image: ".$scontent['content']."will have its own a tag by ".$scontent['user_id']."<br>";
        }
        echo($name);
        echo "END <hr/>";
}

?>
<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
        Upload a profile image:
        <input type="file" name="profileimg">
        <input type="submit" name="uploadprofileimg" value="Upload Image">
</form>