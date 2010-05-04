<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class FunctionalTestingSeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{

    function saveTest($testdesc, $screen, $success, $distance = 999){

        // Turn off auto commit
        $this->db->autocommit(FALSE);

        // Insert Values
        $query = "INSERT INTO SubTests (description, osTestId) VALUES (?, ?)";

        // Prepare Query
        if($stmt = $this->db->prepare($query)){

            // Bind Parameters [s for string]
            $stmt->bind_param("si",$testdesc,$this->osTestId);

            // Execute statement
            $stmt->execute();

            //Get the id of the last insert
            $subTestId = $stmt->insert_id;
            
            // Close Statement
            $stmt->close();
        }else{
            echo "fail";
        }

        $query = "INSERT INTO Results (osTestId, refScreen, screen, success, subTestId, resultNr, distance) VALUES (?,?,?,?,?,?,?)";

        // Prepare Query
        if($stmt = $this->db->prepare($query)){

            // Bind Parameters [s for string]
            $stmt->bind_param("isssiid", $this->osTestId, $this->refScreen, $screen, $success, $subTestId, $this->resultNr, $distance);

            // Execute statement
            $stmt->execute();

            // Close Statement
            $stmt->close();
        }

        // Commit queries
        if(!$this->db->commit()){
            // Rollback last transaction
            $this->db->rollback();
        }

        //raise the number of the test in this ostest
        $this->resultNr++;

    }

    function saveScreenshot($path, $base64screenshot){
        $decodedScreenshot = base64_decode($base64screenshot);
        $fh = fopen($path, 'w') or die("can't open file");
        fwrite($fh, $decodedScreenshot);
        fclose($fh);
    }

    /**
     * Take screenshots of all the parts of the full webpage by scrolling down
     */
    function takeScreenshot(){
        try{
            $this->getEval("window.scrollTo(0,0)");
            $i = 0;
            //capture a screenshot of the webpage
            $base64screenshot = $this->captureScreenshotToString();
            //the testid will be added to the filename later on
            $myFile = $this->screenshotsPath . "/screenshot" . $i . ".png";
            $this->saveScreenshot($myFile, $base64screenshot);

            //crop it
            $this->cropImage($i);

            //take screenshots of the full page and compare each time with the previous screenshot
            do{
                //Remember scroll offset
                $this->lastOffset = 0;
                $offset = $this->getOffset();
                if(($offset - ($i*$this->viewportHeight)) != 0 && $offset != 0){
                    $this->lastOffset = $offset - (($i-1)*$this->viewportHeight);
                }

                $i++;

                //Page down
                $this->getEval("window.scrollTo(0,". ($i * ($this->viewportHeight)) .");");
                usleep(500000);
                //Take new screenshot
                $myFile = $this->screenshotsPath . "/screenshot" . $i . ".png";
                $base64screenshot = $this->captureScreenshotToString();
                $this->saveScreenshot($myFile, $base64screenshot);

                //crop it
                $this->cropImage($i);
            }while(!$this->checkWithPreviousShot($i));

            $this->mergeEntireWebpage();
        }catch(Exception $ex){
            $this->saveTest("takeScreenshot","", false);
        }
    }

    /**
     * Merge all the screenshots together to one big merged screenshot of the complete web page
     */
    function mergeEntireWebpage(){
        try{
	        if ($handle = opendir($this->screenshotsPath . "/cropped/")) {

	            //get all cropped screenshots
	            $arrFiles = array();
	            while (false !== ($file = readdir($handle))) {
	                if(strpos($file,"screenshot") !== false){
	                    array_push($arrFiles, $file);
	                }
	            }

	            usort($arrFiles, array("Example", "cmp"));

	            //crop the last image shorter
                if($this->lastOffset > 0 && count($arrFiles) > 1){
	                $image = new Imagick();
	                $currFile = $this->screenshotsPath . "/cropped/" . $arrFiles[count($arrFiles) - 1];
	                $handleFile = fopen($currFile, "rb");
	                $image->readImageFile($handleFile);
	                $y = $this->viewportHeight - $this->lastOffset;
	                $image->setImagePage(0,0,0,0);
	                $image->cropImage($this->viewportWidth, $this->lastOffset, 0, $y);
	                $image->writeImage($currFile);
                }

	            $im = new Imagick();

	            // Loop over the directory to add all images to the imagick object
	            foreach($arrFiles as $file){
	                //$handleFile = fopen($this->screenshotsPath . "cropped/" . $file, "rb");
	                $im->readImage($this->screenshotsPath . "/cropped/" . $file);//$handleFile);
	            }

	            //Append all the images underneath eachother
	            $im->resetIterator();
	            $combined = $im->appendImages(true);

	            if(!file_exists($this->screenshotsPath . "/merged"))
	                mkdir($this->screenshotsPath . "/merged", 0777);
	
	            //write the merged image to a file
	            $mergedPath = $this->screenshotsPath . "/merged/merged" . $this->m . ".png";
	            $combined->writeImage($mergedPath);

	            //Compare with the reference screenshot
	            $distance = $this->compareWithReference($mergedPath);

	            if($distance == 0)
	                $success = true;
                else
                    $success = false;
	            //save the result
	            $this->saveTest("takeScreenshot", $mergedPath, $success, $distance);
	
                //Delete all the screenshots except the merged ones
	            $this->deleteScreenshots();

	            //Raise the merged index
	            $this->m += 1;
	            
	            //Close the file handle
	            closedir($handle);
	        }
        }catch(Exception $ex){
            $this->saveTest("takeScreenshot","", false);
            echo $ex->message;
        }
    }

    function compareWithReference($newImage){
        //Get the reference image with the testId, browser and os info and resultNr we have
        $query = "SELECT r.screen FROM OSTests o, Results r WHERE o.testId = ? AND o.osId = ? AND o.browserId = ? AND r.osTestId = o.id AND r.resultNr = ? AND r.isRef = 1";

        if($stmt = $this->db->prepare($query)){

            // Bind Parameters [s for string]
            $stmt->bind_param("issi", $this->refTestId, $this->osId, $this->browserId, $this->resultNr);

            // Execute statement
            $stmt->execute();

            $stmt->bind_result($this->refScreen);

            /* fetch values */
            while ($stmt->fetch()) {
                $this->refScreen;
            }

            // Close Statement
            $stmt->close();
        }

        if($this->refScreen){
	        //Compare the two images
	        $img1 = new imagick($this->refScreen);
	        $img2 = new imagick($newImage);
	        $result = $img1->compareImages($img2, Imagick::METRIC_MEANSQUAREERROR);

	        //return result
	        return $result[1];
        }else{
            $this->refScreen = "";
            return 999;
        }
    }

    /**
     * Compare function to sort the array of files
     */
    function cmp($a, $b)
    {
        $number1 = (int)preg_replace("/[^0-9]/", '', $a);
        $number2 = (int)preg_replace("/[^0-9]/", '', $b);;
        $result = ($number1 < $number2) ? -1 : 1;
        return $result;
    }

    /**
     * Compare if two screenshots are the same
     */
    function checkWithPreviousShot($id){
        $image1 = new imagick($this->screenshotsPath . "/cropped/screenshot" . ($id-1) . ".png");
        $image2 = new imagick($this->screenshotsPath . "/cropped/screenshot" . $id . ".png");

        $result = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
        if($result[1] == 0){
            unlink($this->screenshotsPath . "/cropped/screenshot" . $id . ".png");
            return true;
        }
        else
            return false;
    }

    /**
     * Get the offset of the top left corner of the viewport in comparison to the top left of the site
     */
    function getOffset(){
        $scrOfY = 0;
        $test = $this->getEval("typeof(window.pageYOffset)");
        if(strcmp($test,"number") == 0) {
            //Netscape compliant
            $scrOfY = (int)$this->getEval("window.pageYOffset;");
            //scrOfX = window.pageXOffset;
        } else if( (bool)$this->getEval("document.body != null") && (bool)$this->getEval("document.body.scrollTop != null")) {
            //DOM compliant
            $scrOfY = (int)$this->getEval("document.body.scrollTop;");
            //scrOfX = document.body.scrollLeft;
        } else if( (bool)$this->getEval("document.documentElement != null") && (bool)$this->getEval("document.documentElement.scrollTop != null")) {
            //IE6 standards compliant mode
            $scrOfY = (int)$this->getEval("document.documentElement.scrollTop;");
            //scrOfX = document.documentElement.scrollLeft;
        }
        if(!$scrOfY || $scrOfY <= 0)
            $scrOfY = $this->getEval("document.body.offsetHeight");
        if(!$scrOfY || $scrOfY <= 0)
        	$scrOfY = $this->getEval("document.body.scrollHeight");
        
        return $scrOfY;
        //$scrollTop = $this->getEval("document.body.scrollTop;");
	    //$scrollTop = $this->getEval("window.pageYOffset;");
	    //$scrollTop = $this->getEval("document.body.parentElement.scrollTop;");
		//$scrollTop = $this->getEval("document.documentElement.scrollTop");
		//$scrollTop = $this->getEval("document.body.scrollTop");
		
    	//$scrollTop = $this->getEval("document.documentElement.scrollHeight");
    	//$scrollTop = $this->getEval("document.body.scrollHeight");
    	
    	//This gives total height in ff
    	/*$scrollTop = $this->getEval("window.scrollMaxY");
    	
    	
	    if ($this->getEval("window.innerHeight") && $this->getEval("window.scrollMaxY")) {// Firefox
	        $scrollTop = $this->getEval("window.innerHeight") + $this->getEval("window.scrollMaxY");
	        //xWithScroll = $this->getEval("window.innerWidth") + $this->getEval("window.scrollMaxX");
	    } else if ($this->getEval("document.body.scrollHeight") > $this->getEval("document.body.offsetHeight")){ // all but Explorer Mac
	        $scrollTop = $this->getEval("document.body.scrollHeight");
	        //xWithScroll = $this->getEval("document.body.scrollWidth");
	    } else { // works in Explorer 6 Strict, Mozilla (not FF) and Safari
	        $scrollTop = $this->getEval("document.body.offsetHeight");
	        //xWithScroll = $this->getEval("document.body.offsetWidth");
	    }
    
    	
    	
    	
		echo "result: " . $scrollTop . '\n';
		return $scrollTop;*/
    }

    /**
     * Crop an image with the viewport coordinates
     */
    function cropImage($id){
        $image = new imagick($this->screenshotsPath . "/screenshot" . $id . ".png");
        $image->cropImage($this->viewportWidth, $this->viewportHeight, $this->topPosX, $this->topPosY);
        if(!file_exists($this->screenshotsPath . "/cropped")){
            mkdir($this->screenshotsPath . "/cropped", 0777);
        }
        $image->writeImage($this->screenshotsPath . "/cropped/screenshot" . $id . ".png");
    }

    /**
     * Configure the viewport, paths, browserinformation
     */
    function setConfiguration($osTestId, $browserId, $osId, $refTestId){

        //Set the merged index and resultNr variables to 0
        $this->m = 0;
        $this->resultNr = 0;

        //Set the osTestId
        $this->osTestId = $osTestId;
        $this->osId = $osId;
        $this->browserId = $browserId;
        $this->refTestId = $refTestId;

        // Create the general paths
        date_default_timezone_set("Europe/London");
        $today = getdate();

        $this->generalPath = "../functional_testing/php-src/screenshots/" . $today["year"] . "/" . $today["mon"] . "/" . $today["mday"] . "/" . "osTest_" . $osTestId . "/";
        $this->screenshotsPath = $this->generalPath . $osId . "/" . $browserId;
        if(!file_exists($this->screenshotsPath)){
            mkdir($this->screenshotsPath, 0777, true);
        }

        // Delete the screenshots folder
        // realpath = /Users/hh354/Sites
        $this->deleteDir($this->screenshotsPath);

        // Maximize the window
        $this->windowMaximize();
        $this->windowFocus();

        // Put window to top left (only for FF)
        $this->getEval("window.moveTo(1,0);");

        // Find the viewport
        $this->findViewport();

        //Setup database connection
        $this->db = new mysqli('localhost','hh354','cycling','selenium', 8889);
        if(mysqli_connect_errno()){
            echo mysqli_connect_error();
        }
    }

    /**
     * Find the viewport coordinates
     */
    function findViewport(){
        try{
            $configPath = $this->screenshotsPath  . "/config/";
            if(!file_exists($configPath)){
                mkdir($configPath, 0777, true);
            }

            //capture a screenshot of the webpage
            $base64screenshot = $this->captureScreenshotToString();

            //save it to a folder
            $this->saveScreenshot($configPath . "config.png", $base64screenshot);

            $im = new Imagick($configPath . "config.png");

            //find the viewport
            $x = 20;
            $y = 200;

            //check the top side
            do{
                if($y > 0){
                    // Getting pixel color by position
                    $pixel = $im->getImagePixelColor($x,$y);
                    $colors = $pixel->getColor();
                    $y--;
                }else{
                    break;
                }
            }while($colors["r"] == 255 && $colors["g"] == 255 && $colors["b"] == 255);
            //Add 2 pixels in the end because he went two pixels too far because of the do while
            $this->topPosY = $y+2;

            //check the left side
            $x = 20;
            $y = 200;
            do{
                if($x > 0){
                    // Getting pixel color by position x=20 and y=150
                    $pixel = $im->getImagePixelColor($x,$y);
                    $colors = $pixel->getColor();
                    $x--;
                }else{
                    break;
                }
            }while($colors["r"] == 255 && $colors["g"] == 255 && $colors["b"] == 255);
            $this->topPosX = $x+2;

            $x = $this->getEval("document.body.clientWidth");
            $y = $this->getEval("document.body.clientHeight");

            //check the bottom side
            do{
                if($y < ($im->getImageHeight() - 1)){
                    // Getting pixel color by position
                    $pixel = $im->getImagePixelColor($x,$y);
                    $colors = $pixel->getColor();
                    $y++;
                }else{
                    break;
                }
            }while($colors["r"] == 255 && $colors["g"] == 255 && $colors["b"] == 255);
            $bottomPosY = $y-2;
            
            //check the right side
            $x = 20;
            $y = 200;
            do{
                if($x < ($im->getImageWidth() - 1)){
                    // Getting pixel color by position
                    $pixel = $im->getImagePixelColor($x,$y);
                    $colors = $pixel->getColor();
                    $x++;
                }else{
                    break;
                }
            }while($colors["r"] == 255 && $colors["g"] == 255 && $colors["b"] == 255);
            $bottomPosX = $x-2;

            $this->viewportWidth = $bottomPosX - $this->topPosX - 15;
            $this->viewportHeight = $bottomPosY - $this->topPosY;
        }catch(Exception $ex){
            echo $ex->getMessage();
        }
    }

    /**
     * Delete the screenshot folder and all files underneath
     */
    function deleteDir($dirname) {
        $dir_handle = null;
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
                else
                    $this->deleteDir($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
    }

    /**
     * Delete all the unused screenshots
     */
    function deleteScreenshots(){
        $dir_handle = opendir($this->screenshotsPath);
        if(!$dir_handle)
            return false;
        while($file = readdir($dir_handle)){
            if($file == "cropped"){
                $this->deleteDir($this->screenshotsPath . "/cropped");
            }
            if(is_file($this->screenshotsPath . "/" . $file))
                unlink($this->screenshotsPath . "/" .$file);
        }
        closedir($dir_handle);
    }
}
?>