<?

chdir("..");

require_once("./php/TemplateMaker.php"); 
include_once('./php/RemoteRegistry.php');
require_once("./php/XPSkins.php");

$skin=$HTTP_GET_VARS["skin"];

$sk = new XPSkins();
if (isset($HTTP_GET_VARS["skin"])){
   $skin=$HTTP_GET_VARS["skin"];
} else {
   $skin="default";
}
$sk->CurrentSkin($skin);

$rg=new RemoteRegistry("","..");  

if (!isset($HTTP_GET_VARS["file"])) 
	$HTTP_GET_VARS["file"]='';

if (isset($HTTP_GET_VARS["file"])){

  $file=urldecode($HTTP_GET_VARS["file"]);

  if (is_file($file)) {
	  $path=$sk->GetPath("imagem",$item);
      $meta=get_meta_tags($path);
	  $img=getimagesize($file);
   	  $item=$HTTP_GET_VARS["item"];
	  if ($img[0]>$meta['picture-max-width']){
		  $p=$img[0]/$meta['picture-max-width'];
		  $img[0]=$img[0]/$p;
		  $img[1]=$img[1]/$p;
	  }
	  if ($img[0]>$meta['picture-max-height']){
		  $p=$img[0]/$meta['picture-max-height'];
		  $img[0]=$img[0]/$p;
		  $img[1]=$img[1]/$p;
	  }
	  $mdoc = new TemplateXP();
	  $mdoc->ReadFile($path);
	  $mdoc->AssignValue("file",".$file");
	  $mdoc->AssignValue("width",$img[0]);
	  $mdoc->AssignValue("height",$img[1]);
	  $text=$mdoc->ParseTemplate();
  } else {

	  $path=$sk->GetPath("imagem","notexist");
	  $mdoc = new TemplateXP();
	  $mdoc->ReadFile($path);
	  $text=$mdoc->ParseTemplate();
  }

}

print $text;
?>