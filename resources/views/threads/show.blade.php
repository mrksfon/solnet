@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row ">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="level">
                            <span class="flex">
                                <a href="{{route('profile',$thread->creator)}}">{{$thread->creator->name}}</a>
                                posted:{{$thread->title}}
                            </span>
                            <form action="{{$thread->path()}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link">Delete Thread</button>
                            </form>
                        </div>

                    </div>

                    <div class="card-body">
                        {{$thread->body}}
                    </div>
                </div>

                @foreach($replies as $reply)
                    @include('threads.reply')
                @endforeach

                {{$replies->links()}}

                @if(auth()->check())
                    <form action="{{$thread->path() . '/replies'}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <textarea name="body" id="body" class="form-control" placeholder="Have something to say?"
                                      rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                @else
                    <p class="text-center">Please <a href="{{route('login')}}">sign in</a> to participate in this
                        discussion</p>
                @endif
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p>
                            This thread was published {{$thread->created_at->diffForHumans()}} by
                            <a href="">{{$thread->replies_count}}</a> and currently has {{$thread->replies()->count()}}
                            {{\Illuminate\Support\Str::plural('comment',$thread->replies_count)}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
