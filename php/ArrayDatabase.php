<?

include_once("./php/RemoteRegistry2.php");


class ArrayDatabase{

     var $x=Array();

     function Load($database){
		 if (!isset($this->x[$database]))
			 $this->x[$database]=new RemoteDatabase($database);
	 }

	 function UnLoad($database){
		 if (isset($this->x[$database]))
			unset($this->x[$database]);
	 }

	function Save(){
		foreach ($this->x as $key=>$value){
//			print $key."$value";
			$this->x[$key]->SaveTable();
		}
	}

}

?>