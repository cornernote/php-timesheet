<?php
  	
  	require_once('xml.php');
		
  	$xml=new xml();
  	$xml->load_file('example.xml');
  	$array=$xml->xml2array();
  	
  	echo "<pre>";
  	var_dump($array);
  	
?>