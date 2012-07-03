<?php

   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"Files","Default")) return $text;

   $fTypes[folder]="folder";
   $fTypes[file]="file";
   
   $fitem=0;
   $fitem=trim($HTTP_GET_VARS["item"]);
   if ($fitem=="") $fitem=0;

   $ftype = $rg->ReadValue("files","$fitem.Type");
    
   //============Objekto duomenu rodymas
   if (($action=="")||($action=="show")) {
	
    $kelias=$sk->GetPath("files","index");

    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
    $fmeta=get_meta_tags($kelias);
    $fcolors=$fmeta["colors"];

    if ($ftype!=$fTypes[file]) {
       $mdoc2 = new TemplateXP();
       $kelias=$sk->GetPath("files","addfolder");
       $mdoc2->ReadFile($kelias);
       $mdoc2->AssignValue("item","$fitem");
       $mdoc->AssignValue("addfolder",$mdoc2->ParseTemplate());
	   
	   if ($fitem>0) {
          $mdoc2 = new TemplateXP();
          $kelias=$sk->GetPath("files","deletefolder");
          $mdoc2->ReadFile($kelias);
          $mdoc2->AssignValue("item","$fitem");
          $mdoc->AssignValue("deletefolder",$mdoc2->ParseTemplate());

		  $mdoc2 = new TemplateXP();
	      $kelias=$sk->GetPath("files","addfile");
	      $mdoc2->ReadFile($kelias);
		  $mdoc2->AssignValue("item","$fitem");
	      $mdoc->AssignValue("addfile",$mdoc2->ParseTemplate());
	   } else {
          $mdoc->AssignValue("deletefolder","");
		  $mdoc->AssignValue("addfile","");
	   }	   	   
	 
      $mdoc->AssignValue("deletefile","");
	 
	  $fcount=$rg->ReadValue("files","$fitem.Count");
	  if ($fcount==0) {
	    $mdoc2 = new TemplateXP();
        $kelias=$sk->GetPath("files","nofiles");
        $mdoc2->ReadFile($kelias);
	    $mdoc->AssignValue("content",$mdoc2->ParseTemplate()); 
	  } else {
	    $textx="";
 	    $fcolorsindex=0;
	    for ($i=1;$i<($fcount+1);++$i){
	     $mdoc2 = new TemplateXP();
         $kelias=$sk->GetPath("files","index-inside");
         $mdoc2->ReadFile($kelias);
		 $fnr=$rg->ReadValue("files","$fitem.Item.$i.Number");
		 $ftype1=$rg->ReadValue("files","$fnr.Type");
		 $mdoc2->AssignValue("icon","<img src=\"./skins/default/files/$ftype1.gif\">");
		 $mdoc2->AssignValue("item","$fnr");
  		 ++$fcolorsindex;
		 if ($fcolorsindex>$fcolors) 
 		    $fcolorsindex=1; 
		 $mdoc2->AssignValue("color",$fmeta["color$fcolorsindex"]);		 
			$mdoc2->AssignValue("title",$rg->ReadValue("files","$fnr.Title"));
		 $mdoc2->AssignValue("size",$rg->ReadValue("files","$fnr.Size"));
		 $textx=$textx.$mdoc2->ParseTemplate();
		}
        $mdoc->AssignValue("content",$textx);
	  }
	 
   } else {  
       $mdoc2 = new TemplateXP();
       $kelias=$sk->GetPath("files","deletefile");
       $mdoc2->ReadFile($kelias);
       $mdoc2->AssignValue("item","$fitem");
       $mdoc->AssignValue("deletefile",$mdoc2->ParseTemplate());
       $mdoc->AssignValue("addfile","");
       $mdoc->AssignValue("addfolder","");
       $mdoc->AssignValue("deletefolder","");

	   $mdoc2 = new TemplateXP();
	   $file="showfile.html"; 
       $kelias=$sk->GetPath("files","showfile");
       $mdoc2->ReadFile($kelias);
	   $mdoc2->AssignValue("name",$rg->ReadValue("files","$fitem.Title")); 	
	   $mdoc2->AssignValue("size",$rg->ReadValue("files","$fitem.Size")); 
 	   $mdoc2->AssignValue("description",$rg->ReadValue("files","$fitem.Description")); 
	   $mdoc2->AssignValue("count",$rg->ReadValue("files","$fitem.DownloadCount")); 
	   $mdoc2->AssignValue("version",$rg->ReadValue("files","$fitem.Version")); 
	   $mdoc2->AssignValue("type",$rg->ReadValue("files","$fitem.FileType")); 
       $mdoc2->AssignValue("download","?site=files&action=download2&item=$fitem&skin=$skin&user=$curuser"); 
	   $mdoc->AssignValue("content",$mdoc2->ParseTemplate()); 

   }
   }
  
