<?php
/**
 * Class GrindTime
 */
class GrindTime extends Base
{
    /**
     * @var float
     */
    public $hours;
    /**
     * @var
     */
    public $start;
    /**
     * @var
     */
    public $end;
    /**
     * @var null
     */
    public $notes;

    /**
     * @param array $config
     * @param null $notes
     */
    public function __construct($config, $notes = null)
    {
        parent::__construct($config);
        $this->hours = round((strtotime($this->end) - strtotime($this->start)) / (60 * 60), 2);
        $this->notes = $notes;
    }

    /**
     * @param null $start
     * @param null $end
     * @return bool
     */
    public function getIgnore($start = null, $end = null)
    {
        if ($start && strtotime($this->start) < strtotime($start)) {
            return true;
        }
        if ($end && strtotime($this->end) > strtotime($end)) {
            return true;
        }
        return false;
    }

}