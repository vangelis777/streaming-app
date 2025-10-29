{{-- 
    PART 2: BROWSE VIEW (Carousels)
    This shows when no search is active
--}}

<x-app-layout>
    {{-- Include custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/discover.css') }}">

    <x-slot name="header">
        <div class="discover-header">
            <h2 class="discover-title">Discover Movies & Shows</h2>
            
            @if (!$searchTerm)
                <div class="country-selector" x-data="{ open: false }">
                    <button @click="open = !open" class="country-button">
                        <img src="https://flagcdn.com/w40/{{ strtolower($selectedCountry) }}.png" 
                             alt="{{ $countries[$selectedCountry] }}" 
                             class="country-flag">
                        <span>{{ $countries[$selectedCountry] }}</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div class="country-dropdown" :class="{ 'active': open }" @click.away="open = false">
                        @foreach ($countries as $code => $name)
                            <a href="{{ route('discover', ['country' => $code]) }}" 
                               class="country-option {{ $code === $selectedCountry ? 'selected' : '' }}">
                                <img src="https://flagcdn.com/w40/{{ strtolower($code) }}.png" 
                                     alt="{{ $name }}" 
                                     class="country-flag">
                                <span class="country-option-name">{{ $name }}</span>
                                @if ($code === $selectedCountry)
                                    <svg fill="currentColor" viewBox="0 0 20 20" style="width: 1.25rem; height: 1.25rem; margin-left: auto; color: var(--primary);">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="discover-container">
        <div class="discover-wrapper">
            
            {{-- Hero Search Section --}}
            <div class="hero-search {{ $searchTerm ? 'compact' : '' }}">
                <div class="hero-content">
                    <form method="GET" action="{{ route('discover') }}" id="search-form">
                        @if (!$searchTerm)
                            <h1 class="hero-title">Find Where to Watch</h1>
                            <p class="hero-subtitle">Search thousands of movies and shows across all streaming services</p>
                        @endif
                        
                        <div class="search-box">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            
                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                class="search-input"
                                placeholder="Search for movies or TV shows..."
                                value="{{ $searchTerm ?? '' }}"
                                autocomplete="off"
                            >
                            
                            <div id="search-loading" class="search-loading">
                                <div class="spinner"></div>
                            </div>
                            
                            <div id="autocomplete" class="autocomplete-results">
                                <!-- JavaScript will populate this -->
                            </div>
                        </div>
                        
                        @if ($searchTerm)
                            <a href="{{ route('discover', ['country' => $selectedCountry]) }}" class="clear-search">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>Clear search & browse all</span>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Content Area --}}
            <div id="content-area">
                
                @if (!$searchTerm)
                    {{-- BROWSE MODE: Carousels --}}
                    @forelse ($serviceFeeds as $serviceName => $feed)
                        <section class="carousel-section">
                            <div class="carousel-card">
                                <div class="carousel-header">
                                    <h2 class="carousel-title">Popular on {{ $serviceName }}</h2>
                                </div>

                                @if (!empty($feed['shows']))
                                    <div class="carousel-container">
                                        <div class="carousel-wrapper">
                                            <div class="carousel-track" id="carousel-{{ $loop->index }}">
                                                @foreach ($feed['shows'] as $movie)
                                                    @php
                                                        $title = $movie['title'] ?? 'Untitled';
                                                        $year = $movie['releaseYear'] ?? $movie['firstAirYear'] ?? '';
                                                        $posterUrl = $movie['imageSet']['verticalPoster']['w240'] ?? 'https://via.placeholder.com/180x270/333/fff?text=' . urlencode($title);
                                                        $searchLink = route('discover', ['search' => $title, 'country' => $selectedCountry]);
                                                        $rating = $movie['rating'] ?? null;
                                                    @endphp
                                                    
                                                    <div class="movie-card">
                                                        <a href="{{ $searchLink }}">
                                                            <div class="movie-poster-wrapper">
                                                                <img src="{{ $posterUrl }}" 
                                                                     alt="{{ $title }}" 
                                                                     class="movie-poster"
                                                                     loading="lazy">
                                                                
                                                                <div class="movie-overlay">
                                                                    <div class="movie-overlay-title">{{ $title }}</div>
                                                                    @if ($rating)
                                                                        <div class="movie-rating">
                                                                            <svg class="rating-star" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                            </svg>
                                                                            <span class="rating-value">{{ number_format($rating / 10, 1) }}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="movie-info">
                                                                <div class="movie-title">{{ $title }}</div>
                                                                @if ($year)
                                                                    <div class="movie-year">{{ $year }}</div>
                                                                @endif
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <button class="carousel-nav carousel-nav-prev" data-carousel="carousel-{{ $loop->index }}" data-direction="prev">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                                                </svg>
                                            </button>
                                            
                                            <button class="carousel-nav carousel-nav-next" data-carousel="carousel-{{ $loop->index }}" data-direction="next">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div style="padding: 4rem; text-align: center; color: var(--gray-500);">
                                        <p>No movies available for {{ $serviceName }}</p>
                                    </div>
                                @endif
                            </div>
                        </section>
                    @empty
                        <div class="carousel-card" style="padding: 4rem; text-align: center;">
                            <p style="color: var(--gray-500); font-size: 1.125rem;">No content available at the moment</p>
                        </div>
                    @endforelse
                    
                @else
                    {{-- SEARCH RESULTS VIEW - See Part 3 --}}
                    @include('partials.search-results')
                @endif

            </div>
        </div>
    </div>

    {{-- JavaScript - See Part 4 --}}
    @include('partials.discover-scripts')
</x-app-layout>