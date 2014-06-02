<?php

/**
 * Class Base
 */
abstract class Base
{
    /**
     * @var array
     */
    public $ignoreProperties = array(
        'searchTerm',
        'sortColumn',
        'sortOrder',
        'twitterFormat',
        'timeSortColumn',
        'timeSortOrder',
        'dueAlerted',
        'estimateAlerted',
    );

    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        if (isset($config)) {
            $properties = get_object_vars($this);
            foreach ($config as $key => $value) {
                if (!array_key_exists($key, $properties)) {
                    if (in_array($key, $this->ignoreProperties)) {
                        continue;
                    }
                    echo '<br/>missing property [' . get_class($this) . '/' . $key . ']';
                    Kint::trace();
                }
                $this->$key = $value;
            }
        }
    }
}