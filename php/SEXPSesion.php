<?php
class SEXPSession{
  
  var $maxidletime=15;

  function SEXPSession(){
    Global $ar;
	$ar->Load('login');
	$ar->Load('users');
	$ar->Load('users.stats');
  }

  function Login($userid){
    Global $ar;
    $IP = getenv("HTTP_X_FORWARDED_FOR");
    if(empty($IP)) $IP = getenv("REMOTE_ADDR");
	$laikas=Time();
	$data=array("UserID"=>$userid,"IP"=>$IP,"Time"=>$laikas);
	$id=$ar->x['login']->AddRow($data)-1;
	$ar->x['users.stats']->SetCellData($userid,"LastLoggedOn",$laikas);
	$ar->x['users.stats']->SetCellData($userid,"Logged",$id);
	return $id;
  }

  function Logout($userid) {
    Global $ar;
	$id=$ar->x['users.stats']->GetCellData($userid,"Logged");
	$ar->x['login']->DeleteRow($id);
	$id=$ar->x['users.stats']->SetCellData($userid,"Logged",-1);
  }
 
  function GetUser(){
 	GLOBAL $HTTP_GET_VARS,$ar;
    $IP = getenv("HTTP_X_FORWARDED_FOR");
    if(empty($IP)) $IP = getenv("REMOTE_ADDR");
	if (isset($user)) unset($user);
	if (isset($HTTP_GET_VARS["user"]))
	    if ($HTTP_GET_VARS["user"]>=0){
			$id=$HTTP_GET_VARS["user"];
			$user['id']=$ar->x['login']->GetCellData($id,"userid");
			print $user['id'];
			$tmp=$ar->x['login']->GetCellData($id,"IP");
			if ($IP!=$tmp) {
				$user['id']=-1;
				$user['name']="Anonymous";
				$user['sessionid']=-1;
			} else {
				$this->UpdateLoginData($user['id']);
				$user['name']=$ar->x['users']->GetCellData($user['id'],"Nick");
				$user['sessionid']=$HTTP_GET_VARS["user"];
			}
			$user['time']=$ar->x['login']->GetCellData($id,"Time");
			$user['IP']=$tmp;
		} 
    if (!isset($user)){
 		$user['time']=time();
		$user['IP']=$IP;
		$user['id']=-1;
		$user['name']="Anonymous";
		$user['sessionid']=-1;
	}
	$this->RemoveUnused();
	return $user;
  }

  function UpdateLoginData($userid){
    Global $ar;
	$laikas=time();
	$id=$ar->x['users.stats']->GetCellData($userid,"Logged");
	$ar->x['login']->SetCellData($id,"Time",$laikas);
  }

  function RemoveUnused(){
    Global $ar;
//	return;
	$laikas=time()-$this->maxidletime*60*100;
	$ar->x['login']->SelectAll();
    $ar->x['login']->SelectRows($ar->x['login']->rtDC->Contitions['<'],"Time",$laikas);
	$ar->x['login']->DoIt($ar->x['login']->rtDC->Actions['delete']);
  }

}
?>