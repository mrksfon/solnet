<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Rules\SpamFree;
use Illuminate\Http\Request;

class RepliesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    public function index(Channel $channel, Thread $thread)
    {
        return $thread->replies()->paginate(25);
    }

    public function store($channelId, Thread $thread)
    {
        if (\Gate::denies('create', new Reply)) {
            return response('You are posting to frequently please take a break.', 422);
        }
        try {
            $this->validate(request(), ['body' => ['required', new SpamFree],]);

            $reply = $thread->addReply([
                'body' => \request('body'),
                'user_id' => auth()->id()
            ]);
        } catch (\Exception $e) {
            return response('Sorry,your reply could not be saved at this time', 422);
        }


        return $reply->load('owner');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        try {
            $this->validate(request(), ['body' => ['required', new SpamFree]]);

            $reply->update(['body' => \request('body')]);
        } catch (\Exception $e) {
            return response('Sorry,your reply could not be saved at this time', 422);
        }
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply Deleted']);
        }

        return back();
    }
}
