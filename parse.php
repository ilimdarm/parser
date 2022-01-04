<?php

class Rates{
    public $data = array();

    public function __construct($text, $split_reviews) {
        foreach (explode("\n", $text) as $value) {
            $entry = explode(";", $value);
            try{
                if ($split_reviews) $t =  explode(".", $entry[6]);  else $t = $entry[6];
                $this->data[$entry[0]][$entry[1]][$entry[2]] = array(
                    'give_id' =>  $value[0],
                    'get_id' =>  $entry[1],
                    'exchange_id' =>  $entry[2],
                    'rate' =>  (float)$entry[3] / ((float)$entry[4]),
                    'reserve' =>  $entry[5],
                    'reviews' => $t,
                    'min_sum' =>  $entry[8],
                    'max_sum' =>  $entry[9],
                    'city_id' =>  $entry[10]
                );
            }
            catch (DivisionByZeroError $e){
            }
        }
    }
    public function get(){
        return $this->data;
    }

}

class Common{
    public function __construct(){
        $this->data = array();
    }
    public function get(){
        return $this->data;
    }
}

class Currencies extends Common{
    public function __construct($text){
        parent::__construct();
        foreach (explode("\n", $text) as $row){
            $val = explode(";", $row);
            $this->data[$val[0]] = array(
                'id' => $val[0],
                'pos_id' => $val[1],
                'name' => $val[2]
            );
        }
        uasort($this->data, function($a, $b) {
            return $a['name'] <=> $b['name'];
           });
    } 
}

class Exchangers extends Common{
    public function __construct($text){
        parent::__construct();
        foreach (explode("\n", $text) as $row){
            $val = explode(";", $row);
            $this->data[$val[0]] = array(
                'id' => $val[0],
                'name' => $val[1],
                'wmbl' => $val[3], 
                'reserve_sum' => $val[4]
            );
        }
        uasort($this->data, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });
    }
}


class Cities extends Common{
    public function __construct($text){
        parent::__construct();
        foreach (explode("\n", $text) as $row){
            $val = explode(";", $row);
            $this->data[$val[0]] = array(
                'id' => $val[0],
                'name' => $val[1]
            );
        }
        uasort($this->data, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });
    }
}

class BestChange {
    private $URL = 'http://api.bestchange.ru/info.zip';
    private $FILENAME = 'info.zip';

    public $currencies = array();
    public $exchangers = array();
    public $rates = array();
    public $cities = array();
    public $res = false;
    public $exc = '';
    
    public $file_currencies = 'bm_cy.dat';
    public $file_exchangers = 'bm_exch.dat';
    public $file_rates = "bm_rates.dat";
    public $file_cities = 'bm_cities.dat';

    public function __construct($load = true,  $cache=true, $cache_seconds=15, $cache_path='./', $exchangers_reviews=false,
    $split_reviews=false) {
        $this->cache = $cache;
        $this->cache_seconds = $cache_seconds;
        $this->cache_path = $cache_path  .$this->FILENAME;
        $this->exchangers_reviews = $exchangers_reviews;
        $this->split_reviews = $split_reviews;
        if ($load){
            $this->load();
            $this->res = $this->data(0);
        }
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
    }
    public function save(){
        try{
            $result = file_put_contents($this->FILENAME, fopen($this->URL, 'r'));
            if ($result === false) {
                $this->exc = 'Не удалось сохранить файл!';
                return false;
            }
        } catch (Exception $e) {
            $this->exc = 'Не удалось загрузить файл!';
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
            if ($zip->locateName($this->file_rates) !== false){
                $text = iconv("windows-1251", "utf-8", $zip->getFromName($this->file_rates));
                $this->rates = new Rates( $text, $this->split_reviews);
            }
            if ($zip->locateName($this->file_currencies)  !== false){
                $text = iconv("windows-1251", "utf-8", $zip->getFromName($this->file_currencies));
                $this->currencies = new Currencies($text);
            }
            if ($zip->locateName($this->file_exchangers)  !== false){
                $text = iconv("windows-1251", "utf-8", $zip->getFromName($this->file_exchangers));
                $this->exchangers = new Exchangers($text);
            }
            if ($zip->locateName($this->file_cities)  !== false){
                $text = iconv("windows-1251", "utf-8", $zip->getFromName($this->file_cities));
                $this->cities = new Cities($text);
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
    public function cities(){
        return $this->cities;
    }
}

function get_data($rates, $currencies, $exchangers, $fr, $ton){
    global $count;
    global $s;
    global $table;
    foreach ($rates[$fr][$ton] as $exch_id => $entry) {
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
        $table .= '<a target="_blank" href="https://www.bestchange.ru/click.php?id=' . $exchangers[$exch_id]['id'] . '">' . $exchangers[$exch_id]['name'] . '</a> </div>
            <div class="info__row-value">';
        $table .= ($entry["rate"] < 1 ? 1 : $rate) . ' ' . $currencies[$fr]['name'] . '</div>
            <div class="info__row-value">';
        $table .= ($entry["rate"] < 1 ? $rate2 : 1) . ' ' . $currencies[$ton]['name'] . '</div>
            <div class="info__row-value">';
        $table .= $entry["reserve"] . '</div>
            <div class="info__row-value" style="width:70px">';
        $table .= str_replace('.', '/', $entry["reviews"]) . '</div></div>';
        $count++;
        $s += $entry["reserve"];
    }
}

?>