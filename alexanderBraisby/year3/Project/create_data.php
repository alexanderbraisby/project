<?php

//Read details for MySQL server:
require_once "credentials.php";

//Connect to the host:
$connection = mysqli_connect($dbhost, $dbuser, $dbpass);

//Exit the script with message if there was an error:
if (!$connection){
	die("Connection failed: " . $mysqli_connect_error);
}
  
//Make the database
$sql = "CREATE DATABASE IF NOT EXISTS " . $dbname;

//Test if the database was created successfully
if (mysqli_query($connection, $sql)){
	
	echo "Database created successfully, or already exists<br>";
	
}else{
	
	die("Error creating database: " . mysqli_error($connection));
	
}

//Connect to database:
mysqli_select_db($connection, $dbname);

///////////////////////////////////////////
////////////// MEMBERS TABLE //////////////
///////////////////////////////////////////

//Delete old versions of the table
$sql = "DROP TABLE IF EXISTS members";

//Test if dropped correctly
if (mysqli_query($connection, $sql)){
	
	echo "Dropped existing table: members<br>";
	
}else{
	
	die("Error checking for existing table: " . mysqli_error($connection));
	
}

//Make the table
$sql = "CREATE TABLE members (username VARCHAR(16), password VARCHAR(32), canPost BOOLEAN DEFAULT TRUE, PRIMARY KEY(username))";

//Test if the table was created successfully
if (mysqli_query($connection, $sql)){
	
	echo "Table created successfully: members<br>";
	
}else{
	
	die("Error creating table: " . mysqli_error($connection));
	
}

//Dummy data for the table
$usernames[] = 'Cpt.Kirk';	 	$passwords[] = 'Enterprise'; 	$canPost[] = 0;
$usernames[] = 'Cpt.Picard'; 	$passwords[] = 'NCC-1701-D';	$canPost[] = 1;
$usernames[] = 'Cpt.Sisko';		$passwords[] = 'DeepSpaceNine'; $canPost[] = 1;
$usernames[] = 'Cpt.Janeway';	$passwords[] = 'Voyager';		$canPost[] = 1;
$usernames[] = 'Cpt.Archer';	$passwords[] = 'NX-01'; 		$canPost[] = 1;


//Added additional line to incorporate an admin account
$usernames[] = 'admin'; $passwords[] = 'secret'; $canPost[] = 1;

//Loop through the arrays above and add rows to the table:
for ($i=0; $i<count($usernames); $i++){
	
	//Added a md5 hash to make the passwords more secure. Now I'm aware md5 isn't a cryptographically secure system, I have chosen to use it as a proof of concept. In a live environment a much stronger method of encryption, with salt, would be used
	$sql = "INSERT INTO members (username, password, canPost) VALUES ('$usernames[$i]', md5('$passwords[$i]'), '$canPost[$i]')";
	
	//Test data was inserted correctly
	if (mysqli_query($connection, $sql)){
		
		echo "row inserted<br>";
		
	}else{
		
		die("Error inserting row: " . mysqli_error($connection));
		
	}
}

///////////////////////////////////////////
////////////// FEED TABLE /////////////////
///////////////////////////////////////////

//Delete old versions of the table
$sql = "DROP TABLE IF EXISTS feed";

//Test if dropped correctly
if (mysqli_query($connection, $sql)){
	
	echo "Dropped existing table: feed<br>";
	
}else{	

	die("Error checking for existing table: " . mysqli_error($connection));
	
}

//Make the table
$sql = "CREATE TABLE feed (username VARCHAR(16), comment VARCHAR(140), date timestamp DEFAULT CURRENT_TIMESTAMP, likes INT(7) DEFAULT 0)";

//Test if the table was created successfully
if (mysqli_query($connection, $sql)){
	
	echo "Table created successfully: feed<br>";
	
}else{
	
	die("Error creating table: " . mysqli_error($connection));
	
}

//Clear this array (as it was used above)
$usernames = array(); 

//Dummy data for the table
$usernames[] = 'Cpt.Kirk'; 		$comment[] = 'Beam me up!';																	$date[] = '1996-09-02 00:00:00';
$usernames[] = 'Cpt.Picard';	$comment[] = 'Tea. Earl Grey. Hot'; 														$date[] = '1996-09-02 01:00:00';
$usernames[] = 'Cpt.Sisko'; 	$comment[] = 'My father used to say that the road to hell is paved with good intentions';	$date[] = '1996-09-02 02:00:00';
$usernames[] = 'Cpt.Janeway';	$comment[] = 'Coffee. Black'; 																$date[] = '1996-09-02 03:00:00';
$usernames[] = 'Cpt.Archer'; 	$comment[] = 'Warp 5!'; 																	$date[] = '1996-09-02 04:00:00';


//Added additional line to incorporate an admin comment
$usernames[] = 'admin'; 		$comment[] = 'The Q Collective'; 															$date[] = '1996-09-02 08:00:00';

//Loop through the arrays above and add rows to the table:
for ($i=0; $i<count($usernames); $i++){
	
	$sql = "INSERT INTO feed (username, comment, date) VALUES ('$usernames[$i]', '$comment[$i]', '$date[$i]')";
	
	//Test data was inserted correctly
	if (mysqli_query($connection, $sql)){
		
		echo "row inserted<br>";
		
	}else{
		
		die("Error inserting row: " . mysqli_error($connection));
		
	}
}

////////////////////////////////////////////
////////////// PROFILES TABLE //////////////
////////////////////////////////////////////

