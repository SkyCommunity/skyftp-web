<?
   include_once("./php/TemplateMaker.php"); 
   include_once("./php/RemoteRegistry.php");

   $veiksmas=$HTTP_GET_VARS["action"];

   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"RemoteRegistryFileEdit",$veiksmas)) return $text;
   
   $kelias=$sk->GetPath("rrfedit","index");
   $mdoc = new TemplateXP();
   $mdoc->ReadFile($kelias);
   
   $rg = new RemoteRegistry();

   if (trim("$HTTP_POST_VARS[content]")!=""){
	   $data=explode("\n",StripSlashes($HTTP_POST_VARS[content]));
//	   $data = array_slice($data, 0,count($data));  
       for ($i=0;$i<count($data);++$i)
		   if (trim("$data[$i]")=="")
		       unset($data[$i]);
	   $rg->SetFileData("$veiksmas",$data);
   }

   $mdoc->AssignValue("file",$veiksmas);
   
   $data=$rg->GetFileData("$veiksmas");
   $text="";
   for ($i=0;$i<count($data);++$i)
      $text.=$data[$i];//."\n";

   $mdoc->AssignValue("content",$text);
   $text=$mdoc->ParseTemplate();

?>