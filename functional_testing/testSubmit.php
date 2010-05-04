<?php 
//Setup database connection
$db = new mysqli('localhost','hh354','cycling','selenium');
if(mysqli_connect_errno()){
    echo mysqli_connect_error();
}

$refTestId = $_POST["refTestId"];
//TODO Fill this value with the actual userId
$userId = 0;

if($refTestId){
	//get the data from the old test
	$query = "SELECT t.description, t.code, t.url, t.userId, t.subTests, o.browserId, o.osId FROM Tests t, OSTests o WHERE t.id = $refTestId AND o.testId = t.id";

	//Execute Query
    if($result = $db->query($query)){

        // Cycle through results (columns are: code, url, id, browserId)
        while($row = $result->fetch_object()){
            //Get all the data
            $code = $row->code;
            $url = $row->url;
            $description = $row->description;
            $tests = $row->subTests;
            $browsers[] = $row->browserId;
            $oss[] = $row->osId;
        }

        // Free result set
        $result->close();
    }
}else{
	foreach ($_POST as $var => $value) {
	    if ($value == "on"){
	        $browseros = split("_",$var);
	        $browsers[] = $browseros[0];
	        $oss[] = $browseros[1];
	    }
	}
	
	//Get the values from the form
	$url = $_POST["siteToCheck"];
	$code = $_POST["codeInput"];
	$description = $_POST["description"];
    //echo $code;
	//find all the asserts and takeScreenshots
	$firstassert = 0;
	$tsOffset = 0;
	while($firstassert = strpos($code,"assert",$firstassert)){
		if(strpos($code, "takeScreenshot",$tsOffset)){
			while(strpos($code, "takeScreenshot",$tsOffset) < $firstassert && strpos($code, "takeScreenshot",$tsOffset)){
			    $tsOffset = strpos($code, "takeScreenshot",$tsOffset) + 1;
			    $tests[] = "takeScreenshot";
			}
		}

        //Find the position of the occurence
        $endofline = strpos($code, "\n", $firstassert);
        $assert = substr($code,$firstassert,$endofline-$firstassert-2);
        $tests[] = str_replace("\$this->","",$assert);
	
	    //Set the offset one further to find the next assert
	    $firstassert++;
	}
	
	if(strpos($code, "takeScreenshot",$tsOffset)){
		while(strpos($code, "takeScreenshot",$tsOffset)){
		        $tsOffset = strpos($code, "takeScreenshot",$tsOffset) + 1;
		        $tests[] = "takeScreenshot";
		}
	}
	$tests = implode(";",$tests);
}

// Turn off auto commit
$db->autocommit(FALSE);

// Compose Query
$query = "INSERT INTO Tests (description, code, url, userId, subTests, refTestId) VALUES (?,?,?,?,?,?)";

if($stmt = $db->prepare($query)){

    // Bind Parameters [s for string]
    $refTestId = ($refTestId) ? $refTestId : 0;
    $stmt->bind_param("sssisi", $description, $code, $url, $userId, $tests, $refTestId);

    // Execute statement
    $stmt->execute();

    //save the testId
    $testId = $stmt->insert_id;

    // Close Statement
    $stmt->close();
}

$query = "INSERT INTO OSTests (testId, osId, browserId, run) VALUES (?,?,?,?)";
for($i = 0, $il = count($browsers); $i < $il; $i++){

	// Prepare Query
	if($stmt = $db->prepare($query)){
	
		// Bind Parameters [s for string]
		//The run parameter is always 0 from here because it says that the test hasn't been run (0=not run, 1=running, 2 = has run)
		$stmt->bind_param("issi", $testId, $oss[$i], $browsers[$i], $run);
		$run = 0;
		// Execute statement
		$stmt->execute();

		// Close Statement
		$stmt->close();
	}
}

// Commit queries
if(!$db->commit()){
    // Rollback last transaction
    $db->rollback();
}

//redirect to the results page
// find out the domain:
$domain = $_SERVER['HTTP_HOST'];
// find out the path to the current file:
$path = $_SERVER['SCRIPT_NAME'];
$path = str_replace(basename($path), "", $path);
header( 'Location: http://' . $domain . $path . 'results.php?testid='. $testId ) ;
//header( 'Location: http://localhost/~bp323/results.php?testid=' . $testId) ;

?>