<?php

$currencies = array();
$exchangers = array();
$rates = array();
function get_data(){

    $filename = "info.zip";

    $fp = fopen($filename, "r");
    fclose($fp);

    $zip = new ZipArchive;
    global $currencies;
    global $exchangers;
    global $rates;
    if (!$zip->open($filename)) exit("error");
    foreach (explode("\n", $zip->getFromName("bm_cy.dat")) as $value) {
        $entry = explode(";", $value);
        $currencies[$entry[0]] = iconv("windows-1251", "utf-8", $entry[2]);
        asort($currencies, SORT_STRING);
    }
    foreach (explode("\n", $zip->getFromName("bm_exch.dat")) as $value) {
        $entry = explode(";", $value);
        $exchangers[$entry[0]] = iconv("windows-1251", "utf-8", $entry[1]);
    }
    foreach (explode("\n", $zip->getFromName("bm_rates.dat")) as $value) {
        $entry = explode(";", $value);
        $rates[$entry[0]][$entry[1]][$entry[2]] = array("rate"=>$entry[3] / $entry[4], "reserve"=>$entry[5], "reviews"=>str_replace(".", "/", $entry[6]));
    }
    $zip->close();

}

?>