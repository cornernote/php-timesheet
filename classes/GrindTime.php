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
            $startDate = date('d-M-Y', strtotime($this->start));
            $endDate = date('d-M-Y', strtotime($this->end));
            if ($startDate != $endDate) {
                //                echo "<br/>  ignoring a task ending on " . $this->end . " instead of ending at " . $end . "  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
                trigger_error('a possible midnight spanning task spanning from ' . $this->start . ' to ' . $this->end, E_USER_ERROR);
            }
            return true;
        }
        return false;
    }

}