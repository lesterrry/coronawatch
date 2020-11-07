#!/usr/bin/env php
<?php

$dir = '/bin/coronawatch_data';
$cache = json_decode(file_get_contents($dir . '/cache.json'), true);

$curl = curl_init();
curl_setopt_array($curl, [
        CURLOPT_URL => "https://covid-193.p.rapidapi.com/statistics?country=russia",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: covid-193.p.rapidapi.com",
                "x-rapidapi-key: KEY"
        ],
]);

$response = curl_exec($curl);

$data = json_decode($response, true);
$today = str_replace('+', '', $data['response'][0]['cases']['new']);
if($today == 0){
        echo ("ERR: No data for today yet");
} else {
        $rec = (($cache['record'] < $today) ? $today : $cache['record']);
        $def = array("today" => $today, "record" => $rec, "date" => date("m/d/y"));
        $dif = ($today - $cache['today']);
        if(in_array('-t', $argv)){
                $ret = '[CW]: *\\+' . $today . '* \\> *' . $dif . '*, R: *' . $cache['record'$
                $swret = '[CW]: +' . $today . ' \(' . $dif . '\) R: ' . $cache['record'];
                shell_exec('telegrambotreport -m "' . $ret . '"');
                shell_exec('sudo send -NSA-'. $swret);
        } else {
                $ret = '+' . $today . ' (' . $dif . '), R: ' . $cache['record'];
                echo($ret . "\n");
        }

        if(date("m/d/y") != $cache["date"]){
                file_put_contents($dir . '/cache.json', json_encode($def));
        }
}
