<?php 

session_start();
include("connect.php");

	$author = $_POST['author'];
	$message = $_POST['message'];
	$author = addslashes($author);
	$author = htmlspecialchars($author);
	$author = stripslashes($author);

	$message = addslashes($message);
	$message = htmlspecialchars($message);
	$message = stripslashes($message);

	mysqli_query($mysql, "INSERT INTO comments (author, message, date) VALUES ('$author','$message', NOW())");	
	echo 0;
?>