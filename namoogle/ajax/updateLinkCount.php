<?php
include("../config.php");   // get access to database

// Update the database from clicks
if (isset($_POST["linkId"])) {
    // Increase click count by 1
    $query = $con->prepare("UPDATE sites SET clicks = clicks + 1 WHERE id=:id");
    $query->bindParam(":id", $_POST["linkId"]);

    $query->execute();
} else {
    echo "No link passed to page";
}
?>
