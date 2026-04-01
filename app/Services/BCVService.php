<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BCVService
{
    public function getUSDValue()
    {
        return Cache::remember('bcv_usd_rate', 3600, function () { // Cache for 1 hour
            try {
                // BCV sometimes blocks simple crawlers, we add a User-Agent
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ])->withoutVerifying()->get('https://www.bcv.org.ve/');

                if ($response->successful()) {
                    $html = $response->body();


                    if (preg_match('/<div id="dolar"[^>]*>.*?<strong>\s*([0-9,.]+)\s*<\/strong>/s', $html, $matches)) {
                        $value = str_replace(',', '.', $matches[1]);
                        Log::info('BCV Scraping successful: USD value found - ' . $value);
                        return (float) $value;
                    }
                }

                Log::error('BCV Scraping failed: Pattern not found or site unreachable');
                return null;
            } catch (\Exception $e) {
                Log::error('BCV Scraping error: ' . $e->getMessage());
                return null;
            }
        });
    }
}
