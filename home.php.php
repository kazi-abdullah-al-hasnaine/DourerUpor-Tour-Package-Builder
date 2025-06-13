<?php
session_start();
$_SESSION['current-page'] = 'home';

if(isset($_SESSION['admin'])){
    unset($_SESSION['admin']);
}

// Database connection
require_once 'db_connection/db.php';

include "decoration.php";
include "DesignPatterns/pageDecorator.php";
include "DesignPatterns/imageProxy.php";
// Step 5: Page Implementations
$homePage = new BasePage("Build your path, customize every path <br>
and explore the extraordinary");

// Step 6: Wrap Pages with Decorators
$decoratedHomePage = new FooterDecorator(
    new BuildPackagesSection(
        new ExploreSection(
            new PopularSection($homePage),
            4,
            "section"
        )
    )
);


// Render Pages
$decoratedHomePage->render();