//Delete old versions of the table
$sql = "DROP TABLE IF EXISTS profiles";

//Test if dropped correctly
if (mysqli_query($connection, $sql)){
	
	echo "Dropped existing table: profiles<br>";
	
}else{
	
	die("Error checking for existing table: " . mysqli_error($connection));
	
}

//Make the table
$sql = "CREATE TABLE profiles (username VARCHAR(16), firstname VARCHAR(40), lastname VARCHAR(50), email VARCHAR(50), dob DATE, steamID VARCHAR(17), PRIMARY KEY (username))";

//Test if the table was created successfully
if (mysqli_query($connection, $sql)){
	
	echo "Table created successfully: profiles<br>";
	
}else{
	
	die("Error creating table: " . mysqli_error($connection));
	
}

//Clear this array (as it was used above)
$usernames = array(); 

//Dummy data for the table
$usernames[] = 'Cpt.Kirk';		$firstnames[] = 'James'; 		$lastnames[] = 'Kirk'; 		$emails[] = 'kirk@roddenberry.com'; 	$dobs[] = '1966-09-08'; $steamIDs[] = '76561198116773767';
$usernames[] = 'Cpt.Picard';	$firstnames[] = 'Jean-Luc'; 	$lastnames[] = 'Picard'; 	$emails[] = 'picard@roddenberry.com'; 	$dobs[] = '1987-09-28'; $steamIDs[] = '76561198116773767';
$usernames[] = 'Cpt.Janeway'; 	$firstnames[] = 'Kathryn'; 		$lastnames[] = 'Janeway'; 	$emails[] = 'janeway@roddenberry.com'; 	$dobs[] = '1995-01-16'; $steamIDs[] = '76561198111931808';

//Added additional line to incorporate an admin account
$usernames[] = 'admin'; 		$firstnames[] = 'Alexander'; 	$lastnames[] = 'Braisby'; 	$emails[] = 'admin@socialnetwork.com'; 	$dobs[] = '1996-09-02';	$steamIDs[] = '76561198116773767';

//Loop through the arrays above and add rows to the table:
for ($i=0; $i<count($usernames); $i++){
	
	$sql = "INSERT INTO profiles (username, firstname, lastname, email, dob, steamID) VALUES ('$usernames[$i]', '$firstnames[$i]', '$lastnames[$i]', '$emails[$i]', '$dobs[$i]','$steamIDs[$i]')";
	
	//Test data was inserted correctly
	if (mysqli_query($connection, $sql)){
		
		echo "row inserted<br>";
		
	}else{
		
		die("Error inserting row: " . mysqli_error($connection));
		
	}
}


////////////////////////////////////////////
////////////// Postbox TABLE ///////////////
////////////////////////////////////////////

//Delete old versions of the table
$sql = "DROP TABLE IF EXISTS postbox";

//Test if dropped correctly
if (mysqli_query($connection, $sql)){
	
	echo "Dropped existing table: postbox<br>";
	
}else{	

	die("Error checking for existing table: " . mysqli_error($connection));
	
}

//Make the table
$sql = "CREATE TABLE postbox (messageID int NOT NULL AUTO_INCREMENT, date timestamp DEFAULT CURRENT_TIMESTAMP, message VARCHAR(140), recipient VARCHAR(16), author VARCHAR(16), PRIMARY KEY (messageID), FOREIGN KEY (recipient) REFERENCES members(username) ON DELETE CASCADE, FOREIGN KEY (author) REFERENCES members(username) ON DELETE CASCADE)";

//Test if the table was created successfully
if (mysqli_query($connection, $sql)){
	
	echo "Table created successfully: postbox<br>";
	
}else{
	
	die("Error creating table: " . mysqli_error($connection));
	
}

//Dummy data for the table
$timeSent[] = '1966-09-08'; 	$message[] = 'Archer to Kirk'; 		$recipient[] = 'Cpt.Kirk'; 		$author[] = 'Cpt.Archer';
$timeSent[] = '1966-09-08'; 	$message[] = 'Kirk to Picard'; 		$recipient[] = 'Cpt.Picard';	$author[] = 'Cpt.Kirk';
$timeSent[] = '1966-09-08'; 	$message[] = 'Picard to Sisko';	 	$recipient[] = 'Cpt.Sisko'; 	$author[] = 'Cpt.Picard';
$timeSent[] = '1966-09-08'; 	$message[] = 'Sisko to Janeway'; 	$recipient[] = 'Cpt.Janeway';	$author[] = 'Cpt.Sisko';
$timeSent[] = '1966-09-08'; 	$message[] = 'Janeway to Archer'; 	$recipient[] = 'Cpt.Archer';	$author[] = 'Cpt.Janeway';
$timeSent[] = '1966-09-08'; 	$message[] = 'QQQQQQQQQQQQQQQ'; 	$recipient[] = 'Admin';			$author[] = 'Admin';

//Loop through the arrays above and add rows to the table:
for ($i=0; $i<count($timeSent); $i++){
	
	$sql = "INSERT INTO postbox (date, message, recipient, author) VALUES ('$timeSent[$i]', '$message[$i]', '$recipient[$i]', '$author[$i]')";
	
	//Test data was inserted correctly
	if (mysqli_query($connection, $sql)){
		
		echo "row inserted<br>";
		
	}else{
		
		die("Error inserting row: " . mysqli_error($connection));
		
	}
}


// we're finished, close the connection:
mysqli_close($connection);
?>