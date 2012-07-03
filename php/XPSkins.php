<? 

// Ði klasë yra skirta tinklalapio apvalkalams
class XPSkins{

   var $skin="default";
   var $Error=Array("done" => 0,
                   "skinexist" => 1,
				   "nopath" => 2,
				   "noshortname" => 3,
				   "nofullname" => 4,
				   "skinwasnotfount" => 5);

   function XPSkins(){
	    GLOBAL $ar;
		$ar->Load('skins');
   }
   
   function CurrentSkin($skin){
	   if (trim("$skin")==""){
		   return $this->skin;
	   } else {
		   $this->skin=$skin;
		   return;
	   }	
   }

   function GetPath($site,$page){
	    GLOBAL $ar;
	    $id=$ar->x['skins']->GetItemID(array("Name"=>$this->skin));
        $value=$ar->x['skins']->GetCellData($id,"Path");
		$realpath=$value."/".$site."/".$page.".html";
		return $realpath;
   }

   function GetPath2($site){
	    GLOBAL $ar;
	    $id=$ar->x['skins']->GetItemID(array("Name"=>$this->skin));
        $value=$ar->x['skins']->GetCellData($id,"Path");
		$realpath=$value."/".$site;
//		print $realpath;
		return $realpath;
   }

   function GetTitle(){
	    GLOBAL $ar;
	    $id=$ar->x['skins']->GetItemID(array("Name"=>$this->skin));
        $value=$ar->x['skins']->GetCellData($id,"Title");
		return $value;
   }

   function GetSkins(){
	    GLOBAL $ar;
		$ar->x['skins']->SelectAll(true);
		$value=$ar->x['skins']->DoIt($ar->x['skins']->rtDC->Actions['get']);
		return $value;
   }

   // Sitie yra siaip sau parasyti... 
   // galbut galima kadanors bus ishtaisyti ju klaidas

   function AddSkin($shortname,$fullname,$path){
	    GLOBAL $ar;
		if (trim("$shortname")=="") return $this->Error['noshortname'];
		if (trim("$fullname")=="") return $this->Error['nofullname'];
		if (trim("$path")=="") return $this->Error['nopath'];
        $id=$ar->x['skins']->GetItemID(array("Name"=>"$shortname"));
		if ($id>=0) return $this->Error['skinexist'];
 	    $id=$ar->x['skins']->AddRow(array("Name"=>$shortname,"Caption"=>$fullname,"Path"=>$path));
		return $this->Error['done'];
   }
   
   function RemoveSkin($shortname){
       GLOBAL $ar;
  	   if (trim("$shortname")=="") return $this->Error['noshortname'];
       $id=$ar->x['skins']->GetItemID(array("Name"=>"$shortname"));
	   if ($id>=0) return $this->Error['skinwasnotfount'];
  	   $ar->x['skins']->DeleteRow($id);   
	   return $this->Error['done'];
   }
}
?>