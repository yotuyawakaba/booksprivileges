@extends('layouts.app')

@section('content')

        <div class="center">
            <div class="text-center">
                <h1>トップページ</h1>
                <br>
                <br>
                <p><h2>{!! link_to_route('books.index', 'まんが王', [], ['class' => 'link']) !!}</h2></p>
            </div>
        </div>
@endsection