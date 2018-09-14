@extends('layouts.app')

@push('header')
    <link href="/css/vendor/jquery.atwho.css" rel="stylesheet">
    <script>
        window.thread = @json($thread); //make thread object global to use in vue
    </script>
@endpush



@section('content')

<thread-view inline-template :thread="{{ $thread }}">
    <div class="container">
        <div class="row">
            <div class="col-md-8" v-cloak>
                @include('threads._question')
                <br>

                <replies @removed="repliesCount--" @added="repliesCount++"></replies>    

                {{-- {{ $replies->links() }} --}}
                
                {{-- @auth
                    <form method="POST" action="{{ $thread->path() . '/replies' }}">
                        @csrf
                        <div class="form-group">
                            <textarea name="body" id="body" class="form-control" placeholder="Have something to say ?" rows="5"></textarea>
                        </div>

                        <button type="submit" class="btn btn-default">Post</button>
                        
                    </form>
                @else
                    <p class="text-center">Please <a href="{{ route('login') }}">sign in</a> to participate in this discussion.</p>
                @endauth --}}

            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p>This thread was published {{ $thread->created_at->diffForHumans() }} by <a href="">{{ $thread->creator->name }}</a>, and currently has <span v-text="repliesCount"></span> comment{{ $thread->replies_count > 1 ? 's' :'' }}.
                        </p>
                        @auth
                            <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}"></subscribe-button>
                        @endauth

                        <button :class="locked ? 'btn btn-outline-warning' : 'btn btn-outline-secondary'" v-if="authorize('isAdmin')" @click="toggleLock" v-text="locked ? 'Unlock' : 'Lock'"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
@endsection
