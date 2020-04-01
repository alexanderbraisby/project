<?php

// execute the header script:
require_once "header.php";

if (!isset($_SESSION['loggedInProject']))
{
	// user isn't logged in, display a message saying they must be:
	echo "You must be logged in to view this page.<br>";
}
else
{
	// user is already logged in, read their username from the session:
	$username = $_SESSION["username"];
	
	// now read their profile data from the table...
	
	// connect directly to our database (notice 4th argument):
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection)
	{
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	// check for a row in our profiles table with a matching username:
	$query = "SELECT * FROM profiles WHERE username='$username'";
	
	// this query can return data ($result is an identifier):
	$result = mysqli_query($connection, $query);
	
	// how many rows came back? (can only be 1 or 0 because username is the primary key in our table):
	$n = mysqli_num_rows($result);
		
	// if there was a match then extract their profile data:
	if ($n > 0)
	{
		// use the identifier to fetch one row as an associative array (elements named after columns):
		$row = mysqli_fetch_assoc($result);
		// display their profile data:
		echo <<<_END
				
				<h3> Username: {$row['username']} </h3>
				<h3> First name: {$row['firstname']} </h3>
				<h3> Last name: {$row['lastname']} </h3>
				<h3> Email: {$row['email']} </h3>
				<h3> DOB: {$row['dob']} </h3>
				<h3> SteamID: {$row['steamID']} </h3>
_END;
	}
	else
	{
		// no match found, prompt user to set up their profile:
		echo "You still need to set up a profile! <br> Please <a href='set_profile.php'>set up a profile!</a><br>";
	}
	
	echo <<<_END
	<br>
	<br>
	<form action="show_profile.php" method="post">
		Please type in your password then hit submit to delete your account:
		<br>
		<input class = "center" type="password" name="password" maxlength="32">
		<br>
		<input type="submit" value="Delete Profile?">
	</form>	
_END;

	if (isset($_POST['password'])){
		
		//echo "tried to delete account!";
		
		//SANITISATION
		$password = sanitise($_POST['password'], $connection);
		
		//VALIDATION
		$password_val = validateString($password, 1, 32);
		
		if ($password_val == ""){
			//No errors reported
			
			//hash the password inputted to see if it matched the hashed result in the database 
			$hashedPassword = md5($password);
			$deletingUsername = $_SESSION['username'];
			
			//This is the query to check the user is in the database and their credentials are correct
			$query = "DELETE FROM `members` WHERE `username` = '$deletingUsername' AND `password` = '$hashedPassword';";
			mysqli_query($connection, $query);
			
			$checkForDelete = mysqli_affected_rows($connection);
			
			//Checks the query managed to successfully delete the member
			if($checkForDelete == 1){
				
				//Remove the profile data stored for that user
				$query = "DELETE FROM `profiles` WHERE `username` = '$deletingUsername';";
				mysqli_query($connection, $query);
				
				//Wipe the session array saved
				$_SESSION = array();
				// then the cookie that holds the session ID:
				setcookie(session_name(), "", time() - 2592000, '/');
				//Remove session data on the server:
				session_destroy();

				echo "You have successfully deleted your account, please <a href='sign_in.php'>click here</a><br>";
				
			}else{
				
				echo "error whilst deleting account, please try again!";
				
			}
			

			
		} else {
			//Errors reported
		}
		
	}

	// we're finished with the database, close the connection:
	mysqli_close($connection);
		
}

// finish off the HTML for this page:
require_once "footer.php";
?>