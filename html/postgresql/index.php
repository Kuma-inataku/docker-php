<?php
$pg_conn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres");

if( $pg_conn ) {
	var_dump("接続に成功しました");
} else {
	var_dump("接続できませんでした");
}

// データベースとの接続を切断
pg_close($pg_conn);