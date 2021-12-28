<?php

class BestChange {
    private $URL = 'http://api.bestchange.ru/info.zip';
    private $FILENAME = 'info.zip';

    public $currencies = array();
    public $exchangers = array();
    public $rates = array();
    public $res = false;
    public $exc = '';
    
    public $file_currencies = 'bm_cy.dat';
    public $file_exchangers = 'bm_exch.dat';
    public $file_rates = 'bm_rates.dat';
    public $file_cities = 'bm_cities.dat';

    public function __construct() {
        $this->load();
        $this->res = $this->data(0);
    }

    public function load() {
        try {
            if (file_exists($this->FILENAME) == false)
            {
                if (!$this->save())
                    return false;
            }
            else{
                if (time() - filemtime($this->FILENAME) > 86400)
                {
                    if (!$this->save())
                        return false;
                }
            }
            
        } catch (Exception $e) {
            $this->exc = 'Не удалось загрузить файл!';
            return false;
        }
        return true;
    }
    public function save(){
        $result = file_put_contents($this->FILENAME, fopen($this->URL, 'r'));
        if ($result === false) {
            $this->exc = 'Не удалось сохранить файл!';
            return false;
        }
    }
    public function data($count){
        $zip = new ZipArchive;
        if ($count < 3){     
            if (!$zip->open($this->FILENAME))
            {
                $this->load();
                return $this->data($count + 1);
            }
        }
        else
        {
            $this->exc = 'Не удалось открыть файл!';
            return false;
        } 
        try{
            foreach (explode("\n", $zip->getFromName("bm_cy.dat")) as $value) {
                $entry = explode(";", $value);
                $this->currencies[$entry[0]] = iconv("windows-1251", "utf-8", $entry[2]);
            }
            asort($this->currencies, SORT_STRING);
            foreach (explode("\n", $zip->getFromName("bm_exch.dat")) as $value) {
                $entry = explode(";", $value);
                $this->exchangers[$entry[0]] = iconv("windows-1251", "utf-8", $entry[1]);
            }
            foreach (explode("\n", $zip->getFromName("bm_rates.dat")) as $value) {
                $entry = explode(";", $value);
                $this->rates[$entry[0]][$entry[1]][$entry[2]] = array("rate" => $entry[3] / $entry[4], "reserve" => $entry[5], "reviews" => str_replace(".", "/", $entry[6]));
            }
            $zip->close();
        } catch (Exception $e) {
            $this->exc = 'Не удалось спарсить данные!';
            return false;
        }
        return true;
    }

    public function currencies(){
        return $this->currencies;
    }
    public function exchangers(){
        return $this->exchangers;
    }
    public function rates(){
        return $this->rates;
    }
}

function get_data($api, $fr, $ton){
    global $count;
    global $s;
    global $table;
    foreach ($api->rates[$fr][$ton] as $exch_id => $entry) {
        $row = '';
        if ($count % 2 == 1) {
            $row = ' row';
        }
        $reverse = strrev(round($entry["rate"], 0));
        $rate = strrev(chunk_split($reverse, 3, ' '));
        $rev2 = strrev(round(1 / $entry["rate"], 0));
        $rate2 = strrev(chunk_split($rev2, 3, ' '));
        $table .= '<div class="table__info-row' . $row . '">
        <div class="info__row-value">';
        $table .= '<a target="_blank" href="https://www.bestchange.ru/click.php?id=' . $exch_id . '">' . $api->exchangers[$exch_id] . '</a> </div>
            <div class="info__row-value">';
        $table .= ($entry["rate"] < 1 ? 1 : $rate) . ' ' . $api->currencies[$fr] . '</div>
            <div class="info__row-value">';
        $table .= ($entry["rate"] < 1 ? $rate2 : 1) . ' ' . $api->currencies[$ton] . '</div>
            <div class="info__row-value">';
        $table .= $entry["reserve"] . '</div>
            <div class="info__row-value" style="width:70px">';
        $table .= $entry["reviews"] . '</div></div>';
        $count++;
        $s += $entry["reserve"];
    }
}

?>