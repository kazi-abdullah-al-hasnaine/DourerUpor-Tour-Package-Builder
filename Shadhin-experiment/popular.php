<?php
include "decoration.php";
include "DesignPatterns/pageDecorator.php";
include "DesignPatterns/imageProxy.php";

// Step 5: Page Implementations
$popularPage = new BasePage("Explore what's trending now! <br> with DourerUpor!");


// Step 6: Wrap Pages with Decorators

$decoratedPopularPage = 
        new FooterDecorator(
            new PopularSection($popularPage)
        );

// Render Pages

$decoratedPopularPage->render();

?>
