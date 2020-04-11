<?php

function current_user()
{
    return auth()->user();
}

function current_player()
{
    return auth()->user()->player;
}

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

    function niceCount($input, $spacer = ', ')
    {
        if ($input->count() == 0)
        {
            return '';
        }
        if ($input->count() == 1)
        {
            return $input->get(0);
        }

        return $input->implode($spacer);
    }
}

if (!function_exists('printDate'))
{

    function printDate($input)
    {
        if ($input->isToday())
        {
            $date = 'heute ';
        } elseif ($input->isYesterday())
        {
            $date = 'gestern';
        } else
        {
            $date = 'am ' . date('j.n.Y', strtotime($input));
        }
        $time = ' um ' .
                date('H:i', strtotime($input)) .
                ' Uhr';

        return $date . $time;
    }
}
