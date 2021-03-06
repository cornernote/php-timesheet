<?php
class ActiveCollab extends Base
{
    public $url;
    public $token;
    public $archivePath;
    public $profiles = array();
    public $users = array();
    public $user_id;
    public $projects = array();
    public $tickets = array();
    public $errors = array();

    public function uploadTimesheets()
    {
        $errors = array();
        foreach (glob(bp() . 'data/GrindStone/timesheets/*', GLOB_ONLYDIR) as $entry) {
            $path = pathinfo($entry);
            $staff = $path['basename'];
            foreach (glob(bp() . 'data/GrindStone/timesheets/' . $staff . '/pending/*.tso') as $entry) {
                $this->errors = array();
                $path = pathinfo($entry);
                $timesheetFile = $path['basename'];
                $timesheet = unserialize(file_get_contents($entry));
                $this->uploadTimesheet($timesheet, $staff);
                if (!empty($this->errors)) {
                    $errors[$staff][$timesheetFile] = $this->errors;
                }
            }
        }
        return $errors;
    }

    public function uploadTimesheet($timesheet, $staff)
    {
        $times = $this->convertTimesheet($timesheet, $staff);
        if ($times) {
            foreach ($times as $time) {
                $upload = $this->addTime($time);
                if ($upload) {
                    debug($upload);
                }
                else {
                    debug('REALLY BAD ERROR - CANT UPLOAD TIME');
                    debug($time);
                    debug($this->errors);
                }
            }
        }
        else {
            return false;
        }
        return true;
    }

    public function convertTimesheet($timesheet, $staff)
    {
        $times = array();
        foreach ($timesheet->profiles as $profile) {
            $projectId = safeIndex($this->profiles, $profile->name);
            if (!$project = $this->project($projectId)) {
                $this->errors[] = "invalid project '{$projectId}' for profile '{$profile->name}'";
                return false;
            }
            foreach ($profile->tasks as $task) {
                if ($task->ticketId && !$ticket = $this->ticket($projectId, $task->ticketId)) {
                    $this->errors[] = "invalid ticket '{$task->ticketId}' for project '{$projectId}'";
                    return false;
                }
                foreach ($task->times as $time) {
                    if (!$time->hours) continue;
                    $times[] = array(
                        'user_id' => $this->users[$staff],
                        'project_id' => $projectId,
                        'project_name' => $project->name,
                        'task_id' => $task->ticketId,
                        'task_name' => $task->name,
                        'notes' => '[' . substr($time->start, 11) . ']' . ($time->notes ? ' -- ' . str_replace("\r\n", ' -- ', $time->notes) : null),
                        'hours' => $time->hours,
                        'date' => substr($time->start, 0, 10),
                    );
                }
            }
        }
        return $times;
    }

    public function addTime($options)
    {
        $options = array_merge(array(
            'user_id' => $this->getUserId(),
            'job_type_id' => 1,
            'project_id' => 0,
            'task_id' => 0,
            'notes' => null,
            'hours' => 0,
            'date' => null,
            'billable' => 1,
        ), $options);
        extract($options);

        // DO NOT ADD ELEMENTS TO THIS ARRAY THIS OR IT WILL
        // CAUSE ACTIVECOLLAB TIMES TO BE REUPLOADED
        $file = $this->archivePath . md5(serialize(array(
            'user_id' => $options['user_id'],
            'project_id' => $options['project_id'],
            'task_id' => $options['task_id'],
            'notes' => $options['notes'],
            'hours' => $options['hours'],
            'date' => $options['date'],
        )));

        if (file_exists($file)) {
            $time = unserialize(file_get_contents($file));
            if ($time->value == $options['hours']) {
                return $time;
            }
        }

        $project = $this->project($options['project_id']);
        if (!$project) {
            $this->errors[] = "invalid project '{$options['project_id']}'";
            return false;
        }

        $parent_id = null;
        if ($options['task_id']) {
            $ticket = $this->ticket($options['project_id'], $options['task_id']);
            if (!$ticket) {
                $this->errors[] = "invalid ticket '{$options['task_id']}' for project '{$options['project_id']}'";
                return false;
            }
            $parent_id = $ticket->id;
        }

        // create a time entry
        $response = $this->call("projects/{$options['project_id']}/tracking/time/add", array(
            'submitted' => 'submitted',
            'time_record[user_id]' => $options['user_id'],
            'time_record[job_type_id]' => $options['job_type_id'],
            'time_record[parent_id]' => $parent_id,
            'time_record[record_date]' => $options['date'],
            'time_record[value]' => $options['hours'],
            'time_record[billable_status]' => $options['billable'],
            'time_record[body]' => $options['notes'],
        ));
        if ($response && $response->id) {
            if (!file_exists(dirname($file))) mkdir(dirname($file), 0777, true);
            file_put_contents($file, serialize($response));
            return $response;
        }
    }

    public function project($project_id)
    {
        if (!$project_id) return false;
        if (isset($this->projects[$project_id])) {
            return $this->projects[$project_id];
        }
        $file = bp() . 'data/ActiveCollab/Project/' . $project_id;
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));
        }
        $project = $this->call("projects/{$project_id}");
        if (!file_exists(dirname($file))) mkdir(dirname($file), 0777, true);
        file_put_contents($file, serialize($project));
        $this->projects[$project_id] = $project;
        return $project;
    }

    public function ticket($project_id, $task_id)
    {
        if (!$project_id || !$task_id) return false;
        if (isset($this->tickets[$project_id][$task_id])) {
            return $this->tickets[$project_id][$task_id];
        }
        $file = bp() . 'data/ActiveCollab/Ticket/' . $project_id . '-' . $task_id;
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));
        }
        $ticket = $this->call("projects/{$project_id}/tasks/{$task_id}");
        if (!file_exists(dirname($file))) mkdir(dirname($file), 0777, true);
        file_put_contents($file, serialize($ticket));
        $this->tickets[$project_id][$task_id] = $ticket;
        return $ticket;
    }

    private function getUserId()
    {
        if (!$this->user_id) {
            $token = explode('-', $this->token);
            $this->user_id = $token[0];
        }
        return $this->user_id;
    }

    private function call($action, $request = null)
    {
        require_once(bp() . '/vendors/snoopy/Snoopy-1.2.4/Snoopy.class.php');
        $snoopy = new Snoopy();
        $snoopy->accept = 'application/json';
        //$snoopy->set_submit_normal();
        $url = $this->url . '?path_info=' . $action . '&auth_api_token=' . $this->token;
        $snoopy->submit($url, $request);
        if ($snoopy->status != 200) {
            $this->errors[] = array(
                'header' => trim($snoopy->headers[0]),
                'action' => $action,
                'request' => $request,
            );
        }
        return json_decode($snoopy->results);
    }

}