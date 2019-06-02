<?php


//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

/*-------------------------------
	画面処理
-------------------------------*/
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：' . print_r($dbFormData, true));
// DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：' . print_r($userData, true));

if (!empty($_POST)) {
    debug('post送信があります。');
    debug('post情報：' . print_r($_POST, true));
    debug('file情報：' . print_r($_FILES, true));

    $username = $_POST['username'];
    $email = $_POST['email'];
    $msg = $_POST['msg'];
    // 画像をアップロードし、パスを格納
    $user_img = (!empty($_FILES['user_img']['name'])) ? uploadImg($_FILES['user_img'], 'user_img') : '';
    // 画像をpostしなかった場合、既にDBに登録されていたらDBのパスを入れる
    $user_img = (empty($user_img) && !empty($userData['user_img'])) ? $userData['user_img'] : $user_img;

    // DBの情報と入力情報が異なる場合にバリデーションチェックを行う
    if ($userData['username'] !== $username) {
        // 未入力チェック
        validRequired($username, 'username');

        // ユーザー名文字数チェック
        validMaxLen($username, 'username', 20);
    }
    if ($userData['email'] !== $email) {
        // 未入力チェック
        validRequired($email, 'email');
        if (empty($err_msg['email'])) {
            // emailの形式チェック
            validEmail($email, 'email');
            // emailの最大文字数チェック
            validMaxLen($email, 'email');
            // email重複チェック
            validEmailDup($email);
        }
    }
    if ($userData['msg'] !== $msg) {
        // メッセージ文字数チェック
        validMaxLen($msg, 'msg', 100);
    }

    if (empty($err_msg)) {
        debug('バリデーションOKです。');

        try {
            // DBへ接続
            $dbh = dbConnect();
            // SQL文作成
            $sql = 'UPDATE users SET username = :u_name, email = :email, msg = :msg, user_img = :user_img WHERE id = :u_id';
            $data = array(':u_name' => $username, ':email' => $email, ':msg' => $msg, ':user_img' => $user_img, ':u_id' => $dbFormData['id']);
            // クエリ実行
            $stmt = queryPost($dbh, $sql, $data);

            // クエリ成功の場合
            if ($stmt) {

                debug('マイページへ遷移します。');
                header("Location:mypage.php?u_id=" . $_SESSION['user_id']);
            }
        } catch (Exception $e) {
            error_log('エラー発生：' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php require('head.php'); ?>

<body>
    <!-- ヘッダー -->
    <?php require('header.php'); ?>


    <!--  メインコンテンツ -->
    <main>
        <div class="mypage-wrap">
            <div class="my-contents">
                <div class="site-wrap">
                    <form action="" method="post" class="form" enctype="multipart/form-data">
                        <h2>プロフィールを編集</h2>
                        <div class="form-wrap">
                            <div class="err_msg">
                                <?php getErrMsg('common'); ?>
                            </div>
                            <div class="profImgDrop-wrap">


                                <label class="prof-img-area js-area-drop <?php if (!empty($err_msg['user_img'])) echo 'err'; ?>">
                                    <i class="far fa-user fa-3x"></i>
                                    <input type="file" name="user_img" class="input-file">
                                    <img src="<?php echo getFormData('user_img'); ?>" alt="ユーザー画像" class="prev-img" style="<?php if (empty(getFormData('user_img'))) echo 'display:none' ?>">
                                </label>

                                <div class="err_msg">
                                    <?php getErrMsg('user_img'); ?>
                                </div>
                            </div>

                            <label class="<?php if (!empty($err_msg['username'])) echo 'err'; ?>">
                                ユーザー名
                                <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
                            </label>

                            <div class="err_msg">
                                <?php getErrMsg('username'); ?>
                            </div>

                            <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                                メールアドレス
                                <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                            </label>

                            <div class="err_msg">
                                <?php getErrMsg('email'); ?>
                            </div>

                            <label class="<?php if (!empty($err_msg['msg'])) echo 'err'; ?>">
                                メッセージ
                                <textarea id="js-countup" name="msg" rows="10"><?php echo getFormData('msg'); ?></textarea>
                            </label>

                            <p class="counter-text"><span id="js-countup-view">0</span>/100</p>
                            <div class="err_msg">
                                <?php getErrMsg('msg'); ?>
                            </div>
                            <div class="btn-container btn-right">
                                <input type="submit" class="btn-primary btn-mid" value="保存">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require('sidebar_mypage.php'); ?>
        </div>
    </main>

    <!-- フッター -->
    <?php require('footer.php'); ?>