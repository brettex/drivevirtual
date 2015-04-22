<?php
// include FTP class 
include_once("SFTP/SFTP.php");

// Set Include Path for SSH-SFTP Library
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
// Additional Class to allow Secure FTP Connections
include('phpseclib/Net/SFTP.php');

 
// include Config Parameters
include_once("config.php");

//Default Directory to Home
$directory = '/';
if(isset($_GET['dir']))$directory = $_GET['dir']; 
if(isset($_GET['check']))$check = $_GET['check']; 
if(isset($_GET['ftp']))$ftp = $_GET['ftp'];
if(isset($_GET['sftp']))$sftp = $_GET['sftp']; // True or False 
if(isset($_GET['userID']))$userID = $_GET['userID'];
if(isset($_POST['sessionID']))$sessionID = $_POST['sessionID'];
if(isset($_GET['FTPUser']))$user = $_GET['FTPUser'];
if(isset($_GET['Host']))$host = $_GET['Host'];
if(isset($_POST['password']))$pass = $_POST['password'];
if(isset($_GET['fileName']))$fileName = $_GET['fileName'];
if(isset($_GET['port']))$fport = $_GET['port'];

// Are we getting directories or just checking the connection?
if($check == 'true'){
	
	// Check with Regular FTP
	if($fport == 21){
		// Set SFTP object, use host, username and password 
		$ftp = new SFTP($host, $user, $pass); 
		
		if($ftp->connect()) { 
				$connected  = true;
		} else { 
				// Connection failed, display last error 
				//print "Connection failed: " . $ftp->error; 
				$connected = false;
		}
	} elseif($fport == 22){
		//Connect with Secure FTP
		$sftp = new Net_SFTP($host);
		if (!$sftp->login($user, $pass)) {
			$connected = false;
		} else {
			$connected  = true;
		}
	}
	$result['msg'] = $connected;
	
}elseif($check == 'download'){
	
	// Default to the Users 1st account
		$query = $mysqli->query("SELECT * FROM `FTP Accounts` WHERE UserID = '$userID' LIMIT 1");
		if($query->num_rows > 0){	
			$creds = $query->fetch_array();
			$host = $creds['FTPHost'];
			$user = $creds['FTPUser'];
			$pass = $creds['FTPPassword'];
			$port = $creds['FTPPort'];
			$FTPID = $creds['ID'];
		}
	//Connect with Regular FTP	
	if($port == 21){
		// set SFTP object, use host, username and password 
		$ftp = new SFTP($host, $user, $pass); 
		
		//Remove file name from directory
		$directory = str_replace($fileName, '', $directory);
		//Check the connection
		if($ftp->connect()) {
			//Go to correct directory
			$ftp->cd($directory);
			
			//Check user directory on Drive Virtual exists, if not, create it!
			if(!is_dir('../../downloads/'.$userID)){
				mkdir('../../downloads/'.$userID, 0755);
			}
			//Check to get the file 
			if($ftp->get($fileName, '../../downloads/'.$userID.'/'.$fileName)) { 
					$result['result'] = 'success';
			  } else { 
					$result['result'] = 'fail';
			  }
		
			$result['result'] = 'success';
		} else {
			$result['result'] = 'fail';
		}
	}elseif($port == 22){
		//Connect with Secure FTP
		$sftp = new Net_SFTP($host);
		
		//Remove file name from directory
		$directory = str_replace($fileName, '', $directory);
		//Check the connection
		if($sftp->login($user, $pass)) {
			//Go to correct directory
			$curDirectory = $sftp->pwd();
			
			//Check user directory on Drive Virtual exists, if not, create it!
			if(!is_dir('../../downloads/'.$userID)){
				mkdir('../../downloads/'.$userID, 0755);
			}
			//Check to get the file 
			if($sftp->get($fileName, '../../downloads/'.$userID.'/'.$fileName)) { 
					$result['result'] = 'success';
			  } else { 
					$result['result'] = 'fail';
			  }
		
			$result['result'] = 'success';
		} else {
			$result['result'] = 'fail';
		}
	}
	$result['url'] = 'downloads/file.php?file='.$userID.'/'.$fileName.'&sessionID='.$sessionID;
} else {
	// Simply get list of files and direcrories from current working FTP connection
	if($ftp == 'default'){
		// Default to the Users 1st account
		if($query = $mysqli->query("SELECT * FROM `FTP Accounts` WHERE UserID = '$userID' LIMIT 1")) {
			
			$creds = $query->fetch_array();
			$host = $creds['FTPHost'];
			$user = $creds['FTPUser'];
			$pass = $creds['FTPPassword'];
			$port = $creds['FTPPort'];
			$FTPID = $creds['ID'];
		}
		
	} else {
		//Get the FTP Creds from the database
		if($query = $mysqli->query("SELECT * FROM `FTP Accounts` WHERE UserID = $userID AND ID = '$ftp'")) {
			
			$creds = $query->fetch_array();
			$host = $creds['FTPHost'];
			$user = $creds['FTPUser'];
			$pass = $creds['FTPPassword'];
			$port = $creds['FTPPort'];
			$FTPID = $creds['ID'];
		}
	}
	
	//Regular FTP
	if($port == 21){
		// set SFTP object, use host, username and password 
		$ftp = new SFTP($host, $user, $pass); 
		
			if($ftp->connect()) { 
			//print "Connection successful"; 
				$ftp->cd($directory);
				$curDirectory = $ftp->pwd($directory);  
		
				  
				// get list of files/directories in directory "/mydir" 
				$directories = $ftp->ls($curDirectory); 
				//print_r($directories);
				$connected  = true;
			
		
		} else { 
				// connection failed, display last error 
				// print "Connection failed: " . $ftp->error; 
				$connected = false;
		}
	} elseif($port == 22){
		
		//Connect with Secure FTP
		$sftp = new Net_SFTP($host);
		if (!$sftp->login($user, $pass)) {
			$connected = false;
		} else {
			$connected  = true;
		}
		//Change the current Directory
		$sftp->chdir($directory);
		//Get the Current Directory
		$curDirectory = $sftp->_realpath();
		//echo $curDirectory;
		$directories = $sftp->nlist($curDirectory); 
		//print_r($directories);
		
	}
		
		if($connected){
			$typeArray = array();
			$i=0;
			foreach($directories as $dir){
					//SFTP returns ONLY current path, not directory path, so add it!
					if($port == 22){ $dir  = $curDirectory."/".$dir;}
					$name  = explode('/', $dir); // Remove File path from name
					$name = end($name);
				if($name != '/.' && $name != '/..' && $name != '..' && $name != '.'){
					if($port == 21){
						$isFile = false;
						if(!$ftp->cd($dir)){
							$isFile = true;
						}
					} else {
						$isFile = true;	
						//Get some info on file/directory
						$info = $sftp->stat($dir);
						if($info['type'] == 2){
							$isFile = false;	
						}
					}
					//Is it a file or directory
					if($isFile){
						//Set up the return data
						$data[$i]['dir'] = $dir;
						$data[$i]['type'] = "file";
						$data[$i]['name'] = $name;
						// Get file Extension
						$ext = explode('.', $dir);
						$data[$i]['ext'] = end($ext);	
					} else {
	
						$data[$i]['dir'] = $dir;
						$data[$i]['type'] = "folder";
						$data[$i]['name'] = $name;
					}
					$typeArray[$i] =  $data[$i]['type'];
					$i++;
				}
			} 
			//Sort the Files and Folders in order and group them
			asort($typeArray);
			//Resort the Array
			$sortedData= array();
			$i=0;
			foreach($typeArray as $key => $type){
				$sortedData['result'][$i]['dir'] = $data[$key]['dir']; // Directory Path
				$sortedData['result'][$i]['name'] = $data[$key]['name']; //Name
				$sortedData['result'][$i]['type'] = $type; //Type, i.e Filer or Folder
				if($type == 'file') $sortedData['result'][$i]['ext'] = $data[$key]['ext'];  //Extension
				$i++;	
			}
			$sortedData['connect'] = 'true';
			$sortedData['FTP'] = $FTPID;
			$result = $sortedData;
		} else {
			$sortedData['connect'] = 'false';
			$result['msg'] = 'Could not connect to the FTP Server at this time.';
		}
}
	
	// Encode the Results
echo $_GET['jsoncallback'] . '(' . json_encode($result) . ');';

?>
    
    

