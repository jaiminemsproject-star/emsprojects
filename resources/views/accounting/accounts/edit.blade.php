@extends('layouts.erp')

@section('title', 'Edit Account')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Edit Account: {{ $account->name }}</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('accounting.accounts.update', $account) }}">
                @method('PUT')
                @include('accounting.accounts._form', ['account' => $account])
            </form>
        </div>
    </div>
</div>
@endsection
