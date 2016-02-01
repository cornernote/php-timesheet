<?php
// global init
require('includes/init.php');

// get page or action
$page = arg(0) ? arg(0) : 'index';

// perform page actions
if ($page == 'grindstone') {
    $ci = new CsvImport(config('CsvImport'));
    $csvTimesheets = $ci->convertTimesheets();
    debug($csvTimesheets);
    $gs = new GrindStone(config('GrindStone'));
    $sheetImport = $gs->importTimesheets();
    debug($sheetImport);
    $page = false;
    //die;
}
if ($page == 'redmine') {
    set_time_limit(60 * 5);
    debug('Check at the bottom for errors.');
    $rm = new Redmine(config('Redmine'));
    $errors = $rm->uploadTimesheets();
    debug($errors);
    $page = false;
    //die;
}
if ($page == 'saasu') {
    set_time_limit(60 * 5);
    $saasu = new Saasu(config('Saasu'));
    $invoices = $saasu->getInvoices();
    $tasks = $saasu->createInvoices($invoices);
    $result = $saasu->uploadInvoices($tasks);
    debug($result);
    $page = false;
    //die;
}

// collect page content
$content = $page ? render($page, array(), true) : '';

// global layout
render('elements/layout', array(
    'content' => $content,
));