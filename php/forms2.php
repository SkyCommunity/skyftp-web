<?
   $fields=$rg->ReadValue("users","FieldCount");
   $fields2=$rg->ReadValue("forms","Fields");

   if (isset($HTTP_GET_VARS["action"])) {

       $findit=$HTTP_GET_VARS["action"];
	   $alldata=$rg->GetAllItems("users");

	   if (isset($key)) unset($key);

	   for ($i=0;$i<count($alldata);++$i)
		   if (strtolower($alldata[$i][1])==strtolower($findit))
				$key=$i;
       
	   $tmp=explode(".",$alldata[$key][0]);
       $key=intval($tmp[0]);
       
	   if (!isset($key)) {
		   $kelias=$sk->GetPath("forms2","notexist");
		   $mdoc = new TemplateXP();
		   $mdoc->ReadFile($kelias);
		   $text=$mdoc->ParseTemplate();
		   return;
	   }

	   $kelias=$sk->GetPath("forms2","index-inside");
	   $text="";
       for ($i=1;$i<($fields2+1);++$i)
		   if ($rg->ReadValue("forms","Field.$i.Type")!="password"){
			   $mdoc = new TemplateXP();
			   $mdoc->ReadFile($kelias);
			   $mdoc->AssignValue("key",$rg->ReadValue("forms","Field.$i.Caption"));
			   $reiksme=$rg->ReadValue("users","$key.Field.$i.Value");
			   if (($user<1) && ($rg->ReadValue("forms","Field.$i.Hide")=='1'))
				   $reiksme="-";
			   $reiksme=$xf->SmartURLtoHTML($reiksme);
			   $mdoc->AssignValue("value",$reiksme);
			   $text .=$mdoc->ParseTemplate();
		   }
	
      $kelias=$sk->GetPath("forms2","index");
      $mdoc = new TemplateXP();
	  $mdoc->ReadFile($kelias);
	  $mdoc->AssignValue("content",$text);
	  $mdoc->AssignValue("nick",$findit);
      $mdoc->AssignValue("picture","./php/imagem.php?skin=$skin&file=".urlencode($rg->ReadValue("pictures",$findit))."&item=trumb");
      $text =$mdoc->ParseTemplate();
	  return;
   }
   
	   $mas=Array();
       for ($i=1;$i<($fields+1);++$i)
		   $mas[$i-1]=$rg->ReadValue("users","$i.Field.1.Value");

       ksort($mas);
	   reset($mas);

	   $kelias=$sk->GetPath("forms2","list-inside");      
	   $text="";
       for ($i=0;$i<count($mas);++$i){
		      $mdoc = new TemplateXP();
			  $mdoc->ReadFile($kelias);
			  $mdoc->AssignValue("item",$mas[$i]);
			  $mdoc->AssignValue("url","?site=forms2&action=".$mas[$i]."&skin=$skin&user=$curuser");
 		      $text .=$mdoc->ParseTemplate();
	   }

	  $kelias=$sk->GetPath("forms2","list");
      $mdoc = new TemplateXP();
	  $mdoc->ReadFile($kelias);
	  $mdoc->AssignValue("content",$text);
      $text =$mdoc->ParseTemplate();
	  return;
?>
