<?php
include("../config.php");   // get access to database

// Update the database from clicks
if (isset($_POST["imageURL"])) {
    // Increase click count by 1
    $query = $con->prepare("UPDATE images SET clicks = clicks + 1 WHERE imageURL=:imageURL");
    $query->bindParam(":imageURL", $_POST["imageURL"]);

    $query->execute();
} else {
    echo "No image URL passed to page";
}
?>
