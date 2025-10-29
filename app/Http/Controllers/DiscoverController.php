<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StreamingApiService;

class DiscoverController extends Controller
{
    protected $apiService;

    // Available streaming services
    protected $services = [
        'netflix' => 'Netflix',
        'prime' => 'Prime Video',
        'disney' => 'Disney+',
        'apple' => 'Apple TV+',
        'hbo' => 'Max',
        'paramount' => 'Paramount+',
    ];

    // Available countries
    protected $countries = [
        'us' => 'United States',
        'gb' => 'United Kingdom', 
        'ca' => 'Canada',
        'gr' => 'Greece',
        'de' => 'Germany',
        'fr' => 'France',
        'es' => 'Spain',
        'it' => 'Italy',
        'jp' => 'Japan',
        'au' => 'Australia',
    ];

    public function __construct(StreamingApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the discover page with movie feeds or search results.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');
        $selectedCountry = $request->input('country', 'gr'); // Default to Greece
        $searchResult = null;
        $serviceFeeds = [];

        // Validate country code
        if (!array_key_exists($selectedCountry, $this->countries)) {
            $selectedCountry = 'gr';
        }

        if ($searchTerm) {
            // Search mode: find specific title
            $searchResult = $this->apiService->searchByTitle($searchTerm);
        } else {
            // Browse mode: show popular movies from each service
            $serviceFeeds = $this->getServiceFeeds($selectedCountry);
        }

        return view('discover', [
            'searchTerm' => $searchTerm,
            'searchResult' => $searchResult,
            'serviceFeeds' => $serviceFeeds,
            'services' => $this->services,
            'countries' => $this->countries,
            'selectedCountry' => $selectedCountry,
        ]);
    }

    /**
     * Get movie feeds for all configured services.
     * 
     * @param string $country Country code
     * @return array
     */
    protected function getServiceFeeds(string $country): array
    {
        $feeds = [];

        foreach ($this->services as $serviceKey => $serviceName) {
            $feed = $this->apiService->getDiscoverFeed($country, $serviceKey, 20);
            
            // Only include services that returned valid data
            if (!isset($feed['error']) && !empty($feed['shows'])) {
                $feeds[$serviceName] = $feed;
            } else {
                // Log but don't show empty services to keep UI clean
                \Log::info("Skipping {$serviceName} for {$country}: " . ($feed['error'] ?? 'No shows available'));
            }
        }

        return $feeds;
    }

    /**
     * Handle autocomplete search suggestions.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggest(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $query = $request->input('q');
        $suggestions = $this->apiService->getSearchSuggestions($query);

        if (isset($suggestions['error'])) {
            return response()->json(['error' => $suggestions['error']], 500);
        }

        return response()->json($suggestions);
    }

    /**
     * Change the selected country (AJAX endpoint).
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeCountry(Request $request)
    {
        $request->validate([
            'country' => 'required|string|size:2',
        ]);

        $country = strtolower($request->input('country'));

        if (!array_key_exists($country, $this->countries)) {
            return response()->json(['error' => 'Invalid country code'], 400);
        }

        $serviceFeeds = $this->getServiceFeeds($country);

        return response()->json([
            'success' => true,
            'country' => $country,
            'countryName' => $this->countries[$country],
            'serviceFeeds' => $serviceFeeds,
        ]);
    }
}