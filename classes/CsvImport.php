<?php

/**
 * Class CsvImport
 */
class CsvImport extends Base
{
    /**
     * @var
     */
    public $csvPaths;

    /**
     * @return array
     */
    public function convertTimesheets()
    {
        $timesheets = array();
        foreach ($this->csvPaths as $staff => $csvPath) {
            $profiles = array();
            foreach (glob($csvPath['path'] . '/*.csv') as $csvFile) {
                $csv = $this->csvToArray($csvFile);
                foreach ($csv as $row) {
                    $profiles[$row['profile']][] = array(
                        '@attributes' => array('name' => $row['task']),
                        'time' => array('@attributes' => array(
                            'start' => date('c', strtotime($row['start'])),
                            'end' => date('c', strtotime($row['end'])),
                        )),
                    );
                }
                rename($csvFile, str_replace('/grindstone/', '/grindstone/_archive/' . date('Y-m-d') . '-' . uniqid() . '-', $csvFile));
            }
            $grindstone = array('profile' => array());
            foreach ($profiles as $profile => $tasks) {
                $_tasks = array();
                foreach ($tasks as $task) {
                    $_tasks[] = $task;
                }
                $grindstone['profile'][] = array(
                    '@attributes' => array('name' => $profile),
                    'task' => $_tasks,
                );
            }
            $xml = Array2XML::createXML('config', $grindstone);
            file_put_contents($csvPath['file'], $xml->saveXML());
            $timesheets[$staff] = $grindstone;
        }
        return $timesheets;
    }

    /**
     * @param $fileName
     * @param string $delimiter
     * @param int $headerRow
     * @return array
     */
    public function csvToArray($fileName, $delimiter = ',', $headerRow = 1)
    {
        $handle = fopen($fileName, 'r');
        $rows = array();
        while ($headerRow > 1) {
            $headerRow--;
            fgetcsv($handle, null, $delimiter);
        }
        $header = $headerRow ? fgetcsv($handle, null, $delimiter) : false;
        while (($data = fgetcsv($handle, null, $delimiter)) !== FALSE) {
            $row = array();
            if ($header) {
                foreach ($header as $key => $heading) {
                    $heading = trim($heading);
                    $row[$heading] = (isset($data[$key])) ? $data[$key] : '';
                }
                $rows[] = $row;
            } else {
                $rows[] = $data;
            }
        }
        fclose($handle);
        return $rows;
    }

}