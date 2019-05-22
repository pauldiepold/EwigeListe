<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Round;
use Illuminate\Http\Request;

class CommentController extends Controller {

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required',
            'round_id' => 'required|integer|exists:rounds,id',
        ]);

        $input = $request->all();

        $round = Round::findOrFail($input['round_id']);

        $comment = Comment::create([
            'body' => $input['body'],
            'created_by' => auth()->user()->player->id
        ]);

        $round->comments()->save($comment);

        return back();
    }

    public function show(Comment $comment)
    {
        //
    }

    public function edit(Comment $comment)
    {
        //
    }

    public function update(Request $request, Comment $comment)
    {
        //
    }

    public function destroy(Comment $comment)
    {
        //
    }
}
