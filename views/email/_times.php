<?php
foreach ($times as $staff => $tasks) {
    echo "\r\n" . "================" . "\r\n" . "Hours per Task by {$staff}" . "\r\n" . "================" . "\r\n";
    foreach ($tasks as $date => $entries) {
        $dayTotal = 0;
        $dayTasks = array();
        foreach ($entries as $task => $time) {
            $dayTotal += $time;
            $dayTasks[] = Helper::formatHours($time) . ' - ' . htmlspecialchars($task);
        }
        echo "\r\n" . "-- " . date('Y-m-d - l', strtotime($date)) . " --" . "\r\n";
        echo implode("\r\n", $dayTasks) . "\r\n";
        echo "--" . "\r\n";
        echo Helper::formatHours($dayTotal) . " - day total" . "\r\n";
    }
}