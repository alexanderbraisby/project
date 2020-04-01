<?php
// execute the header script:
require_once "header.php";

if (!isset($_SESSION['loggedInProject'])){
	
	// user isn't logged in, display a message saying they must be:
	echo "You must be logged in to view this page.<br>";
	
}else{
	
	//User is already logged in, read their username from the session:
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
	if ($n > 0){
		
		// use the identifier to fetch one row as an associative array (elements named after columns):
		$row = mysqli_fetch_assoc($result);
		// display their profile data:
		echo <<<_END
				<h2 class = 'center'> SteamID: {$row['steamID']} </h2>
_END;

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=".$steamAPI."&steamid={$row['steamID']}&relationship=friend",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		//Decode the JSON response from Steam
		$decodedFriendsList = json_decode($response,true);
		
		//Check a result was returned, do they have friends?
		if (empty($decodedFriendsList)){
			
			echo "<h2 class = 'center'> No friends found for this account.</h2>";
			
		}else{		
		
		//Build a table to display the friendslist
		echo "<table class = 'center'><th>Steam Name</th><th>Activity</th><th>Avatar</th>";
	
		foreach ($decodedFriendsList['friendslist']['friends'] AS $d){
			//Initialise the request
			$curl = curl_init();
			
			//Set up the request
			curl_setopt_array($curl, array(
				//Using my own steamID for now, will swap that out for user inputted data -- Updated: Now takes user inputs 
				CURLOPT_URL => "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$steamAPI."&steamids=".$d['steamid']."",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
			));

			//The result of the request saved to $response
			$response = curl_exec($curl);

			//Close the connection to save resources
			curl_close($curl);
			
			//Decode the JSON response from Steam
			$decodedResponseFriendTable = json_decode($response,true);
			
			//Steam name for table
			$tableSteamName = $decodedResponseFriendTable['response']['players'][0]['personaname'];
			
			//Profile activity (online/offline etc)	
			//0 - Offline, 1 - Online, 2 - Busy, 3 - Away, 4 - Snooze, 5 - looking to trade, 6 - looking to play. If the player's profile is private, this will always be "0", except if the user has set their status to looking to trade or looking to play, because a bug makes those status appear even if the profile is private.
			$tableSteamActivity = $decodedResponseFriendTable['response']['players'][0]['personastate'];
			
			//Get the player avatar picture (medium size)
			$tableProfilepic = $decodedResponseFriendTable['response']['players'][0]['avatarmedium'];
			
			switch ($tableSteamActivity) {
				case "0":
					$tableSteamActivity = "Offline!";
					break;
				case "1":
					$tableSteamActivity = "Online!";
					break;
				case "2":
					$tableSteamActivity = "Busy!";
					break;
				case "3":
					$tableSteamActivity = "Away!";
					break;
				case "4":
					$tableSteamActivity = "Snooze!";
					break;
				case "5":
					$tableSteamActivity = "Looking to trade!";
					break;
				case "6":
					$tableSteamActivity = "Looking to play!";
					break;
				default:
					$tableSteamActivity = "Unknown";
			}
			
			echo "<tr>	<td>".$tableSteamName."</td> <td>".$tableSteamActivity."</td> <td> <img src=".$tableProfilepic."> </td></tr>";
		}
		
		echo "</table>";}
		

		
	}else{
		
		// no match found, prompt user to set up their profile:
		echo "You still need to set up a profile! <br> Please <a href='set_profile.php'>set up a profile!</a><br>";
	}
	
	// we're finished with the database, close the connection:
	mysqli_close($connection);
		
}

require_once "footer.php";
?>