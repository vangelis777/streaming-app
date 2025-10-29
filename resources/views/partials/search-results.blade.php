{{-- 
    UPDATED PART 3: SEARCH RESULTS VIEW (with grouped services)
    Save this as: resources/views/partials/search-results.blade.php
--}}

<style>
/* Updated styles for grouped service cards */
.service-card {
    padding: var(--spacing-lg);
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-xl);
    transition: all var(--transition-base);
    display: block;
}

.service-card:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-xl);
    transform: translateY(-2px);
}

.service-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-md);
    border-bottom: 1px solid var(--gray-200);
}

.service-logo {
    height: 36px;
    object-fit: contain;
}

/* Service options container */
.service-options {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.service-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--gray-50);
    border-radius: var(--radius-md);
    transition: background var(--transition-fast);
}

.service-option:hover {
    background: var(--gray-100);
}

.service-option-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex: 1;
}

.service-type-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    box-shadow: var(--shadow-sm);
    flex-shrink: 0;
}

.service-type-subscription {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.service-type-rent {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: black;
}

.service-type-buy {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.service-type-free {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.service-type-addon {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

.service-quality {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-600);
    font-size: 0.875rem;
    font-weight: 500;
}

.service-quality svg {
    width: 1rem;
    height: 1rem;
}

.external-link-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: var(--gray-400);
    flex-shrink: 0;
    transition: color var(--transition-fast);
}

.service-option:hover .external-link-icon {
    color: var(--primary);
}

/* Other existing styles remain the same */
.search-results-container {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.movie-hero {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.movie-backdrop {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.movie-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, 
        white 0%, 
        rgba(255, 255, 255, 0.7) 70%, 
        transparent 100%);
}

.movie-hero-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: var(--spacing-xl);
}

.movie-hero-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    gap: var(--spacing-lg);
    align-items: flex-end;
}

.movie-hero-poster {
    width: 224px;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-2xl);
    border: 4px solid white;
}

.movie-hero-info {
    flex: 1;
}

.movie-main-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: var(--spacing-sm);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.movie-meta {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.movie-year {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--gray-700);
}

.movie-rating-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #fbbf24;
    color: black;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-weight: 700;
    box-shadow: var(--shadow-lg);
}

.movie-genre-tag {
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(8px);
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-800);
    box-shadow: var(--shadow-md);
}

.movie-simple-header {
    padding: var(--spacing-xl);
}

.movie-simple-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    gap: var(--spacing-lg);
}

.movie-simple-poster {
    width: 288px;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-2xl);
}

.movie-simple-info {
    flex: 1;
}

.movie-overview {
    font-size: 1.125rem;
    line-height: 1.7;
    color: var(--gray-700);
    margin-top: var(--spacing-md);
}

.streaming-section {
    padding: var(--spacing-xl);
    background: var(--gray-50);
}

.streaming-inner {
    max-width: 1200px;
    margin: 0 auto;
}

.availability-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-lg);
}

.country-jump {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
    padding: var(--spacing-lg);
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    margin-bottom: var(--spacing-xl);
    align-items: center;
}

.country-jump-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
}

.country-jump-flag {
    position: relative;
    transition: transform var(--transition-base);
}

.country-jump-flag:hover {
    transform: scale(1.1);
}

.country-jump-flag img {
    width: 36px;
    height: 27px;
    object-fit: cover;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    border: 2px solid var(--gray-200);
    transition: border-color var(--transition-fast);
}

.country-jump-flag:hover img {
    border-color: var(--primary);
}

.country-jump-tooltip {
    position: absolute;
    bottom: -2.5rem;
    left: 50%;
    transform: translateX(-50%);
    background: var(--gray-900);
    color: white;
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-sm);
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity var(--transition-fast);
    font-weight: 500;
}

.country-jump-flag:hover .country-jump-tooltip {
    opacity: 1;
}

.country-section {
    margin-bottom: var(--spacing-xl);
    scroll-margin-top: 6rem;
}

.country-section-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
}

.country-section-flag {
    width: 36px;
    height: 27px;
    object-fit: cover;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
}

