/**
 * Declare some variables to be used in the document
 */
// testSettings: header, clickable for settings
$testSettings = $('#test_settings');
// settingsDiv: div containing settings data
$settingsDiv = $('#settings_div');
// contentcontent: Contains all content data
$contentcontent = $('#result_container');
// displayBox: Overlay that shows more information about the topic clicked
$displayBox = $('#displayBox');
// hiddenClicker: simulates click on a link
$hiddenClicker = $("#hiddenclicker");
// fancyBoxContent: Content to be shown in the fancybox component
$fancyBoxContent = $("#fancy_box_content");

/**
 * With scrollbars floats can become messy
 * If you add some extra width this is fixed but still dynamic
 */
var setFancyBoxWidth = function(){
    $("#fancybox-inner").width($("#fancybox-inner").width() + 40);
};

/**
 * Show the fancybox and give some parameters to go with it
 */
$("#hidden_clicker").fancybox({
    'titlePosition':'inside',
    'transitionIn':'fade',
    'transitionOut':'fade',
    'onComplete' : function() {
       var t=setTimeout("setFancyBoxWidth()",1);
    }
});

/**
 * Add clickhandlers
 */
$(".test_header").live("click", function(){showHideTest($(this));});
$(".browser_images").live("click", function(){showReport($(this));});
$(".ok_compare_img").live("click", function(){showReport($(this));});
$(".error_compare_img").live("click", function(){showReport($(this));});
$(".no_reference_screenshot").live("click", function(){showReport($(this));});
$("#show_browser_results").live("click", function(){showReport($(this));});

/**
 * Show the testresults or not
 * @param {Object} ev event that's fired on click of the header
 */ 
var showHideTest = function(ev){
    // Animate all results
    ev.next().animate({height: "toggle", opacity: "toggle"},function(){
        var browser = ev.children('h1').html().substring(2);
        if (ev.siblings().is(":hidden")) {
            ev.children('h1').html("+ " + browser);
        } else {
            ev.children('h1').html("- " + browser);
        }
    });
    // Animate browser images
    ev.prev().animate({opacity: "toggle"});
};

/**
 * Show report for a specific browser on a specific os
 * @param {Object} ev event that came in after click
 */
var showBrowserReport = function(ev){
    // Add browser title and os to title tag
    var template = '<h1>' + ev.context.title + '</h1>';
    // Add browser image to the right top corner
    template += '<img class="browser_image" src="' + ev.context.src + '" class="browser_images" alt="' + ev.context.alt + '" title="' + ev.context.title + '"/>';
    // Add description of the test
    $descriptionColumn = $("div.description_column." + ev.context.className.split(' ')[1]);
    template += '<div class="description_column">';
    $descriptionColumn.children().each(function(){
        template += '<div class="description_column_content"><p>' + $(this).children('p').html() + '</p></div>';
    });
    template += '</div>';
    // Add the whole description next to the short description
    template += '<div class="test_content_column">';
    $descriptionColumn.children().each(function(){
        template += '<div class="test_content_column_content">';
        template += $(this).children('p')[0].title;
        template += '</div>';
    });
    // Close testContentColumn div
    template += '</div>';
    // Add the testresults next to the description
    // Get the contentcolumn with results
    $contentcolumn = $("div.test_content_column." + ev.context.className.split(' ')[1] + '.' + ev.context.className.split(' ')[2]);
    template += '<div class="test_content_column">';
    $contentcolumn.children().each(function(){
        template += '<div class="test_content_column_content">';
        template += '<img src="' + $(this).children('img')[0].src + '" alt="' + $(this).children('img')[0].alt + '" class="' + $(this).children('img')[0].className + '"/>';
        template += '</div>';
    });
    // Close testContentColumn div
    template += '</div>';
    
    // Add the template to the html
    $fancyBoxContent.html(template);
    
    // Loop all children of the testcontencolumn
    $contentcolumn.children().each(function(){
        // If there is a child that is a screenshot get his data and add it to the same screenshot on the fancybox page
        if ($(this).children('img')[0].className.split(' ')[0] == "error_compare_img"){
            // Give the browser header the same data as the image
            $fancyBoxContent.children('div').children('div').children("img."+$(this).children('img')[0].className.split(' ')[0]+"."+$(this).children('img')[0].className.split(' ')[1]+"."+$(this).children('img')[0].className.split(' ')[2]).data("reference", $(this).children('img').data("reference"));
        }
    });
    
    // Trigger click to show fancybox
    $("#hidden_clicker").trigger("click");
};

