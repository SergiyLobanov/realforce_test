<?php

namespace App\Service;

class FormatsChecker
{
    /**
     * @param string $date_str
     * @return bool
     */
    public static function checkDate(string $date_str): bool
    {
        $date_arr = explode('-', $date_str);

        if (empty($date_arr[0]) || empty($date_arr[1]) || empty($date_arr[2])) {
            return false;
        }

        return checkdate($date_arr[1], $date_arr[2], $date_arr[0]);
    }
}