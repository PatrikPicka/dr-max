<?php

if(!defined("ACCESS_CONTROL_ORIGIN")){
    define("ACCESS_CONTROL_ORIGIN", "127.0.0.1/dr-max");
}
$baseUrl = "https://news.ycombinator.com/news?p=";
$page = 1;
$data = [];

while (count($data) < 100){
    $data = array_merge($data, getData($baseUrl, $page));
    $page++;
}

$returnArray = [];

for ($x = 0; $x <= 99; $x++){
    $returnArray[$x] = $data[$x];
}

// echo "<pre>" . print_r($returnArray, true) . "</pre>";
jsonResponse($returnArray, 200, true);


/**
 * Function which gets the data from the page
 * @baseUrl = Base url to the news
 * @page = Page from which the function should get news 
 */
function getData($baseUrl, $page){
    $pathToData = $baseUrl . $page;

    $arrContextOptions = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
    ];

    $pageData = file_get_contents($pathToData, false, stream_context_create($arrContextOptions));

    $dataWithoutHeader = explode('<table border="0" cellpadding="0" cellspacing="0" class="itemlist">', $pageData)[1];

    $dataWithoutFooter = explode('<tr class="morespace" style="height:10px"></tr>', $dataWithoutHeader)[0];

    $fullHtmlTable = "<table>" . $dataWithoutFooter . "</tbody></table>";

    $htmlDom = new DOMDocument;
    @$htmlDom->loadHTML('<?xml encoding="utf-8" ?>' . $fullHtmlTable);
    $tags = $htmlDom->getElementsByTagName('a');
    $anchors = [];

    foreach ($tags as $anchorTag) {
        if ($anchorTag->nodeValue != NULL) {
            $anchors[] = [
                'href' => $anchorTag->getAttribute('href'),
                'title' => $anchorTag->nodeValue,
            ];
        }
    }


    $data = [];
    $counter = 0;
    foreach ($anchors as $anchorsArray){
        if($counter !== 99){
            if (strpos($anchorsArray["href"], "https://") !== false || strpos($anchorsArray["href"], "http://") !== false || strlen($anchorsArray["title"]) >= 33) {
                $data[$counter]["title"] = $anchorsArray["title"];
                $data[$counter]["external_link"] = $anchorsArray["href"];
            } elseif (strpos($anchorsArray["title"], "ago") !== false && strpos($anchorsArray["href"], "item?") !== false) {
                $dt = new DateTime($anchorsArray["title"]);
                $data[$counter]["internal_link"] = $anchorsArray["href"];
                $data[$counter]["date_time"] = $dt->format("Y-m-d H:h:s");
                $counter++;
            }
        }
    }
    return $data;
}

/**
 * Helper function for JSON response
 */
function jsonResponse($resp = "", $code, $success = null)
    {
        header("Acces-Control-Alow-Origin: " . ACCESS_CONTROL_ORIGIN);
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($code);
        if (isset($success)) {
            echo json_encode(["message" => $resp, "success" => $success]);
        } elseif ($resp !== "") {
            echo json_encode($resp);
        }
        exit;
    }