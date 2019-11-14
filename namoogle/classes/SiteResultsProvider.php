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
        $search = $this->con->prepare("SELECT *
                                        FROM sites WHERE title LIKE :query
                                        OR url LIKE :query
                                        OR keywords LIKE :query
                                        OR description LIKE :query
                                        ORDER BY clicks DESC");

        $searchQuery = "%".$query."%";
        $search->bindParam(":query", $searchQuery);
        $search->execute();

        $resultsHTML = "<div class='siteResults'>";

        // Fetch all results in between the DIV
        while ($row = $search->fetch(PDO::FETCH_ASSOC)) {
            $title = $row["title"];
            $resultsHTML .= "$title <br>";

        }

        $resultsHTML .= "</div>";
        return $resultsHTML;
    }
}

?>
