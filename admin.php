<!-- ひと言掲示板　管理ページ -->
<?php

//管理ページのログインパスワード
define('PASSWORD', 'adminPassword');

//データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'board');

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

//変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_messege = null;
$error_message = array();
$clean = array();

session_start();

// if( !empty($_GET['btn_logout']) ) {
//     unset($_SESSION['admin_login']);
// }

if(!empty($_POST['btn_submit']) ) {
    if(!empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD) {
        $_SESSION['admin_login'] = true;
    } else {
        $error_message[] = 'ログインに失敗しました。';
    }
    //データベースに接続
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    //接続エラーの確認
    if($mysql->connect_errno) {
        $error_message[] = 'データの読み込みに失敗しました。　エラー番号 '.$mysql->connect_errno.': '.$mysql->connect_error;
    } else {
        $sql = "SELECT id,view_name,message,post_date FROM message ORDER BY post_date DESC";
        $res = $mysql->query($sql);
        if($res) {
            $_SESSION['success_message'] = 'メッセージを書き込みました。';
        } else {
            $error_message[] = '書き込みに失敗しました。';
        }
        //データベースの接続を閉じる
        $mysql->close();
    }
    header('Location: ./');
}  
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ひと言掲示板　管理ページ</title>
<style>
/*------------------------------
Reset Style
------------------------------*/
html, body, div, span, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
abbr, address, cite, code,
del, dfn, em, img, ins, kbd, q, samp,
small, strong, sub, sup, var,
b, i,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, figcaption, figure,
footer, header, hgroup, menu, nav, section, summary,
time, mark, audio, video {
    margin:0;
    padding:0;
    border:0;
    outline:0;
    font-size:100%;
    vertical-align:baseline;
    background:transparent;
}
body {
    line-height:1;
}
article,aside,details,figcaption,figure,
footer,header,hgroup,menu,nav,section {
    display:block;
}
nav ul {
    list-style:none;
}
blockquote, q {
    quotes:none;
}
blockquote:before, blockquote:after,
q:before, q:after {
    content:'';
    content:none;
}
a {
    margin:0;
    padding:0;
    font-size:100%;
    vertical-align:baseline;
    background:transparent;
}
/* change colours to suit your needs */
ins {
    background-color:#ff9;
    color:#000;
    text-decoration:none;
}
/* change colours to suit your needs */
mark {
    background-color:#ff9;
    color:#000;
    font-style:italic;
    font-weight:bold;
}
del {
    text-decoration: line-through;
}
abbr[title], dfn[title] {
    border-bottom:1px dotted;
    cursor:help;
}
table {
    border-collapse:collapse;
    border-spacing:0;
}
hr {
    display:block;
    height:1px;
    border:0;
    border-top:1px solid #cccccc;
    margin:1em 0;
    padding:0;
}
input, select {
    vertical-align:middle;
}
/*------------------------------
Common Style
------------------------------*/
body {
	padding: 50px;
	font-size: 100%;
	font-family:'ヒラギノ角ゴ Pro W3','Hiragino Kaku Gothic Pro','メイリオ',Meiryo,'ＭＳ Ｐゴシック',sans-serif;
	color: #222;
	background: #f7f7f7;
}
a {
    color: #007edf;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
h1 {
	margin-bottom: 30px;
    font-size: 100%;
    color: #222;
    text-align: center;
}
/*-----------------------------------
入力エリア
-----------------------------------*/
label {
    display: block;
    margin-bottom: 7px;
    font-size: 86%;
}
input[type="text"],
input[type="password"],
textarea {
	margin-bottom: 20px;
	padding: 10px;
	font-size: 86%;
    border: 1px solid #ddd;
    border-radius: 3px;
    background: #fff;
}
input[type="text"],
input[type="password"] {
	width: 200px;
}
textarea {
	width: 50%;
	max-width: 50%;
	height: 70px;
}
input[type="submit"] {
	appearance: none;
    -webkit-appearance: none;
    padding: 10px 20px;
    color: #fff;
    font-size: 86%;
    line-height: 1.0em;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #37a1e5;
}
input[type=submit]:hover,
button:hover {
    background-color: #2392d8;
}

input[name=btn_logout] {
    margin-top: 40px;
    background-color: #666;
}
input[name=btn_logout]:hover {
    background-color: #777;
}

hr {
	margin: 20px 0;
	padding: 0;
}
.success_message {
    margin-bottom: 20px;
    padding: 10px;
    color: #48b400;
    border-radius: 10px;
    border: 1px solid #4dc100;
}
.error_message {
    margin-bottom: 20px;
    padding: 10px;
    color: #ef072d;
    list-style-type: none;
    border-radius: 10px;
    border: 1px solid #ff5f79;
}
.success_message,
.error_message li {
    font-size: 86%;
    line-height: 1.6em;
}
/*-----------------------------------
掲示板エリア
-----------------------------------*/
article {
	margin-top: 20px;
	padding: 20px;
	border-radius: 10px;
	background: #fff;
}
article.reply {
    position: relative;
    margin-top: 15px;
    margin-left: 30px;
}
article.reply::before {
    position: absolute;
    top: -10px;
    left: 20px;
    display: block;
    content: "";
    border-top: none;
    border-left: 7px solid #f7f7f7;
    border-right: 7px solid #f7f7f7;
    border-bottom: 10px solid #fff;
}
	.info {
		margin-bottom: 10px;
	}
	.info h2 {
		display: inline-block;
		margin-right: 10px;
		color: #222;
		line-height: 1.6em;
		font-size: 86%;
	}
	.info time {
		color: #999;
		line-height: 1.6em;
		font-size: 72%;
	}
    .info p {
        display: inline-block;
        line-height: 1.6em;
        font-size: 86%;
    }
    article p {
        color: #555;
        font-size: 86%;
        line-height: 1.6em;
    }
@media only screen and (max-width: 1000px) {
    body {
        padding: 30px 5%;
    }
    input[type="text"] {
        width: 100%;
    }
    textarea {
        width: 100%;
        max-width: 100%;
        height: 70px;
    }
}
</style>
</head>
<body>
<h1>ひと言掲示板　管理ページ</h1>
<?php if(!empty($error_message)): ?>
    <ul class="error_message">
        <?php foreach($error_message as $value): ?>
            <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<section>
<?php if(!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true): ?>
<form method="get" action="./download.php">
    <select name="limit">
        <option value="">全て</option>
        <option value="10">10件</option>
        <option value="30">30件</option>
    </select>
    <input type="submit" name="btn_download" value="ダウンロード">
</form>

<?php if( !empty($message_array) ): ?>
<?php foreach( $message_array as $value ): ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
        <p><a href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>  <a href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a></p>
    </div>
    <p><?php echo nl2br($value['message']); ?></p>
</article>
<?php endforeach; ?>
<?php endif; ?>
    <form method="get" action="">
    <input type="submit" name="btn_logout" value="ログアウト">
</form>
<?php else: ?>
<?php endif; ?>
</section>
</body>
</html>