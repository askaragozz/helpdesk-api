@extends('layouts.app')

@section('title', 'Ticket Details')
@section('header', 'Ticket #' . $ticket->id . ' — ' . $ticket->title)

@section('content')
<div class="row">

    <div class="col-lg-8">
        @include('tickets.partials.header', ['ticket' => $ticket])
        @include('tickets.partials.conversation', ['ticket' => $ticket])
        @include('tickets.partials.comment-form', ['ticket' => $ticket])
    </div>

    <div class="col-lg-4">
        @include('tickets.partials.meta', ['ticket' => $ticket])
    </div>

</div>
@endsection
