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
                    <p>{!! link_to_route('stores.mangaoh_scraping', 'まんが王特典更新', [], ['class' => 'btn btn-primary']) !!}</p>
                    <p>{!! link_to_route('stores.melonbooks_scraping', 'メロンブックス特典更新', [], ['class' => 'btn btn-primary']) !!}</p>
                    <p>{!! link_to_route('stores.publisher_delete', '特典削除', [], ['class' => 'btn btn-danger']) !!}</p>
                </ul>
                @endif
            </div>
        </div>
@endsection