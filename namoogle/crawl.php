<?php
include("./classes/DomDocumentParser.php");
include("./config.php");

// WebPages that are already crawled will be stored here
$alreadyCrawled = array();
// Links still needed to be crawled
$crawling = array();
// Images still needed to be crawled
$alreadyFoundImages = array();

function linkExists($url) {
    // referencing global config variable for connecting to local database
    global $con;

    $query = $con->prepare("SELECT * FROM sites WHERE url = :url");

    $query->bindParam(":url", $url);
    $query->execute();

    return $query->rowCount() != 0;
}

function insertLink($url, $title, $description, $keywords) {
    // referencing global config variable for connecting to local database
    global $con;

    $query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
                            VALUES(:url, :title, :description, :keywords)");

    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();
}

function insertImage($url, $src, $title, $alt) {
    // referencing global config variable for connecting to local database
    global $con;

    $query = $con->prepare("INSERT INTO images(siteURL, imageURL, title, alt)
                            VALUES(:siteURL, :imageURL, :title, :alt)");

    $query->bindParam(":siteURL", $url);
    $query->bindParam(":imageURL", $src);
    $query->bindParam(":title", $title);
    $query->bindParam(":alt", $alt);

    return $query->execute();
}

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

/* Get Title, Description, Keyword Snippets */
function getDetails($url) {
    // referencing global config variable for inserting images to local database
    global $alreadyFoundImages;

    $parser = new DomDocumentParser($url);

    $titleArray = $parser->getTitleTags();

    if ($titleArray->item(0) == NULL || sizeof($titleArray) == 0) {
        return;
    }

    $title = $titleArray->item(0)->nodeValue;
    $title = str_replace("\n", "", $title); // replace all new lines with empty string

    // Don't crawl link and don't save to database
    if ($title == "") {
        return;
    }

    $description = "";
    $keywords = "";

    $metasArray = $parser->getMetaTags();

    foreach ($metasArray as $meta) {
        if ($meta->getAttribute("name") == "description") {
            $description = $meta->getAttribute("content");
        }

        if ($meta->getAttribute("name") == "keywords") {
            $description = $meta->getAttribute("content");
        }
    }

    $description = str_replace("\n", "", $description);
    $keywords = str_replace("\n", "", $keywords);

    // Check to see whether or not url already exist in database
    if (linkExists($url)) {
        echo "$url already exists<br>";
    } else if (insertLink($url, $title, $description, $keywords)) {
        echo "Successfully added $url<br>";
    } else {
        echo "ERROR: Failed to insert $url<br>";
    }

/*
    $imageArray = $parser->getImgTags();

    foreach ($imageArray as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        // ignore images that do not have title and alt
        if (!$title && !$alt) {
            continue;
        }

        // convert relative link to absolute link to find the image
        $src = createLink($src, $url);

        if (!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src;

            // Insert images into database
            echo "INSERT: " . insertImage($url, $src, $title, $alt);
        }
    }
*/
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

            getDetails($href);
            // Insert $href into database;
        }
        // else {
        //     return;
        // }

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
