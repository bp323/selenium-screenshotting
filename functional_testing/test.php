<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/screen.css" rel="stylesheet" type="text/css" media="screen">
<title>Functionality testing - test</title>
</head>
<body>

<div id="header">
    <div id="header_content">
        <img src="images/functionaltestinglogo.png" alt="functional testing logo" id="logo"/>
        <div id="menu_buttons">
            <ul>
                <li class="activebutton">Test</li>
                <li><a href="results.php" title="Watch your results">Results</a></li>
                <li><a href="mytests.php" title="About the functional tester">My tests</a></li>
                <li><a href="index.php" title="About the functional tester">About</a></li>
            </ul>
        </div>
    </div>
</div>

<div id="content">
    <div id="content_content">
        <p id="test_explanation">You can test your webpages for inconsistencies on this page. Once the test is submitted you will be redirected to the results page were your results will appear as soon as they are ready.</p>
        <div id="test_container">
            <form name ="checkboxform" method="POST" action="http://10.0.0.60:8888/functional_testing/testSubmit.php">
                <div id="test_preview">

                </div>
                <div id="code_input">
                    <label for="site_to_check">Website to check</label>
                    <input type="text" id="site_to_check" name="siteToCheck">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description">
                    <label for="code_input">Test input</label>
                    <textarea name="codeInput" id="code_input" cols="70" rows="15"></textarea>
                </div>
                <div id="submit_test_form">
                    <input id="submit_form_button" type="Submit" name="submitCheckboxes" value="Start test">
                    <input id="reset_form_button" type="Reset" name="resetForm" value="Reset form">
                </div>
            </form>
        </div>
        <hr/>
    </div>
</div>
<div id="footer">
    <div id="footer_content">
        <div id="footer_shadow">
        </div>
        
    </div>
</div>

<!-- TABLE WITH CHECKBOXES TEMPLATE -->
<div id="table_with_checkboxes_template" style="display:none;"><!--
    <div class="test_container">
        <div class="test_header">
            <h1>Configure test</h1>
            <div class="browser_images_container">
                {for b in browser}
                    {if b.isSupported == "true"}
                        <img src="images/browsers/${b.browserpic}" id="${b.browserId}" alt="${b.browserName}" title="${b.browserName}" class="browser_images"/>
                    {/if}
                {/for}
            </div>
        </div>
        <div class=test_content>
            <div class="description_column">
                {for o in os}
                    {if o.isSupported == "true"}
                        <div class="description_column_content"><p class="os_description" id="${o.osId}">${o.osName}</p></div>
                    {/if}
                {/for}
            </div>
            {for b in browser}
                {if b.isSupported == "true"}
                    <div class="test_content_column">
                        {for o in os}
                            {if o.isSupported == "true"}
                                <div class="test_content_column_content">
                                    <input type ="checkbox" id="${b.browserId}" name="${b.browserId}_${o.osId}" class="${b.browserId} ${o.osId} browserandosinputchk" disabled=true></input>
                                </div>
                            {/if}
                        {/for}
                    </div>
                {/if}
            {/for}
            <hr>
        </div>
    </div>-->
</div>
</body>
<script type="text/javascript" src="libs/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="libs/roundedcorners.js"></script>
<script type="text/javascript" src="libs/trimpath.js"></script>
<script type="text/javascript" src="js/test.js"></script>
</html>