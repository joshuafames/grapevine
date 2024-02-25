<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');


if (isset($_GET['topic'])) {

	if (DB::query('SELECT topics FROM posts WHERE FIND_IN_SET(:topic, topics)', array(':topic'=>$_GET['topic']))) {
		
		$posts = DB::query('SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics)', array(':topic'=>$_GET['topic']));

		foreach ($posts as $post) {
			echo $post['body']."<hr />";
		}

	}

}else {
	die('No Topic Identified');
}

$exploreQuery = DB::query('SELECT * FROM posts WHERE postimg IS NOT NULL');
$tree = array();
$i = 0;

foreach ($exploreQuery as $star) {
	if (!empty($star['postimg'])) {
		#$tree['"no'.++$i.'"'] = $i;
		#$tree['"myid'.$i.'"'] = $star['user_id'];		
		$tree['"img'.++$i.'"'] = $star['postimg'];
	}
}



foreach (array_slice($tree, 0, 3) as $imgX) {
	$s = 0;
	echo "Line - ".$imgX."<br>";
}

echo "<hr>";

foreach (array_slice($tree, 3, 3) as $imgX) {
	$s = 0;
	echo "Line - ".$imgX."<br>";
}

echo "<hr>";

/*echo "<pre>";
print_r($tree);
echo "</pre>";*/


?>