<?

/**
 * FTP Class 
 * version 0.50
 * Copyright Raditha Dissanayake 2003
 * Licence Jabber Open Source Licence.
 *
 * vist http://www.raditha.com/ for updates.
 */

DEFINE (FPERM,0);
DEFINE (FINODE,1);
DEFINE (FUID,2);
DEFINE (FGID,3);
DEFINE (FSIZE,4);
DEFINE (FMONTH,5);
DEFINE (FDAY,7);
DEFINE (FTIME,7);
DEFINE (FNAME,8);

class FTP {
var $port;
var $host;

var $sock;
var $data_sock;

var $errno;
var $errstr;
var $message;

var $enable_log=0;


/**
 * constructor
 */
function FTP($host,$port=21)
{
$this->host = $host;
$this->port = $port;
socket_set_timeout(1);
}

/**
 * connects to the server and returns the socket which wil
 * be null if the connection failed. In which case you should
 * look at the $errno and $errstr variables.
 */
function connect()
{

$this->sock = @fsockopen($this->host,$this->port,&$this->errno,&$this->errstr,30);
@set_socket_blocking($this->sock,false);
return $this->sock;
}


/**
 * will read data from the socket. Since we are using non blocking 
 * mode, if we attempt to read the data even a millisecond before 
 * it becomes available, the method just returns null.
 *
 * So what's the cure? we will loop for a while and try to read it
 * in multiple times. Note that if we used blocking mode there may
 * be instances when this function NEVER returns.
 */
function sock_read(){
do {
    $data = fgets($this->sock);
    if (strlen($data) == 0) {
        break;
    }
    $contents .= $data;
} while(true);
print "A$contents!";
$this->log($s);
return $s;
}

/**
 * this is a hack. We are just checking if the server returns a 
 * 5xx number which indentifies an error. We assume that 
 * everything else is ok. Which may not always be true. That's 
 * some functionality which should be improved in the feature.
 */
 
function is_ok()
{
$this->message = $this->sock_read();

if($this->message == "" || preg_match('/^5/',$this->message) )
{
return 0;
}
else
{
return 1;
}
}

/**
 * Utility method so that we can take care of logging and
 * adding the carriage return etc.
 */
function sock_write($s)
{
fputs($this->sock,"$s\r\n");
$this->log($s);

}

/**
 * retrieves a listing of the given directory or the current
 * working directory is no path has been specified. Returns
 * a string which contains all the directory entries separated
 * by new lines.
 */
function dir_list($path="")
{
$s="";
if($this->pasv())
{
if($path == '')
{
$this->sock_write("LIST");
}
else
{
$this->sock_write("LIST $path");
}
if($this->is_ok())
{
while(true)
{
$line = fgets($this->data_sock);
$s .= $line;
if($line =='')
break;
}
}
}
return $s;
}

/**
 * establishes a passive mode data connection
 */
function pasv()
{
$this->sock_write("PASV");
if($this->is_ok())
{
$offset = strpos($this->message,"(");

$s = substr($this->message,++$offset,strlen($this->messsage)-2);
$parts = split(",",trim($s));
$data_host = "$parts[0].$parts[1].$parts[2].$parts[3]";
$data_port = ((int)$parts[4] << 8) + (int) $parts[5];

$this->data_sock = fsockopen($data_host,$data_port,&$errno,&$errstr,30);
return $this->data_sock;
}
return "";
}

/**
 * log the user in with the given username and password, default
 * to annoymous with a dud email address if no username password
 * have been given.
 */
function login($user="anonymous",$pass="nobody@nobody.com")
{
$this->sock_write("USER $user");
if($this->is_ok())
{
$this->sock_write("PASS $pass");

if($this->is_ok())
{

return 1;
}
}

ob_flush();
return 0;
}

/**
 * change this method to suite your log system.
 * be sure to setting the $enable_log to 1 
 */
function log($str){
  if($this->enable_log)
	echo "$str<br>\n";
 }
}

/**
 * this is not a member of the FTP class because it does not
 * work directly with the FTP server in any way. It will instead
 * process the output from the FTP server.
 *
 * Notice that the Table does not have the table start and end
 * tag. this is to allow you to create a header outside of this
 * function.
 *
 * Overall i think template schemes of this nature suck but to
 * use my beloved XSLT would be overkill.
 */
function show_list($data)
{

$list = split("\n",$data);

$pattern = "/[dwrx\-]{10}/";

$list = array_slice($list,1,count($list)-1);
foreach($list as $file)
{

$file = preg_split("/ /",$file,20,PREG_SPLIT_NO_EMPTY);

//$downlink = "getfile.php?filename=". urlencode("$path/".$file[FNAME]);
$downlink = "javascript:alert('We will add this functionality in the second part of this tutorial coming up soon')";

printf('<tr><td class="cell1">%s</td><td class="cell1">%s</td>
<td class="cell1">%s</td><td class="cell1">%s</td>
<td class="cell1">%s %s %s</td>
<td  class="cell1"><a href="%s">%s</a></td>
</tr>',
$file[FPERM],$file[FUID],$file[FGID], $file[FSIZE],
$file[FMONTH],$file[FDAY],$file[FTIME],
$downlink, $file[FNAME]);

}
}

$mftp = new FTP("10.2.33.5","21");
$mftp->enable_log=1;
$mftp->connect();
$mftp->login();
$mftp->sock_write("CWD");
$data=$mftp->dir_list();
print "<table>";
show_list($data);
print "</table>";

?>