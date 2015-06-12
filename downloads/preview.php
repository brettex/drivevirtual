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
			
			header("Expires:0");
			header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
			header("Content-Type:application/vnd.ms-excel");
			header("Content-Disposition:attachment;filename=\"".$file_name);
			header("Content-Transfer-Encoding:binary");
			header("Content-Length:".filesize($file_path));
			header('location:'.$file_path);
			
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


/** FUNCTION TO SET CORRECT MIME TYPE **/	
if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}
?>