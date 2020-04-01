<?php
//Run the header script:
require_once "header.php";

if (!isset($_SESSION['loggedInProject']))
{
	//User isn't logged in, display a message saying they must be:
	echo "You must be logged in to view this page.<br>";
}
else
{
	//Display the page content if this is the admin account (all other users get a "you don't have permission..." message):
	if ($_SESSION['username'] == "admin")
	{
		//Connect to the database
		$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		
		//Test if connection failed 
		if (!$connection)
		{
			die("Connection failed: " . $mysqli_connect_error);
		}
	
		echo "Select a user to mute";
		
		//This query gets a list of all the profiles who haven't been muted and orders them
		$query = "SELECT * FROM `members` WHERE `canPost` = TRUE ORDER BY `username` ASC";
		
		
		//The result of the query
		$result = mysqli_query($connection, $query);
		
		//Checks for returned rows:
		$n = mysqli_num_rows($result);
		
		//Checks the number of returned rows
		if($n > 0){ 
			
			echo " <form action='developer_tools.php' method='post'> <select name = 'muteSelector'> ";
			
			for($i=0;$i<$n;$i++){
				
				//Collect data by rows
				$row = mysqli_fetch_assoc($result);
				
				//Create a drop down list of the users who can be muted
				echo "<option name = {$row['username']} >{$row['username']}</option>";
				
			}
			
			echo "</select> <br> <input type='submit' value='Submit'> </form>";
			
		}
			
			if(isset($_POST['muteSelector'])){
				
				//When the submit button is pressed the database is updated to not allow the user to post to the global feed 

				$mutedUser = $_POST['muteSelector'];
				
				$sqlMute = "UPDATE members SET `canPost`= FALSE WHERE username='$mutedUser'";
				
				//The result of the query
				$result = mysqli_query($connection, $sqlMute);
				
				//A little notice to tell the admin the user has been muted
				echo "$mutedUser has been muted";
				
				
			}
			
		echo "<br> <a href='http://localhost/phpmyadmin/index.php'>PHPMyAdmin</a> <br>";

		echo "Select a user to unmute";
		
		
		//This query gets a list of all the profiles who have been muted and orders them
		$query = "SELECT * FROM `members` WHERE `canPost` = 'FALSE' ORDER BY `username` ASC";
		
		
		//The result of the query
		$result = mysqli_query($connection, $query);
		
		//Checks for returned rows:
		$n = mysqli_num_rows($result);
		
		//Checks the number of returned rows
		if($n > 0){ 
			
			echo " <form action='developer_tools.php' method='post'> <select name = 'unmuteSelector'> ";
			
			for($i=0;$i<$n;$i++){
				
				// collect data by rows
				$row = mysqli_fetch_assoc($result);
				
				//Create a drop down list of the users who can be unmuted
				echo "<option name = {$row['username']} >{$row['username']}</option>";
				
			}
			
			echo "</select> <br> <input type='submit' value='Submit'> </form>";
			
		}
			
			if(isset($_POST['unmuteSelector'])){
				
				//When the submit button is pressed the database is updated to allow the user to once again post to the global feed 
					
				$unmutedUser = $_POST['unmuteSelector'];
				
				$sqlUnmute = "UPDATE members SET `canPost`= TRUE WHERE username='$unmutedUser'";
				
				// the result of the query
				$result = mysqli_query($connection, $sqlUnmute);
				
				//A little notice to tell the admin the user has been unmuted 
				echo "$unmutedUser has been unmuted";
						
			}
	}
	else
	{
		echo "You don't have permission to view this page...<br>";
	}
}

// finish off the HTML for this page:
require_once "footer.php";
?>