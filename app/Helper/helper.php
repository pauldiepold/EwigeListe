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
if (!function_exists('niceCount'))
{

    function niceCount($input)
    {
        if ($input->count() == 0)
        {
            return '';
        }
        if ($input->count() == 1)
        {
            return $input->get(0);
        }

        return $input->implode(', ');
    }
}