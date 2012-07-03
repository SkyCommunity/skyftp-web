<?
class XPFormat{

function SmartURLtoHTML($text){
	$text=preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i","$1http://$2",$text);
    $text= preg_replace("/(?:^|\b)((((http|https|ftp):\/\/)|(www\.))([\w\.]+)([,:%#&\/?~=\w+\.-]+))(?:\b|$)/is", "<a href=\"$1\" target=\"_blank\">apsilankyti puslapyje</a>", $text);
	return $text;
}

function HTMLtoText($textas){
	$textas = str_replace(">", "&gt;", $textas); 
	$textas = str_replace("<", "&lt;", $textas); 
    return $textas;
}

function NewLinestoHTML($textas){
    $textas = str_replace("\r", "<br>", $textas); 
    $textas = str_replace("\n", "<br>", $textas); 
	return $textas;
}

function ReplaceSpaces($textas){
   $textas=str_replace("  ", "&nbsp; ", $textas);
   return $textas;
}

function RemoveSlashes($textas){
   $textas=str_replace("\\\"", "\"", $textas);
   $textas=str_replace("\\\'", "\'", $textas);
   return $textas;
}

function ToBBCode($article) {
	$article=$this->HTMLtoText($article);
	$article=$this->NewLinestoHTML($article);
	$article=$this->ReplaceSpaces($article);
	$article=$this->RemoveSlashes($article);
    //do UBB code
    $article=preg_replace("/\[b\](.*?)\[\/b\]/i","<b>\\1</b>", $article);
    $article=preg_replace("/\[i\](.*?)\[\/i\]/i","<i>\\1</i>", $article);
    $article=preg_replace("/\[u\](.*?)\[\/u\]/i","<u>\\1</u>", $article);
    $article=preg_replace("/\[link\=(.*?)\](.*?)\[\/link\]/i","<a target=\"_top\" href=\"\\1\">\\2</a>", $article);
    $article=preg_replace("/\[url\=(.*?)\](.*?)\[\/url\]/i","<a target=\"_blank\" href=\"\\1\">\\2</a>", $article);
    $article=preg_replace("/\[url\](.*?)\[\/url\]/i","<a target=\"_blank\" href=\"\\1\">\\1</a>", $article);
    $article=preg_replace("/\[color\=(.*?)\](.*?)\[\/color\]/i","<font color=\\1>\\2</font>", $article);
    $article=preg_replace("/\[email\=(.*?)\](.*?)\[\/email\]/i","<a href=\"mailto:\\1\">\\2</a>", $article);
    $article=preg_replace("/\[email\](.*?)\[\/email\]/i","<a href=\"mailto:\\1\">\\1</a>", $article);
	$article=preg_replace("/\[img\](.*?)\[\/img\]/i","<img src=\"\\1\">", $article);
	$article=preg_replace("/\[quote\](.*?)\[\/quote\]/i","<blockquote>\\1</blockquote>", $article);
	$article=preg_replace("/\[code\](.*?)\[\/code\]/i","<code>\\1</code>", $article);
	$article=preg_replace("/\[tt\](.*?)\[\/tt\]/i","<tt>\\1</tt>", $article);
    $article=preg_replace("/\[div=(.*?)\](.*?)\[\/div\]/i","<div align=\"\\1\">\\2</div>", $article);
    $article=preg_replace("/\[div (.*?)\](.*?)\[\/div\]/i","<div align=\"\\1\">\\2</div>", $article);
    $article=preg_replace("/\[textlist=(.*?)\](.*?)\[\/textlist\]/i","<b>\\1</b> \\2", $article);
    $article=preg_replace("/\[tlist=(.*?)\](.*?)\[\/ttlist\]/i","<b>\\1</b> \\2", $article);
	$article=preg_replace("/\[boxlist\](.*?)\[\/boxlist\]/i","<b>[x]</b> \\1", $article);
	$article=preg_replace("/\[blist\](.*?)\[\/blist\]/i","<b>[x]</b> \\1", $article);
    $article=preg_replace("/\[list\](.*?)\[\/list\]/i","<li>\\1</li>", $article);
//    $article=preg_replace("/\[size\=(.*?)\](.*?)\[\/size\]/i","<font size=\\1>\\2</font>", $article);
    $article=preg_replace("/\[big\](.*?)\[\/big\]/i","<big>\\1</big>", $article);
    $article=preg_replace("/\[small\](.*?)\[\/small\]/i","<small>\\1</small>", $article);

//keiciami specialus zenklai
    $article = str_replace("[sc=copyright]", "&copy;", $article); 
    $article = str_replace("[sc=registered]", "&reg;", $article); 
    $article = str_replace("[sc=trademark]", "&trade;", $article); 
    $article = str_replace("[sc=and]", "&amp;", $article); 
    $article = str_replace("[sc=less]", "&lt;", $article); 
    $article = str_replace("[sc=more]", "&gt;", $article); 
    $article = str_replace("[sc=paragraph]", "&para;", $article);  
    $article=preg_replace("/\[sc=(.*?)\]/i","\&\#\\1\;", $article);
      
    return $article;
}

}
?>