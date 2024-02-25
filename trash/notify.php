<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Notify.php');
include('./classes/Post.php');

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        echo 'Not logged in';
}

echo "<h1>Notifications</h1>";

if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid))) {
        $notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid ORDER BY id DESC', array(':userid'=>$userid));
              //$sName = DB::query('SELECT username FROM users WHERE id=:sid', array(':sid'=>$s));
              //echo($sName);
        foreach ($notifications as $n) {
                $sName = DB::query('SELECT username FROM users WHERE id=:sid', array(':sid'=>$n['sender']))[0]['username'];

                if ($n['type'] == 1) {

                        if ($n['extra'] == "") {
                                echo "New Notification BroHam!<hr />";

                        }else {
                                $extra = json_decode($n['extra']);
                                echo '<a href="#">@'.$sName.' mentioned you in a post</a> - '.$extra->postbody;
                                echo "<hr />";
                        }
                
                }elseif ($n['type'] == 2) {
                        echo $sName." liked your post!"."<hr />";
                }
      }
}/* $essay = "its all about it now
and now andddd noe
lorem ipusum";
 
$essay = preg_replace("/\r\n|\r|\n/", ' 8ccc1o ', $essay);
echo($essay);

echo "<hr/>";

$essay = explode(" ", $essay);

echo "<pre>";
print_r($essay);*/

?>