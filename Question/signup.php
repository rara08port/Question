<?php

//共通変数・関数ファイルを読込み
require('function.php');


debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if (!empty($_POST)) {
    //変数にユーザー情報を代入
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //未入力チェック
    validRequired($username, 'username');
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if (empty($err_msg)) {
        // ユーザー名文字数チェック
        validMaxLen($username, 'username', 20);

        //emailの形式チェック
        validEmail($email, 'email');
        //emailの最大文字数チェック
        validMaxLen($email, 'email');
        //email重複チェック
        validEmailDup($email);

        //パスワードの半角英数字チェック
        validHalf($pass, 'pass');
        //パスワードの最大文字数チェック
        validMaxLen($pass, 'pass');
        //パスワードの最小文字数チェック
        validMinLen($pass, 'pass');

        //パスワード（再入力）の最大文字数チェック
        validMaxLen($pass_re, 'pass_re');
        //パスワード（再入力）の最小文字数チェック
        validMinLen($pass_re, 'pass_re');
        //パスワードとパスワード（再入力）一致チェック
        validMatch($pass, $pass_re, 'pass_re');
        if (empty($err_msg)) {
            debug('パリデーションチェックOK!');
            //例外処理
            try {
                // DBへ接続
                $dbh = dbConnect();
                // SQL文作成
                $sql = 'INSERT INTO users (username,email,password,login_time,created_date) VALUES(:username,:email,:pass,:login_time,:created_date)';
                $data = array(
                    ':username' => $username, ':email' => $email,
                    ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                    ':login_time' => date('Y-m-d H:i:s'),
                    ':created_date' => date('Y-m-d H:i:s')
                );

                // クエリ実行
                $stmt = queryPost($dbh, $sql, $data);

                // クエリ成功の場合
                if ($stmt) {
                    //ログイン有効期限（デフォルトを１時間とする）
                    $sesLimit = 60 * 60;
                    // 最終ログイン日時を現在日時に
                    $_SESSION['login_date'] = time();
                    $_SESSION['login_limit'] = $sesLimit;
                    // ユーザーIDを格納
                    $_SESSION['user_id'] = $dbh->lastInsertId();

                    debug('セッション変数の中身：' . print_r($_SESSION, true));

                    //header("Location:mypage.php"); //マイページへ
                    header("Location:mypage.php?u_id=" . $_SESSION['user_id']);
                } else {
                    error_log('クエリに失敗しました。');
                    $err_msg['common'] = MSG07;
                }
            } catch (Exception $e) {
                error_log('エラー発生:' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php require('head.php'); ?>

<body class="page-signup">
    <!-- ヘッダー -->
    <?php require('header.php'); ?>

    <main>
        <div class="site-wrap">
            <form action="" method="post" class="form">
                <h2>ユーザー登録</h2>
                <div class="form-wrap">
                    <div class="err_msg">
                        <?php getErrMsg('common'); ?>
                    </div>
                    <label class="<?php if (!empty($err_msg['username'])) echo 'err'; ?>">
                        ユーザー名
                        <input type="text" name="username" value="<?php if (!empty($_POST['username'])) {
                                                                        echo sanitize($_POST['username']);
                                                                    } ?>">
                    </label>
                    <div class="err_msg">
                        <?php getErrMsg('username'); ?>
                    </div>
                    <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                        メールアドレス
                        <input type="text" name="email" value="<?php if (!empty($_POST['email'])) {
                                                                    echo sanitize($_POST['email']);
                                                                } ?>">
                    </label>
                    <div class="err_msg">
                        <?php getErrMsg('email'); ?>
                    </div>
                    <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">
                        パスワード（6文字以上）
                        <input type="password" name="pass" value="<?php if (!empty($_POST['pass'])) {
                                                                        echo sanitize($_POST['pass']);
                                                                    } ?>">
                    </label>
                    <div class="err_msg">
                        <?php getErrMsg('pass'); ?>
                    </div>
                    <label class="<?php if (!empty($err_msg['pass_re'])) echo 'err'; ?>">
                        パスワード再入力
                        <input type="password" name="pass_re" value="<?php if (!empty($_POST['pass_re'])) {
                                                                            echo sanitize($_POST['pass_re']);
                                                                        } ?>">
                    </label>
                    <div class="err_msg">
                        <?php getErrMsg('pass_re'); ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn-primary" value="登録">
                    </div>
                </div>
            </form>
        </div>
    </main>


    <?php require('footer.php'); ?>