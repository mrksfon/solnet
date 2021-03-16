<reply :attributes="{{$reply}}"inline-template v-cloak>
    <div>
        <div id="reply-{{$reply->id}}" class="card-header">
            <div class="level">
                <h6 class="flex">
                    <a href="{{route('profile',$reply->owner)}}">
                        {{$reply->owner->name}}
                    </a> said {{$reply->created_at->diffForHumans()}}...
                </h6>
                <div>
                    <form method="post" action="{{route('replies.favorite',$reply->id)}}">
                        @csrf
                        <button type="submit" class="btn btn-primary" {{$reply->isFavorited() ? 'disabled' : ''}}>
                            {{$reply->favorites_count}} {{Str::plural('Favorite',$reply->favorites_count)}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div v-if="editing">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body"></textarea>
                    </div>
                    <button class="btn btn-primary btn-sm" @click="update">Update</button>
                    <button class="btn btn-link btn-sm" @click="editing = false">Cancel</button>
                </div>
                <div v-else v-text="body">

                </div>
            </div>
            @can('update',$reply)
                <div class="card-footer level">
                    <button class="btn btn-sm btn-primary mr-1" @click="editing=true">Edit</button>
                    <form action="/replies/{{$reply->id}}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</reply>
