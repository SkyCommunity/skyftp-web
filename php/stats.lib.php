<?
function DisplayIT($kelias,$string){
	$rg = new RemoteRegistry();
	$data=$rg->GetItems("statrates",$string);
	$text=str_replace("MS Internet Explorer","Internet Explorer",$text);
	$text=str_replace("Unknown Unknown",$rg->ReadValue("rs","Unknown"),$text);
	$text=str_replace("Windows NT 5.0","Windows 2000",$text);
	$text=str_replace("Windows NT 5.1","Windows XP",$text);
	$text=str_replace("Windows NT 5.2","Windows 2003",$text);
//	if (isset($data[''])){
//		$data['Unknown']=$data['Unknown']+$data[''];
//		unset($data['']);
//	}
	$text="";
	$max=0;
	foreach ($data as $key => $value)
		$max=$max+$value;
	foreach ($data as $key => $value){
		$mdoc2 = new TemplateXP();
		$mdoc2->ReadFile($kelias);
		if ((trim($key)=="Unknown")||(trim($key)==""))
			$key="Neþinoma";
		$mdoc2->AssignValue("name",$key);
		$mdoc2->AssignValue("value",$value);
		$temp=number_format(100/$max*$value, 2, '.', '');;
		$mdoc2->AssignValue("percent",$temp);
		$text=$text.$mdoc2->ParseTemplate();
	}
    return $text;
}
?>