<?php
class NetFunctions{
  
  var $myname="Me Myself";
  var $myemail="myself@email.com";
  var $encoding="windows-1257";

  function SetDefaultMailOptions($myname,$myemail,$encoding){
    $this->myname=$myname;
	$this->myemail=$myemail;
	$this->encoding=$encoding;
  }
  
  function SendMail($contactname,$contactemail,$subject,$message){
	
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=".$this->encoding."\r\n";
	$headers .= "From: ".$this->myname." <".$this->myemail.">\r\n";
	$headers .= "To: ".$contactname." <".$contactemail.">\r\n";
	$headers .= "Reply-To: ".$this->myname." <".$this->myemail.">\r\n";
	$headers .= "X-Priority: 1\r\n";
	$headers .= "X-MSMail-Priority: Normal\r\n";
	$headers .= "X-Mailer: SkinEngineXP";

	mail($contactemail, $subject, $message, $headers); 
  }
  
  function TestServer($server,$port,$timeout){
	 $temp = @fsockopen ($server, $port, $errno, $errstr, $timeout);
	 return $temp;
  }
  
  function SmartTestServer($url,$timeout){
    $h=parse_url($url);
//	 [scheme] => http
//   [host] => hostname
//   [user] => username
//   [pass] => password
//   [path] => /path
//   [query] => arg=value
//   [fragment] => anchor
	$server = $h[host];
    $ports=array("http",80,"ftp",21);
	$port=80;
	if (count($temp)<=1) {
    for ($i=0;$i<count($ports);$i=$i+2)
		if ($h[scheme]==$ports[$i])
		   $port = $ports[$i+1];
	} else {
		$port=$temp[1];
	}
	$ip = gethostbyname("$server");
    $long = ip2long($ip);
//	print $ip.":".$port;
	if ($long === -1) {
		$rez=1;
		return $rez;
	}
	if (!$this->TestServer($server,$port,$timeout)){
		$rez=2;
		return $rez;
	}
	return 0;
  }

}
?>