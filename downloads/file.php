<?php
ini_set('display_errors','on');
error_reporting(E_ALL);

	// include Config Parameters
	include_once("../assets/includes/config.php");
	
	// Get the Variables
	if(isset($_GET['sessionID']))$sessionID = $_GET['sessionID'];
	if(isset($_GET['file']))$filePath= $_GET['file'];

	//Extract UserID from File Path
	$userID = dirname($filePath);
	
	//Get session ID from Database
	$query = $mysqli->query("SELECT * FROM UserSessions s LEFT JOIN Users u ON s.UserID = u.ID WHERE s.UserID = '$userID' AND `SessionID` = '$sessionID'");
	
	//Only Allow download if user is Authenticated
	if($query->num_rows > 0){
	
		if(file_exists($filePath)) {
			$fileName = basename($filePath);
			$fileSize = filesize($filePath);
	
			// Output headers.
			header("Cache-Control: private");
			header("Content-Type: application/stream");
			header("Content-Length: ".$fileSize);
			header("Content-Disposition: attachment; filename=".$fileName);
			
			//Ignore if te user cancels download
			ignore_user_abort(true);
			// Output file.
			readfile ($filePath);
			//Delete file for Security purposes
			unlink($filePath);                   
			exit();
		}
		else {  
			die('File doesent exist!');
    	}
		
		
	} else {
		
		die('Not Authorized!');
	}
?>