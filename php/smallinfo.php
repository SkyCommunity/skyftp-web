<?

  $vnt=5;
  if (isset($HTTP_GET_VARS["msp"])) 
	$vnt=$HTTP_GET_VARS["msp"];

  $rez=Array();
  if (isset($HTTP_GET_VARS["act"])) 
	switch($HTTP_GET_VARS["act"]) {
		case "nowconnected":
			$tmp=$rg->GetAllItems("login");
		    $i=0;
			if (count($tmp)>0)
		      foreach($tmp as $key => $value){
				$vardas=$rg->ReadValue("users",$value[0].".Field.1.Value");
				if (trim("$vardas")!="") {
					$rez[$i][0]=$vardas;
					$rez[$i++][1]="$_SERVER[PHP_SELF]?site=forms2&action=$vardas&user=$curuser&skin=$skin";
				}
				if ($i>$vnt) break;
			}
	        break;
		case "lastfiles" :
            $count=$rg->ReadValue("files","Count");
		    $o=$count;
			$i=0;
		    do {
			   $tmp=$rg->ReadValue("files","$o.Type");
			   if ($tmp=="file"){
                   $rez[$i][0]=$rg->ReadValue("files","$o.Title");
				   $rez[$i++][1] = "$_SERVER[PHP_SELF]?site=files&action=show&item=$o&user=$curuser&skin=$skin";
			   }
			   $o=$o-1;
			   if ($i>$vnt) break;
			} while ($o>0);
	        break;
		case "dates" :
			$runninginside="Calendar";
			include('./php/calendar.php');
			$i=0;
			foreach ($calendar as $key => $value){
				$rez[$i++][0]="$value $key";
				if ($i>$vnt) break;
			}
	        break;
/*		default:
			echo ("JAV");*/
	}


//  print_r($rez);

  if (count($rez)<1) {
	  $kelias=$sk->GetPath("smallinfo","nodata");
	  $mdoc = new TemplateXP();
	  $mdoc->ReadFile($kelias);
	  $text = $mdoc->ParseTemplate();
	  return;
  }

  $kelias=$sk->GetPath("smallinfo","index-inside");
  if (!isset($rez[0][1]))
    $kelias=$sk->GetPath("smallinfo","index-dates");
  $text="";
  $i=1;
  foreach ($rez as $key => $value) {
    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
    $mdoc->AssignValue("title",$value[0]);
	$mdoc->AssignValue("nr",$i++);
	if (isset($rez[0][1]))
	  $mdoc->AssignValue("url",$value[1]);
	$text .= $mdoc->ParseTemplate();
  }

  $kelias=$sk->GetPath("smallinfo","index");
  $mdoc = new TemplateXP();
  $mdoc->ReadFile($kelias);
  $mdoc->AssignValue("content",$text);
  $text = $mdoc->ParseTemplate();

?>