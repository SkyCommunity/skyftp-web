<?

include_once("../php/MiscFunctions.php");

print gInfo()."<=<br>";

$text=sInfo(array("site"=>"news","action"=>"show","id"=>0,"user"=>"anonymous","skin"=>"default"));

print $text;

exit;

include_once("../php/RemoteRegistry2.php");

$ref = new RemoteDatabase("privilegies");
print $ref->GetItemID(array("Site"=>"Stats","Item"=>"fbr"))."A";

exit;

require_once("../php/error.lib.php");

$filename="../config/database.dbf";
$id=dbase_open($filename, 2);

$data[0]="0";
$data[1]=rand(2,1000).rand(2,1000).rand(2,1000).rand(2,1000).rand(2,1000).rand(2,1000)."mekdrop";
$data[2]=$data[1]."@omni.lt";
$data[3]="10.2.33.5";
$data[4]="www.skyftp.skynet.lt";
$data[5]="21Interactyve";
$data[6]="";
$data[7]="SkyNet";
$data[8]="true";
$data[9]="1024";
$data[10]="a";
$data[11]=100;
$data[12]="a";
$data[13]="mgs+";
$data[14]="a";
$data[15]="false";
//$data[16]="a";

//$k=dbase_add_record($id,$data);
//$rez=dbase_get_record($id,$k);
$db=$id;
$rec = dbase_get_record($db, 1);
$nf  = dbase_numfields($db);
for ($i=0; $i < $nf; $i++) {
   print $rec[$i]."<br>\n";
}
dbase_close($id);
//print "a".$rez[1];

exit;

include_once("../php/RemoteRegistry2.php");

$ref = new RemoteDatabase("users");
$ref->rtDatabase->rtEncodeTable=false;
$data = Array();

for ($i=0;$i<2;++$i){
$data['Nick']=rand(2,1000).rand(2,1000).rand(2,1000).rand(2,1000).rand(2,1000).rand(2,1000)."mekdrop";
$data['E-Mail']=$data['Nick']."@omni.lt";
$data['FTP']="10.2.33.5";
$data['WWW']="www.skyftp.skynet.lt";
$data['Password']="21Interactyve";
$data['Server Name']="";
$data['Network']="SkyNet";
$data['Can I login anonymous?']=true;
$data['Speed Limit']=1024;
$data['Buy URL']="";
$data['*UserLevel']=100;
$data['*LastWorked']="";
$data['*Flags']="mgs+";
$data['*LastLoggedOn']="";
$data['*IsLogged']=false;
$data['Comments']="";
if (!$ref->IsInTable("nick",$data['Nick'])){
	print "nera duombazeje.";
	print "prideta.";
	$id=$ref->AddRow($data);
	print "[$id]";
	$ref->SaveTable();
} else {
	print "yra duombazeje.";
}
}
print_r($ref->rtTableData);

//$ref->SelectRows($ref->rContitions['firstchar'],"nick","a");
//$ref->SelectRows($ref->rContitions['firstchar'],"nick","m");
//print_r($ref->DoIt($ref->rActions['del']));
//	$ref->SaveTable();
?>