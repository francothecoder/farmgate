<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        Enquiry::create([
            'user_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'related_type' => $request->related_type,
            'related_id' => $request->related_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'new',
        ]);

        return back()->with('success', 'Your enquiry has been sent.');
    }
}
