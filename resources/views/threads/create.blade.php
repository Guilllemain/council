@extends('layouts.app')

@push('header')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create a new thread</div>

                <div class="card-body">
                    <form method="POST" action="/threads">
                        @csrf

                        <div class="form-group">
                            <label for="title">Title :</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="body">Body :</label>
                            <wysiwyg name="body"></wysiwyg>
                            {{-- <textarea name="body" class="form-control" rows="10" required>{{ old('body') }}</textarea> --}}
                        </div>

                        <div class="form-group">
                            <select name="channel_id" class="form-control" required>
                                <option value="">Select a channel</option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2 g-recaptcha" data-sitekey="6LfIe28UAAAAAM8mikV1eEiXcfQGJj8_jr769Zzd"></div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Publish</button>
                        </div>
                        
                        @if(count($errors))
                            <ul class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
