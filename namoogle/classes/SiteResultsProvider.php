<?php
// Class gives us sites result
class SiteResultsProvider {

    private $con;

    // $con allows us to communicate with database
    public function __construct($con) {
        $this->con = $con;
    }

    public function getNumResults($query) {
        // Return count to a new column called total
        // SQL "LIKE" yields %query% meaning if query = chicken alfredo
        // % will get any sites that has any words before and after chicken alfredo
        $search = $this->con->prepare("SELECT COUNT(*) as total
                                        FROM sites WHERE title LIKE :query
                                        OR url LIKE :query
                                        OR keywords LIKE :query
                                        OR description LIKE :query");

        $searchQuery = "%".$query."%";
        $search->bindParam(":query", $searchQuery);
        $search->execute();

        // Store results in key value array
        $row = $search->fetch(PDO::FETCH_ASSOC);

        return $row["total"];
    }

    public function getResultsHTML($page, $pageSize, $query) {

        // page 1 : (1 - 1) * 20 = 0
        // page 2 : (2 - 1) * 20 = 20
        // page 3 : (3 - 1) * 20 = 40
        $fromLimit = ($page - 1) * $pageSize;

        $search = $this->con->prepare("SELECT *
                                        FROM sites WHERE title LIKE :query
                                        OR url LIKE :query
                                        OR keywords LIKE :query
                                        OR description LIKE :query
                                        ORDER BY clicks DESC
                                        LIMIT :fromLimit, :pageSize");

        $searchQuery = "%".$query."%";
        $search->bindParam(":query", $searchQuery);
        $search->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $search->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $search->execute();

        $resultsHTML = "<div class='siteResults'>";

        // Fetch all results in between the DIV
        while ($row = $search->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
            $url = $row["url"];
            $title = $row["title"];
            $description = $row["description"];

            $title = $this->trimField($title, 55);
            $description = $this->trimField($description, 230);

            $resultsHTML .= "<div class='resultContainer'>
                                <h3 class='title'>
                                    <a class='result' href='$url' data-linkId='$id'>
                                        $title
                                    </a>
                                </h3>
                                <span class='url'>$url</span>
                                <span class='description'>$description</span>
                            </div>";
        }

        $resultsHTML .= "</div>";
        return $resultsHTML;
    }

    private function trimField($string, $characterLimit) {
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }
}

?>
