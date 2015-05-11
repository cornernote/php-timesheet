<?php
$contents = array();
$total = 0;
foreach ($times as $staff => $tasks) {
    $staffTotal = 0;
    foreach ($tasks as $date => $entries) {
        foreach ($entries as $task => $time) {
            $staffTotal += $time;
            $total += $time;
        }
    }
    $contents[] = '================================';
    $contents[] = "Hours per Task by {$staff}";
    $contents[] = '================================';
    foreach ($tasks as $date => $entries) {
        $dayTotal = 0;
        $dayTasks = array();
        foreach ($entries as $task => $time) {
            $dayTotal += $time;
            $dayTasks[] = $time . ' - ' . htmlspecialchars($task);
        }
        $contents[] = date('Y-m-d - l', strtotime($date));
        $contents[] = '--------------------------------';
        $contents[] = implode("\r\n", $dayTasks);
        $contents[] = '--------------------------------';
        $contents[] = $dayTotal . " - day total";
        $contents[] = '================================';
    }
    $contents[] = "{$staffTotal} - Total for {$staff}";
    $contents[] = '================================';
    $contents[] = '';
}
$contents[] = '================================';
$contents[] = "{$total} - Grand Total";
$contents[] = '================================';

echo implode("\r\n", $contents);
