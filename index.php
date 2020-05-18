<?php
$html = file_get_contents('http://www.sismologia.cl/links/ultimos_sismos.html');

$DOM = new DOMDocument();
$DOM->loadHTML($html);

$Header = $DOM->getElementsByTagName('th');
$Detail = $DOM->getElementsByTagName('td');

$registros = $DOM->getElementsByTagName('tr');

//#Get header name of the table
foreach($Header as $NodeHeader) 
{
    $aDataTableHeaderHTML[] = trim($NodeHeader->textContent);
}

//#Get row data/detail table without header name as key
$i = 0;
$j = 0;
foreach($Detail as $sNodeDetail) 
{
    $aDataTableDetailHTML[$j][] = trim($sNodeDetail->textContent);
    $i = $i + 1;
    $j = $i % count($aDataTableHeaderHTML) == 0 ? $j + 1 : $j;
}

$i = 0;
foreach ($aDataTableDetailHTML as $event) {
    foreach ($event as $key => $value) {
        $nombre = $input = preg_replace("/[^a-zA-Z]+/", "", $aDataTableHeaderHTML[$key]);
        $sisObj[$i][$nombre] = $value;
        if ($nombre == "FechaLocal" || $nombre == "FechaUTC") {
            $timezone=null;
            if ($nombre == "FechaLocal") {
                $timezone = new DateTimeZone("America/Santiago");
            }
            $date = DateTime::createFromFormat("Y/m/d H:i:s", $value, $timezone)->format(DateTime::ATOM);
            $sisObj[$i][$nombre] = $date;
        }
        if ($nombre == "Latitud" || $nombre == "Longitud" || $nombre == "ProfundidadKm") {
            $sisObj[$i][$nombre] = (float)$value;
        }
    }
    $i++;
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($sisObj);
?>