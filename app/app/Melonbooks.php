<?php

namespace App\app;

use Weidner\Goutte\GoutteFacade as GoutteFacade;
use App\Store;
use App\Book;

class Melonbooks {
    
    public function scraping () {
        $date_Y_M = date("Y-m-01");
        $current_date = date("Y-m-d");
        $url = 'https://www.melonbooks.co.jp/privilege/privilege.php?genre=&chara=&week=&type=&date_before=2020-04-07&date_after=2020-04-14&category=4&sort_type=&orderby=&disp_number=1000&pageno=1&picker_date_before=' . $date_Y_M . '&picker_date_after=' . $current_date;
        $calender_url = 'https://www.melonbooks.co.jp/calender/index.php?category=4&month=&rate=-1&orderby=&pageno=1&text_type=all&name=&disp_number=10000';
        $melonbooks_store = Store::find(2);

        $crawler_meron_calender = GoutteFacade::request('GET', $calender_url);
        $melon_arraytable = array();
        $count_key;

        $crawler_meron_calender->filter("table#calendar_table")->each(function ($nodetable_melon) use (&$melon_arraytable) {
            $melon_arraytabletr = array();
            $nodetable_melon->filter("tr")
                ->each(function ($nodetabletr_melon) use (&$melon_arraytabletr) {
                $melon_arraytabletd = array();
                $nodetabletr_melon->filter("td")
                    ->each(function ($nodetabletd_melon) use (&$melon_arraytabletd) {
                    $td = $nodetabletd_melon->text();
                    $melon_arraytabletd[] = $td;
                });

                $melon_arraytabletr[] = $melon_arraytabletd;
            });
            $melon_arraytable[] = $melon_arraytabletr;
            
        });
        foreach ($melon_arraytable as $key_melon_test => $calender_melon_test) {
            foreach ($calender_melon_test as $key_melon_test2 => $calender_melon_test2) {
                $count_key = $key_melon_test2;
            }
        }

        $crawlerMeron = GoutteFacade::request('GET', $url);
        $sample = array();

        $crawlerMeron->filter("div.products")->each(function ($node2) use (&$sample) {
            $test_array = array();
            $test_array[] = $node2->filter("div.plus_information div.layout_list div.product div.title p.title")
                ->text();
            $test_array[] = $node2->filter("div.plus_information div.layout_list div.product div.title p.circle")
                ->text();
            $test_array[] = $node2->filter("div.plus_information div.layout_list div.product div.title p.title")
                ->filter("a")
                ->attr("href");
            // 特典URL画像を表示させるように加工する
            $privileges_img = $node2->filter(".products div.thumb a img")
                ->image()
                ->getUri();
            $privileges_img2 = substr_replace($privileges_img, '', 109);
            $privileges_img3 = substr_replace($privileges_img2, 'g', 87, 0);
            $test_array[] = substr_replace($privileges_img3, '70', 110, 0);
            $sample[] = $test_array;
        });

        // DBに登録する
        foreach ($sample as $key3_melon => $privileges_melon) {
            for ($key2_melon = 0; $key2_melon < $count_key; $key2_melon ++) {
                // 特典一覧のタイトルと発売日一覧のタイトルが一致したら日付を表示させる処理
                if ($sample[$key3_melon][0] == $melon_arraytable[0][$key2_melon + 1][3]) {
                    // 日付を加工
                    $str_melon = $melon_arraytable[0][$key2_melon + 1][0];
                    $str_count = strlen($str_melon);

                    // 20**年**月**日(**年**月*)のものだったら(**年**月*)を消す
                    if ($str_count > 18) {
                        $date_melon;
                        if ($str_count == 34) {
                            // $pos：開始位置を取得
                            $pos = strpos($str_melon, "(");
                            // $len：置換文字の長さを取得
                            $len = strlen("(2020年04月中)");
                            $date_melon = substr_replace($str_melon, '', $pos, $len);
                            // 年月日をyyyy-mm-ddに変更する
                            $date_melon = rtrim(str_replace(array('年','月'), '-', $date_melon), '日');
                            // 日付が更新されていたらupdateする
                            if (Book::where('title', '=', $sample[$key3_melon][0])->exists() && ! Book::where('date', '=', $date_melon)->exists()) {
                                Book::where('store_id', '=', 2)->where('title', $sample[$key3_melon][0])->update([
                                    'date' => $date_melon
                                ]);
                            }
                            // すでに登録されている情報かチェック
                            if (Book::where('store_id', '=', 2)->where('title', '=', $sample[$key3_melon][0])->exists()) {
                                continue 2;
                            }
                        } else if ($str_count == 35) {
                            // すでに登録されている情報かチェック
                            if (Book::where('store_id', '=', 2)->where('title', '=', $sample[$key3_melon][0])->exists()) {
                                continue 2;
                            }
                            // $pos：開始位置を取得
                            $pos = strpos($str_melon, "月");
                            // $len：置換文字の長さを取得
                            $len = strlen("月下旬(2020年04月中)");
                            $date_melon = substr_replace($str_melon, '', $pos, $len);
                            $str_melon = rtrim(str_replace('年', '-', $date_melon));
                            $date_melon = $str_melon . "-01";
                        }

                        $melonbooks_store->books()->create([
                            'date' => $date_melon,
                            'title' => $sample[$key3_melon][0],
                            'publisher' => $sample[$key3_melon][1],
                            'privilege_url' => $sample[$key3_melon][3],
                            'show_url' => $sample[$key3_melon][2]
                        ]);
                    } else if ($str_count == 15 || $str_count == 18) {
                        // すでに登録されている情報かチェック
                        if (Book::where('store_id', '=', 2)->where('title', '=', $sample[$key3_melon][0])->exists()) {
                            continue 2;
                        }
                        // 20**年**月中or20**年**月〇旬だったらyyyy-mm-99に設定する処理
                        if ($str_count == 15) {
                            $str_melon = rtrim(str_replace(array(
                                '年',
                                '月'
                            ), '-', $str_melon), '中');
                            $str_melon = $str_melon . "01";
                        } else if ($str_count == 18) {
                            // $pos：開始位置を取得
                            $pos = strpos($str_melon, "月");
                            // $len：置換文字の長さを取得
                            $len = strlen("月下旬");
                            $date_melon = substr_replace($str_melon, '', $pos, $len);
                            $str_melon = rtrim(str_replace('年', '-', $date_melon));
                            $str_melon = $str_melon . "-01";
                        }

                        $melonbooks_store->books()->create([
                            'date' => $str_melon,
                            'title' => $sample[$key3_melon][0],
                            'publisher' => $sample[$key3_melon][1],
                            'privilege_url' => $sample[$key3_melon][3],
                            'show_url' => $sample[$key3_melon][2]
                        ]);
                    } else if ($str_count == 17) {
                        // 20**年**月**日たっだらyyyy-mm-ddに設定する処理
                        $str_melon = rtrim(str_replace(array(
                            '年',
                            '月'
                        ), '-', $str_melon), '日');
                        // 日付が更新されていたらupdateする
                        if (Book::where('title', '=', $sample[$key3_melon][0])->exists() && ! Book::where('date', '=', $str_melon)->exists()) {
                            Book::where('store_id', '=', 2)->where('title', $sample[$key3_melon][0])->update([
                                'date' => $str_melon
                            ]);
                        }
                        // すでに登録されている情報かチェック
                        if (Book::where('store_id', '=', 2)->where('title', '=', $sample[$key3_melon][0])->exists()) {
                            continue 2;
                        }
                        $melonbooks_store->books()->create([
                            'date' => $str_melon,
                            'title' => $sample[$key3_melon][0],
                            'publisher' => $sample[$key3_melon][1],
                            'privilege_url' => $sample[$key3_melon][3],
                            'show_url' => $sample[$key3_melon][2]
                        ]);
                    }
                }
            }
        }
    }
}