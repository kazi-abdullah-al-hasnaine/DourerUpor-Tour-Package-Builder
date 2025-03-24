<?php
include "decoration.php";
include "DesignPatterns/pageDecorator.php";
include "DesignPatterns/imageProxy.php";

// Step 5: Page Implementations
$popularPage = new BasePage("<p>Popular page Content</p>");


// Step 6: Wrap Pages with Decorators

$decoratedPopularPage = new HeaderDecorator(
        new PopularSection(
            new FooterDecorator($popularPage)
        )
);

// Render Pages

$decoratedPopularPage->render();

?>
