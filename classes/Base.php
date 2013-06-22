<?php
abstract class Base 
{
    public function __construct($config=array())
    {
        if (isset($config)){
            foreach($config as $key=>$value){
                $this->$key=$value;
            }
        }
    }
}