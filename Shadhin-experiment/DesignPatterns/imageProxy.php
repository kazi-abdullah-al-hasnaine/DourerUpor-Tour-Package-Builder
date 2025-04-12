<?php

// the Image Interface
interface Image {
    public function display();
    public function getUrl();
}

//  Real Image Class
class RealImage implements Image {
    private $filename;
    
    public function __construct($filename) {
        $this->filename = $filename;
        $this->loadImageFromDisk();
    }
    
    private function loadImageFromDisk() {
        echo "Loading image: " . $this->filename . "<br>";
    }
    
    public function display() {
        echo "<img data-src='" . $this->filename . "' class='lazyload' alt='Image'><br>";
    }
    
    public function getUrl() {
        return $this->filename;
    }
}

// Proxy Image Class (Lazy Loading)
class ProxyImage implements Image {
    private $realImage;
    private $filename;
    
    public function __construct($filename) {
        $this->filename = $filename;
    }
    
    public function display() {
        if ($this->realImage == null) {
            $this->realImage = new RealImage($this->filename);
        }
        $this->realImage->display();
    }
    
    public function getUrl() {
        return $this->filename;
    }
}

//  Usage in Different Modules
function displayModuleImage($imageName) {
    $image = new ProxyImage($imageName);
    $image->display();
}


function getLazyBackgroundImage($imageName) {
    return "data-bg='" . $imageName . "' class='lazy-bg'";
}


// Include the hero section module
// echo "<h3>Hero Section</h3>";
// include 'hero_section.php';

// // Example Usage in Different Modules
// echo "<h3>Popular Section</h3>";
// displayModuleImage("popular_image.jpg");

// echo "<h3>Explore Section</h3>";
// displayModuleImage("explore_image.jpg");

// echo "<h3>Build Packages Section</h3>";
// displayModuleImage("build_packages_image.jpg");

?>

<!-- Step 5: Include Simplified Lazy Load Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".lazyload").forEach(img => {
        img.src = img.dataset.src;
        img.classList.remove("lazyload");
    });

    document.querySelectorAll(".lazy-bg").forEach(bg => {
        const bgImage = bg.getAttribute("data-bg");
        if (bgImage) {
            bg.style.backgroundImage = `url('${bgImage}')`;
            bg.classList.remove("lazy-bg");
        }
    });
});

</script>
