<?php
// Uncomment these to Turn on Errors for this page
	ini_set('display_errors','on');
	error_reporting(E_ALL);

include('config.php');


$ID = $_GET['id'];
// Query the Database for the enetered Email
$query = mysql_query("SELECT * FROM Users WHERE ID > '$ID'");


//Loop through each user and encrypt password
while($user = mysql_fetch_array($query)){
	
	echo $password = $user['Password'];
	$userID = $user['ID'];
	
	$hash = password_hash($password, PASSWORD_DEFAULT);
	
	//Save to Database
	mysql_query("UPDATE `Users` SET `Password`='$hash' WHERE ID = '$userID'");
	
		
}

?>