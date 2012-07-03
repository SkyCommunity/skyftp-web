<?

  $bfield=$rg->ReadValue("forms","BirthdayField");
  $bdesc=$rg->ReadValue("rs","birthday");

  $calendar=Array();

  $rpc=Array('.'=>' ','-'=>' ');
  $month=date("m");
  $year=date("Y");
  $day=date("d");

  for ($i=1;$i<($rg->ReadValue("users","FieldCount")+1);++$i){
	  $txt=$rg->ReadValue("users","$i.Field.1.Value")." ".$bdesc;
	  $data=$rg->ReadValue("users","$i.Field.$bfield.Value");
	  foreach($rpc as $key => $value)
	     $data=str_replace($key,$value,$data);
	  $data=explode(' ',$data);
	  $data[0]=$year;
      if ($data[1]<$month) {
		  ++$data[0];
	  } elseif (($data[1]<$month) && ($data[2]<$day)) {
		  ++$data[0];
	  }

	  $data=$data[0].' '.$data[1].' '.$data[2];
      $calendar[$txt]=$data;
  }

  $eAll=$rg->GetAllItems("examinations");
  for ($i=0;$i<count($eAll);++$i){
	$txt=$eAll[$i][0];
	$data=$eAll[$i][1];
    foreach($rpc as $key => $value)
	     $data=str_replace($key,$value,$data);
    $data=explode(' ',$data);
    $data[2]=$year;
      if (($data[0]<$month)||($data[1]<$day)) ++$data[2];
    $data=$data[2].' '.$data[0].' '.$data[1];
	$calendar[$txt]=$data;
  }

  $eAll=$rg->GetAllItems("holidays");
  for ($i=0;$i<count($eAll);++$i){
	$txt=$eAll[$i][0];
    $data=$eAll[$i][1];
    foreach($rpc as $key => $value)
	    $data=str_replace($key,$value,$data);
    $data=explode(' ',$data);
    $data[2]=$year;
      if (($data[0]<$month)||($data[1]<$day)) ++$data[2];
    $data=$data[2].' '.$data[0].' '.$data[1];
	$calendar[$txt]=$data;
  }

  natsort($calendar);
  reset($calendar);

  if (isset($runninginside)) {
	  unset($runninginside);
	  return;
  }
//  print_r($calendar);
   
  $kelias=$sk->GetPath("calendar","index-inside");
  $text="";
  foreach ($calendar as $key => $value) {
    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
    $mdoc->AssignValue("date",$value);
	$mdoc->AssignValue("event",$key);
	$text .= $mdoc->ParseTemplate();
  }

  $kelias=$sk->GetPath("calendar","index");
  $mdoc = new TemplateXP();
  $mdoc->ReadFile($kelias);
  $mdoc->AssignValue("content",$text);
  $text = $mdoc->ParseTemplate();

?>