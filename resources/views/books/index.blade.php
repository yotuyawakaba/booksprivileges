
<h2>
    <div class="text-center">まんが王</div>
</h2>
        <div class="Center">
            
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>タイトル</th>
                            <th>出版社</th>
                            <th>特典</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($books as $book)
                        <?php $date = $book->date;
                              $bookDate = date("Y/m", strtotime($date));
                              $currentMonth = date("Y/m");
                        ?>
                        @if($currentMonth != $bookDate)
                        @continue
                        @endif
                        <tr>
                            <td>{{ $book->date }}</td>
                            <td>{!! link_to( 'https://www.mangaoh.co.jp/' .$book->show_url, $book->title , []) !!}</td>
                            <td>{{ $book->publisher }}</td>
                            <td>{!! link_to( $book->privilege_url, '画像', []) !!}</td>
                            
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <br>
                <br>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>タイトル</th>
                            <th>出版社</th>
                            <th>特典</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($books as $book)
                        <?php $date = $book->date;
                              $bookDate = date("Y/m", strtotime($date));
                              $currentMonth = date("Y/m");
                        ?>
                        @if($currentMonth == $bookDate)
                        @continue
                        @endif
                        <tr>
                            <td>{{ $book->date }}</td>
                            <td>{!! link_to( 'https://www.mangaoh.co.jp/' .$book->show_url, $book->title , []) !!}</td>
                            <td>{{ $book->publisher }}</td>
                            <td>{!! link_to( $book->privilege_url, '画像', []) !!}</td>
                            
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            
        </div>
