<?php

session_start();
$_SESSION['current-page'] = 'explore';

// Database connection
require_once 'db_connection/db.php';


include "decoration.php";
include "DesignPatterns/pageDecorator.php";
include "DesignPatterns/imageProxy.php";
// Step 5: Page Implementations
$explorePage = new BasePage("Find your next destination <br> with DourerUpor!");


// Step 6: Wrap Pages with Decorators

$decoratedExplorePage =
    new FooterDecorator(
        new ExploreSection($explorePage, 12, "main-content")
    );

// Render Pages

$decoratedExplorePage->render();
