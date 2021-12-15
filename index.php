<?php
require('./lib/simple_html_dom.php');
function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
session_start();
$array = array('bitcoin', 'bitcoin-ln', 'bitcoin-bep20', 'wrapped-bitcoin', 'bitcoin-cash', 'bitcoin-sv', 'bitcoin-gold', 'ethereum', 'ethereum-bep20', 'wrapped-eth', 'ethereum-classic', 'litecoin', 'ripple', 'monero', 'dogecoin', 'polygon', 'dash', 'zcash', 'tether', 'tether-erc20', 'tether-trc20', 'tether-bep20', 'usd-coin', 'trueusd', 'pax-dollar', 'dai', 'busd', 'nem', 'augur', 'neo', 'eos', 'iota', 'lisk', 'cardano', 'stellar', 'tron', 'waves', 'omg', 'verge', 'zrx', 'binance-coin', 'icon', 'komodo', 'bittorrent', 'bat', 'ontology', 'qtum', 'chainlink', 'cosmos', 'tezos', 'polkadot', 'uniswap', 'ravencoin', 'solana', 'vechain', 'shiba-inu', 'algorand', 'maker', 'avalanche', 'wmz', 'wmr', 'wmp', 'wme', 'wmb', 'wmk', 'wmx', 'paymer', 'paymer-rub', 'perfectmoney-usd', 'perfectmoney-eur', 'perfectmoney-btc', 'pm-voucher', 'yoomoney', 'qiwi', 'qiwi-kzt', 'paypal-usd', 'paypal-rub', 'paypal-euro', 'paypal-gbp', 'advanced-cash', 'advanced-cash-rub', 'advanced-cash-euro', 'advanced-cash-uah', 'advanced-cash-kzt', 'advanced-cash-try', 'payeer', 'payeer-rub', 'payeer-euro', 'skrill', 'skrill-euro', 'idram', 'paxum', 'capitalist', 'capitalist-rub', 'neteller', 'neteller-euro', 'paysera', 'paysera-euro', 'ecopayz', 'nixmoney', 'nixmoney-euro', 'global24', 'velespay', 'epay', 'epay-euro', 'alipay', 'payoneer', 'mobile-wallet-rub', 'mobile-wallet-uah', 'trade-usd', 'trade-euro', 'exmo', 'exmo-rub', 'exmo-uah', 'exmo-btc', 'exmo-tether', 'binance-rub', 'binance-uah', 'cryptex', 'kuna', 'garantex', 'whitebit', 'sberbank', 'sberbank-code', 'alfaclick', 'alfabank-cashin-usd', 'alfabank-cash-in', 'tinkoff', 'tinkoff-cash-in', 'tinkoff-qr-codes', 'vtb', 'russtandart', 'avangard', 'psbank', 'gazprombank', 'kykyryza', 'raiffeisen-bank', 'rnkb', 'openbank', 'post-bank', 'rosselhozbank', 'rosbank', 'mts-bank', 'homecredit', 'privat24-usd', 'privat24-uah', 'raiffeisen-bank-uah', 'oschadbank', 'ukrsibbank', 'pumb', 'monobank', 'sberbank-uah', 'alfabank-uah', 'belarusbank', 'halykbank', 'sberbank-kzt', 'fortebank', 'kaspi-bank', 'jysan-bank', 'visa-mastercard-usd', 'visa-mastercard-rub', 'visa-mastercard-euro', 'visa-mastercard-uah', 'visa-mastercard-byr', 'visa-mastercard-kzt', 'visa-mastercard-sek', 'visa-mastercard-pln', 'visa-mastercard-mdl', 'visa-mastercard-amd', 'visa-mastercard-gbp', 'visa-mastercard-cny', 'visa-mastercard-try', 'visa-mastercard-kgs', 'mir', 'unionpay', 'uscard', 'humo', 'wire-usd', 'wire-rub', 'wire-euro', 'wire-uah', 'wire-byn', 'wire-kzt', 'wire-gel', 'wire-gbp', 'wire-cny', 'wire-try', 'wire-pln', 'wire-thb', 'wire-inr', 'wire-ngn', 'wire-idr', 'sepa', 'erip', 'sbp', 'settlement-rub', 'revolut-usd', 'revolut-euro', 'wu', 'wu-euro', 'wu-rub', 'moneygram', 'moneygram-euro', 'contact-usd', 'contact', 'golden-crown-usd', 'golden-crown', 'uni', 'uni-rub', 'ria-usd', 'ria-euro', 'dollar-cash', 'ruble-cash', 'euro-cash', 'hryvnia-cash', 'belarus-cash', 'tenge-cash', 'pound-cash', 'dirham');
$type = array(93, 131, 43, 73, 172, 137, 184, 139, 212, 218, 160, 99, 161, 149, 115, 138, 140, 162, 163, 36, 10, 208, 23, 24, 189, 203, 206, 173, 174, 177, 178, 179, 180, 181, 182, 185, 133, 48, 124, 168, 19, 104, 134, 27, 61, 135, 26, 197, 198, 175, 201, 202, 205, 82, 8, 210, 216, 213, 217, 1, 2, 76, 3, 18, 47, 96, 87, 144, 40, 41, 128, 156, 6, 63, 127, 45, 98, 80, 164, 88, 121, 120, 142, 33, 20, 108, 117, 122, 44, 123, 11, 74, 145, 85, 72, 136, 152, 35, 200, 109, 125, 112, 32, 154, 97, 165, 103, 49, 12, 148, 153, 129, 130, 169, 186, 50, 126, 110, 190, 28, 16, 214, 42, 209, 52, 143, 62, 105, 46, 147, 51, 64, 79, 53, 95, 57, 157, 132, 176, 170, 34, 195, 191, 215, 55, 56, 158, 68, 22, 118, 84, 196, 37, 4, 90, 114, 75, 66, 207, 58, 59, 65, 60, 54, 111, 155, 38, 194, 5, 146, 30, 83, 25, 17, 100, 9, 199, 69, 71, 70, 102, 31, 113, 219, 81, 166, 39, 29, 167, 119, 188, 183, 171, 187, 21, 159, 192, 193, 67, 15, 7, 78, 77, 101, 106, 116, 107, 86, 14, 150, 151, 89, 91, 141, 92, 13, 94, 204, 211);
$fr = $_POST['from'];
$ton = $_POST['to'];
$from = $array[array_search($fr, $type)]; 
$to = $array[array_search($ton, $type)]; 
if ($_POST["cache"] == "check")
    $_SESSION["cache"] = true;
