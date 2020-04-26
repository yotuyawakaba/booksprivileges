@extends('layouts.app')

@section('content')
<div class="center">
    <div class="text-center">
        <h1>特典一覧</h1>
        <br>
        <br>
    </div>
    @if ($store->id == 1)
        @include('books.index', ['books' => $books])
    @else ($store->id == 2)
        @include('books.index_melon', ['books' => $books])
    @endif
</div>
@endsection