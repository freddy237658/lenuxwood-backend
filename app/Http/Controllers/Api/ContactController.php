<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\User;
use App\Notifications\NewContactAdminAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $contact = Contact::create($validated);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewContactAdminAlert($contact));

        return new ContactResource($contact);
    }

    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return ContactResource::collection($query->latest()->paginate(15));
    }

    public function show(Contact $contact)
    {
        $contact->update(['is_read' => true]);

        return new ContactResource($contact);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'message' => 'Message supprimé.',
        ]);
    }
}