<?php
// execute the header script:
require_once "header.php";

// default values we show in the form:
$username = "";
$password = "";
// strings to hold any validation error messages:
$username_val = "";
$password_val = "";

// should we show the signin form:
$show_signin_form = false;
// message to output to user:
$message = "";

if (isset($_SESSION['loggedInProject']))
{
	// user is already logged in, just display a message:
	echo "You are already logged in, please log out first.<br>";

}
elseif (isset($_POST['username']))
{
	// user has just tried to log in:
	
	// connect directly to our database (notice 4th argument) we need the connection for sanitisation:
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection)
	{
		die("Connection failed: " . $mysqli_connect_error);
	}	
	
	// SANITISATION (see helper.php for the function definition)
	
	// take copies of the credentials the user submitted and sanitise (clean) them:
	$username = sanitise($_POST['username'], $connection);
	$password = sanitise($_POST['password'], $connection);
	
	// VALIDATION (see helper.php for the function definitions)
	$username_val = validateString($username, 1, 16);
	$password_val = validateString($password, 1, 32);
	
	// concatenate all the validation results together ($errors will only be empty if ALL the data is valid):
	$errors = $username_val . $password_val;
	
	// check that all the validation tests passed before going to the database:
	if ($errors == "")
	{
		// connect directly to our database (notice 4th argument):
		$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		
		// if the connection fails, print an error and exit the script:
		if (!$connection)
		{
			die("Connection failed: " . $mysqli_connect_error);
		}
		
		//hash the password inputted to see if it matched the hashed result in the database 
		$hashedPassword = md5($password);
		
		//This is the query to check the user is in the database and their credentials are correct
		$query = "SELECT * FROM `members` WHERE `username` = '$username' AND `password` = '$hashedPassword'";
		$result = mysqli_query($connection, $query);
		
		//This checks the number of rows returned
		$n = mysqli_num_rows($result);
			
		// if there was a match then set the session variables and display a success message:
		if ($n > 0)
		{
			// set a session variable to record that this user has successfully logged in:
			$_SESSION['loggedInProject'] = true;
			// and copy their username into the session data for use by our other scripts:
			$_SESSION['username'] = $username;
			
			// show a successful signin message:
			$message = "Hi, $username, you have successfully logged in, please <a href='show_profile.php'>click here</a><br>";
		}
		else
		{
			// no matching credentials found so redisplay the signin form with a failure message:
			$show_signin_form = true;
			// show an unsuccessful signin message:
			$message = "Sign in failed, please try again<br>";
		}
		
	}
	else
	{
		// validation failed, show the form again with guidance:
		$show_signin_form = true;
		// show an unsuccessful signin message:
		$message = "Sign in failed, please check the errors shown above and try again<br>";
	}
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);

}
else
{
	// user has arrived at the page for the first time, just show them the form:
	
	// show signin form:
	$show_signin_form = true;
}

if ($show_signin_form)
{
// show the form that allows users to log in
// Note we use an HTTP POST request to avoid their password appearing in the URL:
echo <<<_END
<form action="sign_in.php" method="post">
  Please enter your username and password:<br>
  Username:
  <br> 
  <input class = "center" type="text" name="username" maxlength="16" value="$username" required> $username_val
  <br>
  Password: 
  <br>
  <input class = "center" type="password" name="password" maxlength="32" value="$password" required> $password_val
  <br>
  <input type="submit" value="Submit">
</form>	
_END;
}

// display our message to the user:
echo $message;

// finish off the HTML for this page:
require_once "footer.php";
?>