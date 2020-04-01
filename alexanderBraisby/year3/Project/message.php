<?php

// execute the header script:
require_once "header.php";

if (!isset($_SESSION['loggedInProject']))
{
	// user isn't logged in, display a message saying they must be:
	echo "You must be logged in to view this page.<br>";
}
else{
	
	// connect directly to our database:
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	// if the connection fails, we need to know, so allow this exit:
	if (!$connection)
	{
		die("Connection failed: " . $mysqli_connect_error);
	}
	
	echo<<<_END
	<form action="message.php" method="post">
	
		<h2>Write a letter to another user:</h2>
		
		<textarea name="postcardSent" rows='4' cols='50' maxlength='140' placeholder='Write a postcard!'></textarea>
_END;

	//Get a list of all the usernames (potential recipients of postcards)
	$query = "SELECT `username` FROM `members` ORDER BY `username` ASC";
	
	//Run the query and store the result (all the usernames)
	$result = mysqli_query($connection, $query);
	
	//Checks for returned rows:
	$n = mysqli_num_rows($result);
	
	if($n > 0){ 
		
		//Start the unordered list
		echo "<br> Select a recipient <select name = 'intendedRecipient'>";
		
		for($i=0;$i<$n;$i++){
			
			// collect data by rows
			$row = mysqli_fetch_assoc($result);
			//add collected row data to table
			echo "<option value=" . $row['username'] . ">" . $row['username'] . "</option>";
	
		}
		
		//End the unordered list
		echo "</select>	<br> <input type='submit' value='Submit'></form> <br>";
		
	}

	//This query gets a list of all the profiles and orders them
	//A WHERE condition has been added to filter the messages to ones they've been sent
	//An ORDER BY condition has been added to put the newest messages first
	
	$postcardRecipient = $_SESSION['username'];
	$query = "SELECT `date`, `message`, `recipient`, `author` FROM `postbox` WHERE `recipient` = '$postcardRecipient' ORDER BY `date` DESC LIMIT 10";
	
	
	// the result of the query
	$result = mysqli_query($connection, $query);
	
	// checks for returned rows:
	$n = mysqli_num_rows($result);
	//Checks the number of returned rows
	if($n > 0){ 
		
		//Start the unordered list
		echo "<table class = 'center'><tr><th>Time Sent</th><th>Message</th><th>author</th></tr>";
		
		for($i=0;$i<$n;$i++){
			
			// collect data by rows
			$row = mysqli_fetch_assoc($result);
			//add collected row data to table
			echo "<tr><td>" . $row['date'] . "</td><td>" . $row['message'] . "</td><td>" . $row['author'] . "</td></tr>";
	
		}
		
		//End the unordered list
		echo "</table>";
		
	}
	
	if(isset($_POST['postcardSent'])){
		//This sanitises and validates the global feed input 
		$postcardContent = sanitise($_POST['postcardSent'],$connection);
		$postcardContent_val = validateString($postcardContent,1,140);
		$intendedRecipient = $_POST['intendedRecipient'];
		$postcardAuthor = $_SESSION['username'];
		
		if($postcardContent_val == ""){
			
			//Adds to the global feed
			$sql = "INSERT INTO `postbox`(message, recipient, author) VALUES ('$postcardContent','$intendedRecipient','$postcardAuthor')";
				
			// no data returned, we just test for true(success)/false(failure):
			if (mysqli_query($connection, $sql)) {
				
					echo "row inserted<br>";
					
				}else{
					
					die("Error inserting row: " . mysqli_error($connection));
					
				}
				
			}else{
				
				//If the message failed the validation checks this message is displayed
				echo "Invalid message in postcard";
				
			}
			
	}
	
	
}
	


// finish of the HTML for this page:
require_once "footer.php";

?>