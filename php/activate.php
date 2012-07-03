<?
   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"Activate","Default")) return $text;

   if ($rg->ReadValue("activities","$user")>$rg->ReadValue("rs","MaxInactivity")){
  	   $rg->WriteValue("activities","$user",0);
       $kelias=$sk->GetPath("activate","activated");
   } else {
       $kelias=$sk->GetPath("activate","index");
   }

   $mdoc = new TemplateXP();
   $mdoc->ReadFile($kelias);
   $text=$text.$mdoc->ParseTemplate();		
?>