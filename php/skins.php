<?
  include_once("./php/TemplateMaker.php"); 

  //Tikrina ar vartotojas turi tam teises, kad galetu pamatyti shi WWW :)
  if ($text=CanAccess($user,"Skins","Default")) return $text;

  $apvalkalai=$sk->GetSkins();
  
  $kelias=$sk->GetPath("skins","index-inside");

  $text="";
  for ($i=0;$i<count($apvalkalai);++$i){
    $mdoc = new TemplateXP();
    $mdoc->ReadFile($kelias);
	$mdoc->AssignValue("skin",$apvalkalai[$i]);
    $sk->CurrentSkin($apvalkalai[$i]);
	$mdoc->AssignValue("description",$sk->GetTitle());
    $text .= $mdoc->ParseTemplate();
  }

  $kelias=$sk->GetPath("skins","index");
  $mdoc = new TemplateXP();
  $mdoc->ReadFile($kelias);
  $mdoc->AssignValue("skins",$text);
  $text = $mdoc->ParseTemplate();

?>