<?php

   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"Forms","Default")) return $text;

   $action="";
   $action=$HTTP_GET_VARS["action"];
   
   $kelias=$sk->GetPath("forms","index-$action");
   
   $rg = new RemoteRegistry();
   $mvalue = $rg->ReadValue("forms","Fields");
   
   $text="";

   $sexp=new SEXPSession();
    
   //Anketos rodymas   
   if (($action=="") || ($action=="show")) 
      for($i=1;$i<($mvalue+1);++$i){
	     $mdata[0]=$rg->ReadValue("forms","Field.$i.Caption");
		 $mdata[1]=$rg->ReadValue("forms","Field.$i.Type");
		 $mdata[2]=$rg->ReadValue("users",$sexp->Decrypt($user).".Field.$i.Value");
		 if (strtolower($mdata[1])!="password") {
		    if (strtolower($mdata[1])=="checkbox") {
			   if (strtolower($mdata[2])=='') {
			      $mdata[2]='Ne';
			   } else {
			      $mdata[2]='Taip';
			   }	  
			}
	        $mdoc = new TemplateXP();
            $mdoc->ReadFile($kelias);
		    $mdoc->AssignValue("label",$mdata[0]);  
		    $mdoc->AssignValue("value",$mdata[2]);		
 		    $text=$text.$mdoc->ParseTemplate();			
		 }
	  }
  
  //Anketos koregavimas
     if ($action=="edit") {
	  $kelias=$sk->GetPath("forms","edit");
      $mdoc2 = new TemplateXP();
      $mdoc2->ReadFile($kelias);
//	  $text="";
  	  $kelias=$sk->GetPath("forms","index");	 
	  $sub=1;
	  $sitem=$rg->ReadValue("forms","Subs.$sub.Start");
      for($i=1;$i<($mvalue+1);++$i){
		 if ($i==$sitem) {
			 $text .= "<div class=\"formsub\">".$rg->ReadValue("forms","Subs.$sub.Caption")."</div>";
			 ++ $sub;
	 	     $sitem=$rg->ReadValue("forms","Subs.$sub.Start");
		 }
	     $mdata[0]=$rg->ReadValue("forms","Field.$i.Caption");
		 $mdata[1]=$rg->ReadValue("forms","Field.$i.Type");
		 $mdata[2]=$rg->ReadValue("users","$user.Field.$i.Value");
         $mdoc = new TemplateXP();
         $mdoc->ReadFile($kelias);
         $mdoc->AssignValue("label",$mdata[0]);  
		 if (!strstr($mdata[1],"List")) {
		     $mdoc->AssignValue("value","<input name=\"$i\" type=\"$mdata[1]\" value=\"$mdata[2]\">\n");	
		 } else {
			 $listcount=$rg->ReadValue("forms","$mdata[1].Count")+1;
			 $txt="<Select name=\"$i\">\n";
			 for ($kb=1;$kb<$listcount;++$kb){
				$litem=$rg->ReadValue("forms","$mdata[1].Item.$kb.Caption");
				$selected="";
				if ($litem==$mdata[2]) $selected=" Selected";
                $txt .= "<Option$selected>$litem</option>\n";
			 }
			 $txt .= "</Select>\n";
 		     $mdoc->AssignValue("value",$txt);	
		 }
 	     $text .= $mdoc->ParseTemplate();			
	  }
	  $mdoc2->AssignValue("content","$text");
      $text=$mdoc2->ParseTemplate();			
      return;
     }

  //Anketos irasymas
  if ($action=="save") {
      for($i=1;$i<($mvalue+1);++$i)
	      $rg->WriteValue("users","$user.Field.$i.Value",$HTTP_POST_VARS["$i"]);
     $kelias=$sk->GetPath("forms","saved");
     $mdoc2 = new TemplateXP();
     $mdoc2->ReadFile($kelias);
     $text=$mdoc2->ParseTemplate();
   }

 //Anketu duomenu rodymas pagal kriterijus
 if ($action=="showby"){
   
   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"Forms","ShowBy")) return $text;

   $count=$rg->ReadValue("users","FieldCount");
   $criteria[0]=$HTTP_GET_VARS["criteria"];
   $criteria[1]=$HTTP_GET_VARS["value"];
   $criteria[2]="";

   $criteria[2]=$HTTP_GET_VARS["alinks"];
   for ($i=0;$i<$count;++$i){
	  $value[0]=$rg->ReadValue("users","$i.Field.$criteria[0].Value");
	  if ($rg->ReadValue("forms","Field.$criteria[0].Type")=="password")
		  $value[0]="********";
	  $value[1]=$rg->ReadValue("users","$i.Field.$criteria[1].Value");
	  if ($rg->ReadValue("forms","Field.$criteria[1].Type")=="password")
		  $value[1]="********";
	  $mdoc = new TemplateXP();
      $mdoc->ReadFile($kelias);
	  if ($criteria[2]!="") {
 	    $mdoc->AssignValue("label","<a href=\"index.php?action=show&ur=$i\"> $value[0]</a>\n");  
	    $mdoc->AssignValue("value","<a href=\"$value[1]\">$value[1]</a>\n");		
	  } else {
 	    $mdoc->AssignValue("label",$value[0]);  
	    $mdoc->AssignValue("value",$value[1]);		
	  }
      $text=$text.$mdoc->ParseTemplate();			
   }
 }

  //Registracija
     if ($action=="register") {
       
	   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
	   if ($text=CanAccess($user,"Forms","Registration")) return $text;

       //Pirmas zingnis
	   if (($HTTP_GET_VARS["step"]==1) or ($HTTP_GET_VARS["step"]=="")) {
		   $mdata=file("./data/license.html");
		   for ($i=0;$i<count($mdata);++$i)
	         $text=$text.$mdata[$i];
		   $kelias=$sk->GetPath("forms","register.1");
           $mdoc = new TemplateXP();
           $mdoc->ReadFile($kelias);
		   $mdoc->AssignValue("license",$text);
		   $text=$mdoc->ParseTemplate();	
		   return;
	   }

	   
	   //Antras zingnis
	   if ($HTTP_GET_VARS["step"]==2) {
		  if (($HTTP_POST_VARS["Sutinku"]==0)&&($HTTP_POST_VARS["Sutinku"]!="")){
		   $kelias=$sk->GetPath("forms","error_notagree");
           $mdoc = new TemplateXP();
           $mdoc->ReadFile($kelias);
		   $text=$mdoc->ParseTemplate();	
		   return;
		  }
  	      $kelias=$sk->GetPath("forms","register.2");
          $mdoc2 = new TemplateXP();
          $mdoc2->ReadFile($kelias);
		  $text="";
  	      $kelias=$sk->GetPath("forms","index");
		  for($i=1;$i<($mvalue+1);++$i){
			 $mdata[0]=$rg->ReadValue("forms","Field.$i.Caption");
			 $mdata[1]=$rg->ReadValue("forms","Field.$i.Type");
	         $mdoc = new TemplateXP();
	         $mdoc->ReadFile($kelias);
		     $mdoc->AssignValue("label",$mdata[0]);  
		     $mdoc->AssignValue("value","<input name=\"$i\" type=\"$mdata[1]\">\n");	
	 	     $text=$text.$mdoc->ParseTemplate();
		  }
		  $mdoc2->AssignValue("content",$text);
		  $text=$mdoc2->ParseTemplate();
		  return;
	   }

     //Trecias zingsnis
	 if ($HTTP_GET_VARS["step"]==3) {
      $luser=$rg->ReadValue("users","FieldCount")+1;
  	  for ($i=1;$i<$luser;++$i)
		  if ($rg->ReadValue("users","$i.Field.1.Value")==$HTTP_POST_VARS[1]){
		   	 $kelias=$sk->GetPath("forms","err_nickalreadyexist");
		     $mdoc = new TemplateXP();
		     $mdoc->ReadFile($kelias);
			 $text=$mdoc->ParseTemplate();
			 return;
	      }
	  
	  $checkfield=$rg->ReadValue("forms","CheckField");
	  if (trim("$checkfield")!=""){
		  $checkfield=$HTTP_POST_VARS[$checkfield];
		
		  $dp=$rg->ReadValue("forms","Protocol");
		  if ((!strstr($checkfield,$dp)) && ($checkfield!="")){
		  	  $checkfield=$dp.$checkfield;
		  } else {
		  	  $kelias=$sk->GetPath("forms","err_noip");	 
			  $mdoc = new TemplateXP();
			  $mdoc->ReadFile($kelias);
			  $text=$mdoc->ParseTemplate();
			  return;
		  }
	
		  //Tikrina ar vartotojas nesukèiauja
		  $IP = getenv("HTTP_X_FORWARDED_FOR");
		  if(trim("$IP")=="") {
			  $IP = getenv("REMOTE_ADDR");
		  }
          $fi->isGoodURL($IP,$checkfield);

		  $nf=new NetFunctions();
		  if($nf->SmartTestServer($checkfield,10)>0){
			  $kelias=$sk->GetPath("forms","err_wrongip");	 
			  $mdoc = new TemplateXP();
			  $mdoc->ReadFile($kelias);
			  $text=$mdoc->ParseTemplate();
			  return;
		  }
	  }

	  for ($i=1;$i<($mvalue+1);++$i)
		  if ((trim("$HTTP_POST_VARS[$i]")=="") && ($rg->ReadValue("forms","Field.$i.Type")!="checkbox") && ($rg->ReadValue("forms","Field.$i.Type")!="radio") && 
		  ($rg->ReadValue("forms","Field.$i.Type")!="hidden")){
	  		  $kelias=$sk->GetPath("forms","err_missingfields");	 
			  $mdoc = new TemplateXP();
			  $mdoc->ReadFile($kelias);
			  $text=$mdoc->ParseTemplate();
			  return;
		  }

 	  $nf=new NetFunctions();
	  $nf->SetDefaultMailOptions($rg->ReadValue("rs","sitename"),		   $rg->ReadValue("rs","e-mail"),'windows-1257');
	  $nf->SendMail($HTTP_POST_VARS[1],$HTTP_POST_VARS[$rg->ReadValue("forms","E-MailField")], 'Aèiû, kad uþsiregistravote! :)',"Aèiû, kad uþsiregisravote ".$rg->ReadValue("rs","sitename"). " svetainëje. Jûsø registracijos duomenys yra tokie:<br>\n\r  Vartotojo vardas: ".$HTTP_POST_VARS[1]."<br>\n\r Slaptaþodis:".$HTTP_POST_VARS[$rg->ReadValue("forms","PasswordField")]."<br>\n\r Jeigu norësite kada nors vël pakeisti savo duomenis, nesivarþydami, tai galite atlikti prisijungæ tinklalapyje.<p>\n\n\r Sëkmës!");

	  $rg->WriteValue("users","FieldCount",$luser);
      for($i=1;$i<($mvalue+1);++$i)
	      $rg->WriteValue("users","$luser.Field.$i.Value",$HTTP_POST_VARS[$i]);
 	 $kelias=$sk->GetPath("forms","register.3");	 
     $mdoc = new TemplateXP();
     $mdoc->ReadFile($kelias);
	 $text=$mdoc->ParseTemplate();
	 return;
   }

     }

?>