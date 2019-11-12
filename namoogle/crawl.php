<?php
include("./classes/DomDocumentParser.php");

function createLink($src, $url) {
    echo "SRC: $src<br>";
    echo "URL $url<br>";
}

function followLinks($url) {
    $parser = new DomDocumentParser($url);
    $linkList = $parser->getLinks();

    // Loop through to get links
    foreach($linkList as $link) {
        $href = $link->getAttribute("href");

        // Case a tag contains '#'
        if (strpos($href, "#") !== false) {
            continue;
        } else if (substr($href, 0, 11) == "javascript:") {
            continue;
        }

        createLink($href, $url);
        // echo $href . "<br>";
    }
}

$startUrl = "https://www.foodnetwork.com";
followLinks($startUrl);

?>