/**
 * Show screenshots that have no errors.
 * Function will only show taken screenshot and NOT the reference screenshot
 * @param {Object} ev event that came in after click
 */
var showOkScreenshot = function(ev) {
    $fancyBoxContent.width("");
    var template = '<h1>Screenshot successful</h1>';
    template += '<p id="show_browser_results" class="' + ev.context.className.split("ok_compare_img ")[1] + '">Show browser results</p>';
    template += '<a href="' + ev.context.src + '" title="Show full screenshot" target="_blank"><img src="' + ev.context.src + '" class="ok_compare_img_big"></a>';
    // Add the template to the html
    $fancyBoxContent.html(template);

    // Trigger click to show fancybox
    $("#hidden_clicker").trigger("click");
};

/**
 * Show screenshots that have errors.
 * Function will show the reference screenshot if it's present
 * @param {Object} ev event that came in after click
 */
var showErrorScreenshot = function(ev) {
    $fancyBoxContent.width("");
    var template = '<h1>Screenshot error</h1>';
    template += '<p id="show_browser_results" class="' + ev.context.className.split("error_compare_img ")[1] + '">Show browser results</p>';
    //template += '<a href="' + ev.context.nextSibling.innerHTML + '" title="Reference Image" target="_blank"><img src="images/screenshots/' + ev.context.nextSibling.innerHTML + '" class="reference_image"></a>';
    template += '<img src="images/screenshots/' + ev.data("reference") + '" class="reference_image">';
    template += '<a href="' + ev.context.src + '" title="Show full screenshot" target="_blank"><img src="' + ev.context.src + '" class="error_compare_img_big ' + ev.context.className.split("error_compare_img ")[1] + '"></a>';
    
    var reference = ev.data("reference");
    
    // Add the template to the html
    $fancyBoxContent.html(template);
    
    // Put the arbitrary data in the image on the Fancybox
    // It will be needed when going back to the browser overview
    $fancyBoxContent.children('a').children('img').data("reference", reference); 
    // Trigger click to show fancybox
    $("#hidden_clicker").trigger("click");
};

/**
 * Show screenshots that have errors.
 * Function will show the reference screenshot if it's present
 * @param {Object} ev event that came in after click
 */
var showNoReferenceScreenshot = function(ev) {
    $fancyBoxContent.width("");
    var template = '<h1>No screenshot to compare with</h1>';
    template += '<p id="show_browser_results" class="' + ev.context.className.split("no_reference_screenshot ")[1] + '">Show browser results</p>';
    template += '<a href="' + ev.context.src + '" title="Show full screenshot" target="_blank"><img src="' + ev.context.src + '" class="no_reference_screenshot_big"></a>';
    
    // Add the template to the html
    $fancyBoxContent.html(template);
    
    // Trigger click to show fancybox
    $("#hidden_clicker").trigger("click");
};

/**
 * Triggers the click event on the link to show the fancybox
 * Function will select the right function to call based on the event that comes in
 * @param {Object} ev Clickevent
 */
var showReport = function (ev){
    if (ev.context.className.split(' ')[0] == 'browser_images'){
        // Execute function to show information about a specific browser on a specific os
        // Is called when a browser header is clicked and when a user wants more browser information coming from screenshots
        showBrowserReport(ev);
    } else if (ev.context.className.split(' ')[0] == 'ok_compare_img'){
        // Execute function to show information about a specific browser on a specific os
        showOkScreenshot(ev);
    } else if (ev.context.className.split(' ')[0] == 'error_compare_img'){
        // Execute function to show information about a specific browser on a specific os
        showErrorScreenshot(ev);
    } else if (ev.context.id == 'show_browser_results'){
        // Trigger click of browser header
        // This sets all settings correct at once with the same function
        $('.browser_images.' + ev.context.className.split(' ')[0] + '.' + ev.context.className.split(' ')[1]).trigger("click");
    } else if (ev.context.className.split(' ')[0] == 'no_reference_screenshot'){
        // Show a screenshot that hasn't got any reference image
        showNoReferenceScreenshot(ev);
    }
};

