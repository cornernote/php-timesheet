<?php

/**
 * @return string
 */
function bp()
{
    return dirname(dirname(__FILE__)) . '/';
}

/**
 * @return string
 */
function bu()
{
    return dirname($_SERVER['SCRIPT_NAME']);
}

/**
 * @param $k
 * @return mixed
 */
function sf($k)
{
    if (isset($_GET[$k])) {
        return $_GET[$k];
    }
}

/**
 * @param $array
 * @param $index
 * @return null
 */
function safeIndex($array, $index)
{
    if (isset($array[$index])) {
        return $array[$index];
    }
    else {
        return null;
    }
}

/**
 * @param $d
 * @param null $n
 * @param null $l
 */
function debug($d, $n = null, $l = null)
{
    echo "\n";
    echo "\n";
    echo '<div style="border:1px solid black; padding:5px; background:#FF7">';
    if ($l) {
        $bt = debug_backtrace();
        // $file=str_replace(bp(),'',$bt[0]['file']);
        $file = $bt[0]['file'];
        $line = $bt[0]['line'];
        echo "{$file} on line {$line}<br/>";
    }
    if ($n) echo "debug: '{$n}'";
    echo '<pre>';
    echo "\n";
    print_r($d);
    echo "\n";
    echo '</pre>';
    echo '</div>';
    echo "\n";
}

/**
 * @param $object
 * @param string $name
 * @param bool $attributesOnly
 */
function printr($object, $name = '', $attributesOnly = true)
{
    $console = false;
    if (in_array(php_sapi_name(), array('cli'))) {
        $console = true;
    }
    $classHint = '';
    $bt = debug_backtrace();
    $file = $bt[0]['file'];
    if ($console) {
        print  $file . ' on line ' . $bt[0]['line'] . " $name is: ";
    }
    else {
        print '<div style="background: #FFFBD6">';
        $nameLine = '';
        if ($name)
            $nameLine = '<b> <span style="font-size:18px;">' . $name . "</span></b> $classHint printr:<br/>";
        print '<span style="font-size:12px;">' . $nameLine . ' ' . $file . ' on line ' . $bt[0]['line'] . '</span>';
        print '<div style="border:1px solid #000;">';
        print '<pre>';
    }

    if (is_array($object))
        print_r($object);
    else
        var_dump($object);
    if (!$console) {
        print '</pre>';
        echo '</div></div><hr/>';
    }
}

/**
 * @param $page
 * @return string
 */
function url($page = null)
{
    return bu() . '/' . $page;
}

/**
 * @param $page
 * @return string
 */
function absoluteUrl($page = null)
{
    return 'http://' . $_SERVER['HTTP_HOST'] . url($page);
}

/**
 * Render a view element
 *
 * @param $view
 * @param array $params
 * @param bool $return
 * @return string|bool
 * @throws Exception
 */
function render($view, $params = array(), $return = false)
{
    extract($params);
    $include = bp() . '/views/' . $view . '.php';
    if (!file_exists($include)) {
        throw new Exception('View not found: ' . $include);
    }
    if ($return)
        ob_start();
    include($include);
    if ($return)
        return ob_get_clean();
    return true;
}

/**
 * @param $location
 * @param int $statusCode
 */
function redirect($location, $statusCode = 302)
{
    header('Location: ' . url($location), true, $statusCode);
    exit;
}

/**
 * @return string
 */
function args()
{
    $args = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])) + 1);
    return explode('/', $args);
}

/**
 * @param $id
 * @return null
 */
function arg($id)
{
    return safeIndex(args(), $id);
}

/**
 * @param $key
 * @return null
 */
function config($key)
{
    return safeIndex($_ENV['config'], $key);
}