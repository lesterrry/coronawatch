#!/usr/bin/env php
<?php

$response = file_get_contents('http://api.thevirustracker.com/free-api?countryTotal=RU');
$data = json_decode($response, true);
$ret = 'New cases: ' . $data['countrydata'][0]['total_new_cases_today'];

if(in_array('-t', $argv)){
        shell_exec('sudo telegrambotreport ' . $ret);
} else {
        echo($ret . "\n");
}
