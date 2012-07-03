<?
function CanAccess($userid,$site,$subsite){
	GLOBAL $skin, $sk, $pr;
	if (!$pr->CanIDo($userid,$site,$subsite)){
		$kelias=$sk->GetPath("index","accessdenied");
		$docz = new TemplateXP();
		$docz->ReadFile($kelias);
		return $docz->ParseTemplate();
	}
	return "";
}

function GetSite(){
	GLOBAL $HTTP_GET_VARS;
	if (isset($HTTP_GET_VARS["site"]))
		return $HTTP_GET_VARS["site"];
	return "news";
}

function GetSkin(){
	GLOBAL $HTTP_GET_VARS;
	if (empty($HTTP_GET_VARS["skin"])) {
	  echo("<SCRIPT LANGUAGE=\"JavaScript\">\n");
	  echo("function getCookieVal (offset) {\n");
	  echo("var endstr = document.cookie.indexOf (\";\", offset);\n");
	  echo("if (endstr == -1)\n");
	  echo("endstr = document.cookie.length;\n");
	  echo("return unescape(document.cookie.substring(offset, endstr));\n");
	  echo("}\n");
	  echo("function GetCookie (name) {\n");
	  echo("var arg = name + \"=\";\n");
	  echo("var alen = arg.length;\n");
	  echo("var clen = document.cookie.length;\n");
	  echo("var i = 0;\n");
	  echo("while (i < clen) {\n");
	  echo("var j = i + alen;\n");
	  echo("if (document.cookie.substring(i, j) == arg)\n");
	  echo("return getCookieVal (j);\n");
	  echo("i = document.cookie.indexOf(\" \", i) + 1;\n");
	  echo("if (i == 0) break;\n");
	  echo("}\n");
	  echo("return \"default\";\n");
	  echo("}\n");
	  echo("window.location=\"index.php?site=$site&user=$curuser&skin=\"+GetCookie(\"skin\");");
	  echo("</SCRIPT>\n");
	  return "default";
	} 
	return $HTTP_GET_VARS["skin"];
}

function GetSetting($item){
	GLOBAL $ar;
   	$ar->Load('ini');
	$id=$ar->x['ini']->GetItemID(array("Variable"=>$item));
    return $ar->x['ini']->GetCellData($id,"Value");
}

function ParseList_ImageList($dir,$kelias,$selected){
	$text="";
	$handle=opendir("$dir");
	$sl=false;
    while (($file = readdir($handle))!==false){
		if ($file{0}!="."){
			$mdoc2 = new TemplateXP();
		    $mdoc2->ReadFile($kelias);
			$mdoc2->AssignValue("name",$file);		
			if ($selected==$dir."$file") {
				$mdoc2->AssignValue("selected","selected");		
			} else {
				$mdoc2->AssignValue("selected","");		
			}
			if (!$sl && ($selected=="")) {
				$mdoc2->AssignValue("selected","selected");		
				$sl=!$sl;
			}
			$mdoc2->AssignValue("url",$dir."$file");		
			$text.=$mdoc2->ParseTemplate();
		}		
	}
    return $text;
}

function ParseList_SettingsList($table,$key,$value,$kelias,$selected){
	GLOBAL $ar,$language;
	$ar->Load($table);
	$ar->x[$table]->SelectAll(true);
	$data=$ar->x[$table]->DoIt($ar->x[$table]->rtDC->Actions['get']);
	$text="";
	$sl=false;
	for ($i=0;$i<count($data);++$i){
		$mdoc2 = new TemplateXP();
		$mdoc2->ReadFile($kelias);
		$mdoc2->AssignValue("key",$data[$i][strtolower($key)]);		
		if ($selected==$data[$i][strtolower($key)]) {
			$mdoc2->AssignValue("selected","selected");		
		} else {
			$mdoc2->AssignValue("selected","");		
		}
		if (!$sl && ($selected=="")) {
			$mdoc2->AssignValue("selected","selected");		
			$sl=!$sl;
		}
		$mdoc2->AssignValue("value",TranslateString($language,$data[$i][strtolower($value)]));		
		$text.=$mdoc2->ParseTemplate();
	}
    return $text;
}

function GetCategoryFullName($shortname){
	GLOBAL $ar;
	$ar->Load("newscategories");
	$id=$ar->x["newscategories"]->GetItemID(array("ShortName"=>"$shortname"));
/*	if ($id=="") {
		$rez=$shortname;
	} else {
		$rez=$ar->x["newscategories"]->GetCellData($id,"FullName");
	}*/
	$rez=$ar->x["newscategories"]->GetCellData($id,"FullName");
    return $rez;
}

function TranslateString($language,$text){
	GLOBAL $ar;
	$ar->Load("lng");
	$id=$ar->x["lng"]->GetItemID(array("Language"=>$language,"Variable"=>$text));
	if ($id=="") {
		$rez=$text;
	} else {
		$rez=$ar->x["lng"]->GetCellData($id,"Value");
	}
    return $rez;
}

function CreateMagicLink($kelias,$value,$url){
	if ($value=="") return "";
	$mdoc2 = new TemplateXP();
	$mdoc2->ReadFile($kelias);
	$mdoc2->AssignValue("url",$url);		
	return $mdoc2->ParseTemplate();
}

function ParseTemplateItem($kelias){
	GLOBAL $user;
    $mdoc2 = new TemplateXP();
	$mdoc2->ReadFile($kelias);			
    $mdoc2->AssignValue("username",$user['name']);			
    return $mdoc2->ParseTemplate();
}

?>