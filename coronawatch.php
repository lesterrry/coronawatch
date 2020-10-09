#!/usr/bin/env php
<?php

$dir = '/bin/coronawatch_data';
$cache = json_decode(file_get_contents($dir . '/cache.json'), true);
$response = file_get_contents('http://api.thevirustracker.com/free-api?countryTotal=RU');
$data = json_decode($response, true);
$today = $data['countrydata'][0]['total_new_cases_today'];
if($today == 0){
        echo ("ERR: No data for today yet");
} else {
        $def = array("today" => $today, "record" => ($cache['today'] < $today) ? $today : $cache['today'], "date" => date("m/d/y"));

        if(in_array('-t', $argv)){
                $ret = '\\[CW\\]: *\\+' . $today . '* \\> *' . ($today - $cache['today']) . '*, R: *' . $cache['record'] . '*';
                shell_exec('telegrambotreport -m "' . $ret . '"');
        } else {
                $ret = '+' . $today . ' (' . ($today - $cache['today']) . '), R: ' . $cache['record'];
                echo($ret . "\n");
        }

        if(date("m/d/y") != $cache["date"]){
                file_put_contents($dir . '/cache.json', json_encode($def));
        }
}
