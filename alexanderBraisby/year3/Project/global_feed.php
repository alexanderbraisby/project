<?php

// execute the header script:
require_once "header.php";

// how many milliseconds to wait between updates:
$milliseconds = 5000;
// how many recent feed posts to display:
$nrows = 1000;

if (!isset($_SESSION['loggedInProject']))
{
	// user isn't logged in, display a message saying they must be:
	echo "You must be logged in to view this page.<br>";
}
else
{

// CSS to make the table clearly visible, and jQuery to control updates:
echo <<<_END
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>	
	


<form action="global_feed.php" method="post" class = 'center'>
		<h2>Global Feed:</h2>
		<!--Sets the text area size and limits the length of the user input. User input should be sanitised and validated-->
		<textarea name="globalFeedPost" rows='4' cols='50' maxlength='140' placeholder='Add to the global feed!'></textarea>
		<br>
		<input type='submit' value='Submit'>
</form>

<br>

<table id='globalFeed' class = 'center'>
	<tr><th>Username</th><th>Comment</th><th>Date</th></tr>
	<tr class='result'></tr>
</table>

<script>
$(document).ready(function()
{	
	// as soon as the page is ready, start checking for updates:
	update();
});

function checkFeed(){
		
    $.getJSON('api/recent.php', {feedTalk: $nrows})
		.done(function(data) {
			// debug message to help during development:
			//console.log('checkFeed request successful');
			
			// remove the old table rows:
			$('.result').remove();
			
			// loop through what we got and add it to the table (data is already a JavaScript object thanks to getJSON()):
			$.each(data, function(index, value) {
				$('#globalFeed').append("<tr class='result'><td>" + value.username + "</td><td>" + value.comment + "</td><td>" + value.date + "</td></tr>");
			});
		})
		
		.fail(function(jqXHR) {
			// debug message to help during development:
			console.log('request returned failure, HTTP status code ' + jqXHR.status);
		})
		
		.always(function() {
			// debug message to help during development:
			//console.log('checkFeed request completed');
			// call this function again after a brief pause:
			setTimeout(checkFeed, $milliseconds);
		});
}

function checkLikes(){
		
	$('#globalFeed').click(function(event){
		//Increment the likes 
	});
}

function update(){
	checkFeed();
	checkLikes();
}

</script>
_END;


	if(isset($_POST['globalFeedPost'])){
		// connect directly to our database (notice 4th argument):
		$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
		// if the connection fails, we need to know, so allow this exit:
		if (!$connection)
		{
			die("Connection failed: " . $mysqli_connect_error);
		}
		
		//This gets the details of the user who is posting 
		$feedUser = $_SESSION['username'];
		//Gets the canPost attribute of the user
		$checkCanPost = "SELECT `canPost` FROM `members` WHERE `username` = '$feedUser'";
		
		$checkCanPostAnswer = mysqli_query($connection,$checkCanPost);
		$row = mysqli_fetch_assoc($checkCanPostAnswer);
		
		//Checks if the user is allowed to post or not 
		if($row['canPost'] == 1){
			
			//This sanitises and validates the global feed input 
			$feedContent = sanitise($_POST['globalFeedPost'],$connection);
			$feedContent_val = validateString($feedContent,1,140);
			
			if ($feedContent_val == ""){
			
				//Adds to the global feed
				$sql = "INSERT INTO `feed` (username, comment) VALUES ('$feedUser', '$feedContent')";
				
				// no data returned, we just test for true(success)/false(failure):
				if (mysqli_query($connection, $sql)) 
				{
					echo "row inserted<br>";
					
				}
				else 
				{
					die("Error inserting row: " . mysqli_error($connection));
				}
				
			} else {
				
				//If the message failed the validation checks this message is displayed
				echo "Invalid message inputted to global feed";
				
			}
			
		} else {
			
			//If the query shows they can't post don't let them!
			echo "<script> alert('You are not allowed to post to the global feed')</script>";
			
		}

	}
	
}

// finish off the HTML for this page:
require_once "footer.php";

?>