<?php

if (!function_exists('nice_count'))
{

    function nice_count($input = array())
    {
        if (count($input) == 0)
        {
            return '';
        }
        if (count($input) == 1)
        {
            return $input[0];
        }
        $last = array_pop($input);
        $tmp = implode(', ', $input);

        return $tmp . ' und ' . $last;
    }
}