@forelse($threads as $thread)
    <div class="card mb-4">
        <div class="card-header level">
            <div class="flex">
                <h4>
                    <a href="{{route('threads.show', ['channel' => $thread->channel, 'thread' => $thread])}}">
                        @if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                            <strong>{{ $thread->title }}</strong>
                        @else
                            {{ $thread->title }}
                        @endif
                    </a>
                </h4>

                <h5>
                    Posted by : <a href="{{ route('profile', $thread->creator) }}">{{$thread->creator->name}}</a>
                </h5>
            </div>

            <a href="{{ $thread->path() }}">{{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}</a>
        </div>

        <div class="card-body">
            {!! $thread->body !!}
        </div>

        <div class="card-footer">
            {{ $thread->visits()->count($thread) }} visits
        </div>
    </div>
@empty
    <p>There are no currents posts for this channel</p>
@endforelse