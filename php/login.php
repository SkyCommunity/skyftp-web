<?php
   include_once("./php/TemplateMaker.php"); 

   $text="";
   if (isset($HTTP_POST_VARS["username"])){
	   $username=$HTTP_POST_VARS["username"];
   } else {
	   $username="";
   }
   if (isset($HTTP_POST_VARS["password"])){
	   $password=$HTTP_POST_VARS["password"];
   } else {
	   $password="";
   }
   if (isset($HTTP_GET_VARS["action"])){
	   $action=$HTTP_GET_VARS["action"];
   } else {
	   $action="";
   }
 
   if ($action=="logout"){
		
 	   $sexp->Logout($user['id']);
	   $kelias=$sk->GetPath("login","loggedout");
	 
       $mdoc = new TemplateXP();
       $mdoc->ReadFile($kelias);
       $text=$text.$mdoc->ParseTemplate();		
	   return;
   }
 
   if ($action=="logged"){

	   $kelias=$sk->GetPath("login","loggedin");
	 
       $mdoc = new TemplateXP();
       $mdoc->ReadFile($kelias);
       $text=$mdoc->ParseTemplate();		

	   return;
   }

   if (($username=="") && ($password=="")){
 
     if ($user['id']>=0) {

         $kelias=$sk->GetPath("login","logged");
		 $mdoc = new TemplateXP();
         $mdoc->ReadFile($kelias);
	     $mdoc->AssignValue("username",$user['name']);
	     $mdoc->AssignValue("url","?site=login&action=logout");
         return $text=$mdoc->ParseTemplate();
	 }


     $kelias=$sk->GetPath("login","index");
	 
     $mdoc = new TemplateXP();
     $mdoc->ReadFile($kelias);
     $text=$text.$mdoc->ParseTemplate();		
  
     return;

  } else {
  
     if ($username=="") {
	   $file="invalidusername.html";
    
	   $kelias=$sk->GetPath("login","invalidusername"); 
       $mdoc = new TemplateXP();
       $mdoc->ReadFile($kelias);
       $text=$text.$mdoc->ParseTemplate();		
	   
	   return;
	 }

    $ar->Load('users');
	$id=$ar->x['users']->GetItemID(array("Password"=>$password,"Nick"=>$username));
  
	if (trim($id)=="") {
  	   $file="invalidpassword.html";
    
	   $kelias=$sk->GetPath("login","invalidpassword");
	 
       $mdoc = new TemplateXP();
       $mdoc->ReadFile($kelias);
       $text=$text.$mdoc->ParseTemplate();		
       return;
	 }

	   $curuser=$sexp->Login($id);
      
	   $kelias=$sk->GetPath("login","loggedin");
       $mdoc = new TemplateXP();
       $mdoc->ReadFile($kelias);
       $text=$mdoc->ParseTemplate();		   

	   header("Location: $_SERVER[PHP_SELF]?site=login&action=logged&user=$curuser&skin=$skin\r\n");
  
  }
?>