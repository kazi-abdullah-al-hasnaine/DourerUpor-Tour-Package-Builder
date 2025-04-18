<?php
// Step 1: Define the Component Interface
interface PageComponent {
    public function render();
}

// Step 2: Concrete Base Page
class BasePage implements PageComponent {
    private $content;
    
    public function __construct($content) {
        $this->content = $content;
    }
    
    public function render() {
        echo $this->content;
    }
}

// Step 3: Decorators for Common Sections
class HeaderDecorator implements PageComponent {
    private $page;
    
    public function __construct(PageComponent $page) {
        $this->page = $page;
    }
    
    public function render() {
        include './modules/header.php';
        $this->page->render();
    }
}

class FooterDecorator implements PageComponent {
    private $page;
    
    public function __construct(PageComponent $page) {
        $this->page = $page;
    }
    
    public function render() {
        $this->page->render();
        include './modules/footer.php';
    }
}

// Step 4: Additional Decorators for Page Sections

class PopularSection implements PageComponent {
    private $page;
    
    public function __construct(PageComponent $page) {
        $this->page = $page;
    }
    
    public function render() {
        include './modules/popular.php';
        $this->page->render();
    }
}

class ExploreSection implements PageComponent {
    private $page;
    
    public function __construct(PageComponent $page) {
        $this->page = $page;
    }
    
    public function render() {
        include './modules/exploreCities.php';
        $this->page->render();
    }
}

class BuildPackagesSection implements PageComponent {
    private $page;
    
    public function __construct(PageComponent $page) {
        $this->page = $page;
    }
    
    public function render() {
        include './modules/buildPackages.php';
        $this->page->render();
    }
}