/**
 * Fills the table with results coming from the database
 * @param {Object} results Results from the Ajax call that contain results from the test
 */
var fillTableWithResults = function(results) {
    // Unique ID to give to each image on the gui
    var uidForEveryImage = 0;
    // Loop through all tests
    for (var i = 0; i < results.tests[0].testresults.length; i++) { 
        // Loop through all results (eg. firefox and safari)
        for (var j = 0; j < results.tests[0].testresults[i].osresults.length; j++) {
            // display the array on the results page
            for (var k = 0; k < results.tests[0].testresults[i].ostests.length; k++) {
                var testContentHolder = $('div.' + results.tests[0].testresults[i].osId + '.' + results.tests[0].testresults[i].osresults[j].browserId);
                // If the content holder is empty then the test has to be shown
                // otherwise a repeat would occur
                if (testContentHolder.children().size() < results.tests[0].testresults[i].ostests.length ) {
                    template = '<div class="test_content_column_content">';
                    if (results.tests[0].testresults[i].osresults[j].browserresults[k].screenshot == '') {
                        // There is no screenshot, show OK of ERROR sign
                        if (results.tests[0].testresults[i].osresults[j].browserresults[k].success == 'true'){
                            template += '<img src="images/testok.png" alt="Test OK" title="Test OK" class="test_ok_check"></img>';
                        }
                        else {
                            template += '<img src="images/testerror.png" alt="Test Error" title="Test Error" class="test_error_cross"></img>';
                        }
                    }
                    else {
                        // There is a screenshot, display it
                        template += '<img src="http://10.0.0.49:8888/' + (results.tests[0].testresults[i].osresults[j].browserresults[k].screenshot).split('Sites/')[1] + '"';
                        // Check if the error is OK or ERROR
                        if (results.tests[0].testresults[i].osresults[j].browserresults[k].success == 'true'){
                            template += 'alt="OK screenshot" title="screenshot OK" class="ok_compare_img ' + results.tests[0].testresults[i].osId + ' ' + results.tests[0].testresults[i].osresults[j].browserId + ' ' + uidForEveryImage + '"></img>';
                        } else {
                            // Check if there is a reference screenshot available
                            if (results.tests[0].testresults[i].osresults[j].browserresults[k].reference == null) {
                                template += 'alt="Screenshot without reference" title="Screenshot without reference" class="no_reference_screenshot ' + results.tests[0].testresults[i].osId + ' ' + results.tests[0].testresults[i].osresults[j].browserId + ' ' + uidForEveryImage + '"></img>';
                            } else {
                                template += 'alt="Error in screenshot" title="Screenshot error"class="error_compare_img ' + results.tests[0].testresults[i].osId + ' ' + results.tests[0].testresults[i].osresults[j].browserId + ' ' + uidForEveryImage + '"></img>';
                            }
                        }
                    }
                    // Close testContentColumn div
                    template += '</div>';
                    testContentHolder.append(template);
                    // Put arbitrary data in the images tag
                    // The reference screenshot will be kept in this arbitrary data
                    var referenceData = results.tests[0].testresults[i].osresults[j].browserresults[k].reference;
                    var image = null;
                    image = $("img.error_compare_img." + results.tests[0].testresults[i].osId + "." + results.tests[0].testresults[i].osresults[j].browserId + '.' + uidForEveryImage);
                    image.data("reference", referenceData);
                }
                uidForEveryImage ++;
            }
        }
    }
};

