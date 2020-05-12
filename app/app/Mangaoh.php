<?php

namespace App\app;

use Weidner\Goutte\GoutteFacade as GoutteFacade;
use App\Store;
use App\Book;

class Mangaoh {
    
    public function scraping () {
        $crawler = GoutteFacade::request('GET', 'https://www.mangaoh.co.jp/catalog/b/');
        $store = Store::first();
        $arraytable = array();
        $arraybooks = array();
        
        $crawler->filter("table.table")->each(function ($nodetable) use (&$arraytable) {
            $arraytr = array();
            $nodetable->filter("tr")
                ->each(function ($nodetr) use (&$arraytr) {
                $arraytd = array();
                $nodetr->filter("td")
                    ->each(function ($nodetd) use (&$arraytd) {

                    if (count($nodetd->filter("a"))) {
                        $t = $nodetd->filter("a")
                            ->attr("href");

                        $arraytd[] = $t;
                    }

                    $t = $nodetd->text();
                    $arraytd[] = $t;
                });
                $arraytr[] = $arraytd;
            });
            $arraytable[] = $arraytr;
        });

        foreach ($arraytable as $key => $value) {

            foreach ($value as $key2 => $value2) {
                $arraybook = array();
                // スクレイピングしたデータを2列から1列に合体する
                if ($key2 % 2 == 1) {
                    $date = $arraytable[0][$key2][0];
                    
                    $str_count = strlen($date);
                    if($str_count == 5) {
                        //$pos：開始位置を取得
                        $pos = strpos($date, "/");
                        //$len：置換文字の長さを取得
                        $len = strlen("/下");
                        $date = substr_replace($date, '', $pos, $len);
                        $date = $date . "-01";
                        
                        $datefull = date("Y/" . "$date");

                        $arraybook[] = $datefull;
                        $arraybook[] = $arraytable[0][$key2][1];
                        $arraybook[] = $arraytable[0][$key2][2];
    
                        $str = $arraytable[0][$key2][3];
                        $booktitle = str_replace('コミック ', '', $str);
                        $arraybook[] = $booktitle;
                        $arraybook[] = $arraytable[0][$key2 + 1][0];
                        $arraybooks[] = $arraybook;
                        continue;
                    }
                    
                    $datefull = date("Y/" . "$date");

                    $arraybook[] = $datefull;
                    $arraybook[] = $arraytable[0][$key2][1];
                    $arraybook[] = $arraytable[0][$key2][2];

                    $str = $arraytable[0][$key2][3];
                    $booktitle = str_replace('コミック ', '', $str);
                    $arraybook[] = $booktitle;
                    $arraybook[] = $arraytable[0][$key2 + 1][0];
                    $arraybooks[] = $arraybook;
                }
            }
        }
        
        // スクレイピングしたデータをDBに登録する
        foreach ($arraybooks as $key3 => $value3) {
            // 特典情報が更新されていたらprivileges_urlを更新する
            if (Book::where('title', '=', $arraybooks[$key3][3])->exists() && ! Book::where('privilege_url', '=', $arraybooks[$key3][4])->exists()) {
                // ★2020/04/22追加
                Book::where('store_id', '=', 1)->where('title', $arraybooks[$key3][3])->update([
                    'privilege_url' => $arraybooks[$key3][4]
                ]);
            }
            // すでに登録されている情報かチェック
            if (Book::where('store_id', '=', 1)->where('title', '=', $arraybooks[$key3][3])->exists()) {
                continue;
            }
            $store->books()->create([
                'date' => $arraybooks[$key3][0],
                'title' => $arraybooks[$key3][3],
                'publisher' => $arraybooks[$key3][1],
                'privilege_url' => $arraybooks[$key3][4],
                'show_url' => $arraybooks[$key3][2]
            ]);
        }
    }
}