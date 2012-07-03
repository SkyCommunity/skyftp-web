<?
//Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
if ($text=CanAccess($user,"Show","Default")) return $text;

$action=$HTTP_GET_VARS["action"];

//Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
if ($text=CanAccess($user,"Show","$action")) return $text;

if (trim("$action")=="") {
	return ($text="Ðios srities ðiuo metu nëra");
}

$file="./data/show/$action.html";
$text=implode(" ",@file($file));

?>