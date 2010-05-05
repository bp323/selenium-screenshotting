<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/screen.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="libs/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />

<title>Functionality testing - results</title>
</head>
<body>
<div style="display:none">
    <a id="hidden_clicker" href="#fancy_box_content">fancybox</a>
</div>
<div style="display:none;">
    <div id="fancy_box_content">
    </div>
</div>

<div id="header">
    <div id="header_content">
        <img src="images/functionaltestinglogo.png" alt="functional testing logo" id="logo"/>
        <div id="menu_buttons">
            <ul>
                <li><a href="test.php" title="Test your page">Test</a></li>
                <li class="activebutton">Results</li>
                <li><a href="mytests.php" title="About the functional tester">My tests</a></li>
                <li><a href="index.php" title="About the functional tester">About</a></li>
            </ul>
        </div>
    </div>
</div>

<div id="content">
    <div id="content_content">
        <div id="result_container"></div>
    </div>
</div>

<div id="footer">
    <div id="footer_content">
        <div id="footer_shadow">
        </div>
        
    </div>
</div>

<!-- SEARCH TESTS ON ID -->
<div id="search_test_template" style="display:none;">
    <p>Search for your test by putting your test ID in the searchbox below</p>
    <form name ="search_test_form" method="GET">
        <label for="search_test_input">Test ID</label>
        <input type="text" name="testid" id="search_test_input">
        <br>
        <input id="submit_search_test_button" type="Submit" value="Search">
    </form>
    <hr>
</div>

<!-- SETTINGS TABLE TEMPLATE -->
<div id="settings_table_template" style="display:none;"><!--
    <div class=test_container>
        <div class="test_header" id="test_settings">
            <h1>+ Test settings</h1>
        </div>
        <div class="test_content" id="settings_div">
            <h2>Operating systems and browsers</h2>
            <ul>
                {for o in operatingsystems}
                    <li class="os_test_list_item">${o.osName}</li>
                    <ul>
                        {for b in o.browsers}
                            <li class="browser_test_list_item">${b.browserName}</li>
                        {/for}
                    </ul>
                {/for}
            </ul>
            <h2>Website to check</h2>
            <ul class=test_input>
                <li><a href="${url}" target='_blank' title="Visit the website that's tested">${url}</a></li>
            </ul>
            <h2>Description</h2>
            <ul class=test_input>
                <li>${description}</li>
            </ul>
            <h2>Test input</h2>
            <ol id='code_input_list'>
                {for c in codeInput}
                    <li>${c.codeName}</li>
                {/for}
            </ol>
        </div>
    </div>-->
</div>

<!-- RESULTS TABLE TEMPLATE -->
<div id="results_table_template" style="display:none;"><!--
    {for o in operatingsystems}
        <div class="test_container">
            <div class="browser_images_container">
                {for b in o.browsers}
                    <img src="images/browsers/${b.browserPic}" class="browser_images ${o.osId} ${b.browserId}" alt="${b.browserName}" title="${o.osName} - ${b.browserName}"/>
                {/for}
            </div>
            <div class="test_header">
                <h1>- ${o.osName}</h1>
            </div>
            <div class="test_content ${o.osId}">
            <div class="description_column ${o.osId}">
                {for c in codeInput}
                    <div class='description_column_content'><p title="${c.tooltip}">${c.codeName}</p></div>
                {/for}
            </div>
            {for b in o.browsers}
                <div class="test_content_column ${o.osId} ${b.browserId}">
                
                </div>
            {/for}
            <hr/>
        </div>
        </div>        
    {/for}-->
</div>

<!-- NO REFERENCE SCREENSHOT FANCYBOX TEMPLATE -->
<div id="no_reference_screenshot_fancybox_template" style="display:none;"><!--
    <h1>No screenshot to compare with</h1>
    <form>
        <input name="chkref" id="chk_ref" class="chk_ref" value="${data("subtestid")}" type="checkbox" ${data("isref")}/>
        <label for="chk_ref" id="reflabel">Set as reference</label>
    </form>
    <p id="show_browser_results" class="${context.className.split('no_reference_screenshot ')[1]}">Show browser results</p>
    <a href="${context.src}" title="Show full screenshot" target="_blank"><img src="${context.src}" class="no_reference_screenshot_big"></a>-->
</div>

<!-- OK SCREENSHOT FANCYBOX TEMPLATE -->
<div id="ok_screenshot_fancybox_template" style="display:none;"><!--
    <h1>Screenshot successful</h1>
    <p id="show_browser_results" class="${className.split('ok_compare_img ')[1]}">Show browser results</p>
    <a href="${src}" title="Show full screenshot" target="_blank"><img src="${src}" class="ok_compare_img_big"></a>-->
</div>

<!-- ERROR SCREENSHOT FANCYBOX TEMPLATE -->
<div id="error_screenshot_fancybox_template" style="display:none;"><!--
    <h1>Screenshot error</h1>
    <p id="show_browser_results" class="${context.className.split('error_compare_img ')[1]}">Show browser results</p>
    <img src="${data('reference')}" class="reference_image">
    <a href="${context.src}" title="Show full screenshot" target="_blank"><img src="${context.src}" class="error_compare_img_big '${context.className.split("error_compare_img ")[1]}"></a>-->
</div>
</body>

<!-- SHOW BROWSER REPORT TEMPLATE -->
<div id="show_browser_report_template" style="display:none;"><!--
    <h1>${ev.context.title}</h1>
    <img class="browser_image" src="${ev.context.src}" class="browser_images" alt="${ev.context.alt}" title="${ev.context.title}"/>
    <form>
        <input id="chkbrowserref" name="chkbrowserref" class="chk_ref ${ev[0].className.split(' ')[1]} ${ev[0].className.split(' ')[2]}" type="checkbox" ${allRef}/>
        <label for="chkbrowserref" id="reflabel">Set all as reference</label>
    </form>
    <div class="description_column">
        {for i in descrColumns}
            <div class="description_column_content"><p>${i.innerHTML}</p></div>
        {/for}
    </div>
    <div class="test_content_column">
        {for t in descrColumns}
            <div class="test_content_column_content">
                ${t.title}
            </div>
        {/for}
    </div>
    <div class="test_content_column">
        {for img in contentColumns}
            <div class="test_content_column_content">
               <img src=" ${img[0].src}" alt="${img[0].alt}" class="${img[0].className}"/>
            </div>
        {/for}
    </div>
    -->
</div>
<?php 
    echo ("<script>var testId= ''; testId='" . $_GET['testid'] . "'</script>");
?>

<script type="text/javascript" src="libs/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="libs/roundedcorners.js"></script>
<script type="text/javascript" src="libs/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
<script type="text/javascript" src="libs/fancybox/jquery.fancybox-1.3.1.js"></script>
<script type="text/javascript" src="libs/trimpath.js"></script>
<script type="text/javascript" src="js/results.js"></script>
</html>