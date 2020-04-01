<?php

// execute the header script:
require_once "header.php";

if (!isset($_SESSION['loggedInProject']))
{
	// user isn't logged in, display a message saying they must be:
	echo "You must be logged in to view this page.<br>";
}
else{
	
echo <<<_END

<form action="browse_profiles.php" method="post">

  Search for a user by name:<br>
  
  <!--Max length set at 16 because that's the size of the VARCHAR in the database and set to required because it's needed info-->
  Username:
  <br>
  <input class = "center"  type="text" name="username" maxlength="16" value="" required>
  
  <input type="submit" value="Search">
  
</form>	

<br>

_END;

if (isset($_POST['username'])){
	
	// connect directly to our database (notice 4th argument) we need the connection for sanitisation:
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection)
	{
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	//Sanitising the user inputs
	$username = sanitise($_POST['username'], $connection);
	
	// SERVER-SIDE VALIDATION CODE:
	$firstname_val = validateString($username,1,16); //Database set to allow usernames up to 16 characters in length
	
	if($firstname_val==""){
		
		//No validation issues
		$query = "SELECT * FROM profiles WHERE username like '%$username%' AND username != 'admin'";
		
		//Get the result of the query
		$result = mysqli_query($connection, $query);
		
		//Count how many results the query returned
		$n = mysqli_num_rows($result);
			
		// if there was a match then UPDATE their profile data, otherwise INSERT it:
		if ($n > 0){
			
			//Found one or more results for the searched username
			//Start the unordered list
			
			echo "<ul>";
			
			for($i=0;$i<$n;$i++){
				
				// collect data by rows
				$row = mysqli_fetch_assoc($result);
				//add collected row data to table
				echo "<li> <a href='browse_profiles.php?view=" . $row['username'] . "'>" . $row['username'] . "</a> </li>";
		
			}
			
			//End the unordered list
			echo "</ul>";
			
			
		} else {
			
			//No results found
			echo "No results found for $username";
			
		}
		
	} else {
		
		echo "validation error, please try again";
	
	}
	
	
}else{
	
	//echo "It has not been posted";
	
}
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection)
	{
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	//This checks to see if the hyperlinked usernames have been clicked 
	if (isset($_GET['view'])){
		
		//This sanitises the clicked view and puts it into the $us variable  
		$username = sanitise($_GET['view'], $connection);
		
		//This outputs the clicked on username and tells the user who's data has been clicked  
		echo "<h2>$username" ."'s"." Profile</h2>";
		
		//These lines of code select the data relating to the clicked upon username and stores the data returned 
		$memberQuery = "SELECT * FROM profiles WHERE username='$username'";
		$memberResult = mysqli_query($connection, $memberQuery);
		
		//The number of rows returned
		$memberN = mysqli_num_rows($memberResult);

		//This if statement and the for loop within it populates the web page with information gathered from the SQL query 
		if ($memberN > 0){
			
			for($i=0;$i<$memberN;$i++){
				
				$row = mysqli_fetch_assoc($memberResult);
				echo <<<_END
				
				<h3> Username: {$row['username']} </h3>
				<h3> First name: {$row['firstname']} </h3>
				<h3> Last name: {$row['lastname']} </h3>
				<h3> Email: {$row['email']} </h3>
				<h3> DOB: {$row['dob']} </h3>
				
_END;
			}
			
		}
		
	}
	
}

// finish of the HTML for this page:
require_once "footer.php";

?>