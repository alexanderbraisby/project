<?php

// database connection details:
require_once "credentials.php";

// our helper functions:
require_once "helper.php";

// start/restart the session:
session_start();

//Silence warnings to the site
//error_reporting (E_ALL ^ E_NOTICE);

if (isset($_SESSION['loggedInProject']))
{
	// THIS PERSON IS LOGGED IN
	// show the logged in menu options:

echo <<<_END
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="./css/style.css">
<title>A Social Network - By Alexander Braisby</title>

<body class = 'center'>
<div id="headerLinks">
<a href='about.php'>about</a> ||
<a href='set_profile.php'>set profile</a> ||
<a href='link_account.php'>link accounts</a> ||
<a href='show_profile.php'>show profile</a> ||
<a href='browse_profiles.php'>browse profiles</a> ||
<a href='message.php'>message</a> ||
<a href='global_feed.php'>global feed</a> ||
<a href='sign_out.php'>sign out ({$_SESSION['username']})</a>
</div>
_END;
	// add an extra menu option if this was the admin:
	if ($_SESSION['username'] == "admin")
	{
		echo " || <a href='developer_tools.php'>developer tools</a>";
	}
}
else
{
	// THIS PERSON IS NOT LOGGED IN
	// show the logged out menu options:
	
echo <<<_END
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="./css/style.css">
<body class = 'center'>
<div id="headerLinks">
<a href='about.php'>about</a> ||
<a href='sign_up.php'>sign up</a> ||
<a href='sign_in.php'>sign in</a>
</div>
_END;
}
echo <<<_END
<h1>By Alexander Braisby</h1>
_END;
?>