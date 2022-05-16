<?php

if(!defined("ACCESS_CONTROL_ORIGIN")){
    define("ACCESS_CONTROL_ORIGIN", "127.0.0.1/dr-max");
}
$baseUrl = "https://news.ycombinator.com/news?p=";
$page = 1;
$data = "";

while($page <= 4){
    $data .= getData($baseUrl, $page);
    $page++;
}

jsonResponse($data, 200);


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
    $fullHtmlTable = str_replace('<tr class="spacer" style="height:5px"></tr>', '', $fullHtmlTable);
    return $fullHtmlTable;
}

/**
 * Helper function for JSON response
 */
function jsonResponse($resp = "", $code, $success = null)
    {
        header("Acces-Control-Alow-Origin: " . ACCESS_CONTROL_ORIGIN);
        header("Content-Type: text/html; charset=UTF-8");
        http_response_code($code);
        if (isset($success)) {
            echo json_encode(["message" => $resp, "success" => $success]);
        } elseif ($resp !== "") {
            echo $resp;
        }
        exit;
    }