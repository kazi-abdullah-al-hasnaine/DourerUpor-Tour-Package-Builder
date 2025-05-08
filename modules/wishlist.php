<!-- ----For wishlist sidebar ----- -->


    <div id="wishlist-sidebar" class="wishlist-sidebar">
        <button id="close-wishlist" class="close-btn">&times;</button>
        <section id="wishlist-container">
            <div class="wishlist-card">
                <h3 class="package-name card-item">Trip to Rajshahi</h3>
                    <div class="card-item">
                        <button class="theme-btn info-btn">Rajshahi</button>
                        <button disabled class="theme-btn info-btn">2 day/s</button>
                        <button disabled class="theme-btn info-btn">4.1⭐</button>
                        <button disabled class="theme-btn info-btn">5💬</button>
                        <button disabled class="theme-btn info-btn offer">Save 100$</button>
                    </div>
                    <p class="card-item package-brief">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue.</p>
                    <div class="card-item">
                        <button class="theme-btn package-explore-btn">Explore</button>
                        <button class="theme-btn package-explore-btn remove-btn">Remove</button>
                    </div>
            </div>
            <div class="wishlist-card">
                <h3 class="package-name card-item">Trip to Sylhet</h3>
                    <div class="card-item">
                        <button class="theme-btn info-btn">Rajshahi</button>
                        <button disabled class="theme-btn info-btn">2 day/s</button>
                        <button disabled class="theme-btn info-btn">4.1⭐</button>
                        <button disabled class="theme-btn info-btn">5💬</button>
                        <button disabled class="theme-btn info-btn offer">Save 100$</button>
                    </div>
                    <p class="card-item package-brief">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue.</p>
                    <div class="card-item">
                        <button class="theme-btn package-explore-btn">Explore</button>
                        <button class="theme-btn package-explore-btn remove-btn">Remove</button>
                    </div>
            </div>
        </section>

    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const wishlistButton = document.querySelector("li:nth-child(5) a");
        const wishlistSidebar = document.getElementById("wishlist-sidebar");
        const closeWishlist = document.getElementById("close-wishlist");

        if (wishlistButton && wishlistSidebar && closeWishlist) {
            wishlistButton.addEventListener("click", function(event) {
                event.preventDefault();
                wishlistSidebar.classList.add("active-sidebar");
            });

            closeWishlist.addEventListener("click", function() {
                wishlistSidebar.classList.remove("active-sidebar");
            });
        }
    });
</script>