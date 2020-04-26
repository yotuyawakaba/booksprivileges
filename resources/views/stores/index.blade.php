@extends('layouts.app')

@section('content')

        <div class="center">
            <div class="text-center">
                <h1>トップページ</h1>
                <br>
                <br>
                @if (count($stores) > 0)
                <ul class="list-unstyled">
                    @foreach ($stores as $store)
                        <h2><li>
                            <div>
                                <p>{!! link_to_route('stores.show', $store->name, ['id' => $store->id]) !!}</p>
                            </div>
                        </li></h2>
                    @endforeach
                    <p>{!! link_to_route('stores.scraping', '更新', [], ['class' => 'btn btn-primary']) !!}</p>
                </ul>
                @endif
            </div>
        </div>
@endsection