<?php
$server = 'mysql.doksend.com';
$username = 'drivevirtual';
$password = 'Sausage5!';
$database = 'drivevirtual';

header('Access-Control-Allow-Origin: *');

//$con = mysql_connect($server, $username, $password) or die ("Could not connect: " . mysql_error());
//mysql_select_db($database, $con);
define("MYSQL_CONN_ERROR", "Unable to connect to database."); 

// Ensure reporting is setup correctly 
mysqli_report(MYSQLI_REPORT_STRICT); 

// Connect function for database access 
global $mysqli;
function connect($server, $username, $password, $database) { 
   try { 
      $mysqli = new mysqli($server, $username, $password, $database); 
      $connected = true; 
   } catch (mysqli_sql_exception $e) { 
      throw $e; 
   } 
} 

try { 
  connect($server, $username, $password, $database); 
  //echo 'Connected to database'; 
} catch (Exception $e) { 
  //echo $e->errorMessage(); 
} 


/** CONNECT with MYSQLi ****/
$mysqli = new mysqli($server, $username, $password, $database);

/*
 * This is the "official" OO way to do it,
 * BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
 */
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

/*
 * Use this instead of $connect_error if you need to ensure
 * compatibility with PHP versions prior to 5.2.9 and 5.3.0.
 */
if (mysqli_connect_error()) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}

//echo 'Success... ' . $mysqli->host_info . "\n";


?>