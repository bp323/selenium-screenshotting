<?php header('Content-type: application/json'); ?>
{
    "tests": [
        {
            "testId" : "0001",
            "testresults" : [
                {
                    "os" : "Mac OSX",
                    "osId" : "macosx",
                    "ostests" : [
                                {
                                    "testId" : "0001",
                                    "description" : "AssertTrue",
                                    "tooltip" : "assertTrue(isTextPresent('you can test your webpages'));"
                                },
                                {
                                    "testId" : "0002",
                                    "description" : "TakeScreenshot",
                                    "tooltip" : "Take a screenshot"
                                },
                                {
                                    "testId" : "0002",
                                    "description" : "AssertTrue",
                                    "tooltip" : "assertTrue(isTextPresent('on this page you can submit your test'));"
                                }
                            ],
                    "osresults" : [
                        {
                            "browserId" : "firefox",
                            "browserName" : "Firefox 3.6",
                            "browserpic" : "firefox.png",
                            "browserresults" : [
                                {
                                    "testId" : "0001",
                                    "screenshot" : "null",
                                    "success" : "true"
                                },
                                {
                                    "testId" : "0002",
                                    "screenshot" : "screenshot.png",
                                    "success" : "false"
                                },
                                {
                                    "testId" : "0003",
                                    "screenshot" : "null",
                                    "success" : "true"
                                }
                            ]
                        },
                         {
                            "browserId" : "safari",
                            "browserName" : "Safari",
                            "browserpic" : "safari.png",
                            "browserresults" : [
                                {
                                    "testId" : "0001",
                                    "screenshot" : "null",
                                    "tooltip" : "assertTrue(isTextPresent('you can test your webpages'));"
                                },
                                {
                                    "testId" : "0002",
                                    "screenshot" : "screenshot.png",
                                    "tooltip" : "Take a screenshot"
                                },
                                {
                                    "testId" : "0003",
                                    "screenshot" : "null",
                                    "tooltip" : "assertTrue(isTextPresent('you can test your webpages'));"
                                }
                            ]
                        }
                    ]
                },
                {
                    "os" : "Windows 7",
                    "osId" : "windowsseven",
                    "ostests" : [
                                {
                                    "testId" : "0001",
                                    "screenshot" : "null",
                                    "success" : "false"
                                },
                                {
                                    "testId" : "0002",
                                    "screenshot" : "screenshot.png",
                                    "success" : "false"
                                },
                                {
                                    "testId" : "0003",
                                    "screenshot" : "null",
                                    "success" : "false"
                                }
                            ],
                    "osresults" : [
                        {
                            "browserId" : "firefox",
                            "browserName" : "Firefox 3.6",
                            "browserpic" : "firefox.png",
                            "browserresults" : [
                                {
                                    "testId" : "0001",
                                    "screenshot" : "null",
                                    "success" : "false"
                                },
                                {
                                    "testId" : "0002",
                                    "screenshot" : "screenshot.png",
                                    "success" : "false"
                                },
                                {
                                    "testId" : "0003",
                                    "screenshot" : "null",
                                    "success" : "false"
                                }
                            ]
                        },
                        {
                            "browserId" : "safari",
                            "browserName" : "Safari",
                            "browserpic" : "safari.png",
                            "browserresults" : [
                                {
                                    "testId" : "0001",
                                    "screenshot" : "null",
                                    "success" : "false"
                                },
                                {
                                    "testId" : "0002",
                                    "screenshot" : "screenshot.png",
                                    "success" : "false"
                                },
                                {
                                    "testId" : "0003",
                                    "screenshot" : "null",
                                    "success" : "false"
                                }
                            ]
                        },
                        {
                            "browserId" : "ie6",
                            "browserName" : "Internet Explorer 6",
                            "browserpic" : "ie6.png",
                            "browserresults" : [
                                {
                                    "testId" : "0001",
                                    "screenshot" : "null",
                                    "success" : "true"
                                },
                                {
                                    "testId" : "0002",
                                    "screenshot" : "screenshot.png",
                                    "success" : "true"
                                },
                                {
                                    "testId" : "0003",
                                    "screenshot" : "null",
                                    "success" : "true"
                                }
                            ]
                        },
                        {
                            "browserId" : "ie7",
                            "browserName" : "Internet Explorer 7",
                            "browserpic" : "ie7.png",
                            "browserresults" : [
                                {
                                    "testId" : "0001",
                                    "screenshot" : "null",
                                    "success" : "true"
                                },
                                {
                                    "testId" : "0002",
                                    "screenshot" : "screenshot.png",
                                    "success" : "false"
                                },
                                {
                                    "testId" : "0003",
                                    "screenshot" : "null",
                                    "success" : "true"
                                }
                            ]
                        },
                        {
                            "browserId" : "ie8",
                            "browserName" : "Internet Explorer 8",
                            "browserpic" : "ie8.png",
                            "browserresults" : [
                                {
                                    "testId" : "0001",
                                    "screenshot" : "null",
                                    "success" : "true"
                                },
                                {
                                    "testId" : "0002",
                                    "screenshot" : "screenshot.png",
                                    "success" : "true"
                                },
                                {
                                    "testId" : "0003",
                                    "screenshot" : "null",
                                    "success" : "true"
                                }
                            ]
                        }
                    ]
                }     
            ]
        }
    ]
}