<?
include_once("./php/TemplateMaker.php"); 

//Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
if ($text=CanAccess($user,"Album","Default"))  return $text;

$kelias=$sk->GetPath("album","index");

$mdoc = new TemplateXP();
$mdoc->ReadFile($kelias);
$ameta=get_meta_tags($kelias);
$acount=$ameta["count"];
$acount=trim($acount);
//if (trim($acount<1) $acount=3;

$textN="";
$basedir="./images/album";

$veiksmas=urldecode($HTTP_GET_VARS["file"]);

$dir=urldecode($HTTP_GET_VARS["folder"]);
if ($dir=="") {
  $dir=$basedir;
  if ($veiksmas=="")
    $adesc="Ðakninio katalogo nuotraukos:";
} else {
  if ($veiksmas=="")
    $adesc="Katalogo <b>$dir</b> nuotraukos:";
  $dir=$basedir."/".$dir;
}

if ($veiksmas!="") {
  $url="index.php?site=album&folder=".urlencode(urldecode($HTTP_GET_VARS["folder"]))."&user=$curuser&skin=$skin";
  $kelias=$sk->GetPath("album","item_show");
  $mdoc = new TemplateXP();
  $mdoc->ReadFile($kelias);
  $mdoc->AssignValue("url","$dir/$veiksmas");
  $mdoc->AssignValue("url2","$url");
  $mdoc->AssignValue("title","$veiksmas");
  $text=$mdoc->ParseTemplate();
  return;
}  

$handle=opendir("$dir");
readdir($handle);
readdir($handle);

$nr=0;
$textNa="";
if ($dir!==$basedir) {
  ++$nr;
  $kelias=$sk->GetPath("album","item_upfolder");
  $mdoc3 = new TemplateXP();
  $mdoc3->ReadFile($kelias);
  $url="index.php?site=album&user=$curuser&skin=$skin";  
  $mdoc3->AssignValue("url","$url");
  $textNa=$textNa.$mdoc3->ParseTemplate();
}


if ($nr>=$acount) {
   $nr=0;
   $kelias=$sk->GetPath("album","index-inside");
   $mdoc2 = new TemplateXP();
   $mdoc2->ReadFile($kelias);
   $mdoc2->AssignValue("contents",$textNa);
   $textN=$textN.$mdoc2->ParseTemplate();
}

while (($file = readdir($handle))!==false){
  
  if ($nr==0) $textNa="";
  
$dir2=$dir."/".$file;
$h2=@opendir("$dir2");
if ($h2=="") {
  $url="index.php?site=album&file=".urlencode($file)."&folder=".urlencode(urldecode($HTTP_GET_VARS["folder"]))."&user=$curuser&skin=$skin";
  $url2=$dir."/_small/".$file;
  $tcool=$file;
//  $size=(filesize($url)/100);
} else {
  $url="index.php?site=album&folder=".urlencode($file)."&user=$curuser&skin=$skin";
  readdir($h2);
  readdir($h2);
  while (($f2 = readdir($h2))=="_small"){
    }
//  $f2 = readdir($h2);
  $url2=$dir."/".$file."/_small/".$f2;
  $tcool="<b>[-$file-]</b>";
//  $size="Nezinoma";
}

if ($file!="_small") {
  ++$nr;
  $kelias=$sk->GetPath("album","item_picture");
  $mdoc3 = new TemplateXP();
  $mdoc3->ReadFile($kelias);
  $mdoc3->AssignValue("url","$url");
  $mdoc3->AssignValue("url2","$url2");
  $mdoc3->AssignValue("title","$tcool");
  $textNa=$textNa.$mdoc3->ParseTemplate();
 } 

 if ($nr>=$acount) {
   $nr=0;
   $kelias=$sk->GetPath("album","index-inside");
   $mdoc2 = new TemplateXP();
   $mdoc2->ReadFile($kelias);
   $mdoc2->AssignValue("contents",$textNa);
   $textN=$textN.$mdoc2->ParseTemplate();
 }
 
}

if (($nr<$acount)&&($nr!=0)){
   $nr=0;
   $kelias=$sk->GetPath("album","index-inside");
   $mdoc2 = new TemplateXP();
   $mdoc2->ReadFile($kelias);
   $mdoc2->AssignValue("contents",$textNa);
   $textN=$textN.$mdoc2->ParseTemplate();
}

$mdoc->AssignValue("contents",$textN);
$mdoc->AssignValue("description",$adesc);
$text=$mdoc->ParseTemplate();
?>