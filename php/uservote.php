<?

 include_once("./php/TemplateMaker.php"); 
 include_once("./php/RemoteRegistry.php");

 //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
 if ($text=CanAccess($user,"UserVote","Default")) return $text;

 $site=$HTTP_GET_VARS["site"];
 
 $rg = new RemoteRegistry();

 $IP = getenv("HTTP_X_FORWARDED_FOR");
 if(trim("$IP")=="") {
   $IP = getenv("REMOTE_ADDR");
 }

 $checkvote="[$username] $IP";

 //Priskiriami skirtingi taðkai
 $taskai=20;
 $LastVoteFrom=$rg->ReadValue("uservote","LastVoteFrom");
 $MyName=$rg->ReadValue("users",$HTTP_POST_VARS["UserVote"].".Field.1.Value");
 if ($checkvote=="[] $IP") $taskai=1;
 if ($MyName==$username) $taskai=-20; // Uz sukciavima :)
 if ($LastVoteFrom==$checkvote) $taskai=0;

 //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
 if ($text=CanAccess($user,"UserVote","Vote")) return $text;

	 
 //Vyksta balsavimas
 $rg->WriteValue("uservote","LastVoteFrom",$checkvote);
	
 $rg->WriteValue("uservote",$HTTP_POST_VARS["UserVote"], $rg->ReadValue("uservote",$HTTP_POST_VARS["UserVote"]) + $taskai);

 $kelias=$sk->GetPath("uservote","voted");

 $mdoc = new TemplateXP();
 $mdoc->ReadFile($kelias);
 $mdoc->AssignValue("score",$taskai);
 $text=$mdoc->ParseTemplate();
		  
?>