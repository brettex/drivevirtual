<?php 
$host = 'db566476412.db.1and1.com';
$username = 'dbo566476412';
$password = 'DVadmin1!';
$database = 'db566476412';
$host = 'mysql.doksend.com';
$username = 'drivevirtual';
$password = 'Sausage5!';
$database = 'drivevirtual';

//$conn = new PDO( "mysql:host=$host;dbname=$database", $username, $password);
//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//$con = mysql_connect($host, $username, $password) or die ("Could not connect: " . mysql_error());
//mysql_select_db($database, $con);

define("MYSQL_CONN_ERROR", "Unable to connect to database."); 

// Ensure reporting is setup correctly 
mysqli_report(MYSQLI_REPORT_STRICT); 

// Connect function for database access 
function connect($host, $username, $password, $database) { 
   try { 
      $mysqli = new mysqli($host, $username, $password, $databas); 
      $connected = true; 
   } catch (mysqli_sql_exception $e) { 
      throw $e; 
   } 
} 

try { 
  connect($host, $username, $password, $database); 
  echo 'Connected to database'; 
} catch (Exception $e) { 
  echo $e->errorMessage(); 
}

?>