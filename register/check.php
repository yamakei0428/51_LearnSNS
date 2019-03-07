<?php
session_start();
// require(ファイル名);
// 指定されたファイルの中身が丸々移植される
require '../dbconnect.php';
// 不正遷移制御
// signup.phpから来ていない場合、signup.phpに強制遷移させる
if (!isset($_SESSION['51_LearnSNS'])) {
    header('Location: signup.php');
    exit();
}
// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';
$name = $_SESSION['51_LearnSNS']['name'];
$email = $_SESSION['51_LearnSNS']['email'];
$password = $_SESSION['51_LearnSNS']['password'];
$img_name = $_SESSION['51_LearnSNS']['img_name'];
// POST送信時
if (!empty($_POST)) {
    // DBへの登録処理
    // ?に直接変数の値を入れないのはセキュリティ対策
    // SQLインジェクションを防ぐため
    // タプル処理で意図しないDB操作が行われないようにしている
    $sql = 'INSERT INTO `users` (`name`, `email`, `password`, `img_name`, `created`) VALUES (?, ?, ?, ?, NOW())';
    // password_hash(暗号化したい文字列, 暗号化する方法);
    // 指定された文字列をハッシュ化して暗号にする
    $data = [$name, $email, password_hash($password, PASSWORD_DEFAULT), $img_name];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    // セッション情報の破棄
    // unset(配列);
    // 指定された配列をメモリから破棄する
    unset($_SESSION['51_LearnSNS']);
    // 作成完了ページへ遷移
    header('Location: thanks.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Learn SNS</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
</head>
<body style="margin-top: 60px">
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2 thumbnail">
                <h2 class="text-center content_header">アカウント情報確認</h2>
                <div class="row">
                    <div class="col-xs-4">
                        <img src="../user_profile_img/<?php echo htmlspecialchars($img_name); ?>" class="img-responsive img-thumbnail">
                    </div>
                    <div class="col-xs-8">
                        <div>
                            <span>ユーザー名</span>
                            <!--
                                htmlspecialchars(文字列)
                                XSS(クロスサイトスクリプティング)対策
                                悪意のあるプログラムが書き込まれたとしても、単純な文字列として扱い、サービスを攻撃から守る
                            -->
                            <p class="lead"><?php echo htmlspecialchars($name); ?></p>
                        </div>
                        <div>
                            <span>メールアドレス</span>
                            <p class="lead"><?php echo htmlspecialchars($email); ?></p>
                        </div>
                        <div>
                            <span>パスワード</span>
                            <!-- パスワードはソーシャルハッキング対策で表示しない -->
                            <p class="lead">●●●●●●●●</p>
                        </div>
                        <form method="POST" action="check.php">
                            <!--
                                GET送信時のパラメータ送信
                                URL?キー=値
                                signup.phpに「戻る」で遷移したことがわかるようにパラメータを付与している
                            -->
                            <a href="signup.php?action=rewrite" class="btn btn-default">&laquo;&nbsp;戻る</a> | 
                            <!--
                                formタグ内に何もデータがないと、POST送信しても$_POSTが空になってしまう
                                if(!empty($_POST))これが使えなくなってしまう
                                POST送信に値を何かしら入れておきたいので、
                                下記のinputタグを記述
                            -->
                            <input type="hidden" name="action" value="submit">
                            <input type="submit" class="btn btn-primary" value="ユーザー登録">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>