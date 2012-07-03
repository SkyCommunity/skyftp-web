<?

return;

include_once("./php/RemoteRegistry.php");
include_once("./php/TemplateMaker.php"); 
include_once("./php/stats.lib.php");
//require_once("./php/XPSkins.php");

//$sk = new XPSkins();
//$skin=trim($HTTP_GET_VARS["skin"]); 
//if ($skin=="") $skin="default";
//$sk->CurrentSkin($skin);

$time_out=1000;

$date=date('Y.m.d');

$data=getenv("HTTP_USER_AGENT");
$data=explode("(",$data);
$data=implode(")",$data);
$data=explode(")",$data);
$data=implode(";",$data);
$data=explode(";",$data);
for ($i=0;$i<count($data);++$i)
  $data[$i]=trim($data[$i]);
$datex=implode(";",$data);
$data=explode(";",$datex);

//Nustatoma informacija apie narsykle ir kai kuriuos kitus duomenis
//jeigu tik imanoma
$file="./data/browsercheck.dat";
$data1=file($file);
$bUnknown="Unknown";
for ($i=0;$i<count($data1);++$i){
	list($bname,$bsearch,$bfbr,$bplatform,$bos,$bversion)=explode('|',$data1[$i]);
	if (strstr($datex,$bsearch)) {
		$BROWSER=$bname;
		if ($bfbr=="") {
			$FBR=$bUnknown;
		} else {
			if (empty($bfbr)) $bfbr='';
			if (empty($v1)) $v1='';
			if (empty($v2)) $v2='';
			if (empty($v3)) $v3='';
			if (empty($v4)) $v4='';
			list($v1,$v2,$v3,$v4)=explode(".",$bfbr);
			if ($v2<1){
				$FBR=$data[$v1-1];
			} else if ($v3<1){
				$temp=explode(" ",$data[$v1-1]);
                $FBR=$temp[$v2-1];
			} else if ($v4<1){
				$temp=explode(" ",$data[$v1-1]);
                $temp=$temp[$v2-1];
				$temp=explode("/",$temp);
                $FBR=$temp[$v3-1];
			} else {
				$temp=explode(" ",$data[$v1-1]);
                $temp=$temp[$v2-1];
				$temp=explode("/",$temp);
                $temp=$temp[$v3-1];
				$temp=explode(":",$temp);
                $FBR=$temp[$v4-1];
			}
		}
		if ($bplatform==""){
			if ($bos!="") {
				$file="./data/platforms.lst";
				$data2=file($file);
				for ($o=0;$o<count($data2);++$o)
					if (strstr($datex,$temp)){
						$bplatform=$data2[$o];
						break;
					}
			} else {
				$bplatform=$bUnknown;
			}
			$PLATFORM=$bplatform;
		} else {
			list($v1,$v2,$v3,$v4)=explode(".",$bplatform);
			if ($v2<1){
				$PLATFORM=$data[$v1-1];
			} else if ($v3<1){
				$temp=explode(" ",$data[$v1-1]);
                $PLATFORM=$temp[$v2-1];
			} else if ($v4<1){
				$temp=explode(" ",$data[$v1-1]);
                $temp=$temp[$v2-1];
				$temp=explode("/",$temp);
                $PLATFORM=$temp[$v3-1];
			} else {
				$temp=explode(" ",$data[$v1-1]);
                $temp=$temp[$v2-1];
				$temp=explode("/",$temp);
                $temp=$temp[$v3-1];
				$temp=explode(":",$temp);
                $PLATFORM=$temp[$v4-1];
			}
		}
		if ($bos=="") {
			$OS=$bUnknown;
		} else {
			list($v1,$v2,$v3,$v4)=explode(".",$bos);
			if ($v2<1){
				$OS=$data[$v1-1];
			} else if ($v3<1){
				$temp=explode(" ",$data[$v1-1]);
                $OS=$temp[$v2-1];
			} else if ($v4<1){
				$temp=explode(" ",$data[$v1-1]);
                $temp=$temp[$v2-1];
				$temp=explode("/",$temp);
                $OS=$temp[$v3-1];
			} else {
				$temp=explode(" ",$data[$v1-1]);
                $temp=$temp[$v2-1];
				$temp=explode("/",$temp);
                $temp=$temp[$v3-1];
				$temp=explode(":",$temp);
                $OS=$temp[$v4-1];
			}
		}
		if ($bversion!=""){
			list($v1,$v2,$v3,$v4)=explode(".",$bversion);
			if ($v2<1){
				$VERSION=$data[$v1-1];
			} else if ($v3<1){
				$temp=explode(" ",$data[$v1-1]);
                $VERSION=$temp[$v2-1];
			} else if ($v4<1){
				$temp=explode(" ",$data[$v1-1]);
                $temp=$temp[$v2-1];
				$temp=explode("/",$temp);
                $VERSION=$temp[$v3-1];
			} else {
				$temp=explode(" ",$data[$v1-1]);
                $temp=$temp[$v2-1];
				$temp=explode("/",$temp);
                $temp=$temp[$v3-1];
				$temp=explode(":",$temp);
                $VERSION=$temp[$v4-1];
			}
		} else {
			$VERSION=$bUnknown;
		}
		break;
	}
}
if ($BROWSER=="") {
	$BROWSER=$bUnknown;
	$VERSION=$bUnknown;
	$OS=$bUnknown;
	$FBR=$bUnknown;
	$PLATFORM=$bUnknown;
}

