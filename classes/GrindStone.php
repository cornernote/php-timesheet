<?php
/**
 * Class GrindStone
 */
class GrindStone extends Base
{
    /**
     * @var
     */
    public $xmlFiles;
    /**
     * @var
     */
    public $staff;
    /**
     * @var string
     */
    public $createdDate;
    /**
     * @var string
     */
    public $startDate;
    /**
     * @var string
     */
    public $endDate;
    /**
     * @var
     */
    public $days;
    /**
     * @var array
     */
    public $profiles = array();

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->createdDate = $this->extractDate(date('c'));
        $this->startDate = $this->extractDate($this->startDate);
        $this->endDate = $this->extractDate($this->endDate);
        if ($this->days) {
            if ($this->startDate && !$this->endDate) {
                $this->endDate = $this->extractDate(strtotime($this->startDate) + (60 * 60 * 24 * $this->days));
            }
            if (!$this->startDate && $this->endDate) {
                $this->startDate = $this->extractDate(strtotime($this->endDate) - (60 * 60 * 24 * $this->days));
            }
        }
    }

    /**
     * @return array
     */
    public function getTimes()
    {
        $times = array(
            'total' => array(
                'total' => 0,
                'staff' => array(
                    'total' => array(),
                ),
                'profile' => array(
                    'total' => array(),
                ),
            ),
            'daily' => array(
                'total' => array(),
            ),
        );
        foreach ($this->xmlFiles as $staff => $xmlFile) {
            foreach (glob(bp() . '/data/GrindStone/timesheets/' . $staff . '/pending/*.tso') as $entry) {
                $path = pathinfo($entry);
                $timesheetFile = $path['basename'];
                if (file_exists(dirname(dirname($entry)) . '/archive/' . $timesheetFile)) {
                    continue;
                }
                $timesheet = unserialize(file_get_contents($entry));
                foreach ($timesheet->profiles as $profile) {
                    $timesheetDate = substr($timesheetFile, 0, -8);

                    // init the times
                    if (!isset($times['total']['staff']['total'][$staff]))
                        $times['total']['staff']['total'][$staff] = 0;
                    if (!isset($times['total']['staff'][$staff][$profile->name]))
                        $times['total']['staff'][$staff][$profile->name] = 0;
                    if (!isset($times['total']['profile']['total'][$profile->name]))
                        $times['total']['profile']['total'][$profile->name] = 0;
                    if (!isset($times['total']['profile'][$profile->name][$staff]))
                        $times['total']['profile'][$profile->name][$staff] = 0;
                    if (!isset($times['daily']['total'][$timesheetDate]))
                        $times['daily']['total'][$timesheetDate] = 0;
                    if (!isset($times['daily'][$timesheetDate]['staff']['total'][$staff]))
                        $times['daily'][$timesheetDate]['staff']['total'][$staff] = 0;
                    if (!isset($times['daily'][$timesheetDate]['staff'][$staff][$profile->name]))
                        $times['daily'][$timesheetDate]['staff'][$staff][$profile->name] = 0;
                    if (!isset($times['daily'][$timesheetDate]['profile']['total'][$profile->name]))
                        $times['daily'][$timesheetDate]['profile']['total'][$profile->name] = 0;
                    if (!isset($times['daily'][$timesheetDate]['profile'][$profile->name][$staff]))
                        $times['daily'][$timesheetDate]['profile'][$profile->name][$staff] = 0;

                    // get the data from tasks
                    foreach ($profile->tasks as $task) {
                        foreach ($task->times as $time) {
                            $times['total']['total'] += $time->hours;
                            $times['total']['staff']['total'][$staff] += $time->hours;
                            $times['total']['staff'][$staff][$profile->name] += $time->hours;
                            $times['total']['profile']['total'][$profile->name] += $time->hours;
                            $times['total']['profile'][$profile->name][$staff] += $time->hours;
                            $times['daily']['total'][$timesheetDate] += $time->hours;
                            $times['daily'][$timesheetDate]['staff']['total'][$staff] += $time->hours;
                            $times['daily'][$timesheetDate]['staff'][$staff][$profile->name] += $time->hours;
                            $times['daily'][$timesheetDate]['profile']['total'][$profile->name] += $time->hours;
                            $times['daily'][$timesheetDate]['profile'][$profile->name][$staff] += $time->hours;
                        }
                    }
                }
            }
        }
        return $times;
    }

    /**
     * @return array
     */
    public function getTasks()
    {
        $tasks = array();
        foreach ($this->xmlFiles as $staff => $xmlFile) {
            foreach (glob(bp() . '/data/GrindStone/timesheets/' . $staff . '/pending/*.tso') as $entry) {
                $path = pathinfo($entry);
                $timesheetFile = $path['basename'];
                if (file_exists(dirname(dirname($entry)) . '/archive/' . $timesheetFile)) {
                    continue;
                }
                $timesheet = unserialize(file_get_contents($entry));
                foreach ($timesheet->profiles as $profile) {
                    $timesheetDate = substr($timesheetFile, 0, -8);

                    // init the tasks
                    if (!isset($tasks[$timesheetDate][$staff][$profile->name])) {
                        $tasks[$timesheetDate][$staff][$profile->name] = array();
                    }

                    // get the data from tasks
                    foreach ($profile->tasks as $task) {
                        $taskHours = 0;
                        foreach ($task->times as $time) {
                            $taskHours += $time->hours;
                        }
                        $tasks[$timesheetDate][$staff][$profile->name][] = number_format($taskHours, 2) . ' - ' . $task->name;
                    }

                }
            }
        }
        return $tasks;
    }

    /**
     * @return array
     */
    public function importTimesheets()
    {
        $timesheets = array();
        $periods = $this->getPeriods($this->startDate, $this->endDate);
        foreach ($periods as $period) {
            $profiles = $this->extractProfiles($period, $this->extractDate(strtotime($period) + (60 * 60 * 24)));
            foreach ($profiles as $staff => $profile) {
                $timesheet = clone $this;
                $timesheet->staff = $staff;
                $timesheet->profiles = $profile;
                $timesheet->startDate = $period;
                $timesheet->endDate = $this->extractDate(strtotime($period) + (60 * 60 * 24));
                $timesheets[] = $timesheet->importTimesheet($timesheet->startDate, $timesheet->endDate);
            }
        }
        return $timesheets;
    }

    /**
     * @param null $start
     * @param null $end
     * @return $this
     */
    public function importTimesheet($start = null, $end = null)
    {
        file_put_contents($this->getExportFilename($start, $end), serialize($this));
        return $this;
    }

    /**
     * @return array
     */
    public function getPeriods()
    {
        $periods = array();
        foreach (range(0, $this->days - 1) as $period) {
            $periods[] = $this->extractDate(strtotime($this->startDate) + (60 * 60 * 24 * $period));
        }
        return $periods;
    }

    /**
     * @param $date
     * @return string
     */
    public function extractDate($date)
    {
        if (!$date) return;
        if (is_numeric($date)) $date = date('c', $date); // convert from timestamp
        $date = date('Y-m-d 00:00:00', strtotime($date)); // ensure midnight
        $date = date('c', strtotime($date));
        return $date;
    }

    /**
     * @param null $start
     * @param null $end
     * @return array
     */
    public function extractProfiles($start = null, $end = null)
    {
        $_profiles = array();
        foreach ($this->xmlFiles as $staff => $xmlFile) {
            $file = bp() . '/data/GrindStone/source/' . $staff . '/' . date('Y-m-d_D_H-i-s', filemtime($xmlFile['file'])) . '.gsc2';
            if (!file_exists($file)) {
                if (!file_exists(dirname($file))) mkdir(dirname($file), 0777, true);
                if (copy($xmlFile['file'], $file)) {
                    //debug("Backed up $staff GS: $file");
                }
            }
            $array = Xml2Array::convert(file_get_contents($file));
            if (isset($array['config']['profile'])) {
                $profiles = $array['config']['profile'];
                if (isset($profiles['attr'])) $profiles = array(array(
                    'attr' => $profiles['attr'],
                    'task' => $profiles['task'],
                ));
                foreach ($profiles as $profile) {
                    $_profile = new GrindProfile($profile['attr'], safeIndex($profile, 'task'), $start, $end);
                    if (!$_profile->getIgnore()) {
                        $_profiles[$staff][] = $_profile;
                    }
                }
            }
        }
        return $_profiles;
    }

    /**
     * @param null $start
     * @param null $end
     * @return string
     */
    public function getExportFilename($start = null, $end = null)
    {
        $file = bp() . '/data/GrindStone/timesheets/' . $this->staff . '/pending/' . date('Y-m-d_D', strtotime($start)) . '.tso';
        if (!file_exists(dirname($file))) mkdir(dirname($file), 0777, true);
        return $file;
    }

}