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
?>

<!-- Step 5: Include Simplified Lazy Load Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.classList.contains('lazyload')) {
                        img.src = img.dataset.src;
                        img.classList.remove("lazyload");
                        imageObserver.unobserve(img);
                    } else if (img.classList.contains('lazy-bg')) {
                        const bgImage = img.getAttribute("data-bg");
                        if (bgImage) {
                            img.style.backgroundImage = `url('${bgImage}')`;
                            img.classList.remove("lazy-bg");
                            imageObserver.unobserve(img);
                        }
                    }
                }
            });
        });

        // Observe all images with lazyload class
        document.querySelectorAll('.lazyload, .lazy-bg').forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers without IntersectionObserver support
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
    }
});

</script>
