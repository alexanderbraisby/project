<?php
include_once "../credentials.php";

// variables to store the data we'll send back
$thisRow = array();
$allRows = array();

if (!isset($_GET['feedTalk']))
{
	// set the kind of data we're sending back and an error response code:
	header("Content-Type: application/json", NULL, 400);
	// and send:
	echo json_encode($allRows);
	// and exit this script:
	exit;
}
else
{
	$number = $_GET['feedTalk'];
}

// connect directly to our database (notice 4th argument):
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// connection failed, return an internal server error:
if (!$connection)
{
	// set the kind of data we're sending back and a failure code:
	header("Content-Type: application/json", NULL, 500);
	// and send:
	echo json_encode($allRows);
	// and exit this script:
	exit;
}

// find the N most recently updated favourite numbers:
$query = "SELECT * FROM feed ORDER BY date DESC LIMIT $number";

// this query can return data ($result is an identifier):
$result = mysqli_query($connection, $query);
		
// how many rows came back?:
$n = mysqli_num_rows($result);

// if we got some results then add them all into a big array:
if ($n > 0)
{
	// loop over all rows, adding them into our array:
	for ($i=0; $i<$n; $i++)
	{
		// fetch one row as an associative array (elements named after columns):
		$thisRow = mysqli_fetch_assoc($result);
		// and add it to the data we'll send back:
		$allRows[] = $thisRow;
	}
}

// we're finished with the database, close the connection:
mysqli_close($connection);

// set the kind of data we're sending back and a success code:
header("Content-Type: application/json", NULL, 200);

// and send:
echo json_encode($allRows);

?>