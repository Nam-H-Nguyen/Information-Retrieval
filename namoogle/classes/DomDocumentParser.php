<?php

class DomDocumentParser {

    // To remember values when HTML is loaded
    private $doc;

    // Constructor when create object of this class
    // Contains the WebPage loaded
    public function __construct($url) {

        // Get options when requesting WebPage with the User-Agent as header of address
        $options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: namoogleBot/0.1\n"));
        // Use context when making requests
        $context = stream_context_create($options);
        // Perform actions on WebPages - Dom DomDocuments
        $this->doc = new DomDocument();
        // File Get Contents is the webpage itself
        // @Suppress new HTML5 warnings
        @$this->doc->loadHTML(file_get_contents($url, false, $context));
    }

    public function getLinks() {
        // Built in php function to get a tags
        return $this->doc->getElementsByTagName("a");
    }
}

?>
