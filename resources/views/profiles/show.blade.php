@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-2">
                <div class="card-header">
                    <h1>
                        {{$profileUser->name}}
                        <small>Since {{$profileUser->created_at->diffForHumans()}}</small>
                    </h1>
                </div>
                @foreach($activities as $date => $activity)
                    <div class="card-header">
                        {{$date}}
                    </div>
                    @foreach($activity as $record)
                        @include("profiles.activities.{$record->type}",['activity' => $record])
                    @endforeach
                @endforeach
                {{--                {{$threads->links()}}--}}
            </div>
        </div>
    </div>
@endsection