.country-section-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.country-section-count {
    margin-left: auto;
    padding: 0.375rem 0.75rem;
    background: rgba(79, 70, 229, 0.1);
    color: var(--primary);
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}

.service-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--spacing-md);
}

.no-results {
    text-align: center;
    padding: 4rem var(--spacing-xl);
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
}

.no-results svg {
    width: 6rem;
    height: 6rem;
    color: var(--gray-300);
    margin: 0 auto var(--spacing-lg);
}

.no-results h3 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-sm);
}

.no-results p {
    font-size: 1.25rem;
    color: var(--gray-600);
    margin-bottom: var(--spacing-lg);
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: var(--primary);
    color: white;
    border-radius: var(--radius-lg);
    font-weight: 600;
    box-shadow: var(--shadow-lg);
    transition: all var(--transition-base);
}

.back-button:hover {
    background: var(--primary-dark);
    box-shadow: var(--shadow-xl);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .movie-main-title {
        font-size: 2rem;
    }
    
    .movie-hero-inner {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .movie-hero-poster {
        width: 180px;
    }
    
    .service-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@if ($searchResult && !isset($searchResult['error']))
    @php
        $movie = $searchResult;
        $title = $movie['title'] ?? 'Title not found';
        $year = $movie['releaseYear'] ?? $movie['firstAirYear'] ?? 'N/A';
        $overview = $movie['overview'] ?? 'No overview available.';
        $posterUrl = $movie['imageSet']['verticalPoster']['w360'] ?? $movie['imageSet']['verticalPoster']['w240'] ?? 'https://via.placeholder.com/300x450/333/fff?text=' . urlencode($title);
        $backdropUrl = $movie['imageSet']['horizontalBackdrop']['w1440'] ?? null;
        $services = $movie['streamingOptions'] ?? [];
        $countryCount = count($services);
        $rating = $movie['rating'] ?? null;
        $genres = $movie['genres'] ?? [];
        
        function getCountryName($code, $fallbackList) {
            $code = strtoupper($code);
            if (function_exists('locale_get_display_region')) {
                $name = \locale_get_display_region('-' . $code, 'en');
                if ($name && $name !== $code) return $name;
            }
            return $fallbackList[$code] ?? $code;
        }
        
        $countryNames = [
            'US' => 'United States', 'GB' => 'United Kingdom', 'CA' => 'Canada', 
            'AU' => 'Australia', 'DE' => 'Germany', 'FR' => 'France', 'ES' => 'Spain',
            'IT' => 'Italy', 'JP' => 'Japan', 'BR' => 'Brazil', 'MX' => 'Mexico',
            'IN' => 'India', 'KR' => 'South Korea', 'NL' => 'Netherlands', 
            'SE' => 'Sweden', 'NO' => 'Norway', 'DK' => 'Denmark', 'FI' => 'Finland',
            'GR' => 'Greece', 'TR' => 'Turkey', 'PL' => 'Poland'
        ];
        
        // Group services by provider
        function groupServicesByProvider($options) {
            $grouped = [];
            foreach ($options as $option) {
                $serviceName = $option['service']['name'] ?? 'Unknown';
                $serviceId = $option['service']['id'] ?? $serviceName;
                
                if (!isset($grouped[$serviceId])) {
                    $grouped[$serviceId] = [
                        'name' => $serviceName,
                        'logo' => $option['service']['imageSet']['lightThemeImage'] ?? '',
                        'options' => []
                    ];
                }
                
                $grouped[$serviceId]['options'][] = $option;
            }
            return $grouped;
        }
    @endphp
    
    <div class="search-results-container">
        {{-- Hero Section --}}
        @if ($backdropUrl)
            <div class="movie-hero">
                <img src="{{ $backdropUrl }}" alt="{{ $title }}" class="movie-backdrop">
                <div class="movie-hero-overlay"></div>
                <div class="movie-hero-content">
                    <div class="movie-hero-inner">
                        <img src="{{ $posterUrl }}" alt="{{ $title }}" class="movie-hero-poster">
                        <div class="movie-hero-info">
                            <h1 class="movie-main-title">{{ $title }}</h1>
                            <div class="movie-meta">
                                <span class="movie-year">{{ $year }}</span>
                                @if ($rating)
                                    <div class="movie-rating-badge">
                                        <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        {{ number_format($rating / 10, 1) }}/10
                                    </div>
                                @endif
                                @foreach (array_slice($genres, 0, 3) as $genre)
                                    <span class="movie-genre-tag">{{ $genre['name'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="movie-simple-header">
                <div class="movie-simple-content">
                    <img src="{{ $posterUrl }}" alt="{{ $title }}" class="movie-simple-poster">
                    <div class="movie-simple-info">
                        <h1 class="movie-main-title">{{ $title }}</h1>
                        <div class="movie-meta">
                            <span class="movie-year">{{ $year }}</span>
                            @if ($rating)
                                <div class="movie-rating-badge">
                                    <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    {{ number_format($rating / 10, 1) }}
                                </div>
                            @endif
                        </div>
                        <p class="movie-overview">{{ $overview }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Streaming Availability --}}
        <div class="streaming-section">
            <div class="streaming-inner">
                <h2 class="availability-title">🌍 Available in {{ $countryCount }} {{ Str::plural('Country', $countryCount) }}</h2>

                @if ($countryCount > 0)
                    <div class="country-jump">
                        <span class="country-jump-label">Quick jump:</span>
                        @foreach (array_keys($services) as $countryCode)
                            <a href="#country-{{ $countryCode }}" class="country-jump-flag">
                                <img src="https://flagcdn.com/w40/{{ strtolower($countryCode) }}.png" 
                                     alt="{{ $countryCode }}">
                                <span class="country-jump-tooltip">{{ getCountryName($countryCode, $countryNames) }}</span>
                            </a>
                        @endforeach
                    </div>

                    @foreach ($services as $countryCode => $options)
                        @php 
                            $countryName = getCountryName($countryCode, $countryNames);
                            $groupedServices = groupServicesByProvider($options);
                        @endphp
                        
                        <div class="country-section" id="country-{{ $countryCode }}">
                            <div class="country-section-header">
                                <img src="https://flagcdn.com/w40/{{ strtolower($countryCode) }}.png" 
                                     alt="{{ $countryCode }}" 
                                     class="country-section-flag">
                                <h3 class="country-section-name">{{ $countryName }}</h3>
                                <span class="country-section-count">
                                    {{ count($groupedServices) }} {{ Str::plural('service', count($groupedServices)) }}
                                </span>
                            </div>

                            <div class="service-grid">
                                @foreach ($groupedServices as $serviceData)
                                    <div class="service-card">
                                        <div class="service-card-header">
                                            <img src="{{ $serviceData['logo'] ?: 'https://via.placeholder.com/100x40/333/fff?text=' . urlencode($serviceData['name']) }}" 
                                                 alt="{{ $serviceData['name'] }}" 
                                                 class="service-logo">
                                        </div>

                                        <div class="service-options">
                                            @foreach ($serviceData['options'] as $option)
                                                @php
                                                    $type = $option['type'] ?? 'N/A';
                                                    $quality = $option['quality'] ?? '';
                                                    $link = $option['link'] ?? '#';
                                                @endphp
                                                
                                                <a href="{{ $link }}" target="_blank" rel="noopener" class="service-option">
                                                    <div class="service-option-info">
                                                        <span class="service-type-badge service-type-{{ $type }}">
                                                            {{ ucfirst($type) }}
                                                        </span>
                                                        
                                                        @if ($quality)
                                                            <div class="service-quality">
                                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                                </svg>
                                                                <span>{{ $quality }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <svg class="external-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center" style="padding: 4rem; background: white; border-radius: var(--radius-xl);">
                        <p style="color: var(--gray-500); font-size: 1.125rem;">Not available for streaming</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

@else
    {{-- No Results --}}
    <div class="no-results">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h3>No results found</h3>
        <p>We couldn't find "{{ $searchTerm }}"</p>
        <a href="{{ route('discover', ['country' => $selectedCountry]) }}" class="back-button">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back to Browse</span>
        </a>
    </div>
@endif