//print "$user][AAA";
//print $action;
   //============Katalogo pridejimas===================
   if ($action=="addfolder") {
//	print $user;
    //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
    if ($text=CanAccess($user,"Files","AddFolder")) return $text;

    $kelias=$sk->GetPath("files","action_addfolder");

    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
    $fmeta=get_meta_tags($kelias);
    $disabled=$fmeta["disabled"];

    $mdoc->AssignValue("item","$fitem");
 		

   }

   //=======Pridedamas katalogas i duomenu baze=======
   if ($action=="addfolder2") {
	
    $kelias=$sk->GetPath("files","rez_addfolder");

    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
   
    $fname=trim($HTTP_POST_VARS["name"]);
	$fdesc=trim($HTTP_POST_VARS["description"]);
   
    if (($fname=="")||($fdesc=="")){
	
	    $kelias=$sk->GetPath("files","error_fieldsdatamissing");

		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
        
        $text=$text.$mdoc->ParseTemplate();		

		return;
	}

   $fci=$rg->ReadValue("files","$fitem.Count")+1;
   $fcl=$rg->ReadValue("files","Count")+1;
   $rg->WriteValue("files","Count","$fcl");
   $rg->WriteValue("files","$fitem.Count",$fci); 
   $fcl=$fcl;
   $rg->WriteValue("files","$fitem.Item.$fci.Number","$fcl");  
   $rg->WriteValue("files","$fcl.Type",$fTypes[folder]);
   $rg->WriteValue("files","$fcl.Count","0");
   $rg->WriteValue("files","$fcl.Title","$fname");
   $rg->WriteValue("files","$fcl.Description","$fdesc");
   $rg->WriteValue("files","$fcl.Parent","$fitem");
   $rg->WriteValue("files","$fcl.ParentNumber","$fci");

	

 }	

 //============Failo pridejimas===================
   if ($action=="addfile") {
	
	 //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
	 if ($text=CanAccess($user,"Files","AddFile")) return $text;

    $kelias=$sk->GetPath("files","action_addfile");

    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
    $fmeta=get_meta_tags($kelias);
    $disabled=$fmeta["disabled"];

	if ($disabled=="true") {
	
		$kelias=$sk->GetPath("files","error_disabled");
	
		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
     
        $text=$text.$mdoc->ParseTemplate();		

		return;
	}
    
	if ($user<1) {
		
		$kelias=$sk->GetPath("files","error_notloged");
	
		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
     
        $text=$text.$mdoc->ParseTemplate();		

		return;
	}

    $mdoc->AssignValue("item","$fitem");
 
   }
  
   //=======Pridedamas failas i duomenu baze=======
   if ($action=="addfile2") {
	
    $kelias=$sk->GetPath("files","rez_addfile");

    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
   
    $fname=trim($HTTP_POST_VARS["name"]);
	$fdesc=trim($HTTP_POST_VARS["description"]);
	$ftyp2=trim($HTTP_POST_VARS["type"]);
	$fvers=trim($HTTP_POST_VARS["version"]);
	$fsize=trim($HTTP_POST_VARS["size"]);
	$furls=trim($HTTP_POST_VARS["urls"]);
   
    $fbool=(($fname=="")||($fdesc==""));
	$fbool=(($fbool)||($ftype==""));
	$fbool=(($fbool)||($fvers==""));
	$fbool=(($fbool)||($fsize==""));
	$fbool=(($fbool)||($furls==""));
    if ($fbool){
	
	    $kelias=$sk->GetPath("files","error_fieldsdatamissing");

		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
        
        $text=$text.$mdoc->ParseTemplate();		

		return;
	}

   $fci=$rg->ReadValue("files","$fitem.Count")+1;
   $fcl=$rg->ReadValue("files","Count")+1;
   $rg->WriteValue("files","Count","$fcl");
   $rg->WriteValue("files","$fitem.Count","$fci"); 
   $fcl=$fcl;
   $rg->WriteValue("files","$fitem.Item.$fci.Number","$fcl");  
   $rg->WriteValue("files","$fcl.Type",$fTypes[file]);
   $rg->WriteValue("files","$fcl.Title","$fname");
   $rg->WriteValue("files","$fcl.Description","$fdesc");
   $rg->WriteValue("files","$fcl.Size","$fsize");
   $rg->WriteValue("files","$fcl.FileType","$ftyp2");
   $rg->WriteValue("files","$fcl.Version","$fvers");
   $rg->WriteValue("files","$fcl.Parent","$fitem");
   $rg->WriteValue("files","$fcl.DownloadCount","0");
   $rg->WriteValue("files","$fcl.ParentNumber","$fci");
   
   $fURLs=explode("\n",$furls);
   $rg->WriteValue("files","$fcl.Count",count($fURLs));
   for ($i=0;$i<count($fURLs);++$i){
	   $fURLs[$i]=trim($fURLs[$i]);
       $rg->WriteValue("files","$fcl.Item.".($i+1).".URL",$fURLs[$i]);
   }

 }	

 //============Failo Istrinimas===================
   if ($action=="deletefile") {
	
    //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
    if ($text=CanAccess($user,"Files","DeleteFile")) return $text;

    $kelias=$sk->GetPath("files","rez_deletefile");

    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
    $fmeta=get_meta_tags($kelias);
    $disabled=$fmeta["disabled"];

	if ($disabled=="true") {
	
		$kelias=$sk->GetPath("files","error_disabled");
	
		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
     
        $text=$text.$mdoc->ParseTemplate();		

		return;
	}
    
	if ($user<1) {
		
		$kelias=$sk->GetPath("files","error_notloged");
	
		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
     
        $text=$text.$mdoc->ParseTemplate();		

		return;
	}


//   $rg->DeleteKey("files","$fitem.Item.$fci.Number");  

   $fcl=$rg->ReadValue("files","$fitem.Parent");
   $fci=$rg->ReadValue("files","$fitem.ParentNumber");
   $fce=$rg->ReadValue("files","$fcl.Count")+1;
   if ($fitem==$rg->ReadValue("files","Count")){
	   $rg->WriteValue("files","Count",  $rg->ReadValue("files","Count")-1);
   } else {
	   for ($i=$fci;$i<$fce;++$i){
         $nrx="$fcl.Item.".($i+1).".Number";
		 $fnrx=$rg->ReadValue("files",$nrx);
		 $fparentnr=$fnrx.".ParentNumber";
		 if (trim($fparentnr)!=".ParentNumber")
           $rg->WriteValue("files",$fparentnr,"$i");
         $rg->WriteValue("files","$fcl.Item.$i.Number",   $fnrx);
	   }
   }
   $rg->MassDeleteKey("files",$fitem);
   $rg->WriteValue("files","$fcl.Count",  $rg->ReadValue("files","$fcl.Count")-1);
   $rg->DeleteKey("files","$fcl.Item.$fce.Number");

  }

 //============Katalogo Istrinimas===================
   if ($action=="delfolder") {
	
    //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
    if ($text=CanAccess($user,"Files","DeleteFolder")) return $text;

    $kelias=$sk->GetPath("files","rez_deletefolder");

    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
    $fmeta=get_meta_tags($kelias);
    $disabled=$fmeta["disabled"];

	if ($disabled=="true") {
	
		$kelias=$sk->GetPath("files","error_disabled");
	
		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
     
        $text=$text.$mdoc->ParseTemplate();		

		return;
	}
    
	if ($user<1) {
		
		$kelias=$sk->GetPath("files","error_notloged");
	
		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
     
        $text=$text.$mdoc->ParseTemplate();		

		return;
	}


//   $rg->DeleteKey("files","$fitem.Item.$fci.Number");  

   $fcl=$rg->ReadValue("files","$fitem.Parent");
   $rg->WriteValue("files","$fcl.Count",  $rg->ReadValue("files","$fcl.Count")-1);
   $fci=$rg->ReadValue("files","$fitem.ParentNumber");
   $fce=$rg->ReadValue("files","$fcl.Count")+1;
   if ($fitem==$rg->ReadValue("files","Count")){
	   $rg->WriteValue("files","Count",  $rg->ReadValue("files","Count")-1);
   } else {
       $fcl=$rg->ReadValue("files","$fitem.Parent");
//	   print $fcl;
	   for ($i=$fci;$i<(+1);++$i){
         $nrx="$fcl.Item.".($i+1).".Number";
		 $fnrx=$rg->ReadValue("files",$nrx);
		 $fparentnr=$fnrx.".ParentNumber";
		 if (trim($fparentnr)!=".ParentNumber"){
		 }
	   }
   }	
   $fcx=$rg->ReadValue("files","$fitem.Count");
   for ($i=$fcx;$i>0;$i=$i-1){
      $nrx="$fitem.Item.$i.Number";
 	  $fnrx=$rg->ReadValue("files",$nrx);
	  if ($fnrx>0){
        $rg->MassDeleteKey("files",$fnrx);   
	  }
   }
   $rg->MassDeleteKey("files",$fitem);
   $rg->DeleteKey("files","$fcl.Item.$fce.Number");

   $fcm=$rg->ReadValue("files","Count")+1;
   $o=0;
   for ($i=1;$i<$fcm;++$i){
	   $fvalue=$rg->ReadValue("files","$i.Parent");
//	   print "..::$fvalue::..<br>";
	   if ($fvalue==$fcl){
			$o=$o+1;
		    $rg->WriteValue("files","$fcl.Item.$o.Number",   $i);
            $rg->WriteValue("files",$fparentnr,"$o");
//  		    print $fparentnr."<P>$fcl.Item.$i.Number=$o";
       }
   }

  }

   //============Failo Atsiuntimas (Saltiniai+)===================
   if ($action=="download2") {
	
	$fcount=$rg->ReadValue("files","$fitem.Count");
	if ($fcount>1){
	    $kelias=$sk->GetPath("files","selectdownloadlocation");
	} else {
	    $kelias=$sk->GetPath("files","download");
		$furl=$rg->ReadValue("files","$fitem.Item.1.URL");
	}
	$mdoc = new TemplateXP();
	$mdoc->ReadFile($kelias);
	if ($fcount>1){
		++$fcount;
		$ftext="";
		for ($i=1;$i<$fcount;++$i){
	        $kelias=$sk->GetPath("files","selectdownloadlocation-inside");
			$mdoc2 = new TemplateXP();
			$mdoc2->ReadFile($kelias);
			$furl=$rg->ReadValue("files","$fitem.Item.$i.URL");
			$mdoc2->AssignValue("url",$furl);
//			$frez=$nf->SmartTestServer($furl,0);
//			$fstatus="{server is working}";
//			if ($frez==1) $fstatus="{server is not working}";
//			if ($frez==2) $fstatus="{server now does not work}";
//			$mdoc2->AssignValue("status",$fstatus);
			$mdoc2->AssignValue("url2",urlencode($furl));
			$mdoc2->AssignValue("downloadpath","index.php?site=files&action=download3&item=$fitem&url=$i&skin=$skin&user=$curuser");
 		    $ftext=$ftext.$mdoc2->ParseTemplate();		
		}
		$mdoc->AssignValue("content",$ftext);
	} else {
		$mdoc->AssignValue("url",$furl);
	}

   }
  
   //============Failo Atsiuntimas (Galas)===================
   if ($action=="download3") {

	    //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
        if ($text=CanAccess($user,"Files","DownloadFile")) return $text;

	    $kelias=$sk->GetPath("files","download");
        $i=$HTTP_GET_VARS["url"];
		$furl=$rg->ReadValue("files","$fitem.Item.$i.URL");
		$ftitle=$rg->ReadValue("files","$fitem.Title");
		$mdoc = new TemplateXP();
		$mdoc->ReadFile($kelias);
		$mdoc->AssignValue("url",$furl);
	   $rg->WriteValue("files","$fitem.DownloadCount",$rg->ReadValue("files","$fitem.DownloadCount")+1);
   }

  $caption=$rg->ReadValue("files","$fitem.Title");
  if (trim("$caption")=="") 
	$caption="Failai";
  $mdoc->AssignValue("caption","$caption");

  $desc=$rg->ReadValue("files","$fitem.Description");
  $ftype=$rg->ReadValue("files","$fitem.Type");
  if ($ftype==$fTypes[file]){ 
	$desc=$rg->ReadValue("rs","Displaying information about selected file");
  } elseif (trim("$desc")=="") {
	$desc=$rg->ReadValue("rs","Files Index");
  }

  $mdoc->AssignValue("description","$desc");

  $text=$text.$mdoc->ParseTemplate();		
?>