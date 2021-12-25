<?php

class BestChange {
    private $URL = 'http://api.bestchange.ru/info.zip';
    private $FILENAME = 'info.zip';

    public $currencies = array();
    public $exchangers = array();
    public $rates = array();
    public $res = true;
    public $exc = '';

    public function __construct() {
        $this->load();
        $this->res = $this->data(0);
    }

    public function load() {
        try {
            if (file_exists($this->FILENAME) == false)
            {
                if (time() - filemtime($this->FILENAME) > 86400)
                {
                    $result = file_put_contents($this->FILENAME, fopen($this->URL, 'r'));
                    if ($result === false) {
                        $this->exc = 'Не удалось сохранить файл!';
                    }
                }
            }
            
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    public function data($count){
        $zip = new ZipArchive;
        if ($count < 3){     
            if (!$zip->open($this->FILENAME))
            {
                $this->load();
                $count++;
                return $this->data($count);
            }
        }
        else
        {
            return false;
            $this->exc = 'Не удалось открыть файл!';
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
            return false;
            $this->exc = 'Не удалось спарсить данные!';
        }
        return true;
    }
}

?>