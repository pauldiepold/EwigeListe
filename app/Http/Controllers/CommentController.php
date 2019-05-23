<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Round;
use App\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class CommentController extends Controller {

    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required',
            'route' => 'required|string',
            'profileID' => 'required_without:roundID|integer|exists:profiles,id',
            'roundID' => 'required_without:profileID|integer|exists:rounds,id',
        ]);

        $input = $request->all();

        if (!strcmp($input['route'], 'round'))
        {
            $page = Round::findOrFail($input['roundID']);
        } elseif (!strcmp($input['route'], 'profile'))
        {
            $page = Profile::findOrFail($input['profileID']);
        }

        $comment = Comment::create([
            'body' => $input['body'],
            'created_by' => auth()->user()->player->id
        ]);

        $page->comments()->save($comment);

        return back();
    }

    public function destroy(Comment $comment)
    {
        if (Auth::user()->player->id != $comment->created_by)
        {
            return Redirect::back()->withErrors(['Du kannst nur deine eigenen Kommentare löschen!']);
        }
        $comment->delete();

        return redirect()->back();
    }
}
