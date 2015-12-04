<?php

/**
 * Class GrindTask
 */
class GrindTask extends Base
{
    /**
     * @var
     */
    public $name;
    /**
     * @var
     */
    public $ticketId;
    /**
     * @var string
     */
    public $notes;
    /**
     * @var array
     */
    public $times = array();
    /**
     * @var null
     */
    public $startDate;
    /**
     * @var null
     */
    public $endDate;

    /**
     * @param array $config
     * @param null $times
     * @param null $notes
     * @param null $startDate
     * @param null $endDate
     */
    function __construct($config, $times = null, $notes = null, $startDate = null, $endDate = null)
    {
        parent::__construct($config);
        $this->ticketId = $this->extractTicketId($this->name);
        $this->notes = $this->extractNotes($notes);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->times = $this->extractTimes($times);
    }

    /**
     * @param $times
     * @return array
     */
    public function extractTimes($times)
    {
        $_times = array();
        if (!empty($times)) {
            if (isset($times['attr'])) {
                $times = array(array(
                    'attr' => $times['attr'],
                    'value' => isset($times['value']) ? $times['value'] : null,
                ));
            }
            foreach ($times as $time) {
                $_time = new GrindTime(safeIndex($time, 'attr'), $this->extractTimeNotes(safeIndex($time, 'value')), $this->startDate, $this->endDate);
                if ($_time->getIgnore($this->startDate, $this->endDate)) {
                    continue;
                }
                if ($_time->getIsAcrossMidnight()) {
                    $_time->hours = $_time->hours / 2;
                    $_time1 = clone($_time);
                    $_time1->end = date('Y-m-d', strtotime($_time1->end . ' -1day')) . 'T23:59:59+10:30';
                    $_times[] = $_time1;
                    $_time2 = clone($_time);
                    $_time2->start = date('Y-m-d', strtotime($_time2->start . ' +1day')) . 'T00:00:00+10:30';
                    $_times[] = $_time2;
                } else {
                    $_times[] = $_time;
                }
            }
        }
        return $_times;
    }

    /**
     * @param null $taskNotes
     * @return string
     */
    public function extractNotes($taskNotes = null)
    {
        $notes = array();
        if (!$this->ticketId) {
            $notes[] = $this->name;
        }
        if ($taskNotes) {
            $notes[] = $taskNotes;
        }
        if ($this->notes) {
            $_notes[] = $this->notes;
        }
        return implode("\r\n", $notes);
    }

    /**
     * @param null $timeNotes
     * @return string
     */
    public function extractTimeNotes($timeNotes = null)
    {
        $notes = explode("\r\n", $this->notes);
        if ($timeNotes) {
            $notes[] = $timeNotes;
        }
        return implode("\r\n", $notes);
    }

    /**
     * @param null $name
     * @return string
     */
    function extractTicketId($name = null)
    {
        if (!$name) $name = $this->name;
        preg_match('/#([0-9]+)/i', $name, $regs);
        return isset($regs[1]) ? $regs[1] : false;
    }


    /**
     * @return bool
     */
    public function getIgnore()
    {
        foreach ($this->times as $time) {
            /** @var $time GrindTime */
            if (!$time->getIgnore($this->startDate, $this->endDate)) {
                return false;
            }
        }
        return true;
    }

}