else $_SESSION["cache"] = false;


$url = 'https://www.bestchange.ru/'. $fr. '-to-'. $ton. '.html';
if (!isset($_POST['from']) & !isset($_POST['to']))
    $url =  'https://www.bestchange.ru/';

$html = new simple_html_dom();

$load = file_get_contents_curl( $url );
$html= str_get_html( $load );

$bgw = $html->find('optgroup option');
// $bgw[0]->class = "form__input";

// echo $bgw[0];

$names = $html->find('.pc');
$name = $html->find('.pa a');
$prc = $html->find('.bi');
$fs = $html->find('.fs');
$fm1 = $html->find('.fm1');
$fm2 = $html->find('.fm2');

$arp = $html->find('.arp');
$rwl = $html->find('.rwl');
$rwr = $html->find('.rwr');
$bt = $html->find('.m-hint span .bt');
$data = '';

$table = '';
for ($i = 0; $i < count($names); $i++){
    $row = '';
    if ($i % 2 == 1){
        $row = ' row';
    }
    $table .= '<div class="table__info-row'. $row .'">
    <div class="info__row-value">';
    $table.= $names[$i] .'</div>
        <div class="info__row-value">';
    $table.= $prc[2*$i] .'</div>
        <div class="info__row-value">';
    $table.= $prc[2*$i+1] .'</div>
        <div class="info__row-value">';
    $table.= $arp[$i+1] .'</div>
        <div class="info__row-value" style="width:70px">';
    $table.= $rwl[$i]. '/'. $rwr[$i] .'</div></div>';
    $data .= $fr .';' .$ton  .';' .mb_substr($name[$i]->href, stripos($name[$i]->href, '=')+1, stripos($name[$i]->href, '&') - stripos($name[$i]->href, '=')-1) .';';
    $data .= mb_substr($fs[$i], stripos($fs[$i], '>')+1, strpos($fs[$i], '<', strpos($fs[$i], '<') + 1) - stripos($fs[$i], '>')-2) .';';
    $data .= mb_substr($prc[2*$i+1], stripos($prc[2*$i+1], '>')+1, strpos($prc[2*$i+1], '<', strpos($prc[2*$i+1], '<') + 1) - stripos($prc[2*$i+1], '>')-2) .';';
    $data .= $arp[$i+1]->innertext .';' . $rwl[$i]->innertext. '.'. $rwr[$i]->innertext . ';' . mb_substr($fm1[0]->innertext, 1, strlen($fm1[0]->innertext)-2) .';' . mb_substr($fm2[0]->innertext, 1, strlen($fm2[0]->innertext)-2) ."\n";

}

$filename = "bm_rates.dat";
file_put_contents($filename, $data, FILE_APPEND);
if ($_SESSION["cache"]){
    // sleep(10);
    header("Refresh:10");
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

            <form action="index.php" class="form form_signin"  method="POST">
                <h3 class="form__title">Прасинг</h3>
                    <div class="form__title-row">
                        <select name="from" class="form__input">
                            <?php
                                for ($i = 0; $i < count($bgw); $i++)
                                    echo $bgw[$i];
                            ?>
                            
                        </select>
                    
                        <select name="to" class="form__input">
                            <?php
                                for ($i = 0; $i < count($bgw); $i++)
                                    echo $bgw[$i];
                            ?>
                            
                        </select>   
                        <label class="form__input">
                            <input type = "checkbox" name = "cache" value = "check">
                            <span>Кэширование</span>
                        </label>     
                        <button class="form__btn" >Поиск</button>

                    </div>
            </form>
            
            <?php
            if(count($names) == 0)
                echo '<p class="total">Обменников не найдено</p>';
            else echo '<div class="form-table">
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
                <p class="total">Количество обменников по направлению: <?php echo $bt[0]; ?></p>
                <p class="total">Суммарный резерв обменников: <?php echo $bt[1]; ?></p>
            </div>';
            ?>
        </div>

    </article>

</body>


</html>