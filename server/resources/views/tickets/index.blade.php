@extends('layouts.app')

@section('title', 'Tickets')
@section('header', 'Tickets')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">All Tickets</h5>

            <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
                New Ticket
            </a>

        </div>
        @include('tickets.partials.list', ['tickets' => $tickets])

    </div>
</div>

@endsection
