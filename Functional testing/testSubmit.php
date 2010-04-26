<?php 

//Get the complete querystring eg.://testSubmit.php?firefox_windowsxp=on&firefox_windowsvista=on&firefox_windowsseven=on&chrome_windowsvista=on&chrome_windowsseven=on&safari_ubuntu=on&ie6_windowsvista=on&ie6_windowsseven=on&ie7_windowsseven=on&ie8_windowsseven=on&siteToCheck=testUrl&description=testDesc&codeInput=This+Is+My+code&submitCheckboxes=Start+test
$qry = $_SERVER['QUERY_STRING'];
//split all params with the & sign
$params = split("&",$qry);

//loop over all params and extract the browserId and the osId
foreach ($params as $param){
    $param = split("=",$param);
    //check if it is a checkbox by checking if it is set to 'on'
    if($param[1] == "on"){
        //split the browser and the os and save both in an array
        $browseros = split("_",$param[0]);
        $browsers[] = $browseros[0];
        $oss[] = $browseros[1];
    }
}

//Get the values from the form
$url = $_GET["siteToCheck"];
$code = $_GET["codeInput"];
$description = $_GET["description"];
//TODO Fill this value with the actual userId
$userId = 0;

//find all the asserts and takeScreenshots
$offset = 0;
$tsOffset = 0;
while($firstassert = strpos($code,"assert",$offset)){
    if(strpos($code, "takeScreenshot",$tsOffset)){
        while(strpos($code, "takeScreenshot",$tsOffset) < $firstassert){
            $tsOffset = strpos($code, "takeScreenshot",$tsOffset) + 1;
            $tests[] = "takeScreenshot";
        }
    }

    //Find the position of the occurence
    $offset = strpos($code,"assert",$offset);
    $endofline = strpos($code, "\n", $offset);
    $assert = substr($code,$offset,$endofline-$offset-2);
    $tests[] = str_replace("\$this->","",$assert);

    //Set the offset one further to find the next assert
    $offset++;
}

if(strpos($code, "takeScreenshot",$tsOffset)){
    while(strpos($code, "takeScreenshot",$tsOffset)){
            $tsOffset = strpos($code, "takeScreenshot",$tsOffset) + 1;
            $tests[] = "takeScreenshot";
    }
}

$tests = implode(";",$tests);

//Setup database connection
$db = new mysqli('localhost','hh354','cycling','selenium');
if(mysqli_connect_errno()){
    echo mysqli_connect_error();
}

// Turn off auto commit
$db->autocommit(FALSE);

// Compose Query
$query = "INSERT INTO Tests (description, code, url, userId, subTests) VALUES (?,?,?,?,?)";

if($stmt = $db->prepare($query)){

    // Bind Parameters [s for string]
    $stmt->bind_param("sssis", $description, $code, $url, $userId, $tests);

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
    }else{
       echo "fail";
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
//header( 'Location: http://' . $domain . $path . 'results.php?testId='. $testId ) ;
header( 'Location: http://localhost/~bp323/results.php?testid=' . $testId) ;
?>