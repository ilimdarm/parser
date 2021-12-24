<?php

class BestChange {
    private $URL = 'http://api.bestchange.ru/info.zip';
    private $FILENAME = 'info.zip';

    public $currencies = array();
    public $exchangers = array();
    public $rates = array();

    public function __construct() {
        $this->load();
        $this->data();
    }

    public function load() {
        try {
            if (time() - filemtime($this->FILENAME) > 86400)
            {
                $result = file_put_contents($this->FILENAME, fopen($this->URL, 'r'));
                if ($result === false) {
                    throw new Exception('Не удалось сохранить файл');
                }
            }
            
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
    public function data(){
        $fp = fopen($this->FILENAME, "r");
        fclose($fp);

        $zip = new ZipArchive;
        if (!$zip->open($this->FILENAME)) exit("error");
        foreach (explode("\n", $zip->getFromName("bm_cy.dat")) as $value) {
            $entry = explode(";", $value);
            $this->currencies[$entry[0]] = iconv("windows-1251", "utf-8", $entry[2]);
            asort($this->currencies, SORT_STRING);
        }
        foreach (explode("\n", $zip->getFromName("bm_exch.dat")) as $value) {
            $entry = explode(";", $value);
            $this->exchangers[$entry[0]] = iconv("windows-1251", "utf-8", $entry[1]);
        }
        foreach (explode("\n", $zip->getFromName("bm_rates.dat")) as $value) {
            $entry = explode(";", $value);
            $this->rates[$entry[0]][$entry[1]][$entry[2]] = array("rate" => $entry[3] / $entry[4], "reserve" => $entry[5], "reviews" => str_replace(".", "/", $entry[6]));
        }
        $zip->close();
    }
}

?>