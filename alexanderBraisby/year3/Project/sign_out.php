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
	// user just clicked to logout, so destroy the session data:
	// first clear the session superglobal array:
	$_SESSION = array();
	// then the cookie that holds the session ID:
	setcookie(session_name(), "", time() - 2592000, '/');
	// then the session data on the server:
	session_destroy();

	echo "You have successfully logged out, please <a href='sign_in.php'>click here</a><br>";
}

// finish of the HTML for this page:
require_once "footer.php";

?>