<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　スレッド一覧ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
//ログイン認証
require('auth.php');
//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
//掲示板リスト取得
$bordlist = getBordList();
debug('取得したスレッド一覧DBデータ：' . print_r($bordlist, true));
// POST送信時処理
//================================
if (!empty($_POST)) {
    debug('POST送信があります。');
    debug('POST情報：' . print_r($_POST, true));
    debug('file情報：' . print_r($_FILES, true));

    $contents = $_POST['contents'];
    // 画像をアップロードし、パスを格納
    $post_img = (!empty($_FILES['post_img']['name'])) ? uploadImg($_FILES['post_img'], 'post_img') : '';
    $user_id = $_SESSION['user_id'];

    //未入力チェック
    validRequired($contents, 'contents');
    //最大文字数チェック
    validMaxLen($contents, 'contents', 200);
    //セレクトボックスチェック


    if (empty($err_msg)) {
        debug('バリデーションOKです。');

        //例外処理

        try {
            // DBへ接続
            $dbh = dbConnect();
            // SQL文作成
            $sql = 'INSERT INTO post (user_id,contents,post_img,created_date) VALUES(:user_id,:contents,:post_img,:created_date)';
            $data = array(
                ':user_id' => $user_id, ':contents' => $contents,
                ':post_img' => $post_img,
                ':created_date' => date('Y-m-d H:i:s')
            );


            debug('SQL：' . $sql);
            debug('流し込みデータ：' . print_r($data, true));
            // クエリ実行
            $stmt = queryPost($dbh, $sql, $data);

            // クエリ成功の場合
            if ($stmt) {
                debug('クエリ成功。');

                debug('マイページへ遷移します。');
                header("Location:bordAll.php"); //マイページへ
            } else {
                debug('クエリに失敗しました。');
                $err_msg['common'] = MSG07;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');


?>

<?php require('head.php'); ?>

<body class="page-bordAll">

    <!-- ヘッダー -->
    <?php require('header.php'); ?>

    <!-- メインコンテンツ -->
    <main>
        <div class="bord-wrap">
            <div class="bord-contents">
                <div class="bord-site-wrap">
                    <h2>スレッド一覧</h2>
                    <?php
                    if (!empty($bordlist)) {
                        foreach ($bordlist as $key => $val) {
                            ?>
                            <div class="bordlist-body">
                                <a href="bordDetail.php?m_id=<?php echo sanitize($val['id']); ?>">
                                    <p><?php echo mb_substr(sanitize($val['contents']), 0, 30); ?>
                                        <?php if (mb_strlen($val['contents']) > 30) { ?>
                                            ・・・
                                        <?php
                                    } ?>
                                    </p>
                                </a>
                            </div>
                        <?php
                    }
                }
                ?>
                </div>
            </div>
            <form action="" method="post" class="form" enctype="multipart/form-data">
                <div class="form-wrap">

                    <div class="err_msg">
                        <?php getErrMsg('common'); ?>
                    </div>

                    <label class="<?php if (!empty($err_msg['contents'])) echo 'err'; ?>">
                        スレッド入力
                        <textarea id="js-countup" name="contents" cols=63 rows=12><?php if (!empty($_POST['contents'])) {
                                                                                        echo sanitize($_POST['contents']);
                                                                                    } ?></textarea>

                    </label>
                    <p class="counter-text"><span id="js-countup-view">0</span>/200</p>
                    <div class="err_msg">
                        <?php getErrMsg('contents'); ?>
                    </div>

                    <div class="imgDrop-wrap">
                        <label class="img-area js-area-drop <?php if (!empty($err_msg['post_img'])) echo 'err'; ?>">
                            <i class="far fa-image fa-4x"></i>
                            <input type="file" name="post_img" class="input-file">
                            <img src="<?php echo sanitize($_POST['post_img']); ?>" alt="投稿画像" class="prev-img" style="<?php echo 'display:none;' ?>">
                        </label>
                        <div class="err_msg">
                            <?php getErrMsg('post_img'); ?>
                        </div>
                    </div>

                    <div class="btn-container btn-right">
                        <input type="submit" name="submit" class="btn-primary btn-mid" value="送信">
                    </div>
                </div>
            </form>
        </div>
    </main>


    <!-- footer -->
    <?php require('footer.php'); ?>