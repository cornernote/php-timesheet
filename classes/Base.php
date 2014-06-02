<?php
abstract class Base
{
    public $ignoreProperties = array (
        'searchTerm',
        'sortColumn',
        'sortOrder',
        'twitterFormat',
        'timeSortColumn',
        'timeSortOrder',
        'dueAlerted',
        'estimateAlerted',
);
    public function __construct($config = array())
    {
        if (isset($config)) {
            $properties = get_object_vars($this);
            foreach ($config as $key => $value) {
                if (!array_key_exists($key,$properties)) {
                    if (in_array($key ,$this->ignoreProperties)){
                        continue;
                    }
                    echo "<br/> missing property [" . get_class($this) . '/' . $key . "]  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
                }
                $this->$key = $value;
            }
        }
    }
}