<?php
// http://www.redmine.org/projects/redmine/wiki/Rest_api
// http://www.redmine.org/projects/redmine/wiki/Rest_TimeEntries
// https://github.com/kbsali/php-redmine-api

use Redmine\Api\TimeEntry;

require_once(bp() . '/vendors/php-redmine-api/php-redmine-api-3af491886a/lib/Redmine/Client.php');
require_once(bp() . '/vendors/php-redmine-api/php-redmine-api-3af491886a/lib/Redmine/Api/AbstractApi.php');
require_once(bp() . '/vendors/php-redmine-api/php-redmine-api-3af491886a/lib/Redmine/Api/TimeEntry.php');

/**
 * Class Redmine
 */
class Redmine extends Base
{
    /**
     * @var
     */
    public $url;
    /**
     * @var
     */
    public $archivePath;
    /**
     * @var array
     */
    public $profiles = array();
    /**
     * @var array
     */
    public $users = array();
    /**
     * @var array
     */
    public $tickets = array();
    /**
     * @var array
     */
    public $errors = array();

    /**
     * @return array
     */
    public function uploadTimesheets()
    {
        $errors = array();
        foreach ($this->users as $user => $token)
            foreach (glob(bp() . '/data/GrindStone/timesheets/' . $user . '/pending/*.tso') as $entry) {
                $this->errors = array();
                $path = pathinfo($entry);
                $timesheetFile = $path['basename'];
                $timesheet = unserialize(file_get_contents($entry));
                $this->uploadTimesheet($timesheet, $user, $token);
                if (!empty($this->errors)) {
                    $errors[$user][$timesheetFile] = $this->errors;
                }
            }
        return $errors;
    }

    /**
     * @param $timesheet
     * @param $staff
     * @param $token
     * @return bool
     */
    public function uploadTimesheet($timesheet, $staff, $token)
    {
        $times = $this->convertTimesheet($timesheet, $staff);
        if (!$times) {
            return false;
        }
        foreach ($times as $time) {
            //debug($time);
            if (!$this->addTime($time, $token)) {
                debug('REALLY BAD ERROR - CANT UPLOAD TIME');
                debug($time);
                debug($this->errors);
            }
        }
        return true;
    }

    /**
     * @param $timesheet
     * @param $staff
     * @return array
     */
    public function convertTimesheet($timesheet, $staff)
    {
        $times = array();
        foreach ($timesheet->profiles as $profile) {
            if (empty($this->profiles[$profile->name])) continue;
            $ticketIdFallback = $this->profiles[$profile->name];
            $multiplier = Saasu::getStaffMultiplier($staff, $profile->name);
            foreach ($profile->tasks as $task) {
                if (!$task->ticketId) {
                    $task->ticketId = $ticketIdFallback;
                }
                foreach ($task->times as $time) {
                    if (!$time->hours) continue;
                    $times[] = array(
                        'name' => $task->name,
                        'issue_id' => $task->ticketId,
                        'comments' => '[' . substr($time->start, 11) . ']' . ($time->notes ? ' -- ' . str_replace("\r\n", ' -- ', $time->notes) : null),
                        'spent_on' => substr($time->start, 0, 10),
                        'hours' => $time->hours * $multiplier,
                    );
                }
            }
        }
        return $times;
    }

    /**
     * @param $options
     * @param $token
     * @return bool
     */
    public function addTime($options, $token)
    {
        $options = array_merge(array(
            'name' => null,
            'issue_id' => 0,
            'comments' => null,
            'spent_on' => null,
            'hours' => 0,
        ), $options);

        // DO NOT ADD ELEMENTS TO THIS ARRAY THIS OR IT WILL
        // CAUSE REDMINE TIMES TO BE REUPLOADED
        $file = $this->archivePath . md5(serialize($options));
        if (file_exists($file)) {
            $_options = unserialize(file_get_contents($file));
            if ($_options['hours'] == $options['hours']) {
                return true;
            }
        }

        if (!$options['issue_id']) {
            $this->errors[] = "unknown ticket '{$options['name']}'";
            return false;
        }

        // create a time entry
        $client = new Redmine\Client($this->url, $token);
        $request = array(
            'issue_id' => $options['issue_id'],
            'comments' => $options['comments'],
            'spent_on' => $options['spent_on'],
            'hours' => $options['hours'],
        );
        /** @var TimeEntry $api */
        $api = $client->api('time_entry');
        $response = $api->create($request);

        if ($response && !empty($response->id)) {
            if (!file_exists(dirname($file))) mkdir(dirname($file), 0777, true);
            file_put_contents($file, serialize($options));
            return true;
        }
        debug($request);
        debug($response);
        return false;
    }

}