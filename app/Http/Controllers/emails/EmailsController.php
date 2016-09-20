<?php

namespace App\Http\Controllers\emails;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class EmailsController extends Controller
{
    
    public function store(Request $request)
    {
        $all= $request->all();        
        //dd($all);

        Mail:queue('emails.mailtemplate', compact('all'), function($message) use($all) {
            $message->from($all['sender_email'])
                    ->to($all['recipient_email'])
                    ->subject($all['subject']);
        });
    
        return redirect('/email');
    }

}