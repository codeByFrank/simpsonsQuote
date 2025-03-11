<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Support\Facades\Http;

class QuoteController extends Controller
{
    public function index()
    {
        // 1. Ensure that exactly 4 old quotes exist in the database.
        //    If fewer than 4 exist, fill the database while avoiding duplicates.
        while (Quote::count() < 4) {
            $fillResponse = Http::withOptions(['verify' => false])
                ->get('https://thesimpsonsquoteapi.glitch.me/quotes?count=1');
            if ($fillResponse->successful()) {
                $dataFill = $fillResponse->json();
                $quoteDataFill = $dataFill[0] ?? null;
                if ($quoteDataFill) {
                    // Avoid duplicate quotes before inserting
                    if (!Quote::where('quote', $quoteDataFill['quote'])->exists()) {
                        Quote::create([
                            'quote'     => $quoteDataFill['quote'],
                            'character' => $quoteDataFill['character'],
                            'image'     => $quoteDataFill['image'] ?? null,
                        ]);
                    }
                }
            } else {
                break; // Break if the API is not reachable
            }
        }

        // If more than 4 entries exist, delete the extra ones (oldest first)
        while (Quote::count() > 4) {
            $oldest = Quote::orderBy('created_at', 'asc')->first();
            if ($oldest) {
                $oldest->delete();
            } else {
                break;
            }
        }

        // 2. Fetch a fresh quote from the Simpsons API
        $apiResponse = Http::withOptions(['verify' => false])
            ->get('https://thesimpsonsquoteapi.glitch.me/quotes?count=1');
        if (!$apiResponse->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fresh quote'
            ], 500);
        }
        $data = $apiResponse->json();
        $freshQuoteData = $data[0] ?? null;
        if (!$freshQuoteData) {
            return response()->json([
                'success' => false,
                'message' => 'No fresh quote returned'
            ], 500);
        }

        // 3. Retrieve the 4 old quotes from the database before updating,
        //    sorted by created_at in descending order (newest first)
        $oldQuotes = Quote::orderBy('created_at', 'desc')->get();

        // 4. Update the database: Delete the oldest quote (order by ascending)
        //    and insert the fresh quote, so that exactly 4 old quotes remain.
        $oldest = Quote::orderBy('created_at', 'asc')->first();
        if ($oldest) {
            $oldest->delete();
        }
        // Avoid inserting the fresh quote if it already exists
        if (!Quote::where('quote', $freshQuoteData['quote'])->exists()) {
            Quote::create([
                'quote'     => $freshQuoteData['quote'],
                'character' => $freshQuoteData['character'],
                'image'     => $freshQuoteData['image'] ?? null,
            ]);
        }

        // 5. Build the response array:
        //    The fresh quote is the first element, followed by the 4 old quotes (sorted descending)
        $responseArray = [];
        $responseArray[] = [
            'quote'     => $freshQuoteData['quote'],
            'character' => $freshQuoteData['character'],
            'image'     => $freshQuoteData['image'] ?? null,
            'isNew'     => true,
        ];
        foreach ($oldQuotes as $quote) {
            // Avoid duplicate quotes in the response array
            if ($quote->quote !== $freshQuoteData['quote']) {
                $responseArray[] = [
                    'quote'     => $quote->quote,
                    'character' => $quote->character,
                    'image'     => $quote->image,
                    'isNew'     => false,
                ];
            }
        }

        // 6. In rare cases, if fewer than 5 quotes are available, fill the array with additional quotes
        while (count($responseArray) < 5) {
            $fillResponse = Http::withOptions(['verify' => false])
                ->get('https://thesimpsonsquoteapi.glitch.me/quotes?count=1');
            if ($fillResponse->successful()) {
                $dataFill = $fillResponse->json();
                $additionalQuote = $dataFill[0] ?? null;
                // Avoid adding a duplicate of the fresh quote and check if it isn't already in the response
                if (
                    $additionalQuote &&
                    $additionalQuote['quote'] !== $freshQuoteData['quote'] &&
                    !in_array($additionalQuote['quote'], array_column($responseArray, 'quote'))
                ) {
                    $responseArray[] = [
                        'quote'     => $additionalQuote['quote'],
                        'character' => $additionalQuote['character'],
                        'image'     => $additionalQuote['image'] ?? null,
                        'isNew'     => false,
                    ];
                }
            } else {
                break;
            }
        }

        return response()->json($responseArray);
    }
}
