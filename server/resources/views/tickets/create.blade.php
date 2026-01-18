@extends('layouts.app')

@section('title', 'New Ticket')
@section('header', 'Create Ticket')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('tickets.partials.form')

    </div>
</div>
@endsection
