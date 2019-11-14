<?php
	include("./config.php");
	include("./classes/SiteResultsProvider.php");
	// setcookie('samesite-test', '1', 0, '/; samesite=strict');

    // Gets the query value from the URL (when user type in a query)
    // Check to see if the query is set or not (exists or not)
	if(isset($_GET["query"])) {
		$query = $_GET["query"];
	}
	else {
        // Display this message if there are no queries
		exit("You must enter a search query");
	}

	// Type defaults to sites otherwise set to type
	$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
	// Specify page or Set default to page 1
	$page = isset($_GET["page"]) ? $_GET["page"] : 1;

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

							<input class="searchBox" type="text" name="query" value="<?php echo $query?>">
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
				$pageSize = 20;

				$numResults = $resultsProvider->getNumResults($query);
				echo "<p class='resultsCount'>$numResults results found</p>";
				echo $resultsProvider->getResultsHTML($page, $pageSize, $query);
			?>
		</div>

		<div class="paginationContainer">
			<div class="pageButtons">
				<div class="pageNumberContainer">
					<img src="https://drive.google.com/uc?export=view&id=1Lxu_8gNIDjEUjtGv0ZnCYBDxbej1rLdw">
				</div>

				<?php

				// Show only 10 pages
				$pagesToShow = 10;

				// Number of search results divided by page size to get num pages needed
				// to be shown from start to end
				$numPages = ceil($numResults / $pageSize);

				// If user clicks nearing towards the end, how many pages left to be shown
				$pagesLeft = min($pagesToShow, $numPages);

				// If click page 10, will show 5 pages to the left and 5 pages to the right
				$currentPage = $page - floor($pagesToShow / 2);

				// Case: current page selected, above and below current selection won't go
				// below 1
				if ($currentPage < 1) {
					$currentPage = 1;
				}
				
				while ($pagesLeft != 0) {
					// Current page is not clickable
					if ($currentPage == $page) {
						echo "<div class='pageNumberContainer'>
								<img src='https://drive.google.com/uc?export=view&id=1KSbYectB7FhIRijdv9OwI4oXlXNhn3TQ'>
								<span class='pageNumber'>$currentPage</span>
							  </div>";
					} else {
					// Surround numbers with clickable anchor tags to query search page number
						echo "<div class='pageNumberContainer'>
								<a href='search.php?query=$query&type=$type&page=$currentPage'>
									<img src='https://drive.google.com/uc?export=view&id=1xlT7PGYX5L22chm8ZxCmSE0KF0BUdLxP'>
									<span class='pageNumber'>$currentPage</span>
								</a>
							  </div>";

					}
					$currentPage++;
					$pagesLeft--;
				}

				?>

				<div class="pageButtons">
					<img src="https://drive.google.com/uc?export=view&id=17v9VuKcGguCwJp4ovW0xgjfgzdgy4FHv">
				</div>
			</div>
		</div>
	</div>

</body>
</html>
