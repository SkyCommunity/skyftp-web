<?php

// Lenteles RRF2 aprasymas:
// LaukelioVardas1|<-+<0001>+->|LaukelioVardas2....
// reiksme|<-+<0001>+->|reiksme2...
// reiksme|<-+<0001>+->|reiksme2...
// ...

// Klases funkcijos:
//   SetTable($table)
//   GetTable()
//   GetRowData($id)
//   SetRowData($id,$data)
//   GetRowsCount()
//   GetColsCount()
//   GetHeader()
//   RemoteDatabase($table,$value=$this->rType["file"])
//   DeleteRow($id)
//   SaveTable()
//   FindFirstUnused()
//   AddRow($data)
//   SetCellData($id,$colname,$value)
//   GetCellData($id,$colname)
//   SelectRows($condition,$query,$selectmode,$caselookup,$revercevar);
//   DoIt($action,$sortby="")
//   GetAllTableData($table)
//   SetAllTableData($table,$mas)
//   IsInTable($colname,$value,$caselookup=false)
//   GetItemID($data)
//   SelectAll($value=false)

class Database_Constants{
  var $Contitions=array('equal'=>0, '='=>0,
						 'firstchar'=>1, 'fc'=>1,
	                     'instring'=>2,
						 'less'=>3,'<'=>3,
						 'more'=>4,'>'=>4);
  var $Actions=array('get'=>0,'del'=>1,'delete'=>1);
  var $DatabaseType=array('rr2'=>0,'file'=>0);

}

class RemoteDatabase{

  var $rtDC;
  var $rtDatabase;

  function ReadTable(){
     return $this->rtDatabase->ReadTable();
  }

  function SetTable($table){
	$this->rtDatabase->SetTable($table);
  }

  function GetTable(){
	return $this->rtDatabase->GetTable();
  }

  function GetRowData($nr){
	 return $this->rtDatabase->GetRowData($nr);
  }

  function SetRowData($nr,$data){
	$this->rtDatabase->SetRowData($nr,$data);
  }

  function GetRowsCount(){
	return $this->rtDatabase->GetRowCount();
  }
 
  function GetColsCount(){
	return $this->rtDatabase->GetColsCount();
  }

  function GetHeader(){
	return $this->rtDatabase->GetHeader();
  }

  function RemoteDatabase($table,$value=0){
	 $this->rtDC=new Database_Constants();
     switch ($value):
		case $this->rtDC->DatabaseType["rr2"]:
            $this->rtDatabase=new Database_RRF2($table);
	        break;
	    default:
	        return 0;
    endswitch;
  }
 
  function DeleteRow($nr){
	$this->rtDatabase->DeleteRow($nr);
  }

  function SaveTable(){
	$this->rtDatabase->SaveTable();
  }

  function FindFirstUnused(){
	return $this->rtDatabase->FindFirstUnused();
  }

  function AddRow($data){
	 return $this->rtDatabase->AddRow($data);
  }

  function SetCellData($nr,$colname,$value){
	  $this->rtDatabase->SetCellData($nr,$colname,$value);
  }

  function GetCellData($nr,$colname){
	  return $this->rtDatabase->GetCellData($nr,$colname);
  }

  function SelectRows($condition,$query,$selectmode=true,$caselookup=false,$revercevar=true){
	 $this->rtDatabase->SelectRows($condition,$query,$selectmode,$caselookup,$revercevar);
  }

  function DoIt($action,$sortby="id"){
     return $this->rtDatabase->DoIt($action);
  }

  function IsInTable($colname,$value,$caselookup=false){
	return $this->rtDatabase->IsInTable($colname,$value,$caselookup);
  }

  function SetAllTableData($data){
	  $this->rtDatabase->SetAllTableData($data);
  }

  function GetItemID($data){
	  return $this->rtDatabase->GetItemID($data);
  }

  function SelectAll($value=false){
	$this->rtDatabase->SelectAll($value);
  }

}

class Database_RRF2{

  var $rSTR='|<-+<0001>+->|';
  var $rSTR2='|<-+<0000>+->|';
  var $rEncodeSettings=array('|'=>'|<-+<0004>+->|',
	                         "\n"=>'|<-+<0002>+->|',
							 "\r"=>'|<-+<0003>+->|');

  var $rtTableData;
  var $rtTable="";
  var $rtTableHeaders=Array();
  var $rtTableSelectedRows=Array();
  var $rtTableModified=false;
  var $rtType=0;
  var $rtConfigPath="./config/";
  var $rtEncodeTable=false;
  var $rtDC;

  function ReadTable(){
     $filename=$this->rtConfigPath.$this->rtTable.".rr2";
	 $data=file($filename);
	 $data=$this->DecodeCMDL($data);
     return $data;
  }

  function SetTable($table){
	 if	($this->rtTableModified) $this->SaveTable();
	 $this->rtTable=$table;
     $this->rtTableData=$this->ReadTable();
	 $this->rtTableDataTemp=$this->rtTableData;
	 $this->rtTableHeaders=@explode($this->rSTR,strtolower($this->rtTableData[0]));
	 for ($i=0;$i<count($this->rtTableHeaders);++$i){
		 $this->rtTableHeaders[$i]=$this->DecodeRegistryText($this->rtTableHeaders[$i]);
	 }
	 $this->rtTableSelectedRows=Array();
     foreach ($this->rtTableHeaders As $key => $value)
		 $this->rtTableSelectedRows[$key]=false;
  }

