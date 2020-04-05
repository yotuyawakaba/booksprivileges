<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function index()
    {
        //$users = User::orderBy('id', 'desc')->paginate(10);

        return view('books.index', [
            //'users' => $users,
        ]);
    }
}
