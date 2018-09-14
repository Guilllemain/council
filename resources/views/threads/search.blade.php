@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		<ais-index class="col-md-12 row"
	        app-id="{{ config('scout.algolia.id') }}"
	        api-key="{{ config('scout.algolia.key') }}"
	        index-name="threads"
	        query="{{ request('q') }}"
	      >
	        <div class="col-md-8">
	        	<ais-results>
	        	  	<template slot-scope="{ result }">
		        	    <p>
		        	    	<a :href="result.path">
		        	      		<ais-highlight :result="result" attribute-name="title"></ais-highlight>
		        	    	</a>
		        	    </p>
	        	  	</template>
	        	</ais-results>
	        </div>

	        <div class="col-md-4">
				<div class="card">
	        		<div class="card-header">
	        			Search
	        		</div>
	        		<div class="card-body">
						<ais-search-box>
							<ais-input placeholder="Find a thread" :autofocus="true" class="form-control"></ais-input>
						</ais-search-box>
	        		</div>
	        	</div>
				<br>
	        	<div class="card">
	        		<div class="card-header">
	        			Filter by Channel
	        		</div>
	        		<div class="card-body">
						<ais-refinement-list attribute-name="channel.name"></ais-refinement-list>
	        		</div>
	        	</div>
				<br>
	        	{{-- @if(count($trending))
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
	        	@endif --}}
	        </div>
    	</ais-index>
    </div>
</div>
@endsection