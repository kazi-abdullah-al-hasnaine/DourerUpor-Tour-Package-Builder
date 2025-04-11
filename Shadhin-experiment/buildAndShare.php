<?php
session_start();
$_SESSION['current-page'] = 'buildAndShare';

// Database connection
require_once 'db_connection/db.php';

include "decoration.php";
include "DesignPatterns/pageDecorator.php";
include "DesignPatterns/imageProxy.php";
// Step 5: Page Implementations
$homePage = new BasePage("It's your time to share <br>
your experiance");

// Step 6: Wrap Pages with Decorators
$decoratedPackageBuildPage = new FooterDecorator(
    new BuildPackagesForm($homePage)
);


// Render Pages
$decoratedPackageBuildPage->render();