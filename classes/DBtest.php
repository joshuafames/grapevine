<?php

try {
	$pdo = new PDO('mysql:host=127.0.0.1;dbname=d2580661;charset=utf8', 's2580661', 's2580661');

	if ($pdo) {
		echo "Connected to the $db database successfully!";
	}
} catch (PDOException $e) {
	echo $e->getMessage();
}
?>

