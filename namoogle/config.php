<?php
/* Connect to local PHP admin database */
ob_start();

try {
    // PHP data object
    $con = new PDO("mysql:dbname=namoogle;host=localhost", "root", "");
    // If there are any error, it would stop executing or give silent warning
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>
