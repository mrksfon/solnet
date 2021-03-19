<reply :attributes="{{$reply}}" inline-template v-cloak>
    <div>
        <div id="reply-{{$reply->id}}" class="card-header">
            <div class="level">
                <h6 class="flex">
                    <a href="{{route('profile',$reply->owner)}}">
                        {{$reply->owner->name}}
                    </a> said {{$reply->created_at->diffForHumans()}}...
                </h6>
                @auth
                    <div>
                        <favorite :reply="{{$reply}}"></favorite>
                    </div>
                @endauth
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
                    <button class="btn btn-sm btn-danger mr-1" @click="destroy">Delete</button>
                </div>
            @endcan
        </div>
    </div>
</reply>
