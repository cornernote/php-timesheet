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
        return gmdate('H:i', floor($hours * 3600));
    }
}