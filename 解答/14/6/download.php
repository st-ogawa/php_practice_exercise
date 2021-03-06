<?php
// セッションを開始する
session_start();
session_regenerate_id();

// ログインしていないときは、login.phpへリダイレクト
if (empty($_SESSION['user'])) {
    header('Location: ./login.php');
    exit;
}

// 必要なファイルを読み込む
require_once('./class/config/Config.php');
require_once('./class/db/Base.php');
require_once('./class/db/TodoItems.php');

// エラーメッセージをクリア
unset($_SESSION['msg']['err']);

try {
    // todo_itemテーブルクラスのインスタンスを生成する
    $db = new TodoItems();

    // レコードを全件取得する（期限日の古いものから並び替える）
    $list = $db->selectAll();
} catch (Exception $e) {
    // エラーメッセージをセッションに保存してエラーページにリダイレクト
    $_SESSION['msg']['err'] = Config::MSG_EXCEPTION;
    header('Location: ./error.php');
    exit;
}

// 取得したレコードをCSVファイルとしてダウンロードさせる
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="work.csv"');

foreach ($list as $val) {
    foreach ($val as $k => $v) {
        // 配列のキーがtodo_itemのとき
        if ($k == 'todo_item') {
            // 文字コードをSJIS-winからUTF-8に変換する
            $val[$k] = mb_convert_encoding($v, 'SJIS-win', 'UTF-8');
        }
    }
    // 配列を「,」で結合して出力する
    echo implode(',', $val) . "\n";
}
