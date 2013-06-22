<?php

/**
 * @package xml
 * @author Ricardo Alexandre Sismeiro <ricardo@sismeiro.com>
 * @version 1.0.0.0
 * @copyright Copyright (c) 2007, Ricardo Alexandre Sismeiro.
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE
 * @link http://www.sismeiro.com/php/xml.phps
 *
 */

class xml {
	private $doc;
	private $filename;
	public $result;
	public $rootname;

	function __construct(){
		if (!$this->required_functions()){die();}
		$this->result=false;
	}

	private function required_functions(){
	   $result=true;
	   $fun=array('simplexml_load_file','simplexml_load_string');
	   foreach ($fun as $name){
	   	if (!function_exists($name)) {
	   		$result=false;
	   		trigger_error('xml error - this class need some functions like '.$name,E_USER_WARNING);
	   	}
	   }
	   if (!$result) trigger_error('xml error - can\'t proceed',E_USER_ERROR);
	   return $result;
	}

	public function load_file($filename){
		if (is_readable($filename)){
			$this->filename=$filename;
			$this->doc=simplexml_load_file($filename);
		} else {
			trigger_error("xml error - the file $filename is not readable!",E_USER_WARNING);
		}
	}

	public function load_string($string){
		if ($string!=''){
			$this->doc=simplexml_load_string($string);
			if (!is_object($this->doc)){trigger_error("xml error - could not create a simple xml object from the string [$string]",E_USER_WARNING);}
		}
		else {
			trigger_error("xml error - the parameter \$string is ''",E_USER_WARNING);
		}
	}


	private function convert_simplexml_object2array(&$result,$root,$rootname='root'){
	    $n=count($root->children());
	    if ($n>0){
	        if (!isset($result[$rootname]['@attributes'])){
	            $result[$rootname]['@attributes']=array();
	            foreach ($root->attributes() as $atr=>$value)
	               $result[$rootname]['@attributes'][$atr]=(string)$value;
	        }

	        foreach ($root->children() as $child){
	             $name=$child->getName();
	             $this->{__FUNCTION__}($result[$rootname][],$child,$name);
	        }
	    } else {
	        $result[$rootname]=(array) $root;
	        if (!isset($result[$rootname]['@attributes']))
	            $result[$rootname]['@attributes']=array();
	    }
	}

	private function convert_array2simplexml_object($array,$doc=''){
		if (is_array($array)){
			if (!is_object($doc)) $doc=$this->doc;

			if ((isset($array['@attributes'])) && (count($array['@attributes'])>0)){
			   	foreach ($array['@attributes'] as $attribute=>$value)
			   		$doc->addAttribute($attribute, utf8_encode($value));
			   	unset($array['@attributes']);
			}

			foreach ($array as $key=>$value){
				if (is_numeric($key) && is_array($value)){
					list($child)=array_keys($value);
					if (is_array($value[$child][0])){$newchild=$doc->addChild($child);}
					else {$newchild=$doc->addChild($child,utf8_encode($value[$child][0]));}
					$this->{__FUNCTION__}($value[$child],$newchild);
				}
			}
		}
	}

	public function xml2array($save_result=false){
		$result=false;
		if (is_object($this->doc)){
			$result=array();
			$this->rootname = $this->doc->getName();
    		$this->convert_simplexml_object2array($result,$this->doc,$this->rootname);
    		(isset($result[$this->rootname])) ? ($result=$result[$this->rootname]) : ($result=false);
		}
    	if ($save_result) $this->result=$result;
    	return $result;
	}

	public function array2xml($array,$rootname,$save_result=false){
		$xml_string='<?xml version=\'1.0\' encoding=\'utf8\'?'.'>'.PHP_EOL;
		$xml_string.='<'.$rootname.'>'.PHP_EOL;
		$xml_string.='</'.$rootname.'>';
		$this->load_string($xml_string);
		$this->convert_array2simplexml_object($array);
		$result=$this->doc->asXML();
		if ($save_result) $this->result=$result;
		return $result;
	}

}
?>