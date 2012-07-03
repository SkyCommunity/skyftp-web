<?

   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"Admin","Default")) return $text;

   $kelias=$sk->GetPath("admin","index");
   $mdoc = new TemplateXP();
   $mdoc->ReadFile($kelias);
   
   $dir="./config/";
   
   $handle=opendir("$dir");
   readdir($handle);
   readdir($handle);

   $kelias=$sk->GetPath("admin","index-inside");

   $text="";
   while (($file = readdir($handle))!==false){
   
	   $mdoc2 = new TemplateXP();
	   $mdoc2->ReadFile($kelias);
       $mdoc2->AssignValue("file",$file);
	   $text.=$mdoc2->ParseTemplate();
   }

   $mdoc->AssignValue("content",$text);
   $text=$mdoc->ParseTemplate();

   setcookie ("user",$curuser,time()+3600);
?>