/**
 {
    "tests": [
        {
            "testId": 1,
            "testresults": [
                {
                    "osId": "macosx",
                    "ostests": [
                        {
                            "testId": "1",
                            "description": "assertTrue(isTextPresent(\\\"News\\\"))",
                            "tooltip": "assertTrue"
                        },
                        {
                            "testId": "1",
                            "description": "assertTrue(isTextPresent(\\\"Page last updated\\\"))",
                            "tooltip": "assertTrue"
                        },
                        {
                            "testId": "1",
                            "description": "takeScreenshot",
                            "tooltip": "takeScreenshot"
                        }
                    ],
                    "osresults": [
                        {
                            "browserId": "firefox",
                            "browserresults": [
                                {
                                    "testId": "1",
                                    "screenshot": "",
                                    "reference": null,
                                    "success": "1"
                                },
                                {
                                    "testId": "1",
                                    "screenshot": "",
                                    "reference": null,
                                    "success": "1"
                                },
                                {
                                    "testId": "1",
                                    "screenshot": "\/Users\/hh354\/Sites\/php-src\/screenshots\/2010\/4\/26\/_Intel_Mac_OS_X_10_6\/Firefox\/merged\/merged0.png",
                                    "reference": null,
                                    "success": "1"
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ]
}
 * Get the results from the database
 * Above is an example of the JSON file that is returned
 */
var getResults = function(){
    //Make the ajax call
    $.ajax({
        //url: 'json/testresults.php',
        url: 'proxy/pollforresultsproxy.php',
        cache: false,
        dataType:"json",
        success: function(data){
            fillTableWithResults(data);
        },
        error: function(error){
            alert(error);
        },
        data : {
            "testid" : testId
        }
    });
};

/**
 * Create the table in which the data goes in
 * http://plugins.jquery.com/project/jquerytemplate for more information
 * @param {Object} results Results from the Ajax call containing information the user inputted on the page
 */
var createTable = function(results){
    // Create two variables to calculate the max width of the content page
    var highestNumberOfColumns = 0;
    var tempNumberOfColumns = 0;
    for (var i = 0; i < results.operatingsystems.length; i++){
        // Add the testContainer div
        template = '<div class="test_container">';
                // Add browser images on top of the list
        template += '<div class="browser_images_container">';
        for (var j = 0; j < results.operatingsystems[i].browsers.length; j++) {
            template += '<img src="images/browsers/' + results.operatingsystems[i].browsers[j].browserPic + '" class="browser_images ' + results.operatingsystems[i].osId + ' ' + results.operatingsystems[i].browsers[j].browserId + '" alt="' + results.operatingsystems[i].browsers[j].browserName + '" title="' + results.operatingsystems[i].osName + ' - '  + results.operatingsystems[i].browsers[j].browserName + '"/>';
        }
        // Close browserImagesContainer div
        template += '</div>';
        // Add a div that contains the clickable part of the header
        template += '<div class="test_header">';
        // Add a header for this testContainer
        template += '<h1>- ' + results.operatingsystems[i].osName + '</h1>';
        // Close the testContainer div
        template += '</div>';
        // Add testcontent
        template += '<div class="test_content ' + results.operatingsystems[i].osId + '">';
        // Add the description column first
        template += '<div class="description_column ' + results.operatingsystems[i].osId + '">';
        for (var k = 0; k < results.codeInput.length; k++) {
            template += "<div class='description_column_content'><p title='" + results.codeInput[k].tooltip + "'>" + (k+1) + '. ' + results.codeInput[k].codeName + '</p></div>';
        }
        template += '</div>';
        for (var l = 0; l < results.operatingsystems[i].browsers.length; l++) {
            template += '<div class="test_content_column ' + results.operatingsystems[i].osId + ' ' + results.operatingsystems[i].browsers[l].browserId + '">';
            // Close testContentColumn div
            template += '</div>';
            // Add an extra column to the counter
            tempNumberOfColumns ++;
        }
        template += '<hr/>';
        // Close testcontent
        template += '</div>';
        // Insert template into body
        $contentcontent.append($.template(template));
        // Set all contentcolumns equal to the description column
        // If a column is empty at least his height will be there and the dashed line on the right too
        $('.test_content_column').height($('.description_column').height());
        // Check if this is the last item to be put on stage
        // If it is set its bottom corners rounded
        if (i == results.operatingsystems.length -1){
            $("div.test_content." + results.operatingsystems[i].osId).corners("bottom 13px");
        }
        // Check if this OS has the highest number of tests on browsers
        // This means there are more columns and the width has to be wider
        if (highestNumberOfColumns < tempNumberOfColumns){
            highestNumberOfColumns = tempNumberOfColumns;
            tempNumberOfColumns = 0;
        }
    }
    // Calculate the width of the contentpage
    $(".test_container").width(25+136+20+(highestNumberOfColumns*110));
    // Request the results
    getResults();
    var id = setInterval(getResults, 5000);  
};