  function GetTable(){
	 return $this->rtTable;
  }

  function GetRowData($nr){
//	 print_r( $nr);
	 ++$nr;
	 if (!isset($this->rtTableData["$nr"])) return false;
	 $data=$this->rtTableData[$nr];
	 $data=@explode($this->rSTR,$data);
	 for ($i=0;$i<count($data);++$i){
		 $data[$i]=$this->DecodeRegistryText($data[$i]);
	 }
	 return $this->ConvertToKeyData($data);
  }

  function SetRowData($nr,$data){
//	print_r($data);
	$data=$this->ConvertToNumberData($data);
//	print_r($data);
	for ($i=0;$i<count($data);++$i)
	   $data[$i]=$this->EncodeRegistryText($data[$i]);
	++$nr;
    $this->rtTableData[$nr]=implode($this->rSTR,$data);
	$this->rtTableModified=true;
  }

  function GetRowsCount(){
	return count($this->rtTableData)-1;
  }
 
  function GetColsCount(){
	return count($this->rtTableHeaders)-1;
  }

  function GetHeader(){
	return $this->rtTableHeaders;
  }

  function DecodeRegistryText($text){
	foreach ($this->rEncodeSettings as $key => $value) 
       $text = str_replace($value,$key, $text); 
	return trim($text);
  }

  function EncodeRegistryText($text){
	foreach ($this->rEncodeSettings as $key => $value) 
       $text = str_replace($key,$value, $text); 
	return trim($text);
  }  
  
  function DeleteRow($nr){
       $this->rtTableData[$nr+1]="";
  	   $this->rtTableModified=true;
  }

  function EncodeCMDL($data){
	  if ($this->rtEncodeTable)
		  foreach ($data as $key => $text)
			  $data[$key]="-CDML-".base64_encode(gzencode($text, 9));
	  return $data;
  }

  function DecodeCMDL($data){
	  foreach ($data as $key => $text)
		  if (substr($text,0,6)=="-CDML-"){
 		      $text = substr($text, 6);
			  $text = base64_decode($text);
	 	      $text = substr($text, 10);
			  $data[$key]= gzinflate($text);
		  }
//	  print_r($data);
	  return $data;
  }

  function SaveTable(){
	if ($this->rtTableModified==false) return;
    $filename=$this->rtConfigPath.$this->rtTable.".rr2";
	usleep(1);
    if (file_exists($filename))
	    if (!is_writable($filename))
			do{
				usleep(1);
		    } while(!is_writable($filename));
     $h=fopen($filename,"w");
 	 flock($h,LOCK_EX);
	 foreach($this->rtTableData as $key=>$value)
		 $this->rtTableData[$key]=trim($value);
 	 fwrite($h, @implode("\n",$this->EncodeCMDL($this->rtTableData)));
 	 flock($h,LOCK_UN);
     fclose ($h);
   	 $this->rtTableModified=false;
  }

  function FindFirstUnused(){
	 for ($i=1;$i<count($this->rtTableData);++$i)
		if (isset($this->rtTableData[$i])) {
			if (trim($this->rtTableData[$i])=="") 
			   return $i-1;
		    } else {
			   return $i-1;
			}
	return count($this->rtTableData);
  }

  function AddRow($data){
	 $nr=$this->FindFirstUnused();
     $this->SetRowData($nr,$data);
	 $this->rtTableSelectedRows[$nr]=false;
	 return $nr;
  }

  function ConvertToKeyData($data){
	if (isset($data2)) unset($data2);
  	foreach ($this->rtTableHeaders as $key => $value){
       $data2[strtolower($value)]=$data[strtolower($key)];
	}
	return $data2;
  }

  function ConvertToNumberData($data){
	$data2=Array();
  	foreach ($data As $key => $value)
	   foreach ($this->rtTableHeaders As $key2 => $value2)
		  if (trim(strtolower($value2))==trim(strtolower($key))) {
			 $data2[$key2]=$value;
			 break 1;
	      }
	return $data2;
  }

  function SetCellData($nr,$colname,$value){
      $data=$this->GetRowData($nr);   
	  $data["$colname"]=$value;
	  $this->SetRowData($nr,$data);
  }

  function GetCellData($nr,$colname){
      $data=$this->GetRowData($nr); 
	  if (!isset($data[strtolower($colname)]))
		  $data[strtolower($colname)]="";
	  return $data[strtolower($colname)];
  }

  function Equalator($value1,$value2,$revercevar,$caselookup){
    if (!$caselookup) {
 	  $value1=strtolower($value1);
      $value2=strtolower($value2);
	}
	if ($revercevar) {
	    return ($value1==$value2);
	} else {
		return !($value1==$value2);
	}
  }

