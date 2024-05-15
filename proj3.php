<!DOCTYPE html>
<!--
/************************************************************/
/* Author: Rachel Alden
/* Major: Computer Science
/* Creation Date: March 5th, 2024
/* Due Date: March 6th, 2024
/* Course: CSC 242
/* Professor Name: Dr. Carelli
/* Assignment: #3
/* Filename: proj3.php
/* Purpose: Collects a user request from an HTML form, 
/* passing that request to a server, and displaying the
/* requested info in a browser.
/************************************************************/
-->
<html lang="en">
<head>
  <title>Books</title>
  <link rel = "stylesheet" type = "text/css"
       href = "style.css">
</head>
<body>

<h1>Book Information</h1>

<form action="proj3.php" method="get">
  <select name="type">
    <option value="Category">Category</option>
    <option value="Author">Author</option>
  </select>
  <input type="text" name="name">
  <input type="submit" value="Search">
</form>
<br>

<?php
ini_set ('display_errors', 1); // Let me learn from my mistakes!
error_reporting (E_ALL);

if(file_exists("books.csv")){
	$fileHandle = fopen("books.csv","r");
	if(!$fileHandle){
		die ("cannot open file");
	}
}
else{
	die ("file doesn't exist");
}

//READ FORM DATA 

$uniqueAuthors = [];
$uniqueCategories = [];

//read header line to determine x number of inner arrays
$header = fgetcsv($fileHandle);

    if ($header !== false) {
        $masterArray = [];

        foreach ($header as $category) {
			//make array of x arrays
            $masterArray[$category] = [];
        }
		
        while (($data = fgetcsv($fileHandle)) !== false) {
            foreach ($data as $index => $value) {
               $category = $header[$index];
					//store unique categories in a new array
					if ($category === "Category"){
						if (!in_array($value,$uniqueCategories)){
							$uniqueCategories[] = $data[$index];
						} 
					}
					//store unique authors in a new array
					if ($category === "Author"){
						if (!in_array($value,$uniqueAuthors)){
							$uniqueAuthors[] = $data[$index];
						} 
					}
				//read data into arrays
                $masterArray[$category][] = $value;
            }
        }
	}

//SEARCH FOR MATCHES
	
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if(isset($_GET['type']) && isset($_GET['name'])){
        $search_type = $_GET['type'];
        $search_key = $_GET['name'];

		$header_printed = false;
        //loop through the master array to search for matches
        for ($i = 0; $i < count($masterArray[$search_type]); $i++) {
            if ($masterArray[$search_type][$i] === $search_key) {
                if (!$header_printed) {
					echo "<h3>Matching Books</h3>";
					echo "<table border='1'>";
					echo "<tr><th>Title</th><th>Year</th><th>Category</th><th>Author</th></tr>";
					$header_printed = true;
				}
                echo "<tr>";
                echo "<td>" . $masterArray["Title"][$i] . "</td>";
                echo "<td>" . $masterArray["Year"][$i] . "</td>";
                echo "<td>" . $masterArray["Category"][$i] . "</td>";
                echo "<td>" . $masterArray["Author"][$i] . "</td>";
                echo "</tr>";
            }
        }
		echo "</table>";
		if(!$header_printed){ //if no matches
			echo "<h3>Error: Invalid value </h3>";
			echo "Valid values are: <br><br>";
			if ($search_type === "Category"){
				for ($i = 0; $i < count($uniqueCategories); $i++) {
					echo "<li>";
					echo $uniqueCategories[$i] . "<br>";
					echo "</li>";
				}
			}
			else if ($search_type === "Author"){
				for ($i = 0; $i < count($uniqueAuthors); $i++) {
					echo "<li>";
					echo $uniqueAuthors[$i] . "<br>";
					echo "</li>";
				}
			}
			
		}
    } 
}

fclose($fileHandle);

?>
</body>
</html>
