<? // Klaidu reportai :)
   error_reporting(0);

// user defined error handling function
function userErrorHandler ($errno, $errmsg, $filename, $linenum, $vars) {

/* switch ($errno) {
case FATAL:
echo "<b>FATAL</b> [$errno] $errstr<br>\n";
echo " Fatal error in line ".$errline." of file ".$errfile;
echo ", PHP ".PHP_VERSION." (".PHP_OS.")<br>\n";
echo "Aborting...<br>\n";
exit -1;
break;
case ERROR:
echo "<b>ERROR</b> [$errno] $errstr<br>\n";
break;
case WARNING:
echo "<b>WARNING</b> [$errno] $errstr<br>\n";
break;
default:
echo "Unkown error type: [$errno] $errstr<br>\n";
break;
}
*/
// timestamp for the error entry
$dt = date("Y-m-d H:i:s");
// define an assoc array of error string
// in reality the only entries we should
// consider are 2,8,256,512 and 1024
$errortype = array (
1 => "Error", 2 => "Warning", 4 => "Parsing Error", 8 => "Notice",
16 => "Core Error", 32 => "Core Warning", 64 => "Compile Error", 128 => "Compile Warning",
256 => "User Error", 512 => "User Warning", 1024=> "User Notice" );
// set of errors for which a var trace will be saved
$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

$err = " Error entry: \n";
$err .= "\t datetime <i>".$dt." </i>";
$err .= "\t errornum: <b>".$errno."</b>";
$err .= "\t errortype: <b>".$errortype[$errno]."</b><br>\n";
$err .= "\t errormsg: <b>".$errmsg."</b><br>\n";
$err .= "\t scriptname: <b>".$filename."</b> \n";
$err .= "\t scriptlinenum: <b>".$linenum."</b><br>\n";

if (in_array($errno, $user_errors))
$err .= "\tUser errors: <b>".wddx_serialize_value($vars,"Variables")."</b><br>\n";
$err .= " \n\n"; // end of Error entry

// for testing

echo $err;

// save to the error log, and e-mail me if there is a critical user error
error_log($err, 3, "error.log");
if ($errno == E_USER_ERROR)
{ echo "Critical user error";
// mail( "phpdev@mydomain.com","Critical User Error",$err);
}

// trigger_error("Incorrect parameters, arrays expected", E_USER_ERROR);
// trigger_error("Coordinate $i in vector 1 is not a number, using zero", 
// E_USER_WARNING);

}

$old_error_handler = set_error_handler("userErrorHandler");
?>