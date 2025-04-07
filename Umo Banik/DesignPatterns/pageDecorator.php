<?php
// Step 1: Define the Component Interface
interface PageComponent
{
    public function render();
}

// Step 2: Concrete Base Page
class BasePage implements PageComponent
{
    // private $content;
    private $heroTitle;

    public function __construct($heroTitle)
    {
        // $this->content = $content;
        $this->heroTitle = $heroTitle;
    }

    public function render()
    {
        $heroTitle = $this->heroTitle;
        include './modules/header.php';
        // echo $this->content;
    }
    public function getHeroTitle()
    {
        return $this->heroTitle;
    }
}

// Step 3: Decorators for Common Sections
// class HeaderDecorator implements PageComponent {
//     private $page;

//     public function __construct(PageComponent $page) {
//         $this->page = $page;
//     }

//     public function render() {
//         include './modules/header.php';
//         $this->page->render();
//     }
// }

class FooterDecorator implements PageComponent
{
    private $page;

    public function __construct(PageComponent $page)
    {
        $this->page = $page;
    }

    public function render()
    {
        $this->page->render();
        include './modules/footer.php';
    }
}

// Step 4: Additional Decorators for Page Sections

class PopularSection implements PageComponent
{
    private $page;

    public function __construct(PageComponent $page)
    {
        $this->page = $page;
    }

    public function render()
    {
        $this->page->render();
        include './modules/popular.php';
    }
}

class ExploreSection implements PageComponent
{
    private $page;
    private $limit;
    private $type;

    public function __construct(PageComponent $page, $limit, $type)
    {
        $this->limit = $limit;
        $this->type = $type;
        $this->page = $page;
    }

    public function render()
    {
        $limit = $this->limit;
        $type = $this->type;
        $this->page->render();
        include './modules/exploreCities.php';
    }
}

class BuildPackagesSection implements PageComponent
{
    private $page;

    public function __construct(PageComponent $page)
    {
        $this->page = $page;
    }

    public function render()
    {
        $this->page->render();
        include './modules/buildPackages.php';
    }
}
