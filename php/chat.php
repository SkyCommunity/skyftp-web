<?
   include_once("./php/RemoteRegistry.php");
   include_once("./php/TemplateMaker.php"); 

   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"Chat","Default")) return $text;

   $action="";
   $action=trim(str_replace("\\",'',urldecode($HTTP_GET_VARS["action"])));
      
   $kelias=$sk->GetPath("chat","index");
   $mdoc = new TemplateXP();
   $mdoc->ReadFile($kelias);
   
   $data=$rg->GetAllItems("ircservers");
   $text="";
   $kelias=$sk->GetPath("chat","server");
   for ($i=0;$i<count($data);++$i){
	   $mdoc2 = new TemplateXP();
	   $mdoc2->ReadFile($kelias);
       $mdoc2->AssignValue("url",trim($data[$i][1]));  
       $mdoc2->AssignValue("title",trim($data[$i][0]));  
	   $text=$text.$mdoc2->ParseTemplate();
        
   }
   $mdoc->AssignValue("servers",$text);  

   $data=$rg->GetAllItems("chatengines");
   $text="";
   $kelias=$sk->GetPath("chat","chatengines");
   for ($i=0;$i<count($data);++$i){
	   $mdoc2 = new TemplateXP();
	   $mdoc2->ReadFile($kelias);
       $mdoc2->AssignValue("url","?site=chat&action=".$data[$i][0]."&skin=$skin&user=$curuser");  
	   if (trim($action)=="") $action=$data[$i][0];
	   if ($data[$i][0]==$action){
		$mdoc2->AssignValue("selected","selected");  
		$mdoc->AssignValue("url",$data[$i][1]);  
	   } else {
 	    $mdoc2->AssignValue("selected","");  
	   }
       $mdoc2->AssignValue("title",$data[$i][0]);  
	   $text=$text.$mdoc2->ParseTemplate();
        
   }
   $mdoc->AssignValue("chatengines",$text);  

   $data=$rg->GetItems("chatrs","$action.visible");
   $text="";
   $kelias=$sk->GetPath("chat","advancedfields");
   for ($i=0;$i<count($data);++$i){
	   $mdoc2 = new TemplateXP();
	   $mdoc2->ReadFile($kelias);
       $mdoc2->AssignValue("title",$data[$i][0].":");
       $mdoc2->AssignValue("form element",trim($data[$i][1]));
	   $text=$text.$mdoc2->ParseTemplate()."\n";
   }
   $mdoc->AssignValue("advancedfields",$text);  

   $data=$rg->GetItems("chatrs","$action.hidden");
   $text="";
   for ($i=0;$i<count($data);++$i)
     $text=$text."<input type=hidden name=\"".trim($data[$i][0])."\" value=\"".trim($data[$i][1])."\">\n";
   $mdoc->AssignValue("hiddenfields",$text);  

   $mdoc->AssignValue("field - server",$rg->ReadValue("chatrs","$action.field - server"));
   $mdoc->AssignValue("field - nick",$rg->ReadValue("chatrs","$action.field - nick"));
   $mdoc->AssignValue("field - name",$rg->ReadValue("chatrs","$action.field - name"));

   $text=$mdoc->ParseTemplate();
?>