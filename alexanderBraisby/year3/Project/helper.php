<?php
// function to sanitise (clean) user data:
function sanitise($str, $connection)
{
	if (get_magic_quotes_gpc())
	{
		// just in case server is running an old version of PHP with "magic quotes" running:
		$str = stripslashes($str);
	}
	// escape any dangerous characters, e.g. quotes:
	$str = mysqli_real_escape_string($connection, $str);
	// ensure any html code is safe by converting reserved characters to entities:
	$str = htmlentities($str);
	// return the cleaned string:
	return $str;
}

// if the data is valid return an empty string, if the data is invalid return a help message
function validateString($field, $minlength, $maxlength) 
{
    if (strlen($field)<$minlength) 
    {
		// wasn't a valid length, return a help message:		
        return "Minimum length: " . $minlength; 
    }
	elseif (strlen($field)>$maxlength) 
    { 
		// wasn't a valid length, return a help message:
        return "Maximum length: " . $maxlength; 
    }
	// data was valid, return an empty string:
    return ""; 
}

// if the data is valid return an empty string, if the data is invalid return a help message
function validateInt($field, $min, $max) 
{ 
	// see PHP manual for more info on the options: http://php.net/manual/en/function.filter-var.php
	$options = array("options" => array("min_range"=>$min,"max_range"=>$max));
    
	if (!filter_var($field, FILTER_VALIDATE_INT, $options)) 
    { 
		// wasn't a valid integer, return a help message:
        return "Not a valid number (must be whole and in the range: " . $min . " to " . $max . ")"; 
    }
	// data was valid, return an empty string:
    return ""; 
}


//This function checks if the email inputted is a valid email address and returns blank if it is 
function validateEmail($field){
	
	if (!filter_var($field, FILTER_VALIDATE_EMAIL)){
		return "Invalid email format"; 
	}
	
	return "";
	
}

//This function checks if the date inputted is a valid date and returns blank if it is 
function validateDate($field){
	
	if (($timestamp = strtotime($field)) <> true){
		return "Invalid date format"; 
	}
	
	return "";
	
}


?>