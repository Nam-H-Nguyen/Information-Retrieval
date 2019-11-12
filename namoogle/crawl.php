<?php
include("./classes/DomDocumentParser.php");

// WebPages that are already crawled will be stored here
$alreadyCrawled = array();
// Links still needed to be crawled
$crawling = array();

/* Parse url and regenerate valid url links from edge cases */
function createLink($src, $url) {

   $scheme = parse_url($url)["scheme"]; // http https
   $host = parse_url($url)["host"]; // www.apple.com

    // Case: "//www.apple.com"
   if (substr($src, 0, 2) == "//") {
       $src = $scheme . ":" . $src;
    // Case: "/contact/company.php"
   } else if (substr($src, 0, 1) == "/") {
       $src = $scheme . "://" . $host . $src;
    // Case: "./contact/company.php"
   } else if (substr($src, 0, 2) == "./") {
       $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    // Case: "../contact/company.php or contact/company.php"
   } else if (substr($src, 0, 3) == "../" ||
              (substr($src, 0, 4) != "http" && substr($src, 0, 5) != "https")) {
       $src = $scheme . "://" . $host . "/" . $src;
   }

   return $src;
}

/* Parse WebPages from input URL for a tags and return valid URLs */
function followLinks($url) {
    $parser = new DomDocumentParser($url);
    $linkList = $parser->getLinks();

    // reference to global variables
    global $alreadyCrawled;
    global $crawling;

    // Loop through to get links
    foreach($linkList as $link) {
        $href = $link->getAttribute("href");

        // Case a tag contains "#" or "javascript:"
        if (strpos($href, "#") !== false) {
            continue;
        } else if (substr($href, 0, 11) == "javascript:") {
            continue;
        }

        $href = createLink($href, $url);

        // if $href is not in already crawled, add the $href into the array
        if(!in_array($href, $alreadyCrawled)) {
            // next element in array will equal to $href
            $alreadyCrawled[] = $href;
            $crawling[] = $href;

            // Insert $href into database;
        }

        echo $href . "<br>";
    }

    // Once url is already crawled, shift to the next one to be crawled
    array_shift($crawling);

    foreach ($crawling as $site) {
        // recursively call followLinks
        followLinks($site);
    }
}

$startUrl = "https://www.foodnetwork.com";
followLinks($startUrl);

?>
