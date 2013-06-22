<?php
  	
  	require_once('xml.php');
	
	$book=array();
  	$book[]=array('author'=>array('Fernado Pessoa'));
  	$book[]=array('title'=>array('Mensagem'));
  	$book[]=array('year'=>array('1934'));  	
  	$book['@attributes']=array('isbn'=>'9789722332392');
  	  	
  	$archive=array();
  	$archive[]=array('book'=>$book);
  	$archive[]=array('book'=>$book);
  	$archive['@attributes']=array('lang'=>'pt');
   	
  	$archives=array();
  	$archives[]=array('archive'=>$archive);
  	$archives[]=array('archive'=>$archive);
  	$archives[]=array('archive'=>$archive);
  	$archives['@attributes']=array('year'=>'2000','type'=>'literature');
   	
  	$library=array();
  	$library[]=array('archives'=>$archives);  	
  	
  	$xml=new xml();
  	$output=$xml->array2xml($library,'library');
  	
  	echo $output;  
  	
?>