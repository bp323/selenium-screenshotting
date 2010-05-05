<?php

header('Content-Type:application/json');

$db = new mysqli('localhost','hh354','cycling','selenium');
if(mysqli_connect_errno()){
    echo mysqli_connect_error();
}

$query = "SELECT o.id as osTestId, o.testId, o.osId, o.browserId, o.allRef, s.id as subTestId, 
s.description, r.screen, r.refScreen, r.success, r.distance, r.isRef
FROM OSTests o, SubTests s, Results r
WHERE o.testId = ? AND r.osTestId = o.id AND s.osTestId = o.id AND r.subTestId = s.id ORDER BY r.id ASC";

//GET ALL THE DATA FOR THE RESULTS
if($stmt = $db->prepare($query)){

    // Bind Parameters [s for string]
    $stmt->bind_param("i", $_GET['testid']);

    // Execute statement
    $stmt->execute();

    //Create an array with all columns that will come in
    $meta = $stmt->result_metadata();
    while ($field = $meta->fetch_field())
    {
        $params[] = &$row[$field->name];
    }

    //bind the fields that will come in to the fields in the array
    call_user_func_array(array($stmt, 'bind_result'), $params);

    //bind the values to the fields
    while ($stmt->fetch()) {
        foreach($row as $key => $val)
        {
            $c[$key] = $val;
        }
        $result[] = $c;
    } 
    
    //var_dump($result);
    // Close Statement
    $stmt->close();
}


//CREATE THE JSON FILE
//var_dump($result[0]);
$testId = $result[0]["testId"];
foreach($result as $row){
    
	$os = array();
	$os["osId"] = $row["osId"];
	$os["browserId"] = $row["browserId"];
    //find the os
    if($oss){
        foreach($oss as $o){
            if($o["osId"] == $os["osId"]){
                $exists = true;
                break;
            }else
                $exists = false;
        }
        if(!$exists) $oss[] = $os;
    }else{
        $oss[] = $os;
    }
}
$tests["testId"] = $testId;

foreach($oss as $os){
	$firstBrowserId = $os["browserId"];
	$testresult = array();
    $testresult["osId"] = $os["osId"];
    $browserresults = array();

    //ostests
    foreach($result as $row){
    	if($row["osId"] == $os["osId"]){
    		
    		//create the ostests node
    		if($row["browserId"] == $firstBrowserId){
	    		//create the subtest
	    		$ostest = array();
	            $ostest["testId"] = $row["osTestId"];
	            $ostest["description"] = $row["description"];
	            if($pos = strpos($ostest["description"],"("))
	                $codeName = substr($ostest["description"],0,$pos);
	            else
	                $codeName = $ostest["description"];
	            $ostest["tooltip"] = ucwords($codeName);
	            $testresult["ostests"][] = $ostest;
    		}

    		//get all browsers
    		$browserresult = array();
    		$browserresult["osId"] = $row["osId"];
    		$browserresult["allRef"] = $row["allRef"];
    		$browserresult["browserId"] = $row["browserId"];
    		$browserresult["testId"] = $row["osTestId"];
    		$browserresult["subTestId"] = $row["subTestId"];
    		$browserresult["screenshot"] = $row["screen"];
    		$browserresult["reference"] = $row["refScreen"];
    		$browserresult["isRef"] = $row["isRef"];
    		$browserresult["success"] = $row["success"];
    		
    		$browserresults[] = $browserresult;
    	}
    }

    //loop over all browserresult objects
    foreach($browserresults as $brr){

        //check if the osresults array exists
        if($testresult["osresults"]){

            //if it exists we loop over all osresult objects in the array
            foreach($testresult["osresults"] as &$osr){

            	//for each osresult we check if it has the same browserId as the new browserresult 
            	if($osr["browserId"] == $brr["browserId"]){

            		//if the browserId's match, we remove the browser and os ID
                    unset($brr["osId"]);
                    unset($brr["browserId"]);

                    //and we add the browserresult to the browserresults array of this osresult
            		array_push($osr["browserresults"], $brr);

            		//we remember that the browser already existed
            		$browserexists = true;
            	}else{
            		//if the browserId don't match, we remember it and we continue the loop
            		$browserexists = false;
            	}
            }
            //unset the reference to the last $osr object
            unset($osr);

            //if the browserId hasn't been found in the array of osresults, we add it as a new osresult
            if(!$browserexists){

            	//we create a new osresult
            	$osresult = array();

            	//we add the browserId to the osresult
                $osresult["browserId"] = $brr["browserId"];

                //we remove the browser and os ID
                unset($brr["osId"]);
                unset($brr["browserId"]);
                
                $osresult["allRef"] = $brr["allRef"];

                //we add the browserresult object to the browserresults array of the osresult
                $osresult["browserresults"][] = $brr; 

                //we add the new osresult to the array of osresults
                $testresult["osresults"][] = $osresult;
            }
        }else{
        	//if the osresults node doesn't exist
        	//we create an osresult object
        	$osresult = array();
        	//we add the browserId to the osresult
            $osresult["browserId"] = $brr["browserId"];

            //we remove the browser and os ID
            unset($brr["osId"]);
            unset($brr["browserId"]);
            
            $osresult["allRef"] = $brr["allRef"];
            
            //we add the browserresult to the browserresults array
            $osresult["browserresults"][] = $brr; 
            //we add the osresult with the browserresults and browserid to the osresults
            $testresult["osresults"][] = $osresult;
        }
    }
    
    $tests["testresults"][] = $testresult;
}

//create a tests node
$json["tests"][] = $tests;
print json_encode($json);













?>