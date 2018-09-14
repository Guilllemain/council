<div class="card" v-if="editing">
    <div class="card-header level">
        <div class="flex">
            <input class="form-control" type="text" v-model="form.title">
        </div>
        
    </div>
 
    <div class="card-body form-group">
        <wysiwyg name="body" v-model="form.body" :value="form.blody"></wysiwyg>
        {{-- <textarea rows="10" class="form-control" v-model="form.body"></textarea> --}}
    </div>

    <div class="card-footer">
        <div class="level">
            <button class="btn btn-outline-primary mr-1" @click="updateThread">Update</button>
            <button class="btn btn-outline-secondary" @click="cancelThread">Cancel</button>
            @can ('update', $thread)
                <form method="POST" action="{{ $thread->path() }}" class="ml-a">
                    @csrf
                    {{ method_field('DELETE') }}

                    <button type="submit" class="btn btn-link">Delete thread</button>
                    
                </form>
            @endcan
        </div>
    </div>
</div>

<div class="card" v-else>
    <div class="card-header level">
        <div class="flex">
            <img src="{{ $thread->creator->avatar_path }}" width="40" height="40" class="mr-1">
            <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a>
            posted : <span v-text="title"></span>
        </div>
    </div>

    <div class="card-body" v-html="body"></div>

    <div v-if="authorize('updateThread', thread)" class="card-footer">
        <button class="btn btn-outline-secondary" @click="editing = true">Edit</button>
    </div>
</div>