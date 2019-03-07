<?php
session_start();
require 'dbconnect.php';
// 不備を入れておく配列
// 最初は何もないので空で定義
$errors = [];
// POST送信時
if (!empty($_POST)) {
    $email = $_POST['input_email'];
    $password = $_POST['input_password'];
    // 空チェック
    if ($email != '' && $password != '') {
        // 正常系
        // 入力されたメールアドレスと一致する登録データを1件取得
        $sql = 'SELECT * FROM `users` WHERE `email` = ?';
        $data = [$email];
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>';
        // var_dump($record);
        // echo '</pre>';
        // メールアドレスが登録されていない場合
        if ($record == false) {
            $errors['signin'] = 'failed';
        }
        // 取得したデータのパスワードと入力されたパスワードが等しいか確認
        // password_verify(文字列, ハッシュ化された文字列);
        // 指定された二つの文字列が合致するか確認
        // 等しい場合true, 異なる場合false
        if (password_verify($password, $record['password'])) {
            // 認証成功
            // TODO: 宿題
            $_SESSION['51_LearnSNS']['id'] = $record['id'];

            //パスワードが正しい場合タイムライン場面に遷移
            header('Location: timeline.php');
            exit();
        // セッションにユーザーのIDを格納
            // パスワードが正しい場合タイムライン画面に遷移
        } else {
            // 認証失敗
            $errors['signin'] = 'failed';
        }
    } else {
        // いずれかが空だった場合
        $errors['signin'] = 'blank';
    }
}
?>
<?php include 'layouts/header.php'; ?>
<body style="margin-top: 60px">
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2 thumbnail">
                <h2 class="text-center content_header">サインイン</h2>
                <form method="POST" action="signin.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="email">メールアドレス</label>
                        <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com">

                        <?php if (isset($errors['signin']) && $errors['signin'] == 'blank'): ?>
                            <p class="text-danger">メールアドレスとパスワードを正しく入力してください</p>
                        <?php endif; ?>

                        <?php if (isset($errors['signin']) && $errors['signin'] == 'failed'): ?>
                            <p class="text-danger">サインインに失敗しました</p>
                        <?php endif; ?>

                    </div>
                    <div class="form-group">
                        <label for="password">パスワード</label>
                        <input type="password" name="input_password" class="form-control" id="password" placeholder="4 ~ 16文字のパスワード">
                    </div>
                    <input type="submit" class="btn btn-info" value="サインイン">
                    <span style="float: right; padding-top: 6px;">
                        <a href="index.php">戻る</a>
                    </span>
                </form>
            </div>
        </div>
    </div>
</body>
<?php include 'layouts/header.php'; ?>
</html>