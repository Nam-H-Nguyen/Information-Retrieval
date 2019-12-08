<?php
// Class gives us sites result
class ImageResultsProvider {

    private $con;

    // $con allows us to communicate with database
    public function __construct($con) {
        $this->con = $con;
    }

    public function getNumResults($query) {
        // Get all the images that are not broken with title or alt
        $search = $this->con->prepare("SELECT COUNT(*) as total
                                        FROM images
                                        WHERE (title LIKE :query
                                        OR alt LIKE :query)
                                        AND broken = 0");

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
                                        FROM images
                                        WHERE (title LIKE :query
                                        OR alt LIKE :query)
                                        AND broken = 0
                                        ORDER BY clicks DESC
                                        LIMIT :fromLimit, :pageSize");

        $searchQuery = "%".$query."%";
        $search->bindParam(":query", $searchQuery);
        $search->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $search->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $search->execute();

        $resultsHTML = "<div class='imageResults'>";

        $count = 0;
        // Fetch all results in between the DIV
        while ($row = $search->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $id = $row["id"];
            $imageUrl = $row["imageURL"];
            $siteUrl = $row["siteURL"];
            $title = $row["title"];
            $alt = $row["alt"];

            if ($title) {
                $displayText = $title;
            } else if ($alt) {
                $displayText = $title;
            } else {
                $displayText = $imageUrl;
            }

            // Keep track of image count
            $resultsHTML .= "<div class='gridItem image$count'>
                                <a href='$imageUrl' data-fancybox data-caption='$displayText'
                                data-siteurl='$siteUrl'>

                                    <script>
                                        $(document).ready(function(){
                                            loadImage(\"$imageUrl\", \"image$count\");
                                        });
                                    </script>

                                    <span class='details'>$displayText</span>
                                </a>
                            </div>";
        }

        $resultsHTML .= "</div>";
        return $resultsHTML;
    }
}

?>
