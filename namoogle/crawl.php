<?php
include("./classes/DomDocumentParser.php");

function createLink($src, $url) {
    /*
    Edge Cases for url links
    scheme: http https
    host: www.apple.com

    #
    javascript:

    //www.apple.com
    /contact/company.php
    ./contact/company.php
    ../contact/company.php
    contact/company.php
    */
   $scheme = parse_url($url)["scheme"]; // http https
   $host = parse_url($url)["host"]; // www.apple.com

   if (substr($src, 0, 2) == "//") {
       // scheme = http https
       $src = $scheme . ":" . $src;
   } else if (substr($src, 0, 1) == "/") {
       $src = $scheme . "://" . $host . $src;
   } else if (substr($src, 0, 2) == "./") {
       $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
   } else if (substr($src, 0, 3) == "../" ||
              (substr($src, 0, 4) != "http" && substr($src, 0, 5) != "https")) {
       $src = $scheme . "://" . $host . "/" . $src;
   }

   return $src;
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

        $href = createLink($href, $url);

        echo $href . "<br>";
    }
}

$startUrl = "https://www.foodnetwork.com";
followLinks($startUrl);

?>