//if ("$BROWSER $VERSION"=="Unknown Unknown") {
//	print $datex;
//	print "$BROWSER $VERSION";
//	return;
//}

//$IP=getenv("REMOTE_ADDR");
$IP = getenv("HTTP_X_FORWARDED_FOR");
if(trim("$IP")=="") {
  $IP = getenv("REMOTE_ADDR");
}

// Laikas
$Time=strftime("%H" ,time())*3600+strftime("%M" ,time())*60+strftime("%S" ,time());

//STATISTIKA i failus! :)
$count=$rg->ReadValue("stats","Count")+0;
for ($i=0;$i<$count;++$i)
	$duom[$i]=$rg->ReadValue("stats","$i.Value");
$found=false;
$ccount=$count;
for ($i=0;$i<$count;++$i){
    $duom[$i]=explode('|',$duom[$i]);
	if (abs($Time-$time_out)>$duom[$i][0]){
		$duom[$i][0]=-1;
		$ccount=$ccount-1;
	}
	if (($IP==$duom[$i][1])&&($duom[$i][5]==$FBR)){
		if ($duom[$i][0]==-1) ++$ccount;
		$duom[$i][0]=$Time;
		$found=true;
 	    $rg->WriteValue("statrates","IP.$IP", 	    $rg->ReadValue("statrates","IP.$IP")+1);
		$rg->WriteValue("statrates","BROWSER.$BROWSER $VERSION", 	 $rg->ReadValue("statrates","BROWSER.$BROWSER $VERSION")+1);
		$rg->WriteValue("statrates","PLATFORM.$PLATFORM", 	    $rg->ReadValue("statrates","PLATFORM.$PLATFORM")+1);
		$rg->WriteValue("statrates","OS.$OS", 	 $rg->ReadValue("statrates","OS.$OS")+1);
		$rg->WriteValue("statrates","FBR.$FBR", 	 $rg->ReadValue("statrates","FBR.$FBR")+1);
		$rg->WriteValue("statrates","DATE.$date", 	 $rg->ReadValue("statrates","DATE.$date")+1);
	}
}

if (!$found){
	$duom[$count][0]=$Time;
	$duom[$count][1]=$IP;
	$duom[$count][2]="$BROWSER $VERSION";
	$duom[$count][3]=$PLATFORM;
	$duom[$count][4]=$OS;
	$duom[$count][5]=$FBR;
	++$count;
	++$ccount;
    $rg->WriteValue("statrates","IP.$IP", 	    $rg->ReadValue("statrates","IP.$IP")+1);
	$rg->WriteValue("statrates","BROWSER.$BROWSER $VERSION", 	 $rg->ReadValue("statrates","BROWSER.$BROWSER $VERSION")+1);
	$rg->WriteValue("statrates","PLATFORM.$PLATFORM", 	    $rg->ReadValue("statrates","PLATFORM.$PLATFORM")+1);
	$rg->WriteValue("statrates","OS.$OS", 	 $rg->ReadValue("statrates","OS.$OS")+1);
	$rg->WriteValue("statrates","FBR.$FBR", 	 $rg->ReadValue("statrates","FBR.$FBR")+1);
	$rg->WriteValue("statrates","DATE.$date", 	 $rg->ReadValue("statrates","DATE.$date")+1);
}

