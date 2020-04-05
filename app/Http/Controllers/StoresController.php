<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use App\Book;

class StoresController extends Controller
{
    public function index()
    {
        $stores = Store::all();

        return view('stores.index', [
            'stores' => $stores,
        ]);
    }
    
    public function show($id)
    {
        $data = [];
        $store = Store::find($id);
        $books = Book::orderBy('date', 'asc')->where('store_id',$id)->get();
        
        $data = [
            'store' => $store,
            'books' => $books
            ];

        return view('stores.show', $data);
    }
}
