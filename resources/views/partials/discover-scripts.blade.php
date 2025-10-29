{{-- 
    PART 4: JAVASCRIPT
    Save this as: resources/views/partials/discover-scripts.blade.php
--}}

<script>
(function() {
    'use strict';
    
    // ========== AUTOCOMPLETE ==========
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const autocompleteEl = document.getElementById('autocomplete');
    const loadingEl = document.getElementById('search-loading');
    let debounceTimer;
    
    if (searchInput && autocompleteEl) {
        
        // Fetch suggestions
        async function fetchSuggestions(query) {
            if (query.length < 2) {
                hideAutocomplete();
                return;
            }
            
            showLoading();
            
            try {
                const response = await fetch(`{{ route('api.search.suggest') }}?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data.error) {
                    showError(data.error);
                } else if (data.length > 0) {
                    showSuggestions(data);
                } else {
                    showEmpty();
                }
            } catch (error) {
                console.error('Autocomplete error:', error);
                showError('Unable to load suggestions');
            } finally {
                hideLoading();
            }
        }
        
        // Show suggestions
        function showSuggestions(suggestions) {
            autocompleteEl.innerHTML = '';
            
            suggestions.slice(0, 8).forEach((movie, index) => {
                const title = movie.title || 'Unknown';
                const year = movie.releaseYear || movie.firstAirYear || '';
                const posterUrl = movie.imageSet?.verticalPoster?.w240 || '';
                
                const item = document.createElement('a');
                item.href = `{{ route('discover') }}?search=${encodeURIComponent(title)}&country={{ $selectedCountry }}`;
                item.className = 'autocomplete-item';
                
                item.innerHTML = `
                    ${posterUrl 
                        ? `<img src="${posterUrl}" alt="${title}" class="autocomplete-poster">`
                        : '<div class="autocomplete-poster-placeholder"></div>'
                    }
                    <div class="autocomplete-info">
                        <div class="autocomplete-title">${escapeHtml(title)}</div>
                        ${year ? `<div class="autocomplete-year">${year}</div>` : ''}
                    </div>
                    <svg style="width: 1.25rem; height: 1.25rem; color: var(--gray-400); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                `;
                
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    searchInput.value = title;
                    hideAutocomplete();
                    searchForm.submit();
                });
                
                autocompleteEl.appendChild(item);
            });
            
            showAutocomplete();
        }
        
        // Show empty state
        function showEmpty() {
            autocompleteEl.innerHTML = '<div class="autocomplete-empty">No matches found</div>';
            showAutocomplete();
        }
        
        // Show error
        function showError(message) {
            autocompleteEl.innerHTML = `<div class="autocomplete-empty" style="color: var(--danger);">${escapeHtml(message)}</div>`;
            showAutocomplete();
        }
        
        // Show/hide helpers
        function showAutocomplete() {
            autocompleteEl.classList.add('active');
        }
        
        function hideAutocomplete() {
            autocompleteEl.classList.remove('active');
        }
        
        function showLoading() {
            if (loadingEl) loadingEl.classList.add('active');
        }
        
        function hideLoading() {
            if (loadingEl) loadingEl.classList.remove('active');
        }
        
        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Event listeners
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            debounceTimer = setTimeout(() => fetchSuggestions(query), 250);
        });
        
        searchInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2 && autocompleteEl.children.length > 0) {
                showAutocomplete();
            }
        });
        
        // Click outside to close
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !autocompleteEl.contains(e.target)) {
                hideAutocomplete();
            }
        });
    }
    
    // ========== CAROUSEL ==========
    const carouselButtons = document.querySelectorAll('.carousel-nav');
    
    carouselButtons.forEach(button => {
        button.addEventListener('click', function() {
            const carouselId = this.dataset.carousel;
            const direction = this.dataset.direction;
            const carousel = document.getElementById(carouselId);
            
            if (!carousel) return;
            
            // Calculate scroll amount: 5 movies × (180px width + 16px gap)
            const scrollAmount = 5 * (180 + 16);
            
            const currentScroll = carousel.scrollLeft;
            const newScroll = direction === 'next' 
                ? currentScroll + scrollAmount 
                : currentScroll - scrollAmount;
            
            // Smooth scroll
            carousel.scrollTo({
                left: newScroll,
                behavior: 'smooth'
            });
            
            // Update button states after scroll
            setTimeout(() => updateCarouselButtons(carousel), 300);
        });
    });
    
    // Update button states (enable/disable)
    function updateCarouselButtons(carousel) {
        const wrapper = carousel.closest('.carousel-wrapper');
        if (!wrapper) return;
        
        const carouselId = carousel.id;
        const prevBtn = wrapper.querySelector(`.carousel-nav-prev[data-carousel="${carouselId}"]`);
        const nextBtn = wrapper.querySelector(`.carousel-nav-next[data-carousel="${carouselId}"]`);
        
        if (!prevBtn || !nextBtn) return;
        
        const maxScroll = carousel.scrollWidth - carousel.clientWidth;
        const currentScroll = carousel.scrollLeft;
        
        // Disable prev button at start
        prevBtn.disabled = currentScroll <= 10;
        
        // Disable next button at end
        nextBtn.disabled = currentScroll >= maxScroll - 10;
    }
    
    // Initialize all carousels
    document.querySelectorAll('.carousel-track').forEach(carousel => {
        updateCarouselButtons(carousel);
        
        // Update on scroll
        carousel.addEventListener('scroll', function() {
            updateCarouselButtons(this);
        });
    });
    
    // ========== SMOOTH SCROLL FOR COUNTRY LINKS ==========
    document.querySelectorAll('a[href^="#country-"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetEl = document.getElementById(targetId);
            
            if (targetEl) {
                const offset = 100; // Offset for fixed header
                const targetPosition = targetEl.getBoundingClientRect().top + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // ========== LAZY LOAD IMAGES ==========
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
})();
</script>