  function QueryEqualator($rowid,$query,$caselookup=false){
	  $data=explode(",",$query);
  	  $data=explode("=",implode("=",$data));
	  $defvalue=false;
      for ($i=0;$i<count($data);$i=$i+2){
		 $value1=$this->GetCellData($rowid,$data[$i]);
		 $value2=$data[$i+1];
         $defvalue = $defvalue || ($value2!=$value1);
	  }
	  return !$defvalue;
  }

  function SelectRows($condition,$query,$selectmode=true,$caselookup=false,$revercevar=true){
	if (isset($query)){
	    $data=explode("=","$query=");
		$colname=$data[0];
		$value=$data[1];
	}
    switch ($condition):
		case $this->rtDC->Contitions["equal"]:
 	        for ($i=0;$i<count($this->rtTableData);++$i){
               if ($this->QueryEqualator($i,$query,$caselookup)){
		          $this->rtTableSelectedRows[$i]=$selectmode;
				} 			
			}
	        break;
		case $this->rtDC->Contitions["firstchar"]:
		    for ($i=0;$i<$this->GetRowsCount();++$i)
               if ($this->Equalator(substr($this->GetCellData($i,$colname),0,strlen($value)),$value,$revercevar,$caselookup))
		          $this->rtTableSelectedRows[$i]=$selectmode;
	        break;
	    case $this->rtDC->Contitions["instring"]:
		    for ($i=0;$i<$this->GetRowsCount();++$i){
               $data=$this->GetCellData($i,$colname);
			   $data=explode($value,$data);
			   if (count($data)>1) 
				  $this->rtTableSelectedRows[$i]=$selectmode;
		    }
	        break;
	    case $this->rtDC->Contitions["less"]:			
		    for ($i=0;$i<$this->GetRowsCount();++$i){
               $data=$this->GetCellData($i,$colname);
			   if ($data<$value) 
				  $this->rtTableSelectedRows[$i]=$selectmode;
		    }
	        break;
	    case $this->rtDC->Contitions["more"]:
			$colname=$colname[0];
			$value=$value[0];
		    for ($i=0;$i<$this->GetRowsCount();++$i){
               $data=$this->GetCellData($i,$colname);
			   if ($data<$value) 
				  $this->rtTableSelectedRows[$i]=$selectmode;
		    }
	        break;
	    default:
	        return 0;
    endswitch;
  }

  function matrixSort($matrix,$sortKey) {
   if (!isset($this->{$matrix})) return;
   if (!is_array($this->{$matrix})) return;
   $tmpArray = array();
   foreach ($this->{$matrix} as $key => $subMatrix)
       $tmpArray[$key] = $subMatrix[$sortKey];
   arsort($tmpArray);
   if (!is_array($this->{$matrix})) {
	   $this->{$matrix} = $tmpArray;
	   return;
   }
   $this->{$matrix} = array_merge($tmpArray,$this->{$matrix});
  }

  function DoIt($action,$sortby="id"){
     switch ($action):
		case $this->rtDC->Actions["get"]:    
			if (isset($sortby))
				if ($sortby!="") $sortdata=explode(",",$sortby);
		    unset ($data);
			$o=0;
 	        for ($i=0;$i<count($this->rtTableData);++$i)
			   if (isset($this->rtTableSelectedRows[$i]))
                if ($this->rtTableSelectedRows[$i])
				  if (trim($this->rtTableData[$i+1])!=""){
	                  $data[$o]=$this->GetRowData($i);
					  $data[$o++]['id']=$i;
					  $this->rtTableSelectedRows[$i]=false;
				  }
			if (isset($sortby))
				if ($sortby!=""){
					$data=$this->matrixSort($data,"id");
				}
			if (!isset($data)) return;
			return $data;
	        break;
		case $this->rtDC->Actions["delete"]:
		    for ($i=0;$i<$this->GetRowsCount();++$i)
               if ($this->rtTableSelectedRows[$i])
   		           $this->DeleteRow($i);
	        break;
	    default:
	        return 0;
    endswitch;
	$this->SelectAll(false);
  }


  function GetItemID($data){
        for ($i=0;$i<$this->GetRowsCount();++$i){
 		      $reiksme=true;
          	  foreach($data as $key=>$value){
			     $reiksme=$reiksme && $this->xEqualator($i,$key,$value);
			  }
			  if ($reiksme) return $i;
		}
  }

  function xEqualator($id,$colname,$value){
	  return $this->Equalator($this->GetCellData($id,$colname),$value,true,false);
  }

  function IsInTable($colname,$value,$caselookup=false){
     for ($i=0;$i<$this->GetRowsCount();++$i)
         if ($this->Equalator($this->GetCellData($i,$colname),$value,true,$caselookup))
			return true;
	 return false;
  }

  function SetAllTableData($data){
	  $this->rtTableData=$data;
	  $this->SaveTable();
  }
  
  function Database_RRF2($table){
	 $this->rtDC=new Database_Constants();
	 $this->SetTable($table);
  }

  function SelectAll($v2){
	  for ($i=0;$i<$this->GetRowsCount();++$i)
   		 $this->rtTableSelectedRows[$i]=$v2;
  }

}

?>