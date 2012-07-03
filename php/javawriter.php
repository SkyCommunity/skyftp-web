<?
   include_once("./php/RemoteRegistry.php");
   include_once("./php/TemplateMaker.php"); 

   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"JavaWriter","Default")) return $text;

   $rg = new RemoteRegistry();

   $data[0]=urldecode($HTTP_POST_VARS["applet_source"]);
   $data[1]=urldecode($HTTP_POST_VARS["applet_width"]);
   $data[2]=urldecode($HTTP_POST_VARS["applet_height"]);
   $data[3]=urldecode($HTTP_POST_VARS["applet_archive"]);

   $text="<APPLET CODE=\"$data[0]\" WIDTH=\"$data[1]\" HEIGHT=\"$data[2]\" archive=\"$data[3]\">\n";
   foreach ($HTTP_POST_VARS as $key => $value) {
     $text=$text."<param name=\"$key\" value=\"$value\">\n";
//	 print "$key=$value<br>\n";
   }
   $text=$text."</APPLET>\n";
?>