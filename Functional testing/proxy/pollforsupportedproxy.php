<?php
// Set your return content type
header('Content-type: application/xml');

// Website url to open
$daurl = 'http://10.0.0.71:8888/polldbforsupported.php';

// Get that website's content
$handle = fopen($daurl, "r");

// If there is something, read and return
if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        echo $buffer;
    }
    fclose($handle);
}
?>