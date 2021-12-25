<?php

require_once('parse.php');

$api = new BestChange();
$post_exc = '';

if (isset($_POST['from']) & isset($_POST['to'])) {
    $fr = $_POST['from'];
    $ton = $_POST['to'];
} 
else if (isset($_POST['from']) || isset($_POST['to'])){
    $post_exc = 'Ошибка получения данных из формы!';
}
$from = $api->currencies[$fr];
$to = $api->currencies[$ton];
$count = 0;
$s = 0;
$table = '';
if ($api->res != false){
    if (count($api->rates[$fr][$ton]) > 0){
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
            $table .= '<a target="_blank" href="https://www.bestchange.ru/click.php?id="' . $exch_id . '>' . $api->exchangers[$exch_id] . '</a> </div>
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
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    
    <script src="" defer></script>

</head>
<body>

<article class="container">


    <div class="form-box">

        <form action="index.php" class="form" method="POST">
            <h3 class="form__title">Прасинг</h3>
            <div class="form__title-row">
                <?php
                    if ($api->res != false){
                        echo '<select name="from" class="form__input">';
                        foreach ($api->currencies as $cy_id => $cy_name)
                            echo('<option value="' . $cy_id . '">' . $cy_name . '</option>');
                        echo '</select>                  
               
                    <select name="to" class="form__input">';
                       
                        foreach ($api->currencies as $cy_id => $cy_name)
                            echo('<option value="' . $cy_id . '">' . $cy_name . '</option>');


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
        if ($post_exc != ''){
            if($count == 0)
                echo '<p class="total">Обменников по направлению '. $from .' - ' .$to  .' не найдено.</p>';
            else echo '<div class="form-table">
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
                    <p class="total">Количество обменников по направлению: ' . $count++ . '</p>
                    <p class="total">Суммарный резерв обменников: ' . $s . ' ' . $api->currencies[$ton] . '</p>
                </div>';
        }
        else echo '<p class="total">'. $post_exc .'</p>';
        ?>
    </div>

</article>

</body>

<script>
$(document).ready(function() {
    $('.form__input').select2();
});
</script>

</html>