$rg->WriteValue("stats","Count",$ccount);
$acount=$rg->ReadValue("stats","AllCount");
++$acount;
$rg->WriteValue("stats","AllCount",$acount);
for ($i=0;$i<$count;++$i)
	if ($duom[$i][0]!=-1) {
	    $duom[$i]=implode('|',$duom[$i]);
		$rg->WriteValue("stats","$i.Value",$duom[$i]);
	}

//Jeigu sritis vadinasi stats rodo visa surinkta statistika
if (isset($HTTP_GET_VARS["site"]))
	if ($HTTP_GET_VARS["site"]=="stats"){

	//Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
	if ($text=CanAccess($user,"Stats","Default")) return $text;

//	$file="stats.html"; 
	$kelias=$sk->GetPath("stats","stats");

	$mdoc = new TemplateXP();
	$mdoc->ReadFile($kelias);
	$mdoc->AssignValue("on-line",$rg->ReadValue("stats","Count"));
	$mdoc->AssignValue("count",$rg->ReadValue("stats","AllCount"));
    
	//Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
	$ct="";
	if (isset($HTTP_GET_VARS["action"]))
		$ct=$HTTP_GET_VARS["action"];
    if ($text=CanAccess($user,"Stats","$ct")) return $text;

	$kelias=$sk->GetPath("stats","stats-item");
	if ((trim($ct)!="")&&($HTTP_GET_VARS["site"]=="stats")) {
		$mdoc->AssignValue("content",DisplayIT($kelias,$HTTP_GET_VARS["action"]));
		$mdoc->AssignValue("DATE-","");
		$mdoc->AssignValue("DATE+","");
		$mdoc->AssignValue("BROWSER-","");
		$mdoc->AssignValue("BROWSER+","");
		$mdoc->AssignValue("IP-","");
		$mdoc->AssignValue("IP+","");
		$mdoc->AssignValue("PLATFORM-","");
		$mdoc->AssignValue("PLATFORM+","");
		$mdoc->AssignValue("OS-","");
		$mdoc->AssignValue("OS+","");
		$mdoc->AssignValue("FBR-","");
		$mdoc->AssignValue("FBR+","");
		$mdoc->AssignValue("$ct-","[");
		$mdoc->AssignValue("$ct+","]");
	}
/*	$mdoc->AssignValue("browsers",DisplayIT($kelias,"BROWSER"));
	$mdoc->AssignValue("os",DisplayIT($kelias,"OS"));
	$mdoc->AssignValue("ips",DisplayIT($kelias,"IP"));
	$mdoc->AssignValue("browsers",DisplayIT($kelias,"BROWSER"));
	$mdoc->AssignValue("platforms",DisplayIT($kelias,"PLATFORM"));
	$mdoc->AssignValue("fbrs",DisplayIT($kelias,"FBR"));
	$mdoc->AssignValue("dates",DisplayIT($kelias,"DATE"));*/
	$mdoc->AssignValue("defaulturl","?site=stats&skin=$skin&user=$curuser");

    $text=$mdoc->ParseTemplate();
	$text=str_replace("MS Internet Explorer","Internet Explorer",$text);
	$text=str_replace("Unknown Unknown",$rg->ReadValue("rs","Unknown"),$text);
	$text=str_replace("Windows NT 5.0","Windows 2000",$text);
	$text=str_replace("Windows NT 5.1","Windows XP",$text);
	$text=str_replace("Windows NT 5.2","Windows 2003",$text);

	return;
    
}

//Isvedama galutine informacija
$kelias=$sk->GetPath("stats","index");

$mdoc = new TemplateXP();
$mdoc->ReadFile($kelias);
$mdoc->AssignValue("on-line",$ccount);
$mdoc->AssignValue("count",$acount);
$text=$mdoc->ParseTemplate();

?>