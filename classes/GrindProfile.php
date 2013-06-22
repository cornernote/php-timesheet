<?php
/**
 * Class GrindProfile
 */
class GrindProfile extends Base
{
    /**
     * @var
     */
    public $name;
    /**
     * @var array
     */
    public $tasks = array();
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
     * @param null $tasks
     * @param null $startDate
     * @param null $endDate
     */
    public function __construct($config, $tasks = null, $startDate = null, $endDate = null)
    {
        parent::__construct($config);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->tasks = $this->extractTasks($tasks);
    }

    /**
     * @param $tasks
     * @return array
     */
    public function extractTasks($tasks)
    {
        $_tasks = array();
        if (!empty($tasks)) {
            if (isset($tasks['attr'])) {
                $tasks = array(array(
                    'attr' => $tasks['attr'],
                    'time' => isset($tasks['time']) ? $tasks['time'] : null,
                    'value' => isset($tasks['value']) ? $tasks['value'] : null,
                ));
            }
            foreach ($tasks as $task) {
                $_task = new GrindTask(safeIndex($task, 'attr'), safeIndex($task, 'time'), safeIndex($task, 'value'), $this->startDate, $this->endDate);
                if (!$_task->getIgnore()) {
                    $_tasks[] = $_task;
                }
            }
        }
        return $_tasks;
    }

    /**
     * @return bool
     */
    public function getIgnore()
    {
        if (strpos($this->name, '-archive') !== false) {
            return true;
        }
        foreach ($this->tasks as $task) {
            if (!$task->getIgnore()) {
                return false;
            }
        }
        return true;
    }
}