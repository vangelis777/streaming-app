<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class StreamingApiService
{
    protected $baseUrl;
    protected $apiKey;
    protected $timeout = 10; // seconds
    protected $retryTimes = 2;
    protected $retryDelay = 100; // milliseconds

    public function __construct()
    {
        $this->baseUrl = 'https://streaming-availability.p.rapidapi.com';
        $this->apiKey = config('services.streaming_api.key');
    }

    /**
     * Get the discover feed for a country and service.
     * 
     * @param string $country Country code (e.g., 'us', 'gr')
     * @param string $service Service ID (e.g., 'netflix', 'prime')
     * @param int $limit Number of results to return
     * @return array
     */
    public function getDiscoverFeed(string $country, string $service, int $limit = 20)
    {
        if (!$this->apiKey) {
            Log::error('Streaming API: API key is not configured.');
            return $this->errorResponse('API key is not configured.');
        }

        $cacheKey = "discover_feed_{$country}_{$service}_{$limit}";

        // Cache for 6 hours - discover feeds don't change frequently
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($country, $service, $limit) {
            try {
                $response = $this->makeRequest('GET', '/shows/search/filters', [
                    'country' => $country,
                    'show_type' => 'movie',
                    'order_by' => 'popularity_1year',
                    'catalogs' => $service,
                    'output_language' => 'en',
                ]);

                if ($this->isErrorResponse($response)) {
                    Cache::forget($cacheKey); // Don't cache errors
                    return $response;
                }

                // Limit results to requested amount
                $shows = $response['shows'] ?? [];
                return [
                    'shows' => array_slice($shows, 0, $limit),
                    'hasMore' => count($shows) > $limit,
                ];

            } catch (ConnectionException $e) {
                Log::error("Streaming API Connection Error (Discover Feed - {$service}): " . $e->getMessage());
                Cache::forget($cacheKey);
                return $this->errorResponse('Unable to connect to streaming service. Please try again later.');
            } catch (\Exception $e) {
                Log::error("Streaming API Unexpected Error (Discover Feed - {$service}): " . $e->getMessage());
                Cache::forget($cacheKey);
                return $this->errorResponse('An unexpected error occurred.');
            }
        });
    }

    /**
     * Search for a title and get its full availability across all countries.
     * 
     * @param string $title Movie/show title to search for
     * @return array
     */
    public function searchByTitle(string $title)
    {
        if (!$this->apiKey) {
            Log::error('Streaming API: API key is not configured.');
            return $this->errorResponse('API key is not configured.');
        }

        // Cache search results for 24 hours - availability doesn't change that often
        $cacheKey = 'search_' . md5(strtolower(trim($title)));

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($title) {
            try {
                // Step 1: Search for the show by title
                $searchResponse = $this->makeRequest('GET', '/shows/search/title', [
                    'title' => $title,
                    'country' => 'us',
                    'output_language' => 'en',
                ]);

                if ($this->isErrorResponse($searchResponse)) {
                    return $searchResponse;
                }

                if (empty($searchResponse)) {
                    return $this->errorResponse('No results found for "' . htmlspecialchars($title) . '".');
                }

                // Step 2: Get the ID of the first (best) result
                $firstShowId = $searchResponse[0]['id'] ?? null;
                if (!$firstShowId) {
                    return $this->errorResponse('Could not find a valid show ID.');
                }

                // Step 3: Get full details including all countries
                $detailResponse = $this->makeRequest('GET', "/shows/{$firstShowId}", [
                    'output_language' => 'en',
                ]);

                if ($this->isErrorResponse($detailResponse)) {
                    return $detailResponse;
                }

                return $detailResponse;

            } catch (ConnectionException $e) {
                Log::error("Streaming API Connection Error (Search): " . $e->getMessage());
                return $this->errorResponse('Unable to connect to streaming service. Please try again later.');
            } catch (\Exception $e) {
                Log::error("Streaming API Unexpected Error (Search): " . $e->getMessage());
                return $this->errorResponse('An unexpected error occurred.');
            }
        });
    }

    /**
     * Get search suggestions for autocomplete.
     * 
     * @param string $query Search query
     * @return array
     */
    public function getSearchSuggestions(string $query)
    {
        if (!$this->apiKey) {
            Log::error('Streaming API: API key is not configured.');
            return $this->errorResponse('API key is not configured.');
        }

        if (strlen($query) < 2) {
            return [];
        }

        // Cache suggestions for 1 hour - they don't need to be real-time
        $cacheKey = 'suggest_' . md5(strtolower(trim($query)));

        return Cache::remember($cacheKey, now()->addHour(), function () use ($query) {
            try {
                $response = $this->makeRequest('GET', '/shows/search/title', [
                    'title' => $query,
                    'country' => 'us',
                    'output_language' => 'en',
                ]);

                if ($this->isErrorResponse($response)) {
                    return $response;
                }

                // Return only the most relevant results (first 10)
                return array_slice($response, 0, 10);

            } catch (ConnectionException $e) {
                Log::error("Streaming API Connection Error (Suggestions): " . $e->getMessage());
                return $this->errorResponse('Unable to fetch suggestions.');
            } catch (\Exception $e) {
                Log::error("Streaming API Unexpected Error (Suggestions): " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Make an HTTP request to the API with retry logic.
     * 
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $params Query parameters
     * @return array
     */
    protected function makeRequest(string $method, string $endpoint, array $params = [])
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => 'streaming-availability.p.rapidapi.com',
            'X-RapidAPI-Key' => $this->apiKey,
        ])
        ->timeout($this->timeout)
        ->retry($this->retryTimes, $this->retryDelay)
        ->{strtolower($method)}($this->baseUrl . $endpoint, $params);

        // Check for HTTP errors
        if ($response->failed()) {
            $message = $response->json('message', 'Unknown API error');
            Log::error("Streaming API HTTP Error ({$endpoint}): {$response->status()} - {$message}");
            return $this->errorResponse("API request failed: {$message}");
        }

        $data = $response->json();

        // Check for API-level errors
        if (isset($data['message']) && isset($data['error'])) {
            Log::error("Streaming API Error ({$endpoint}): {$data['message']}");
            return $this->errorResponse($data['message']);
        }

        return $data;
    }

    /**
     * Create a standardized error response.
     * 
     * @param string $message Error message
     * @return array
     */
    protected function errorResponse(string $message): array
    {
        return ['error' => $message];
    }

    /**
     * Check if a response is an error.
     * 
     * @param array $response API response
     * @return bool
     */
    protected function isErrorResponse(array $response): bool
    {
        return isset($response['error']);
    }

    /**
     * Clear all caches related to a specific query or service.
     * Useful for manual cache invalidation.
     * 
     * @param string|null $pattern Cache key pattern to clear
     * @return void
     */
    public function clearCache(?string $pattern = null): void
    {
        if ($pattern) {
            Cache::forget($pattern);
        } else {
            // Clear all streaming API caches
            Cache::flush();
        }
    }
}