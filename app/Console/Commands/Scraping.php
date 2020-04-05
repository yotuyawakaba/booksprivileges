<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade as GoutteFacade;
use App\Store;
use App\Book;



class Scraping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scraping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crawler = GoutteFacade::request('GET', 'https://www.mangaoh.co.jp/catalog/product_list4.php?i_date=2020-03-01&i_category=b');
        $store = Store::first();
        $arraytable=array();
        $arraybooks=array();
$crawler->filter("table.table")->each(function ($nodetable) use (&$arraytable){
  $arraytr=array();
  $nodetable->filter("tr")->each(function ($nodetr) use (&$arraytr){
    $arraytd=array();
    $nodetr->filter("td")->each(function ($nodetd) use (&$arraytd){
        
        if(count($nodetd->filter("a"))) {
         $t=$nodetd->filter("a")->attr("href");
        
        $arraytd[]=$t;
        }
        
      $t=$nodetd->text();
      $arraytd[]=$t;
    });
    $arraytr[]=$arraytd;
  });
  $arraytable[]=$arraytr;
});

foreach($arraytable as $key=>$value) {
    
    foreach($value as $key2=>$value2) {
        $arraybook=array();
        //スクレイピングしたデータを2列から1列に合体する
        if($key2 % 2 == 1) {
                $date = $arraytable[0][$key2][0];
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

//先々月以上のデータをDBから削除する
$dateSearch = date('m', strtotime('-2 month'));

//トリガー：DBに１レコードでも先々月のデータがあった場合
$bookDate = optional(Book::whereMonth('date', '=', $dateSearch)->first())->date;
if($bookDate == null) {
    $bookDateMonth = $bookDate;
} else {
    $bookDateMonth = date('m', strtotime($bookDate));
}

//先々月のデータをすべて削除する
if($bookDateMonth == $dateSearch) {
    Book::whereMonth('date', $dateSearch)->delete();
}

//スクレイピングしたデータをDBに登録する
foreach($arraybooks as $key3=>$value3) {
    //特典情報が更新されていたらprivileges_urlを更新する
    if (Book::where('title', '=', $arraybooks[$key3][3])->exists() && !Book::where('privilege_url', '=', $arraybooks[$key3][4])->exists()) {
        Book::where('title',$arraybooks[$key3][3])->update(['privilege_url' => $arraybooks[$key3][4]]);
    }
    //すでに登録されている情報かチェック
    if(Book::where('title', '=', $arraybooks[$key3][3])->exists()) {
        continue;
    }
    $store->books()->create([
            'date' => $arraybooks[$key3][0],
            'title' => $arraybooks[$key3][3],
            'publisher' => $arraybooks[$key3][1],
            'privilege_url' => $arraybooks[$key3][4],
            'show_url' => $arraybooks[$key3][2],
            ]);
}
        
    }
}
