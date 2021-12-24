<?php

class BestChange {
    private $URL = 'http://api.bestchange.ru/info.zip';
    private $FILENAME = 'info.zip';

    public $currencies = array();
    public $exchangers = array();
    public $rates = array();

    public function __construct() {
        $this->load();
    }

    public function load() {
        try {
            $result = file_put_contents($this->FILENAME, fopen($this->URL, 'r'));
            if ($result === false) {
                throw new Exception('Не удалось сохранить файл');
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}

?>