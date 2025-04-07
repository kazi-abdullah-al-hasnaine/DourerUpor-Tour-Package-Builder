<?php
if ($type == "section") {
    $sectionType = "small-section";
    $cardType = "small-card";
} else if ($type == "main-content") {
    $cardType = "large-card";
    $sectionType = "large-section";
}
?>

<section id="explore-section" class="<?php echo $sectionType; ?>">
    <div class="title-container">
        <h1>Cities to explore</h1>
    </div>
    <div class="explore-package-card-container">
        <div class="<?php echo $cardType; ?> exlpore-card lazy-bg" <?php echo getLazyBackgroundImage("img/rajshahi.jpg"); ?> style="background-repeat: no-repeat;
                                      background-size: cover; border-radius: 10px;">
            <div class="explore-wrapper">
                <h3>Rajshahi</h3>
                <p>4.8⭐ • Bangladesh</p>
                <div>
                    <button class="theme-btn explore-city-btn">Explore city</button>
                </div>
            </div>
        </div>
        <div class="<?php echo $cardType; ?> exlpore-card lazy-bg" <?php echo getLazyBackgroundImage("img/jaflong.jpg"); ?> style="background-repeat: no-repeat;
                                      background-size: cover; border-radius: 10px;">
            <div class="explore-wrapper">
                <h3>Sylhet</h3>
                <p>4.5⭐ • Bangladesh</p>
                <div>
                    <button class="theme-btn explore-city-btn">Explore city</button>
                </div>
            </div>
        </div>
        <div class="<?php echo $cardType; ?> exlpore-card lazy-bg" <?php echo getLazyBackgroundImage("img/cox.jpg"); ?> style="background-repeat: no-repeat;
                                      background-size: cover; border-radius: 10px;">
            <div class="explore-wrapper">
                <h3>Cox's Bazar</h3>
                <p>3.9⭐ • Bangladesh</p>
                <div>
                    <button class="theme-btn explore-city-btn">Explore city</button>
                </div>
            </div>
        </div>
        <div class="<?php echo $cardType; ?> exlpore-card lazy-bg" <?php echo getLazyBackgroundImage("img/ctg.jpg"); ?> style="background-repeat: no-repeat;
                                      background-size: cover; border-radius: 10px;">
            <div class="explore-wrapper">
                <h3>Chittagong</h3>
                <p>4.2⭐ • Bangladesh</p>
                <div>
                    <button class="theme-btn explore-city-btn">Explore city</button>
                </div>
            </div>
        </div>
    </div>
    <div class="btn-section">
        <button class="theme-btn view-more">view more</button>

    </div>


</section>