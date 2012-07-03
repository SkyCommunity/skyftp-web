<?
 //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
// if ($text=CanAccess($user,"News",$action)) return $text;

 $ar->Load('news');
 $ar->Load('comments');

 $dir=GetSetting("news image path");

 switch ($action):
	case "add":
		$kelias=$sk->GetPath("news","addnews");
  	    $mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
        $mdoc->AssignValue("Author",$user['name']);
        $mdoc->AssignValue("Date",date(GetSetting("dateformat")));
        $mdoc->AssignValue("Time",date(GetSetting("timeformat")));
        $mdoc->AssignValue("smallinfo","");
        $mdoc->AssignValue("allinfo","");
        $mdoc->AssignValue("title","");
		$kelias=$sk->GetPath("news","addnews_pictures");
		$mdoc->AssignValue("pictures",ParseList_ImageList($dir,$kelias,""));		
		$kelias=$sk->GetPath("news","addnews_languages");
		$mdoc->AssignValue("languages",ParseList_SettingsList("languages","Code","Language",$kelias,""));	
		$kelias=$sk->GetPath("news","addnews_categories");	
		$mdoc->AssignValue("categories", ParseList_SettingsList("newscategories","ShortName","FullName",$kelias,""));	
	break;
	case "preview":

		$xf=new XPFormat();

		$kelias=$sk->GetPath("news","addnews_preview");
  	    $mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
        
		$kelias=$sk->GetPath("news","index-inside-expanded");
  	    $mdoc2 = new TemplateXP();
	    $mdoc2->ReadFile($kelias);
        $mdoc2->AssignValue("author",$_POST['Author']);
        $mdoc2->AssignValue("date",$_POST['Date']);
        $mdoc2->AssignValue("time",$_POST['Time']);
        $mdoc2->AssignValue("picture",$_POST['picture']);
        $mdoc2->AssignValue("message",$xf->ToBBCode($_POST['smallinfo']));
        $mdoc2->AssignValue("message2",$xf->ToBBCode($_POST['allinfo']));
        $mdoc2->AssignValue("title",$_POST['title']);
        $mdoc2->AssignValue("category",TranslateString($language,GetCategoryFullName($_POST['category'])));
        $mdoc2->AssignValue("comments","0");
        $mdoc2->AssignValue("url-comments","#");
        $mdoc->AssignValue("content",$mdoc2->ParseTemplate());
  
		$kelias=$sk->GetPath("news","addnews");
  	    $mdoc2 = new TemplateXP();
	    $mdoc2->ReadFile($kelias);
        $mdoc2->AssignValue("Author",$_POST['Author']);
        $mdoc2->AssignValue("Date",$_POST['Date']);
        $mdoc2->AssignValue("Time",$_POST['Time']);
        $mdoc2->AssignValue("smallinfo",$_POST['smallinfo']);
        $mdoc2->AssignValue("allinfo",$_POST['allinfo']);
        $mdoc2->AssignValue("title",$_POST['title']);
		$kelias=$sk->GetPath("news","addnews_pictures");
		$mdoc2->AssignValue("pictures",ParseList_ImageList($dir,$kelias,$_POST['picture']));		
		$kelias=$sk->GetPath("news","addnews_languages");
		$mdoc2->AssignValue("languages",ParseList_SettingsList("languages","Code","Language",$kelias,$_POST['language']));	
		$kelias=$sk->GetPath("news","addnews_categories");	
		$mdoc2->AssignValue("categories", ParseList_SettingsList("newscategories","ShortName","FullName",$kelias,$_POST['category']));
        $mdoc->AssignValue("edit",$mdoc2->ParseTemplate());
	    $mdoc->AssignValue("author",$_POST['Author']);
        $mdoc->AssignValue("date",$_POST['Date']);
        $mdoc->AssignValue("time",$_POST['Time']);
        $mdoc->AssignValue("picture",$_POST['picture']);
        $mdoc->AssignValue("smallinfo",urlencode($xf->ToBBCode($_POST['smallinfo'])));
        $mdoc->AssignValue("allinfo",urlencode($xf->ToBBCode($_POST['allinfo'])));
        $mdoc->AssignValue("title",$_POST['title']);
        $mdoc->AssignValue("category",$_POST['category']);
        $mdoc->AssignValue("language",$_POST['language']);

	break;
	case "submit":

 	   $xf=new XPFormat();

  	   $data=Array("Language"=>$_POST['language'],
					"Category"=>$_POST['category'],
					"Tittle"=>$_POST['title'],
					"Picture"=>$_POST['picture'],
					"Small Info"=>urldecode($_POST['smallinfo']),
					"All Info"=>urldecode($_POST['allinfo']),
					"Date"=>$_POST['Date'],
					"Time"=>$_POST['Time'],
					"Author"=>$_POST['Author'],
					"Moderated"=>"0");
		$ar->x['news']->AddRow($data);

		$kelias=$sk->GetPath("news","rez_addnews");
  	    $mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);       

    break;
	case "readmore":

  	    $xf=new XPFormat();

		$i=0;
		if (isset($_GET["item"])) $i=$_GET["item"];
		$data=$ar->x['news']->GetRowData($i);

	    $ar->x['comments']->SelectRows("=","NewsID=$i",true);
	    $comments=$ar->x['comments']->DoIt($ar->x['comments']->rtDC->Actions['get'],"");

		$kelias=$sk->GetPath("news","index-inside-expanded");
  	    $mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
        $mdoc->AssignValue("author",$data['author']);
        $mdoc->AssignValue("date",$data['date']);
        $mdoc->AssignValue("time",$data['time']);
        $mdoc->AssignValue("picture",$data['picture']);
        $mdoc->AssignValue("message",$xf->ToBBCode($data['small info']));
        $mdoc->AssignValue("message2",$xf->ToBBCode($data['all info']));
        $mdoc->AssignValue("title",$data['tittle']);
        $mdoc->AssignValue("category",TranslateString($language,GetCategoryFullName($data['category'])));
        $mdoc->AssignValue("comments",count($comments));	$mdoc->AssignValue("url-comments","index.php?site=news&action=comments&item=$i&user=$user[sessionid]&skin=$skin");
     break;
	case "comments": 

		$i=0;
		if (isset($_GET["item"])) $i=$_GET["item"];
		$data=$ar->x['news']->GetRowData($i);

        if (isset($_POST['nick']) && isset($_POST['text'])){
  	        $comment=Array("newsid"=>"$i","comment"=>$_POST['text'],
					"nick"=>$_POST['nick'],
					 "date"=>date(GetSetting("dateformat")),
				     "time"=>date(GetSetting("timeformat")));
 			$ar->x['comments']->AddRow($comment);	
            
		}

	    $ar->x['comments']->SelectRows("=","NewsID=$i",true);
	    $comments=$ar->x['comments']->DoIt($ar->x['comments']->rtDC->Actions['get'],"date,time");

		$kelias=$sk->GetPath("news","comments");
  	    $mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);
        $mdoc->AssignValue("title",$data['tittle']);
        $mdoc->AssignValue("category",TranslateString($language,GetCategoryFullName($data['category'])));
        if (count($comments)==0) {
			$kelias=$sk->GetPath("news","comments-empty");
			$text=ParseTemplateItem($kelias);
		} else {
			$kelias=$sk->GetPath("news","comments-inside");
  		    $mdoc2 = new TemplateXP();
			$mdoc2->ReadFile($kelias);			
			$text="";
			for ($o=0;$o<count($comments);++$o){
		        $mdoc2->AssignValue("author",$comments[$o]['nick']);
		        $mdoc2->AssignValue("date",$comments[$o]['date']);
		        $mdoc2->AssignValue("time",$comments[$o]['time']);
		        $mdoc2->AssignValue("message",$comments[$o]['comment']);
				$text.=$mdoc2->ParseTemplate();
			}
		}
		$mdoc->AssignValue("content",$text);
		$kelias=$sk->GetPath("news","comments-form");
		$mdoc->AssignValue("form",ParseTemplateItem($kelias));
        $mdoc->AssignValue("item",$i);
        break;

    default:
		$kelias=$sk->GetPath("news","index");

		$mdoc = new TemplateXP();
	    $mdoc->ReadFile($kelias);

	    $nmeta=get_meta_tags($kelias);

		if (isset($nmeta["disabled"])) {
		    $ndisabled=$nmeta["disabled"];
		} else {
			$ndisabled="";
		}

		if (isset($HTTP_GET_VARS["msgperpage"])) {
		    $howmanyshow=$HTTP_GET_VARS["msgperpage"];
		} else {
			$howmanyshow=GetSetting("msgperpage");
			if ($howmanyshow==0) $howmanyshow=5;
		}
		if (isset($HTTP_GET_VARS["item"])) {
		    $vnt=$HTTP_GET_VARS["item"];
		} else {
			$vnt=0;
		}

		if (trim($item)!="") {
 		    $ar->x['news']->SelectRows("=","Language=$language,Moderated=1,Category=$item",true);
		} else { 
			$ar->x['news']->SelectRows("=","Language=$language,Moderated=1",true);
		}

		$news=$ar->x['news']->DoIt($ar->x['news']->rtDC->Actions['get'],"date,time");
		$newscount=count($news);

		$nto=$vnt+$howmanyshow-1;
		if ($nto>$newscount) $nto=$newscount;
		$textP="";
		for ($i=$vnt;$i<$nto;++$i){
		   if (isset($news[$i]['date'])){
		       $kelias=$sk->GetPath("news","index-inside");	   
			   $mdoc2 = new TemplateXP();
			   $mdoc2->ReadFile($kelias);		
			   $mdoc2->AssignValue("date",$news[$i]['date']);
			   $mdoc2->AssignValue("message",$news[$i]['small info']);
			   $mdoc2->AssignValue("author",$news[$i]['author']);
   			   $mdoc2->AssignValue("picture",$news[$i]['picture']);
   			   $mdoc2->AssignValue("category",TranslateString($language,GetCategoryFullName($news[$i]['category'])));
		       $kelias=$sk->GetPath("news","index-inside-readmore");	   
			   $mdoc2->AssignValue("read more",CreateMagicLink($kelias,$news[$i]['all info'],"?site=news&action=readmore&item=".$news[$i]['id']."&user=$user[sessionid]&skin=$skin"));
			   			   
			   $mdoc2->AssignValue("title",$news[$i]['tittle']);
		   	   $mdoc2->AssignValue("url-comments","index.php?site=news&action=comments&item=".$news[$i]['id']."&user=$user[sessionid]&skin=$skin");
		   
//		   $mdoc2->AssignValue("url-author","index.php?site=forms&action=show&item=$nauthor&user=$user[sessionid]&skin=$skin");
			   print $news[$i]['id'];
 			   $ar->x['comments']->SelectRows("=","NewsID=".$news[$i]['id'],true);
			   $comments=$ar->x['comments']->DoIt($ar->x['comments']->rtDC->Actions['get'],"date,time");
			   $mdoc2->AssignValue("comments",count($comments));
			   unset($comments);
			   $textP=$textP.$mdoc2->ParseTemplate();
		   }
		}
		$mdoc->AssignValue("content",$textP);

		if (intval($vnt)>0) {
		   $mxx=(intval($vnt)-$howmanyshow);
		   $kelias=$sk->GetPath("news","back");	
		   $mdoc2 = new TemplateXP();
	       $mdoc2->ReadFile($kelias);			
		   $mdoc2->AssignValue("url","index.php?site=news&action=show&item=$mxx&user={user}&skin={skin}");
	       $mdoc->AssignValue("previuos",$mdoc2->ParseTemplate());
		} else {
			$file="back-disabled"; 
		    $kelias=$sk->GetPath("news",$file);			
		    $mdoc2 = new TemplateXP();
	        $mdoc2->ReadFile($kelias);			
		    $mdoc->AssignValue("previuos",$mdoc2->ParseTemplate());
		}

		$temp=$vnt+$howmanyshow;
		if ($temp<$newscount) {
	       $mxx=(intval($vnt)+$howmanyshow);
		   $file="next"; 
		   $kelias=$sk->GetPath("news",$file);
		   $mdoc2 = new TemplateXP();
		   $mdoc2->ReadFile($kelias);			
		   $mdoc2->AssignValue("url","index.php?site=news&action=show&item=$mxx&user=$curuser&skin=$skin");
	       $mdoc->AssignValue("next",$mdoc2->ParseTemplate());
		} else {
		   $file="next-disabled"; 
		   $kelias=$sk->GetPath("news",$file);			
		   $mdoc2 = new TemplateXP();
		   $mdoc2->ReadFile($kelias);			
	       $mdoc->AssignValue("next",$mdoc2->ParseTemplate());
		}
		$mdoc->AssignValue("url","index.php?site=news&action=add&user=".$user['sessionid']."&skin=$skin");
 endswitch;

 $text=$mdoc->ParseTemplate();


?>