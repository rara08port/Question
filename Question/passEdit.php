<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード変更　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

/*-------------------------------
	画面処理 
-------------------------------*/
// DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：' . print_r($userData, true));


// post送信されていた場合
if (!empty($_POST)) {
    debug('POST情報：' . print_r($_POST, true));

    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $new_pass_re = $_POST['new_pass_re'];

    // 未入力チェック
    validRequired($old_pass, 'old_pass');
    validRequired($new_pass, 'new_pass');
    validRequired($new_pass_re, 'new_pass_re');

    if (empty($err_msg)) {
        // 現在のパスワードのバリデーションチェック
        validPass($old_pass, 'old_pass');
        // 新しいパスワードのバリデーションチェック
        validPass($new_pass, 'new_pass');

        // 現在DBにあるパスワードと入力したパスワードが合っているかチェック
        if (!password_verify($old_pass, $userData['password'])) {
            $err_msg['old_pass'] = MSG10;
        }

        if (empty($err_msg)) {
            // 現在のパスワードと新しいパスワードは異なるようにする
            if ($old_pass === $new_pass) {
                $err_msg['new_pass'] = MSG11;
            }
            validMatch($new_pass, $new_pass_re, 'new_pass_re');

            if (empty($err_msg)) {
                debug('バリデーションOK!');

                try {
                    $dbh = dbConnect();
                    $sql = 'UPDATE users SET password = :pass WHERE id = :id';
                    $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($new_pass, PASSWORD_DEFAULT));
                    // クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);

                    // クエリ成功の場合
                    if ($stmt) {

                        header("Location:mypage.php?u_id=" . $_SESSION['user_id']);
                    }
                } catch (Exception $e) {
                    error_log('エラー発生：' . $e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
    }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>




<?php require('head.php'); ?>

<body>
    <?php require('header.php'); ?>

    <main>
        <div class="mypage-wrap">
            <div class="my-contents">
                <div class="site-wrap">
                    <form action="" method="post" class="form">
                        <h2>パスワードを変更</h2>
                        <div class="form-wrap">
                            <div class="err_msg">
                                <?php if (!empty($err_msg['common'])) {
                                    echo $err_msg['common'];
                                } ?>
                            </div>
                            <label class="<?php if (!empty($err_msg['old_pass'])) echo 'err'; ?>">
                                現在のパスワード（6文字以上）
                                <input type="password" name="old_pass" value="<?php if (!empty($_POST['old_pass'])) {
                                                                                    echo sanitize($_POST['old_pass']);
                                                                                } ?>">

                            </label>
                            <div class="err_msg">
                                <?php if (!empty($err_msg['old_pass'])) {
                                    echo $err_msg['old_pass'];
                                } ?>
                            </div>
                            <label class="<?php if (!empty($err_msg['new_pass'])) echo 'err'; ?>">
                                新しいパスワード（6文字以上）
                                <input type="password" name="new_pass" value="<?php if (!empty($_POST['new_pass'])) {
                                                                                    echo sanitize($_POST['new_pass']);
                                                                                } ?>">
                            </label>
                            <div class="err_msg">
                                <?php getErrMsg('new_pass'); ?>
                            </div>
                            <label class="<?php if (!empty($err_msg['new_pass_re'])) echo 'err'; ?>">
                                新しいパスワード再入力
                                <input type="password" name="new_pass_re" value="<?php if (!empty($_POST['new_pass_re'])) {
                                                                                        echo sanitize($_POST['new_pass_re']);
                                                                                    } ?>">
                            </label>
                            <div class="err_msg">
                                <?php getErrMsg('new_pass_re'); ?>
                            </div>
                            <div class="btn-container btn-right">
                                <input type="submit" class="btn-primary btn-mid" value="変更">
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <?php require('sidebar_mypage.php'); ?>
        </div>
    </main>
    <?php require('footer.php'); ?>