<?php
// include Config Parameters
include_once("config.php");

#error_reporting(E_ALL|E_STRICT);
#ini_set('display_errors', 1);
#ini_set('sendmail_from', 'noreply@exnowski.com');



// Set up the user variables
if(isset($_GET['action']))$action = $_GET['action'];
if(isset($_GET['userID']))$userID = $_GET['userID'];
if(isset($_GET['Host']))$Host = $_GET['Host'];
if(isset($_GET['FTPUser']))$Username = $_GET['FTPUser'];
if(isset($_GET['email']))$Email = $_GET['email'];
if(isset($_GET['company']))$Company = $_GET['company'];
if(isset($_GET['sessionID']))$sessionID = $_GET['sessionID'];
if(isset($_POST['password']))$password = $_POST['password'];
if(isset($_GET['port']))$port = $_GET['port'];


//Register the New User Account
if($action == 'addNew'){
	// Add them to the User table
	$query = $mysqli->query("INSERT INTO Users (`FirstName`, `Email`, `Username`, `Password`, `UserLevel` ) VALUES ('$Company', '$Email', '$Username','$password', 2)");
	$userID = $mysqli->insert_id;
	// Add them to the FTP table
$queryFTP = $mysqli->query("INSERT INTO `FTP Accounts` ( UserID, `FTPHost`, `FTPUser`, `FTPPassword`, `FTPPort` ) VALUES ('$userID', '$Host', '$Username', '$password', '$port')");
	// Add them to the UserSession table
	$querySessions = $mysqli->query("INSERT INTO UserSessions ( UserID, `SessionID` ) VALUES ('$userID', '0')");
	
	//Make a Folder for this User for Dowloading files
	if(mkdir("../../downloads/$userID", 0755)){
		$result['dir'] = "Make Directory Success";
	} else {
		$result['dir'] = "Make Directory Failure";
	}
	
	$result['msg'] = true;
}

//Update User Account
if($action == 'update'){
	if(isset($_POST['userid'])){
		
		$userID = $_POST['userid'];
		// Update the User table
		$query = $mysqli->query("UPDATE Users SET `FirstName` = '$Company', `Email`='$Email', `Username`='$Username', `Password`='$password' WHERE ID = '$userID'");
	
		// Update the FTP table
		$queryFTP = $mysqli->query("UPDATE `FTP Accounts` SET `FTPHost`='$Host', `FTPUser`='$Username', `FTPPassword`='$password', `FTPPort`='$port' WHERE UserID = '$userID'");

		$result['msg'] = true;
	} else {
		$result['msg'] = false;
	}
}


// Get All the users FTP Accounts
if($action == 'getFTP'){
	//Set up Query
	$query = $mysqli->query("SELECT * FROM `FTP Accounts` WHERE UserID = '$userID' LIMIT 1");
	
	if($query->num_rows > 0){
	
		$i=0;	
	    while($data = $query->fetch_array()){
			$ftps[$i]['ID'] = $data['ID'];
			$ftps[$i]['Name'] = $data['NickName'];
			$i++;   
   		}
	} else {
		$ftps[0]['ID'] = 'None';
	}
	
	/* free result set */
   // $query->close();
	
	$result = $ftps;

}
// LOG the user in
if($action == 'logIn'){
	
	//Start the Query
	$query = $mysqli->query("SELECT * FROM `Users` WHERE Username = '$userID' AND Password = '$password'");
	if($query->num_rows > 0){
	
		//Authenticated, create a session variable
		$sessionID = generateRandomString(20);
		//Get UserID
		$data = $query->fetch_array();
		$UID = $data['ID'];
		
		
		$queryUpdate = $mysqli->query("UPDATE `UserSessions` SET SessionID = '$sessionID' WHERE UserID = '$UID'");
		
		$queryFTP = $mysqli->query("SELECT * FROM `FTP Accounts` WHERE UserID = '$UID' LIMIT 1");
		
		//Get FTP ID
		$dataFTP = $queryFTP->fetch_array();

		
		$result['result'] = 'success';
		$result['sessionID'] = $sessionID;
		$result['ftp'] = $dataFTP['ID'];
		$result['company'] = $data['FirstName'];
		$result['ID'] = $data['ID'];
	} else {
		$result['result'] = 'On no!  You username/password combination don\'t match. Try again :)';
	}
	
	/* free result set */
   // $query->close();

}

// LOG the user in
if($action == 'getUser'){
	$query = $mysqli->query("SELECT * FROM `Users` u LEFT JOIN `FTP Accounts` f ON u.ID = f.UserID WHERE u.ID = '$userID'");
	if($query->num_rows > 0){
		$data = $query->fetch_array();
		
		$result['company'] = $data['FirstName'];
		$result['username'] = $data['Username'];
		$result['host'] = $data['FTPHost'];
		$result['username'] = $data['FTPUser'];
		$result['password'] = $data['Password'];
		$result['email'] = $data['Email'];
		$result['port'] = $data['FTPPort'];
	} else {
		$result['result'] = 'NO results found!';
	}
	
	/* free result set */
   // $query->close();

}

// Mail the user their password
if($action == 'getPassword'){
	$query = $mysqli->query("SELECT * FROM `Users` WHERE Email = '$Email'");
	if($query->num_rows > 0){
		$data = $query->fetch_array();
		
		$result['msg'] = 'Your password will be emailed to you.';
		$result['result'] = 'success';
		$password = $data['Password'];
		
		/** To User **/
		//$emailHeaders = "Reply-To: noreply@drivevirtual.com\r\n";
		//$emailHeaders .= "Return-Path: noreply@drivevirtual.com\r\n";
		//$emailHeaders .= "From: MUSE <noreply@drivevirtual.com>\r\n";
		//$emailHeaders .= 'Signed-by: drivevirtual.com\r\n"';
		$emailHeaders .= 'MIME-Version: 1.0' . "\r\n";
		$emailHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		//$toEmail = 'brettex25@gmail.com';
		$toEmail = $Email;
		$content = "Your password request from Drive Virtual<br /><br />";
		$content .= "Password: ".$password."<br />";
		
		$mail = mail( $toEmail, 'Your password request from Drive Virtual', $content, $emailHeaders );
		} else {
			$result['msg'] = 'Incorrect Email!';
		}
	
	/* free result set */
   // $query->close();

}

// CHeck the Users Login state
if($action == 'checkLogin'){
		$admin = 'false';
		$logged = 'false';
	// The Query	
	$query = $mysqli->query("SELECT * FROM UserSessions s LEFT JOIN Users u ON s.UserID = u.ID WHERE s.UserID = '$userID' AND `SessionID` = '$sessionID'");
	if($query->num_rows > 0){
		// Fetch Data
		$data = $query->fetch_array();
	
		$logged = 'true';
		//Authenticated, is Admin?
		if($data['UserLevel'] == 1){
			$admin = 'true';
		}
	}	
	
	$result['logged'] = $logged;
	$result['admin'] = $admin;

}

// LOG the user OUT
if($action == 'logOut'){
	
	$queryUpdate = $mysqli->query("UPDATE `UserSessions` SET SessionID = '0' WHERE UserID = '$userID' ");

}
//Close the Connection
$mysqli->close();
	
// Encode the Results
echo $_GET['jsoncallback'] . '(' . json_encode($result) . ');';


function generateRandomString($length) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

?>