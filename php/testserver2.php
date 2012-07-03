<?
  
   chdir("..");

   include_once('./php/NetFunctions.php');
   include_once('./php/RemoteRegistry.php');
   require_once("./php/XPSkins.php");

	$sk = new XPSkins();
	$skin=trim($HTTP_GET_VARS["skin"]); 
	if ($skin=="") $skin="default";
	$sk->CurrentSkin($skin);

   $rg=new RemoteRegistry("","..");
   $path=$sk->GetPath2("testserver");

   $nf=new NetFunctions();
   $rez=$nf->SmartTestServer(urldecode($HTTP_GET_VARS["server"]),10);
   if ($rez==0){
	 $value='on-line';
   } elseif ($rez==1){
	 $value='bad adress';
   } else {
   	 $value='off-line';
   }

echo '<?xml version="1.0" standalone="no"?>';
?>

<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" 
  "http://www.w3.org/Graphics/SVG/1.0/DTD/svg10.dtd">
<svg width="10cm" height="3cm" viewBox="0 0 1000 300"
     xmlns="http://www.w3.org/2000/svg" version="1.1">
  <text x="250" y="150" 
        font-family="Verdana" font-size="55" fill="blue" >
        <?=$value?>
  </text>
</svg>
