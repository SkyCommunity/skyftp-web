<?

include_once('./php/NetFunctions.php');
//include_once('./php/RemoteRegistry.php');

class Filters{
 
    function Filters(){
	    Global $ar;
		$ar->Load('bans');
	}

    function isBanned($IP){
	    Global $ar;
	    $id=$ar->x['bans']->GetItemID(array("IP"=>$IP));
		if (empty($id)) {
			return; 
		} else {
			echo "Pri�jimas i� �io adreso yra laikinai u�draustas.<br>";
			echo $ar->x['bans']->GetCellData($id,"Reason");
			exit;
		}
	}

    function isGoodText($text){
	   $replacement=Array('�'=>'a','�'=>'c','�'=>'e',
	   					  '�'=>'e','�'=>'i','�'=>'s',
						  '�'=>'u','�'=>'u',
				 	      '�'=>'a','�'=>'c','�'=>'e',
	   					  '�'=>'e','�'=>'i','�'=>'s',
						  '�'=>'u','�'=>'u','sh'=>'s');
       $BadText=@file('./data/badwords.lst');
	   foreach ($replacement as $key => $value) 
		   $text = str_replace($key, $value,$text);
	   $text=strtolower($text);
	   foreach ($BadText as $key => $value) {
		   if (stristr(trim("$text"),trim("$value"))) return false;
	   }
	   return true;
	}


    function isGoodURL($reallip,$ip){
	    Global $ar,$nf;
		$BadText =@file('./data/badip.lst');
	    foreach ($BadText as $key => $value)
			if (stristr(trim("$ip"),trim("$value"))) {
				echo "J�s negalite �vedin�ti ne savo adres�!<br>";
				echo "Apie tai buvo prane�ta sistemos administratoriui.<br>";
				echo "Jums u�drausta neribot� laik� prieiti prie �io tinklalapio.";
				$nf=new NetFunctions();
				$nf->SetDefaultMailOptions(GetSetting("sitename"),		   GetSetting("e-mail"),GetSetting('encoding'));
				$nf->SendMail(GetSetting("sitename"),GetSetting("e-mail"), 'Prane�imas apie bandym� �silau�ti',"Vartotojas, kurio adresas yra $reallip, �ved� adres� i� juodojo s�ra�o ($ip).  Svetain� tai palaik� bandymu �silau�ti ir skyr� vartotojo IP auto ban'�.");
	  	        $id=$ar->x['bans']->AddItem(array("IP"=>$IP,"reason"=>GetSetting("defaultbanreason")));
				exit;
			} 
	}


}
?>