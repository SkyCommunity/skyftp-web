<?
include_once("./php/RemoteRegistry.php");
include_once("./php/TemplateMaker.php"); 

//Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
if ($text=CanAccess($user,"ServerView","Default")) return $text;


$action="";
if (isset($HTTP_GET_VARS["action"])) $action=$HTTP_GET_VARS["action"];

$i=$action;

$checkurl=$rg->ReadValue("forms","CheckField");
$dp=$rg->ReadValue("forms","Protocol");
$fsn=$rg->ReadValue("forms","ServerNameField");
$fem=$rg->ReadValue("forms","E-MailField");
$fsp=$rg->ReadValue("forms","SpeedLimitField");
$fan=$rg->ReadValue("forms","AnonymousField");

$kelias=$sk->GetPath("serverview","index");

$mdoc = new TemplateXP();
$mdoc->ReadFile($kelias);

$fuser=$rg->ReadValue("users","$i.Field.1.Value"); //Administratoriaus vardas
$fuser1=$rg->ReadValue("users","$i.Field.$fsn.Value"); //Serverio vardas
$fuser2=$rg->ReadValue("users","$i.Field.$fem.Value"); //E-mail
$fuser3=$rg->ReadValue("users","$i.Field.$fsp.Value"); //Greicio limitas
$fuser4=$rg->ReadValue("users","$i.Field.$fan.Value"); //Anonymous
$server=$rg->ReadValue("users","$i.Field.$checkurl.Value"); //Server

if (!strstr($server,$dp)) $server=$dp.$server;
if (trim("$fuser1")=="") $fuser1="$fuser FTP";
if (trim("$fuser3")!="") {
	$fuser3='Taip';
} else {
	$fuser3='Ne';
}
if (trim("$fuser4")!="") {
	$fuser4='Taip';
} else {
	$fuser4='Ne';
}

$mdoc->AssignValue('server',$fuser1);
$mdoc->AssignValue('e-mail',$fuser2);
$mdoc->AssignValue('admin',$fuser);
$mdoc->AssignValue('anonymous',$fuser4);
$mdoc->AssignValue('speedlimit',$fuser3);
$mdoc->AssignValue('address',$server);

$text=$mdoc->ParseTemplate();
?>