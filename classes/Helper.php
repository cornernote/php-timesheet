<?php
/**
 * Class Helper
 */
class Helper
{
    /**
     * @param number $hours
     * @return string
     */
    static public function formatHours($hours)
    {
        return floor($hours) . ':' . sprintf("%02d", floor(($hours * 60) % 60));
    }
}