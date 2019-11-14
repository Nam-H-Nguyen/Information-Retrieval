<?php
	include("./config.php");
	include("./classes/SiteResultsProvider.php");

    // Gets the query value from the URL (when user type in a query)
    // Check to see if the query is set or not (exists or not)
	if(isset($_GET["query"])) {
		$query = $_GET["query"];
	}
	else {
        // Display this message if there are no queries
		exit("You must enter a search query");
	}

	$type = isset($_GET["type"]) ? $_GET["type"] : "sites";



?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Welcome to Namoogle Search Engine!</title>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css"/>
    </head>
<body>

	<div class="wrapper">

		<div class="header">


			<div class="headerContent">

				<div class="logoContainer">
					<a href="index.php">
                        <img src="https://drive.google.com/uc?export=view&id=1cWtKYOL6eOg4_A15a9R3zRFGl2LSJa1A" alt="Namoogle">
					</a>
				</div>

				<div class="searchContainer">

					<form action="search.php" method="GET">

						<div class="searchBarContainer">

							<input class="searchBox" type="text" name="query">
							<button class="searchButton">
								<img src="https://drive.google.com/uc?export=view&id=1WUN4N6h-gLJczwUDjb1fMVHxQ48e6tPt" alt="Search_Icon">
							</button>
						</div>

					</form>

				</div>

			</div>


			<div class="tabsContainer">

				<ul class="tabList">

					<li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
                        <!-- Use single quotes to embed in php & set url based on query -->
						<a href='<?php echo "search.php?query=$query&type=sites"; ?>'>
							Sites
						</a>
					</li>

					<li class="<?php echo $type == 'images' ? 'active' : '' ?>">
						<a href='<?php echo "search.php?query=$query&type=images"; ?>'>
							Images
						</a>
					</li>

				</ul>


			</div>



		</div>

		<div class="mainResultsSection">
			<!-- Printing out the number of search results -->
			<?php
				$resultsProvider = new SiteResultsProvider($con);
				$numResults = $resultsProvider->getNumResults($query);
				echo "<p class='resultsCount'>$numResults results found</p>";
				echo $resultsProvider->getResultsHTML(1, 20, $query);
			?>
		</div>
	</div>

</body>
</html>
