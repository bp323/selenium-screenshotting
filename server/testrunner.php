<?php 

    //setupConnection();
    //run();
    //function setupConnection(){
        //Setup database connection
        $db = new mysqli('localhost','hh354','cycling','selenium', 8889);
        if(mysqli_connect_errno()){
            echo mysqli_connect_error();
        }
    //}

    //function run(){
        do{
            //Check the connection to the DB and reconnect if the connection is gone (included in ping)
            //Set mysqli.reconnect = On in php.ini
            $db->ping();

            /*
             * What do I need?
             * Tests: Code,url 
             * OSTests: osTestId, browserId
             * browserId will be concatenated with * to get *chrome, *safari, *iexplorer6 ...
             */
            //Check the database for tests for this os
            $query = "SELECT t.code, t.url, ot.id, ot.browserId, ot.osId, t.refTestId, b.sel_name FROM OSTests ot, OperatingSystems os, Tests t, Browsers b WHERE ot.run = 0 AND ot.osId = os.id AND t.id = ot.testId AND b.id = ot.browserId LIMIT 1";

            // Execute Query
            if($result = $db->query($query)){

                // Cycle through results (columns are: code, url, id, browserId)
                while($row = $result->fetch_array()){
                    //Get all the data
                    $code = $row[0];
                    $url = $row[1];
                    $osTestId = $row[2];
                    $browserId = $row[3];
                    $osId = $row[4];
                    $refTestId = $row[5];
                    $browserSelName = $row[6];

                    //Create the test
                    //remove all escape chars
                    $code = preg_replace("/\\\\/","", $code);

                    //replace the require
                    $code = preg_replace("/PHPUnit\/Extensions\/SeleniumTestCase.php/","FunctionalTestingSeleniumTestCase.php", $code);
                    $code = preg_replace("/PHPUnit_Extensions_SeleniumTestCase/","FunctionalTestingSeleniumTestCase", $code);

                    //CHANGE THE SELENIUM SERVER INSTANCE

                    if(substr($osId,0,7) == "windows"){
	                    //Find the setbrowserurl
	                    $startint = strpos($code,"setBrowserUrl");
	                    $setInt = strpos($code,"\n",$startint);
	                    //split in two
	                    $firstPart = substr($code,0,$setInt + 1);
	                    $secondPart = substr($code, $setInt + 1);
	                    //concatenate everything and add the host and port
	                    $code = $firstPart . "\$this->setHost('10.0.0.253');\n\$this->setPort(4444);\n" . $secondPart;
                    }
                    

                    //Find the beginning of the test: testMyTestCase()
                    $startint = strpos($code,"testMyTestCase()");
                    $setInt = strpos($code, "{",$startint);

                    //split in two
                    $firstPart = substr($code,0,$setInt + 2);
                    $secondPart = substr($code, $setInt + 2);

                    //concatenate everything and add the setConfiguration line
                    $code = $firstPart . "\$this->setConfiguration(" . $osTestId . ", '" . $browserId . "', '" . $osId . "', " . $refTestId . ");\n" . $secondPart;

                    //find all the takeScreenshots and remove the // in front of it
                    $code = preg_replace("/\/\/ \\\$this->takeScreenshot/","\$this->takeScreenshot",$code);

                    //change the url
                    $code = preg_replace("/http:\/\/change-this-to-the-site-you-are-testing\//",$url, $code);

                    //change the browser
                    $code = preg_replace("/\*chrome/", "*" . $browserSelName, $code);

                    //change the waitforpagetoload to a sleep
                    $code = preg_replace("/\\\$this->waitForPageToLoad\(\"30000\"\);/","sleep(3);",$code);

                    //find the open statement
                    if($offset = strpos($code,"this->open")){
	                    //find the end of the line
	                    $endofline = strpos($code, "\n", $offset);
	                    //split in two
	                    $firstPart = substr($code,0,$endofline);
	                    $secondPart = substr($code, $endofline + 1);
	                    //concatenate everything
	                    $code = $firstPart . "sleep(3);\n" . $secondPart;
                    }

                    //find a click statement
                    $offset = 0;
                    while($offset = strpos($code, "this->click", $offset)){
                        //check if there's a sleep right after it
                        $nextsleep = strpos($code, "sleep", $offset);
                        $endofline = strpos($code, "\n", $offset);
                        $nextline = strpos($code, "\n", $endofline + 1);
                        if($nextsleep > $nextline){
                            //split the code in two parts
                            $firstPart = substr($code,0,$endofline);
	                        $secondPart = substr($code, $endofline + 1);
	                        //concatenate everything
	                        $code = $firstPart . "sleep(3);\n" . $secondPart;
                        }
                        $offset++;
                    }
                    

                    //find all the asserts and put the saveTest method behind it
                    $offset = 0;
                    while($offset = strpos($code,"assert",$offset)){
                        //Find the position of the occurence
                        //$offset = strpos($code,"assert",$offset);
                        $endofline = strpos($code, "\n", $offset);
                        $assert = substr($code,$offset,$endofline-$offset-2);
                        $assert = str_replace("\$this->","",$assert);

                        //split in two
	                    $firstPart = substr($code,0,$endofline);
	                    $secondPart = substr($code, $endofline + 1);

	                    //concatenate everything
                        $code = $firstPart . "\$this->saveTest('" . addslashes($assert) . "','',true);\n" . $secondPart;

                        //look if there's a catch block
                        $catch = strpos($code,"catch",$endofline);
                        $nextline = strpos($code,"\n",$endofline + 1);
                        $nextline = strpos($code,"\n",$nextline+1);

                        if($catch && $catch < $nextline){
                            //there is a catch statement
                            //split in two
                            $insert = strpos($code,"\n",$catch);
                            $firstPart = substr($code, 0, $insert + 2);
                            $secondPart = substr($code, $insert + 3);

                            // concatenate everything
                            $code = $firstPart . "\$this->saveTest('" . addslashes($assert) . "','',false);\n" . $secondPart;
                        }

                        //Set the offset one further to find the next assert
                        if($insert){
                            $offset = strpos($code,"\n",$insert +1);
                            $insert = null;
                        }
                        else
                            $offset = $nextline;
                    }
                    //Save the test as php file
                    $myFile = "php-src/testMe.php";
                    $fh = fopen($myFile, 'w') or die("can't open file");
                    fwrite($fh, $code);
                    fclose($fh);

                    //Run the test
                    $output = shell_exec('phpunit php-src/testMe.php');
                    echo "<pre>$output</pre>";

                    //remove the test
                    //unlink("php-src/testMe.php");

                    //Put the 'run' column of the test on 2 (finished);
                    $query = "UPDATE OSTests SET run = 2 WHERE id = ?";

					if($stmt = $db->prepare($query)){
					
					    // Bind Parameters [s for string]
					    $stmt->bind_param("i", $osTestId);
					
					    // Execute statement
					    $stmt->execute();
					
					    // Close Statement
					    $stmt->close();
					}
                }

                // Free result set
                $result->close();
            }
        }while(true);
    //}
    

?>