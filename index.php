<?php

//Maþoji konfiguracija :)
$_GLOBAL['Engine']['Name']='SkinEngineXP';
$_GLOBAL['Engine']['Version']='0.7';
$_GLOBAL['Engine']['Author']='MekDrop'; //Labai praðau palikti ðià eilutæ
$_GLOBAL['Engine']['ModifiedBy']='MekDrop'; //Èia galite patys pasireikðti
$_GLOBAL['Engine']['URL']='http://www.21int-lt.tk';

// Áterpiamos reikalingos dalys
require_once("./php/TemplateMaker.php"); 
require_once("./php/ArrayDatabase.php");
require_once("./php/Privileges.php");
require_once("./php/XPSkins.php");
require_once("./php/Filters.php");
include_once("./php/XPFormat.php");
include_once("./php/SEXPSesion.php");
include_once("./php/MiscFunctions.php");

//require_once("./php/error.lib.php");
//error_reporting(0);

//Masyvas duombaziu klasiu
$ar=new ArrayDatabase();

//XP Sesijos
$sexp=new SEXPSession();

//Privilegijø sistema
$pr=new AccessControl();

//Apvalkalai
$sk = new XPSkins();

//$rg = new RemoteRegistry();

//Paimama srities pav
$site=GetSite();

// Tikrina ar apvalkalo nera
$skin=GetSkin();
$sk->CurrentSkin($skin);

//Tikrina ar nereiketu isiminti apvalkalo
if (isset($HTTP_GET_VARS["autosaveconfig"])) {
  echo("<SCRIPT LANGUAGE=\"JavaScript\">\n");
  echo("function SetCookie (name, value) {\n");
  echo("var argv = SetCookie.arguments;\n");
  echo("var argc = SetCookie.arguments.length;\n");
  echo("var expires = (argc > 2) ? argv[2] : null;\n");
  echo("var path = (argc > 3) ? argv[3] : null;\n");
  echo("var domain = (argc > 4) ? argv[4] : null;\n");
  echo("var secure = (argc > 5) ? argv[5] : false;\n");
  echo("document.cookie = name + \"=\" + escape (value) +\n");
  echo("((expires == null) ? \"\" : (\"; expires=\" + expires.toGMTString())) +\n");
  echo("((path == null) ? \"\" : (\"; path=\" + path)) +\n");
  echo("((domain == null) ? \"\" : (\"; domain=\" + domain)) +\n");
  echo("((secure == true) ? \"; secure\" : \"\");\n");
  echo("}\n");
  echo("SetCookie(\"skin\",\"$skin\");\n");
  echo("</SCRIPT>\n");
}

//Paima informacija apie dabartini vartotoja
$user=$sexp->GetUser();

$fi=new Filters();
//Tikrina ar vartotojo ip nëra priskirtas Bannas
$fi->isBanned($user['IP']);

$kelias=$sk->GetPath("index","index");

$doc = new TemplateXP();
$doc->ReadFile($kelias);
$doc->SmartReplace(true);

$action='';
if (isset($HTTP_GET_VARS["action"]))
	$action=$HTTP_GET_VARS["action"];

$language='LT';
if (isset($HTTP_GET_VARS["lng"]))
	$language=$HTTP_GET_VARS["lng"];

$item='';
if (isset($HTTP_GET_VARS["item"]))
	$language=$HTTP_GET_VARS["item"];

if ($user['sessionid']>-1){
       $kelias=$sk->GetPath("login","logged");
	   $mdoc = new TemplateXP();
       $mdoc->ReadFile($kelias);
	   $mdoc->AssignValue("username",$user['name']);
	   $mdoc->AssignValue("url","?site=login&action=logout");
       $doc->AssignValue("login",$mdoc->ParseTemplate());		
} else {
     $kelias=$sk->GetPath("login","index");
     $mdoc = new TemplateXP();
     $mdoc->ReadFile($kelias);
	 $doc->AssignValue("login",$mdoc->ParseTemplate());		
}

$adminmenu="";
if ($pr->CanIDo($user,"Index","AdminMenu"))
	$adminmenu=implode(file($sk->GetPath("login","adminmenu")),"\n\r");

$text='';
include("./php/$site.php");
$doc->AssignValue("content",$text);

$doc->AssignValue("adminmenu",$adminmenu);

//include("./php/uservote.lib.php");
//$doc->AssignValue("uservote",$text);

//print ']['.$user['id'];
$doc->AssignValue("defaulturl","skin=$skin&user=".$user['sessionid']);
$doc->AssignValue("defaultlink","skin=$skin&user=".$user['sessionid']);
$doc->AssignValue("defaultpath",'./skins/'.$skin);
$doc->AssignValue("user",$user['sessionid']);
$doc->AssignValue("skin","$skin");
$doc->AssignValue("username",$user['name']);
$doc->AssignValue("engine",$_GLOBAL['Engine']['Name'].' '.$_GLOBAL['Engine']['Version']);
$doc->AssignValue("engine-name",$_GLOBAL['Engine']['Name']);
$doc->AssignValue("engine-version",$_GLOBAL['Engine']['Version']);
$doc->AssignValue("engine-url",$_GLOBAL['Engine']['URL']);
$doc->AssignValue("engine-modifiedby",$_GLOBAL['Engine']['ModifiedBy']);
$doc->AssignValue("engine-author",$_GLOBAL['Engine']['Author']);

$ar->Save();

//Spausdinamas suformuotas puslapis
print $doc->ParseTemplate();

?>