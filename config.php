<?php
$server = 'db566476412.db.1and1.com';
$username = 'dbo566476412';
$password = 'DVadmin1!';
$database = 'db566476412';

header('Access-Control-Allow-Origin: *');
/*
$con = mysql_connect($server, $username, $password) or die ("Could not connect: " . mysql_error());
mysql_select_db($database, $con);

$q = mysql_query('SELECT * FROM Users');
while($results = mysql_fetch_array($q)){
	echo $results['FirstName'];
}
*/

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