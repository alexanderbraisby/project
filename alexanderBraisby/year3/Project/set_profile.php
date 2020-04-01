<?php

// execute the header script:
require_once "header.php";

// message to output to user:
$message = "";

//These lines get the current date and subtract 13 years from it. This is used to make sure nobody under the age of 13 signs. 
$today = date("Y-m-d");
$youngestDate = date('Y-m-d',strtotime("$today" . "-13 year"));

//This is the oldest date that can be accepted. This can be changed if needed.
$oldestDate = "1900-01-01";

$show_profile_form = "";

if (!isset($_SESSION['loggedInProject']))
{
	// user isn't logged in, display a message saying they must be:
	echo "You must be logged in to view this page.<br>";
}
elseif (isset($_POST['firstname']))
{
	// user just tried to update their profile
	
	// connect directly to our database (notice 4th argument) we need the connection for sanitisation:
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection)
	{
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	//Sanitising the user inputs
	$firstname = sanitise($_POST['firstname'], $connection);
	$lastname = sanitise($_POST['lastname'], $connection);
	$email = sanitise($_POST['email'], $connection);
	$dob = sanitise($_POST['dob'], $connection);
	$steamID = sanitise($_POST['steamID'], $connection);
	
	// SERVER-SIDE VALIDATION CODE:
	$firstname_val = validateString($firstname,1,40); //Database set to allow first names up to 40 characters in length
	$lastname_val = validateString($lastname,1,50); //Database set to allow last names up to 50 characters in length
	$email_val = validateEmail($email); //Calls custom email validation function
	$dob_val = validateDate($dob); //Calls custom date validation function
		
	//Concatenate all validation checks into $errors
	$errors = $firstname_val . $lastname_val . $email_val . $dob_val; 
	
	// check that all the validation tests passed before going to the database:
	if ($errors == "")
	{		
		// read their username from the session:
		$username = $_SESSION["username"];
		
		// check to see if this user already had a favourite:
		$query = "SELECT * FROM profiles WHERE username='$username'";
		
		// this query can return data ($result is an identifier):
		$result = mysqli_query($connection, $query);
		
		// how many rows came back? (can only be 1 or 0 because username is the primary key in our table):
		$n = mysqli_num_rows($result);
			
		// if there was a match then UPDATE their profile data, otherwise INSERT it:
		if ($n > 0)
		{
			// we need an UPDATE:
			$query = "UPDATE profiles SET firstname='$firstname',lastname='$lastname',email='$email',dob='$dob',steamID='$steamID' WHERE username='$username'";
			$result = mysqli_query($connection, $query);		
		}
		else
		{
			// we need an INSERT:
			$query = "INSERT INTO profiles (username,firstname,lastname,email,dob,steamID) VALUES ('$username','$firstname','$lastname','$email','$dob','$steamID')";
			$result = mysqli_query($connection, $query);	
		}

		// no data returned, we just test for true(success)/false(failure):
		if ($result) 
		{
			// show a successful update message:
			$message = "Profile successfully updated<br>";
		} 
		else
		{
			// show the set profile form:
			$show_profile_form = true;
			// show an unsuccessful update message:
			$message = "Update failed<br>";
		}
	}
	else
	{
		// validation failed, show the form again with guidance:
		$show_profile_form = true;
		// show an unsuccessful update message:
		$message = "Update failed, please check the errors above and try again<br>";
	}
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);

}
else
{
	// arrived at the page for the first time, show any data already in the table:
	
	// read the username from the session:
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
		// extract their profile data for use in the HTML:
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$email = $row['email'];
		$dob = $row['dob'];
		$steamID = $row['steamID'];
	}
	
	// show the set profile form:
	$show_profile_form = true;
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);
	
}

if ($show_profile_form == true)
{
	$firstname = "";
	$lastname = "";
	$email = "";
	$dob = "";
	$steamID = "";
	
echo <<<_END
<form action="set_profile.php" method="post">

  Update your profile info:<br>
  <!--Max length set at 40 because that's the size of the VARCHAR in the database and set to required because it's needed info-->
  First name: 
  <br>
  <input class = "center" type="text" name="firstname" maxlength="40" value="$firstname" required>
  <br>
  <!--Max length set at 50 because that's the size of the VARCHAR in the database and set to required because it's needed info-->
  Last name: 
  <br>
  <input class = "center" type="text" name="lastname" maxlength="50" value="$lastname" required>
  <br>
  <!--Changing input type to "email" helps validate the data on the client side and set to 50 because that's the size of the VARCHAR in the database and set to required because it's needed info-->
  Email address: 
  <br>
  <input class = "center" type="email" name="email" maxlength="50" value="$email" required>
  <br>
  <!--Changing input type to "date" helps validate the data on the client side and set to required because it's needed info-->
  Date of birth: 
  <br>
  <input class = "center" type="date" name="dob" max="$youngestDate" min="$oldestDate" value="$dob" required>
  <br>  
  <!--Changing input type to "text" to utilise the pattern attribute to help validate the data on the client side-->
  SteamID: 
  <br>
  <input class = "center" type="text" name="steamID" pattern="[0-9]{17}" title="17 digit SteamID" value="$steamID">
  <br>
  <input type="submit" value="Submit">
</form>	



_END;
}

// display our message to the user:
echo $message;

// finish of the HTML for this page:
require_once "footer.php";
?>