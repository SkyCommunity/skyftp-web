<?

   //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
   if ($text=CanAccess($user,"URLList","Default")) return $text;

   $KeitimoZenklai = Array("[Kita]"=>"0123456789".'`~!@#$%^&*()_-+=|\/,.<>:;"[]{}'."'".' ');

   $kelias=$sk->GetPath("urllist","index");

   $mdoc = new TemplateXP();
   $mdoc->ReadFile($kelias);
   
   $checkurl=$rg->ReadValue("forms","CheckField");
   $dp=$rg->ReadValue("forms","Protocol");

   $action='';
   if (isset($HTTP_GET_VARS["action"])) $action=$HTTP_GET_VARS["action"];

   $fsn=$rg->ReadValue("forms","ServerNameField");
   $ccount=$rg->ReadValue("users","FieldCount");
   $k=0;
   $raides="";
   for ($i=1;$i<($ccount+1);++$i){
	   $temp=$rg->ReadValue("users","$i.Field.$checkurl.Value");
	   $t2=$rg->ReadValue("activities","$i");
	   if (($temp!="") AND ($t2<$rg->ReadValue("rs","MaxInactivity"))) {
		   $data[$k]['url']=$temp;
		   $data[$k]['ID']=$i;
		   $fuser=$rg->ReadValue("users","$i.Field.1.Value");
		   $fuser1=$rg->ReadValue("users","$i.Field.$fsn.Value");
		   if (trim("$fuser1")=="") $fuser1="$fuser FTP";
		   $data[$k]['user']=$fuser1;
		   $data[$k]['char']=strtoupper(substr($data[$k]['user'], 0, 1));
		   foreach ($KeitimoZenklai as $key => $value) 
				if (strstr($value,$data[$k]['char']))
					$data[$k]['char']=$key;
		   if (!strstr($raides, $data[$k]['char']))
			   $raides=$raides.$data[$k]['char'].'|';
		   ++$k;
	   }
   }
   $raides=explode('|',$raides);
   sort($raides);
   reset($raides);

   if ($action=="") $action=$raides[1];

   $text="";
   $kelias=$sk->GetPath("urllist","index-char");
   for ($i=0;$i<(count($raides)+1);++$i){
	   $mdoc2 = new TemplateXP();
	   $mdoc2->ReadFile($kelias);   
//	   if (empty($raides[$i])) $raides[$i]='';
       $mdoc2->AssignValue("url","index.php?site=urllist&action=$raides[$i]&skin=$skin&user=$curuser");
	   if (strstr($raides[$i],'['))
			$raides[$i]=substr($raides[$i], 1, strlen($raides[$i])-2);

	   $mdoc2->AssignValue("char",$raides[$i]);
	   $text=$text.$mdoc2->ParseTemplate();
   }

  $mdoc->AssignValue("char",$text);

  $text="";
  $kelias=$sk->GetPath("urllist","index-inside");
  $fem=$rg->ReadValue("forms","E-MailField");
  $fsp=$rg->ReadValue("forms","SpeedLimitField");
  $fan=$rg->ReadValue("forms","AnonymousField");
  for ($i=0;$i<count($data);++$i){
	  if ($data[$i]['char']==$action) {
	   $mdoc2 = new TemplateXP();
	   $mdoc2->ReadFile($kelias);   
       $mdoc2->AssignValue("name",$data[$i]['user']);
	   if (!strstr($data[$i]['url'],$dp))
		   $data[$i]['url']=$dp.$data[$i]['url'];
	   
	   $mdoc2->AssignValue("e-mail",$rg->ReadValue("users",$data[$i]['ID'].".Field.$fem.Value"));

	   $mdoc2->AssignValue("url",$data[$i]['url']);
	   $mdoc2->AssignValue("id",$data[$i]['ID']);
       $mdoc2->AssignValue("url2",urlencode($data[$i]['url']));
	   $mdoc2->AssignValue("url3","?site=serverview&user=$curuser&skin=$skin&action=".$data[$i]['ID']);

	   $text=$text.$mdoc2->ParseTemplate();
	  }
  }

  $mdoc->AssignValue("content",$text);
  
  $text=$mdoc->ParseTemplate();

?>
