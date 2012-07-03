<?
 include_once("./php/RemoteRegistry2.php");

 class AccessControl{

     var $DefaultUserLevel=0;
	 var $DefaultModifyLevel=0;

     function AccessControl(){
		 Global $ar;
		 $ar->Load('privilegies');
		 $ar->Load('users');
	 }

     function GetLevel($site,$subsite){
 	    Global $ar;
	    $id=$ar->x['privilegies']->GetItemID(array("Site"=>$site,"Item"=>$subsite));
		$value=$ar->x['privilegies']->GetCellData($id,"Level");
		if (trim("$value")=="") {
			$value=$this->DefaultModifyLevel;
			$this->SetLevel($site,$subsite,$value);
		}
		return $value;
	 }

     function SetLevel($site,$subsite,$level){
 	    Global $ar;
	    $id=$ar->x['privilegies']->GetItemID(array("Site"=>$site,"Item"=>$subsite));
		$ar->x['privilegies']->SetCellData($id,"Level",$level);
		return true;
	 }

     function CanIDo($userid,$site,$subsite){      
		$value=$this->GetUserLevel($userid);
        $level=$this->GetLevel($site,$subsite);
		if ($level<($value+1)) {
			return true;
		} else {
			return false;
		}
	 }

     function GetUserLevel($userid){
 	    Global $ar;
		$value=$ar->x['users']->GetCellData($userid,"*UserLevel");
 		if (trim("$value")=="")
			$value=$this->DefaultUserLevel;
		return $value;
     }

     function SetUserLevel($userid,$level){
 	    Global $ar;
		if ($level>100) return false;
		$ar->x['users']->SetCellData($userid,"*UserLevel",$level);
		return true;
	 }

 }


?>