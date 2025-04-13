document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('search-box');
    const searchBtn = document.querySelector('.search-btn');
    
    // Function to handle the search
    function performSearch() {
        const searchQuery = searchBox.value.trim();
        
        if (searchQuery.length > 0) {
            // Show loading indicator
            let resultsContainer = getOrCreateResultsContainer();
            resultsContainer.innerHTML = '<p class="loading">Searching...</p>';
            resultsContainer.style.display = 'block';
            
            // Create AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'modules/backend/search_process.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        // First check if the response starts with "<" which indicates HTML/error
                        if (this.responseText.trim().startsWith('<')) {
                            throw new Error('Received HTML instead of JSON');
                        }
                        
                        // Display search results
                        const results = JSON.parse(this.responseText);
                        displaySearchResults(results);
                    } catch (e) {
                        console.error('Error parsing JSON response:', e);
                        console.log('Raw response:', this.responseText.substring(0, 500));
                        
                        // Display user-friendly error message
                        let resultsContainer = getOrCreateResultsContainer();
                        resultsContainer.innerHTML = '<p class="error">Sorry, there was a problem with the search. Please try again later.</p>';
                        resultsContainer.style.display = 'block';
                    }
                } else {
                    // Handle HTTP errors
                    console.error('HTTP Error:', this.status);
                    let resultsContainer = getOrCreateResultsContainer();
                    resultsContainer.innerHTML = `<p class="error">Error: Could not complete the search (${this.status})</p>`;
                    resultsContainer.style.display = 'block';
                }
            };
            
            xhr.onerror = function() {
                console.error('Network error occurred');
                let resultsContainer = getOrCreateResultsContainer();
                resultsContainer.innerHTML = '<p class="error">Network error. Please check your connection and try again.</p>';
                resultsContainer.style.display = 'block';
            };
            
            xhr.send('query=' + encodeURIComponent(searchQuery));
        } else {
            // Hide results if search is empty
            const resultsContainer = document.querySelector('.search-results');
            if (resultsContainer) {
                resultsContainer.innerHTML = '';
                resultsContainer.style.display = 'none';
            }
        }
    }
    
    // Helper function to get or create results container
    function getOrCreateResultsContainer() {
        let resultsContainer = document.querySelector('.search-results');
        
        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.className = 'search-results';
            document.querySelector('.hero-search-bar').appendChild(resultsContainer);
        }
        
        return resultsContainer;
    }
    
    // Function to display search results
    function displaySearchResults(results) {
        // Get the container for search results
        let resultsContainer = getOrCreateResultsContainer();
        
        // Clear previous results
        resultsContainer.innerHTML = '';
        
        if (results.error) {
            resultsContainer.innerHTML = `<p class="error">Error: ${results.error}</p>`;
            resultsContainer.style.display = 'block';
            return;
        }
        
        if (results.length === 0) {
            resultsContainer.innerHTML = '<p class="no-results">No packages found. Try a different search term.</p>';
            resultsContainer.style.display = 'block';
            return;
        }
        
        // Add results to container
        results.forEach(result => {
            // Create star rating HTML
            let starsHtml = '';
            const rating = result.avg_rating || 0;
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(rating)) {
                    starsHtml += '<span class="star filled">★</span>';
                } else if (i - 0.5 <= rating) {
                    starsHtml += '<span class="star half-filled">★</span>';
                } else {
                    starsHtml += '<span class="star">☆</span>';
                }
            }
            
            // Create result item
            const resultItem = document.createElement('div');
            resultItem.className = 'result-item';
            resultItem.innerHTML = `
                <a href="package.php?id=${result.package_id}">
                    <div class="result-image">
                        <img src="img/package-cover/${result.image}" alt="${result.name}" onerror="this.src='img/unknown.jpg'">
                    </div>
                    <div class="result-info">
                        <h4>${result.name}</h4>
                        <div class="result-destinations">
                            <span>${result.destinations}</span>
                        </div>
                        <div class="result-rating">
                            ${starsHtml}
                            <span class="rating-count">(${result.review_count || 0})</span>
                        </div>
                        <p>${result.description || 'No description available'}</p>
                    </div>
                </a>
            `;
            resultsContainer.appendChild(resultItem);
        });
        
        resultsContainer.style.display = 'block';
    }
    
    // Event listeners
    searchBtn.addEventListener('click', function(e) {
        e.preventDefault();
        performSearch();
    });
    
    // Search on enter key press
    searchBox.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
    
    // Live search as user types (with debounce)
    let typingTimer;
    searchBox.addEventListener('input', function() {
        clearTimeout(typingTimer);
        if (searchBox.value.trim().length > 2) {
            typingTimer = setTimeout(performSearch, 500);
        } else {
            // Hide results if search is less than 3 characters
            const resultsContainer = document.querySelector('.search-results');
            if (resultsContainer) {
                resultsContainer.innerHTML = '';
                resultsContainer.style.display = 'none';
            }
        }
    });
    
    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        const resultsContainer = document.querySelector('.search-results');
        const searchArea = document.querySelector('.hero-search-bar');
        
        if (resultsContainer && !searchArea.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
    });
});