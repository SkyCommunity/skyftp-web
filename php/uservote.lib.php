<?


     function ClearUnusedText($var){
        return ($var[0] != "LastVoteFrom");
	 }

	 $kelias=$sk->GetPath("uservote","index");

	 $mdocX = new TemplateXP();
	 $mdocX->ReadFile($kelias);

	 $text="";
	 $count=$rg->ReadValue("users","FieldCount")+1;
	 for ($i=0;$i<$count;++$i){
	
		 $data=$rg->ReadValue("users","$i.Field.1.Value");
		 if (trim("$data")!="") {
	
			 $kelias=$sk->GetPath("uservote","element");
	
			 $mdoc2 = new TemplateXP();
			 $mdoc2->ReadFile($kelias);
		
			 $mdoc2->AssignValue('value',"$i");
			 $mdoc2->AssignValue('title',$data);

			 $text=$text.$mdoc2->ParseTemplate();
		 }
	 }
	$mdocX->AssignValue('votes',$text);

    $data=$rg->GetAllItems("uservote");
	if (count($data)<=1){

		 $kelias=$sk->GetPath("uservote","novotes");
	
		 $mdoc2 = new TemplateXP();
		 $mdoc2->ReadFile($kelias);

		 $text=$mdoc2->ParseTemplate();
	} else {
  	   
	   $data=array_filter($data,"ClearUnusedText");
	   rsort($data);
	   $text="";
	   $urli=$rg->ReadValue("forms","CheckField");
	   $urlpro=$rg->ReadValue("forms","Protocol");
       $count=count($data);
	   if ($count>5) $count=5;
	   for ($i=0;$i<$count;++$i){
	     $temp=$rg->ReadValue("users",$data[$i][0].".Field.1.Value");
		 if (trim("$temp")!="") {

			 $kelias=$sk->GetPath("uservote","rez");
	
			 $mdoc2 = new TemplateXP();
			 $mdoc2->ReadFile($kelias);
		
			 $mdoc2->AssignValue('title',$temp);			 
			 $temp=$rg->ReadValue("users",$data[$i][0].".Field.$urli.Value");
			 $tpro["scheme"]='';
//			 $tpro=parse_url($temp);
			 if (strstr($temp,$urlpro)) $temp=$urlpro.$temp;
 			 $mdoc2->AssignValue('url',"$temp");

			 $text=$text.$mdoc2->ParseTemplate();
		 }
	   }

    }
	$mdocX->AssignValue('rezults',$text);
    
	$mdocX->AssignValue('url',"index.php?site=uservote&skin=$skin&user=$curuser");

	$text=$mdocX->ParseTemplate();
 
?>