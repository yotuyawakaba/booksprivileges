<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use App\Book;
use App\Console\Commands\Scraping;
use App\app\Mangaoh;
use App\app\Melonbooks;

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
    
    public function mangaoh_scraping() {
        $mangaoh = new Mangaoh();
        $mangaoh->scraping();
        
        return back();
    }
    
    public function melonbooks_scraping () {
        $melonbooks = new Melonbooks ();
        $melonbooks->scraping();
        
        return back();
    }
    
    public function publisher_delete () {
        // 先々月以上のデータをDBから削除する
        $dateSearch = date('m', strtotime('-2 month'));

        // トリガー：DBに１レコードでも先々月のデータがあった場合
        $bookDate = optional(Book::whereMonth('date', '=', $dateSearch)->first())->date;
        if ($bookDate == null) {
            $bookDateMonth = $bookDate;
        } else {
            $bookDateMonth = date('m', strtotime($bookDate));
        }

        // 先々月のデータをすべて削除する
        if ($bookDateMonth == $dateSearch) {
            Book::whereMonth('date', $dateSearch)->delete();
        }
        
        return back();
    }
}
