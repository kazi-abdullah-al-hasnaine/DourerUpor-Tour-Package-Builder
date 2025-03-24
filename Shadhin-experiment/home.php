<?php
include "decoration.php";
include "DesignPatterns/pageDecorator.php";
include "DesignPatterns/imageProxy.php";
// Step 5: Page Implementations
$homePage = new BasePage("<p>Home Page Content</p>");

// Step 6: Wrap Pages with Decorators
$decoratedHomePage = new HeaderDecorator(
        new PopularSection(
            new ExploreSection(
                new BuildPackagesSection(
                    new FooterDecorator($homePage)
                )
            )
        )
);

// Render Pages
$decoratedHomePage->render();
?>