/**
 * Create the settings table on top of the page
 * The settings are received through an Ajax call
 * @param {Object} results Results from the Ajax call
 */
var CreateSettingsTable = function(results) {
    if (results.testId) {
        // Loop all browsers and check if they are ticked off
        // Display all in an overview
        var testvars = '<div class=test_container><div class="test_header" id="test_settings"><h1>+ Test settings</h1></div><div class="test_content" id="settings_div"><h2>Operating systems and browsers</h2>';
        testvars += '<ul>';
        for (var i = 0; i < results.operatingsystems.length; i++) {
            testvars += '<li class="os_test_list_item">' + results.operatingsystems[i].osName + '</li>';
            testvars += '<ul>';
            for (var j = 0; j < results.operatingsystems[i].browsers.length; j++) {
                var browser = results.operatingsystems[i].browsers[j].browserName;
                testvars += '<li class="browser_test_list_item">' + browser + '</li>';
            }
            testvars += '</ul>';
        }
        testvars += '</ul>';
        //
        // Website to check
        testvars += "<h2>Website to check</h2> <ul class=test_input><li><a href='" + results.url + "' target='_blank' title='Visit the website that's tested'>" + results.url + "</a></li></ul>";
        //
        // Description
        testvars += "<h2>Description</h2> <ul class=test_input><li>" + results.description + "</li></ul>";
        //
        // Test input
        testvars += "<h2>Test input</h2> <ol id='code_input_list'>";
        for (var l = 0; l < results.codeInput.length; l++) {
            testvars += "<li>" + results.codeInput[l].codeName + "</li>";
        }
        testvars += "</ol>";
        // Close the settingsDiv tag and then the settingsContainer tag
        testvars += "</div></div>";
        // Put everything on the screen
        $contentcontent.append($.template(testvars));
        
        // Add corners
        $testSettings = $('#test_settings');
        $settingsDiv = $('#settings_div');
        $testSettings.corners("top 13px");
        
        // Show hide testsettings
        $testSettings.click(function(){
            if ($settingsDiv.is(":hidden")) {
                $testSettings.html("<h1>- Test settings</h1>");
            }
            else {
                $testSettings.html("<h1>+ Test settings</h1>");
            }
            $settingsDiv.animate({
                height: "toggle",
                opacity: "toggle"
            });
        });
        
        // Create the result tables
        createTable(results);
    } else {
        // Place template to show search box to search for tests on ID
        var result = TrimPath.processDOMTemplate("search_test_template");
        $contentcontent.html(result);
    }
};

/**
 * {

    testId: "1"
    description: "test bbc"
    url: http://news.bbc.co.uk/
    codeInput: [
            {
                codeName: "AssertTrue"
                tooltip: "assertTrue(isTextPresent(\"News\"))"
            }
            {
                codeName: "AssertTrue"
                tooltip: "assertTrue(isTextPresent(\"Page last updated\"))"
            }
            {
                codeName: "TakeScreenshot"
                tooltip: "takeScreenshot"
            }
    ]
    operatingsystems: [
            {
                osName: "MAC OSX 10.6"
                osId: "macosx"
                osVersion: "",
                browsers: [
                        {
                            browserName: "Mozilla Firefox 3.5"
                            browserId: "firefox"
                            browserVersion: "3.5"
                            browserPic: "firefox-icon.png"
                        }
                        {
                            browserName: "Safari 4"
                            browserId: "safari"
                            browserVersion: "4"
                            browserPic: "safari-icon.png"
                        }
                  ]
            }
      ]
}
 * 
 * Get the test settings from the database
 * Above is an example of the JSON file that is returned
 */
var getSettings = function(){
    //Make the ajax call
    $.ajax({
        url: 'proxy/pollforsetupproxy.php',
        cache: false,
        dataType: "json",
        success: function(data){
            CreateSettingsTable(data);
        },
        error: function(error){
            alert(error.responseText);
        },
        data : {
            "testid" : testId
        }
    });
};

/**
 * Init function that gets the settings of the test or shows a search page
 */
var init = function(){
    getSettings(); 
};

init();