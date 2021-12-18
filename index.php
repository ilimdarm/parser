<?php
// set_time_limit(60);//may take time for downloading
// ini_set("memory_limit", "512M");//may require more memory


$filename = "info.zip";

$fp = fopen($filename, "r");
fclose($fp);

$zip = new ZipArchive;
if (!$zip->open($filename)) exit("error");
$currencies = array();
foreach (explode("\n", $zip->getFromName("bm_cy.dat")) as $value) {
  $entry = explode(";", $value);
  $currencies[$entry[0]] = iconv("windows-1251", "utf-8", $entry[2]);
}
$exchangers = array();
foreach (explode("\n", $zip->getFromName("bm_exch.dat")) as $value) {
  $entry = explode(";", $value);
  $exchangers[$entry[0]] = iconv("windows-1251", "utf-8", $entry[1]);
}
$rates = array();
foreach (explode("\n", $zip->getFromName("bm_rates.dat")) as $value) {
  $entry = explode(";", $value);
  $rates[$entry[0]][$entry[1]][$entry[2]] = array("rate"=>$entry[3] / $entry[4], "reserve"=>$entry[5], "reviews"=>str_replace(".", "/", $entry[6]));
}
$zip->close();
// unlink($filename);
if (isset($_POST['from']) || isset($_POST['to'])){
    $fr = $_POST['from'];
    $ton = $_POST['to'];
}
else{
    $fr = 92;
    $ton = 139;
}
$from = $currencies[$fr]; 
$to = $currencies[$ton]; 

uasort($rates[$fr][$ton], function ($a, $b) {
    if ($a["rate"] > $b["rate"]) return 1;
    if ($a["rate"] < $b["rate"]) return -1;
    return(0);
});



$count = 0;
$s = 0;
$table = '';
foreach ($rates[$fr][$ton] as $exch_id=>$entry) {
    $row = '';
    if ($count % 2 == 1){
        $row = ' row';
    }
    $reverse = strrev(round($entry["rate"], 0));
    $rate = strrev(chunk_split($reverse, 3, ' '));
    $table .= '<div class="table__info-row'. $row .'">
    <div class="info__row-value">';
    $table.= '<a target="_blank" href="https://www.bestchange.ru/click.php?id="' . $exch_id . '>' . $exchangers[$exch_id] . '</a> </div>
        <div class="info__row-value">';
    $table.=  ($entry["rate"] < 1 ? 1 : $rate) .' ' . $currencies[$fr] .'</div>
        <div class="info__row-value">';
    $table.= ($entry["rate"] < 1 ? 1 / $entry["rate"] : 1) .' ' . $currencies[$ton] .'</div>
        <div class="info__row-value">';
    $table.= $entry["reserve"] .'</div>
        <div class="info__row-value" style="width:70px">';
    $table.= $entry["reviews"] .'</div></div>';
    $count++;
    $s += $entry["reserve"];

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
    <script src="" defer></script>
    
</head>
<body>

    <article class="container">

       
        <div class="form-box">

            <form action="index.php" class="form"  method="POST">
                <h3 class="form__title">Прасинг</h3>
                    <div class="form__title-row">
                        <select name="from" class="form__input">
                            
                            <?php
                                // ksort($currencies);
                                foreach ($currencies as $cy_id=>$cy_name) 
                                    echo('<option value="'. $cy_id .'">' .$cy_name. '</option>');
                            ?>
                            
                        </select>
                    
                        <select name="to" class="form__input">
                           
                            <?php
                                // ksort($currencies);
                                foreach ($currencies as $cy_id=>$cy_name) 
                                    echo('<option value="'. $cy_id .'">' .$cy_name. '</option>');
                            ?>
                            
                            
                        </select>   
                        <!-- <label class="form__input">
                            <input type = "checkbox" name = "cache" value = "check">
                            <span>Кэширование</span>
                        </label>      -->
                        <button class="form__btn" >Поиск</button>

                    </div>
            </form>
            
            <?php
            // if(count($names) == 0)
                // echo '<p class="total">Обменников не найдено</p>';
            echo '<div class="form-table">
                <h3 class="form__title">Результат</h3>
                <div class="table">
                    <div class="table__info-row table__main-row">
                        <div class="info__row-value">Имя обменника</div>
                        <div class="info__row-value">Курс отдаю</div>
                        <div class="info__row-value">Курс получаю</div>
                        <div class="info__row-value">Резерв</div>
                        <div class="info__row-value" style="width:70px">Отзывы</div>
                    </div>'
                        .$table
                .'</div>
                <p class="total">Количество обменников по направлению: '  . $count++ .'</p>
                <p class="total">Суммарный резерв обменников: '. $s . ' '.  $currencies[$ton] .'</p>
            </div>';
            ?>
        </div>

    </article>

</body>


</html>