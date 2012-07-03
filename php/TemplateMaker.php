<?php
  
class TemplateXP{

   var $TemplateFile="";
   var $tags=array();
   var $sReplace=false;
   var $sSmiles="";
   
   function ReadFile($file){
     $this->TemplateFile=$file;
   }
    
   function SmartSmiles($value){
     $this->sSmiles=$value;
   }
   
   function AssignValue($tag, $value){
     $this->tags[$tag] = $value;
   }
    
	function SmartReplace($value){
	  $this->sReplace=$value;
	}
    
	function ParseTemplate(){
      GLOBAL $sk,$site,$skin,$fi,$HTTP_GET_VARS,$HTTP_POST_VARS,$action;
	  $contents = @implode("", (@file($this->TemplateFile)));

      foreach ($this->tags as $key => $value){
	     $tag='{'.$key.'}';
         $contents = str_replace($tag, $value, $contents); 
	  }
	  
	  if ($this->sReplace==true) {
		 //$site kintamasis yra reikalui esant
		 //o shis ishimamas del php failu, kurie 
		 //gali veikti ir ne ir ne taip ikishti
 	     $HTTP_GET_VARS["site"]='';
	     $mas=explode('||',$contents);
		 $o=0;
		 $ko=Array();
		 $dp=GetSetting("RealPath");
		 for ($i=0;$i<count($mas);++$i){
			 $tmp="";
			 if (isset($mas[$i])) $tmp=$mas[$i];
			 $tag="$dp/php/$tmp.php";
			 if (file_exists($tag)){
				 $ko[$o][1]=$tag;
				 $ko[$o][0]='||'.$mas[$i].'||';
				 ++$o;
			 } 
		 }
		 for ($pi=0;$pi<count($ko);++$pi){
			 $text="";
 		     include($ko[$pi][1]);
			 $contents = str_replace($ko[$pi][0], $text, $contents);
		 }

	  }
 
	  if ($this->sSmiles!="") {
	    $dat=@file($this->sSmiles);
        for ($i=0;$i<count($dat);++$i) { 
		  $dat[$i]=explode('\|',$dat[$i]);
          $contents = str_replace($dat[$i][0], $dat[$i][1], $contents); 
		}
	  }
	  
      return($contents);
   }
		
 }

?>