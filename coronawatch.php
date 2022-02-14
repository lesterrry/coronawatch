#!/usr/bin/env php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$attempt = 0;
do {
        $attempt += 1;
        $response = curl_exec($curl);
        $data = json_decode($response, true);
        $today = str_replace('+', '', $data['response'][0]['cases']['new']);
        if((int)$today == 0){
                echo ("ERR: No data for today");
        } else {
                $rec = (($cache['record'] < $today) ? $today : $cache['record']);
                $def = array("today" => $today, "record" => $rec, "date" => date("m/d/y"));
                $dif = ($today - $cache['today']);
                if ($dif == 0) {
                        echo ("WARN: Received same data, retrying in 360 sec\n");
                        sleep(360);
                        continue;
                }
                if(in_array('-t', $argv)){
                        $ret = '[CW]: +' . $today . ' \\> ' . $dif . ', R: ' . $cache['record'];
                        $swret = date("m.d") . ' +' . $today . ' \(' . $dif . '\) R: ' . $cache['record'];
                        shell_exec("send -SSA-". $swret);
                        shell_exec("telegrambotreport -m " . $ret);
                } else {
                        $ret = '+' . $today . ' (' . $dif . '), R: ' . $cache['record'];
                        echo($ret . "\n");
                }

                if(date("m/d/y") != $cache["date"]){
                        file_put_contents($dir . '/cache.json', json_encode($def));
                }
        }
} while ($dif == 0 && $attempt < 10);
