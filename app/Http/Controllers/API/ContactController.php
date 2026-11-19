<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReplyMail;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->paginate(10);
        return ContactResource::collection($contacts)->additional([
            'stats' => [
                'total'      => Contact::count(),
                'unread'     => Contact::where('is_read', 0)->count(),
                'this_month' => Contact::whereMonth('created_at', now()->month)->count(),
                'this_year'  => Contact::whereYear('created_at', now()->year)->count(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'subject'    => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'massage'    => 'required|string',
        ]);

        $contact = Contact::create($validated);

        return response()->json([
            'message' => 'Message sent successfully',
            'data'    => new ContactResource($contact)
        ], 201);
    }

    public function show(Contact $contact)
    {
        return new ContactResource($contact);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function unreadCount()
    {
        return response()->json([
            'unread_count' => Contact::where('is_read', 0)->count()
        ]);
    }

    public function markAsRead(Contact $contact)
    {
        $contact->is_read = 1;
        $contact->save();

        return response()->json([
            'message'      => 'Marked as read successfully',
            'is_read'      => $contact->is_read,
            'unread_count' => Contact::where('is_read', 0)->count()
        ]);
    }

    public function latestUnread()
    {
        $messages = Contact::where('is_read', 0)->latest()->take(5)->get();
        return ContactResource::collection($messages);
    }

    /**
     * Send a reply email to the contact.
     */
    public function reply(Request $request, Contact $contact)
    {
        $request->validate([
            'message' => 'required|string|min:5|max:5000',
        ]);

        $recipientName = trim($contact->first_name . ' ' . $contact->last_name);

        Mail::to($contact->email)->send(
            new ReplyMail($recipientName, $request->message, $contact->subject)
        );

        // Mark as read after replying
        if (!$contact->is_read) {
            $contact->update(['is_read' => 1]);
        }

        return response()->json(['message' => 'Reply sent successfully.']);
    }
}
