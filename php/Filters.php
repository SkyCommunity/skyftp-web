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
			echo "Priëjimas ið ðio adreso yra laikinai uþdraustas.<br>";
			echo $ar->x['bans']->GetCellData($id,"Reason");
			exit;
		}
	}

    function isGoodText($text){
	   $replacement=Array('à'=>'a','è'=>'c','æ'=>'e',
	   					  'ë'=>'e','á'=>'i','ð'=>'s',
						  'ø'=>'u','û'=>'u',
				 	      'À'=>'a','È'=>'c','Æ'=>'e',
	   					  'Ë'=>'e','Á'=>'i','Ð'=>'s',
						  'Ø'=>'u','Û'=>'u','sh'=>'s');
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
				echo "Jûs negalite ávedinëti ne savo adresø!<br>";
				echo "Apie tai buvo praneðta sistemos administratoriui.<br>";
				echo "Jums uþdrausta neribotà laikà prieiti prie ðio tinklalapio.";
				$nf=new NetFunctions();
				$nf->SetDefaultMailOptions(GetSetting("sitename"),		   GetSetting("e-mail"),GetSetting('encoding'));
				$nf->SendMail(GetSetting("sitename"),GetSetting("e-mail"), 'Praneðimas apie bandymà ásilauþti',"Vartotojas, kurio adresas yra $reallip, ávedë adresà ið juodojo sàraðo ($ip).  Svetainë tai palaikë bandymu ásilauþti ir skyrë vartotojo IP auto ban'à.");
	  	        $id=$ar->x['bans']->AddItem(array("IP"=>$IP,"reason"=>GetSetting("defaultbanreason")));
				exit;
			} 
	}


}
?>