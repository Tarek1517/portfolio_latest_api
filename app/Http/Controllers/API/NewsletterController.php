<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query('search');
        $subscribers = NewsletterSubscriber::query()
            ->when($query, fn($q) => $q->where('email', 'like', "%{$query}%"))
            ->orderBy('created_at', 'desc')
            ->get();
        $now = now();
        $stats = [
            'total' => NewsletterSubscriber::count(),
            'this_month' => NewsletterSubscriber::whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->count(),
            'this_year' => NewsletterSubscriber::whereYear('created_at', $now->year)->count(),
        ];
        return response()->json([
            'status' => true,
            'data' => $subscribers,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);
        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();
        if ($subscriber) {
            return response()->json([
                'status' => false,
                'message' => 'You are already subscribed to our newsletter.',
            ], 409);
        }
        NewsletterSubscriber::create([
            'email' => $request->email,
            'subscribed_at' => now(),
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Successfully subscribed to newsletter 🎉',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscriber = NewsletterSubscriber::find($id);
        if (!$subscriber) {
            return response()->json([
                'status' => false,
                'message' => 'Subscriber not found',
            ], 404);
        }
        $subscriber->delete();
        return response()->json([
            'status' => true,
            'message' => 'Subscriber deleted successfully',
        ]);
    }
}
