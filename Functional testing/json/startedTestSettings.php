<?php header('Content-type: application/json'); ?>
{
    "testId" : "0001",
    "description" : "Testdescription",
    "url" : "http://www.physx.be/",
    "codeInput" : [
                {
                    "codeName" : "AssertTrue",
                    "tooltip" : "assertTrue(isTextPresent('you can test your webpages'));"
                },
                {
                    "codeName" : "TakeScreenshot",
                    "tooltip" : "Take a screenshot"
                },
                {
                    "codeName" : "AssertTrue",
                    "tooltip" : "assertTrue(isTextPresent('on this page you can submit your test'));"
                }
            ],
    "operatingsystems" : [
        {
            "osName" : "Mac OSX",
            "osId" : "macosx",
            "browsers" : [
                {
                    "browserName" : "Firefox",
                    "browserId" : "firefox",
                    "browserPic" : "firefox.png"
                },
                {
                    "browserName" : "Safari",
                    "browserId" : "safari",
                    "browserPic" : "safari.png"
                }
            ]
        },
        {
            "osName" : "Windows 7",
            "osId" : "windowsseven",
            "browsers" : [
                {
                    "browserName" : "Firefox",
                    "browserId" : "firefox",
                    "browserPic" : "firefox.png"
                },
                {
                    "browserName" : "Safari",
                    "browserId" : "safari",
                    "browserPic" : "safari.png"
                },
                {
                    "browserName" : "IE6",
                    "browserId" : "ie6",
                    "browserPic" : "ie6.png"
                },
                {
                    "browserName" : "IE7",
                    "browserId" : "ie7",
                    "browserPic" : "ie7.png"
                },
                {
                    "browserName" : "IE8",
                    "browserId" : "ie8",
                    "browserPic" : "ie8.png"
                }
            ]
        }
    ]
}