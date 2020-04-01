<?php

// execute the header script:
require_once "header.php";

// default values we show in the form:
$username = "";
$password = "";
// strings to hold any validation error messages:
$username_val = "";
$password_val = "";

// should we show the signup form?:
$show_signup_form = false;
// message to output to user:
$message = "";

if (isset($_SESSION['loggedInProject']))
{
	// user is already logged in, just display a message:
	echo "You are already logged in, please log out first<br>";
	
}
elseif (isset($_POST['username']))
{
	// user just tried to sign up:
	
	// connect directly to our database (notice 4th argument) we need the connection for sanitisation:
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection)
	{
		die("Connection failed: " . $mysqli_connect_error);
	}	
	
	// SANITISATION (see helper.php for the function definition)
	
	// take copies of the credentials the user submitted, and sanitise (clean) them:
	$username = sanitise($_POST['username'], $connection);
	$password = sanitise($_POST['password'], $connection);

	// VALIDATION (see helper.php for the function definitions)
	
	// now validate the data (both strings must be between 1 and 16 characters long):
	// (reasons: we don't want empty credentials, and we used VARCHAR(16) in the database table)
	$username_val = validateString($username, 1, 16);
	$password_val = validateString($password, 1, 16);
	
	// concatenate all the validation results together ($errors will only be empty if ALL the data is valid):
	$errors = $username_val . $password_val;
	
	// check that all the validation tests passed before going to the database:
	if ($errors == "")
	{
		//hash the password ready to be inputted into the database 
		$hashedPassword = md5($password);
		
		// try to insert the new details:
		$query = "INSERT INTO members (username, password) VALUES ('$username', '$hashedPassword');";
		$result = mysqli_query($connection, $query);
		
		// no data returned, we just test for true(success)/false(failure):
		if ($result) 
		{
			// show a successful signup message:
			$message = "Signup was successful, please <a href='show_profile.php'>click here</a><br>";
		} 
		else 
		{
			// show the form:
			$show_signup_form = true;
			// show an unsuccessful signup message:
			$message = "Sign up failed, please try again<br>";
		}
			
	}
	else
	{
		// validation failed, show the form again with guidance:
		$show_signup_form = true;
		// show an unsuccessful signin message:
		$message = "Sign up failed, please check the errors shown above and try again<br>";
	}
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);

}
else
{
	// just a normal visit to the page, show the signup form:
	$show_signup_form = true;
	
}

if ($show_signup_form)
{
// show the form that allows users to sign up
// Note we use an HTTP POST request to avoid their password appearing in the URL:	
echo <<<_END
<form action="sign_up.php" method="post">
  Please choose a username and password:
  <br>
  Username: 
  <br>
  <input class = "center" type="text" name="username" maxlength="16" value="$username" required> $username_val
  <br>
  Password:
  <br>
  <input class = "center" type="password" name="password" maxlength="16" value="$password" required> $password_val
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