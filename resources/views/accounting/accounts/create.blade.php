@extends('layouts.erp')

@section('title', 'Create Account')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Create Account</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('accounting.accounts.store') }}">
                @include('accounting.accounts._form', ['account' => $account])
            </form>
        </div>
    </div>
</div>
@endsection
