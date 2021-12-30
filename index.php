<?php

require_once('parse.php');

$api = new BestChange($cache_seconds=3600, $exchangers_reviews=true, $split_reviews=true);
$post_exc = '';

$currencies = $api->currencies()->get();
$exchangers = $api->exchangers()->get();
$rates = $api->rates()->get();
if (isset($_POST['from']) && isset($_POST['to'])) { 
    $fr = $_POST['from'];
    $ton = $_POST['to'];
} 
else if (!isset($_POST['from']) || !isset($_POST['to'])){
    $fr = 1;
    $i = 0;
    $ton = array();
    foreach ($rates[$fr] as $id => $n){
        $ton[$i] = $id;
        $i++;
    }
}

$count = 0;
$s = 0;
$from = $currencies[$fr]['name'];
if (!is_array($ton))
    $to = $currencies[$ton]['name'];    
else $to = 'Все направления';
$table = '';

if ($api->res != false){
    if (!is_array($ton)){
        if (array_key_exists($fr, $currencies) && array_key_exists($ton, $currencies)){
            if (count($rates[$fr][$ton]) > 0){
                get_data($rates, $currencies, $exchangers, $fr, $ton);
            }
        }
        else $post_exc = 'Ошибка! Валюты с таким Ид не существует';
    }
    else
        for ($i = 0; $i < count($ton); $i++){
            get_data($rates, $currencies, $exchangers, $fr, $ton[$i]);
        }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parser</title>
    <link rel="stylesheet" href="./index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
</head>
<body>

<article class="container">


    <div class="form-box">

        <form action="index.php" class="form" method="POST">
            <h3 class="form__title">Парсинг</h3>
            <div class="form__title-row">
                <?php
                    if ($api->res != false){
                        echo '<select name="from" class="form__input">';
                        foreach ($currencies as $cy_id => $cy_name)
                            echo '<option value="' . $cy_id . '">' . $cy_name['name'] . '</option>';
                        echo '</select>
                    <select name="to" class="form__input">';
                       
                        foreach ($currencies as $cy_id => $cy_name)
                            echo '<option value="' . $cy_id . '">' . $cy_name['name'] . '</option>';
                        echo '</select>';
                    }
                    else if ($api->exc != '') 
                        echo '<p class="total">'. $aoi->exc .'</p>';
                    else  echo '<p class="total">Возникла неизвестная ошибка.</p>';
                ?>
                <!-- <label class="form__input">
                    <input type = "checkbox" name = "cache" value = "check">
                    <span>Кэширование</span>
                </label>      -->
                <button class="form__btn">Поиск</button>

            </div>
        </form>

        <?php
        if ($post_exc == ''){
            if($count == 0)
                {
                    echo $post_exc;
                    echo '<p class="total">Обменников по направлению '. $from .' - ' .$to  .' не найдено.</p>';
                }    
            else {
                echo '<div class="form-table">
                        <h3 class="form__title">Обменники: '. $from .' - ' .$to  .' </h3>
                        <div class="table">
                            <div class="table__info-row table__main-row">
                                <div class="info__row-value">Имя обменника</div> 
                                <div class="info__row-value">Курс отдаю</div>
                                <div class="info__row-value">Курс получаю</div>
                                <div class="info__row-value">Резерв</div>
                                <div class="info__row-value" style="width:70px">Отзывы</div>
                            </div>'
                    . $table
                    . '</div>
                        <p class="total">Количество обменников по направлению: ' . $count . '</p>';
                        if (!is_array($ton))
                        echo '<p class="total">Суммарный резерв обменников: ' . $s . ' ' . $currencies[$ton]['name'] . '</p></div>';
                    else echo '</div>';
                    
                
            }
        }
        else echo '<p class="total">'. $post_exc .'</p>';
        ?>
    </div>

</article>

</body>

<script>
$(document).ready(function() {
    $('.form__input').select2();
    <?php
    if (isset($fr) && isset($ton) && !is_array($ton)){
        if (array_key_exists($fr, $currencies) && array_key_exists($ton, $currencies)){?>
            $('.select2-selection__rendered:first').text("<?=$from?>");
            $('.select2-selection__rendered:last').text("<?=$to?>");
            $('.form__input:first').val("<?=$fr?>");
            $('.form__input:last').val("<?=$ton?>");
            <?php }  
        }
            else{ ?>
            $('.select2-selection__rendered:first').text("<?=$currencies[168]['name']?>");
            $('.select2-selection__rendered:last').text("<?=$currencies[168]['name']?>");
            $('.form__input').val("<?=168?>");
        <?php } ?>
});
</script>

</html>