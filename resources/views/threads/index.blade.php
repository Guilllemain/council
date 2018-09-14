@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('threads._list')

            {{ $threads->links() }}
        </div>

        <div class="col-md-4">
        	<div class="card">
        		<div class="card-header">
        			Search
        		</div>
        		<div class="card-body">
        			<form method="GET" action="/threads/search">
        				<div class="form-group">
	        				<input type="text" name="q" class="form-control" placeholder="Search for something...">
        				</div>
        				<button type="submit" class="btn btn-info">Search</button>
        			</form>
        		</div>
        	</div>
        	<br>
        	@if(count($trending))
	        	<div class="card">
	        		<div class="card-header">
	        			Trending threads
	        		</div>
	        		<div class="card-body">
	        			<ul class="list-group">
	        				@foreach ($trending as $thread)
								<li class="list-group-item">
									<a href="{{$thread->path}}">{{$thread->title}}</a>
								</li>
		        			@endforeach
		        		</ul>
	        		</div>
	        	</div>
        	@endif
        </div>
    </div>
</div>
@endsection
