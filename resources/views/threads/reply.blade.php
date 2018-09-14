<reply :data="{{ $reply }}" inline-template v-cloak>
    <div id="reply-{{ $reply->id }}" class="card">
        <div class="card-header">
            <div class="level">
                <div class="flex">
                    <a href="{{ route('profile', $reply->owner) }}">
                        {{ $reply->owner->name }}
                    </a>
                    said {{ $reply->created_at->diffForHumans() }}...
                </div>
                
                @auth
                    <favorite :reply="{{ $reply }}"></favorite>
                    {{-- <form method="POST" action="{{ action('FavoritesController@store', $reply->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-default" {{ $reply->isFavorited() ? 'disabled' : '' }}>{{ $reply->favorites_count }} {{ str_plural('Like', $reply->favorites_count) }}</button>
                    </form> --}}
                @endauth
            </div>

        </div>
        <div class="card-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                </div>

                <button class="btn btn-primary btn-sm" @click="update">Update</button>
                <button class="btn btn-link btn-sm" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body"></div>
        </div>
        
        @can('update', $reply)
            <div class="card-footer level">
                <button class="btn btn-outline-secondary btn-sm mr-2" @click="editing = true">Edit</button>
                <button class="btn btn-outline-danger btn-sm mr-2" @click="destroy">Delete</button>
            </div>
        @endcan
    </div>
</reply>