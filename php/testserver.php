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

   $checkurl=$rg->ReadValue("forms","CheckField");
   $dp=$rg->ReadValue("forms","Protocol");
   $fem=$rg->ReadValue("forms","E-MailField");
   $id=urldecode($HTTP_GET_VARS["server"]);
   $server=$rg->ReadValue("users","$id.Field.$checkurl.Value");
   if (!strstr($server,$dp)) $server = $dp.$server;

   $nf=new NetFunctions();
   $rez=$nf->SmartTestServer($server,10);
   if ($rez==0){
//	 print "$path/working.png";
     header("Location: $path/working.png \r\n");
     $rg->WriteValue("activities","$id","0");
   } elseif ($rez==1){
	 header("Location: $path/badip.png \r\n");
     $rg->WriteValue("activities","$id",$rg->ReadValue("activities","$id")+1);
	 if ($rg->ReadValue("activities","$id")>$rg->ReadValue("rs","MaxInactivity"))
		 $nf->SendMail($rg->ReadValue("users","$id.Field.1.Value"),$rg->ReadValue("users","$id.Field.$fem.Value"),"SkyFTP tinklalapio prane�imas","Sveiki!\r\n\nJ�s� FTP serveris buvo pa�ym�tas neaktyviu, t.y. jis daugiau nebebus rodomas FTP serveri� s�ra�e. Nor�dami j� aktyvuoti, turite u�eiti � SkyFTP svetain�, prisijungti ir paspausti ant Aktyvavimas nuorodos.\r\n\nS�km�s!");
   } else {
   	 header("Location: $path/notworking.png \r\n");
     $rg->WriteValue("activities","$id",$rg->ReadValue("activities","$id")+2);
	 if ($rg->ReadValue("activities","$id")>$rg->ReadValue("rs","MaxInactivity"))
		 $nf->SendMail($rg->ReadValue("users","$id.Field.1.Value"),$rg->ReadValue("users","$id.Field.$fem.Value"),"SkyFTP tinklalapio prane�imas","Sveiki!\r\n\nJ�s� FTP serveris buvo pa�ym�tas neaktyviu, t.y. jis daugiau nebebus rodomas FTP serveri� s�ra�e. Nor�dami j� aktyvuoti, turite u�eiti � SkyFTP svetain�, prisijungti ir paspausti ant Aktyvavimas nuorodos.\r\n\nS�km�s!");

   }

?>