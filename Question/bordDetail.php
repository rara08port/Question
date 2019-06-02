<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　メッセージ掲示板ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
//ログイン認証
require('auth.php');
//================================
// 画面処理
//================================
$dbPostData = '';
$dbPostUserInfo = '';
$dbCommentList = '';

// 画面表示用データ取得
//================================
// GETパラメータを取得
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
// 投稿IDのGETパラメータを取得

// DBから投稿データを取得
$dbPostData = getPostData($m_id);
debug('取得したDBデータ：' . print_r($dbPostData, true));
// 投稿者の情報
$dbPostUserInfo = getUser($dbPostData['user_id']);

// DBからコメントを取得
$dbCommentList = getComment($m_id);
debug('取得したDBデータ：' . print_r($dbCommentList, true));

// post送信されていた場合
if (!empty($_POST)) {
    debug('POST送信があります。');
    debug('POST情報：' . print_r($_POST, true));
    $user_id = $_SESSION['user_id'];



    //バリデーションチェック
    //$comment = (isset($_POST['comment'])) ? $_POST['comment'] : '';
    $comment = $_POST['comment'];
    //最大文字数チェック
    validMaxLen($comment, 'comment', 200);
    //未入力チェック
    validRequired($comment, 'comment');

    if (empty($err_msg)) {
        debug('バリデーションOKです。');


        //例外処理
        try {
            // DBへ接続
            $dbh = dbConnect();
            // SQL文作成
            $sql = 'INSERT INTO comment (post_id,user_id,comment,created_date) 
			VALUES(:post_id,:user_id,:comment,:created_date)';
            $data = array(
                ':post_id' => $m_id, ':user_id' => $user_id, ':comment' => $comment,
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
                //header("Location: ".bordDetail.'.'.php.'?m_id='.$m_id); //マイページへ
                header("Location: " . $_SERVER['PHP_SELF'] . '?m_id=' . $m_id); //自分自身に遷移する
            } else {
                debug('クエリに失敗しました。');
                $err_msg['msg'] = MSG07;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
require('head.php');
?>

<body class="page-msg">

    <!-- メニュー -->
    <?php
    require('header.php');
    ?>

    <main>
        <div class="bord-wrap">
            <div class="bord-contents">
                <div class="bord-site-wrap">
                    <h2>スレッド詳細</h2>
                    <i class="fa fa-heart icn-like js-click-like <?php if (isLike($_SESSION['user_id'], $m_id)) {
                                                                        echo 'active';
                                                                    } ?>" aria-hidden="ture" data-goodid="<?php echo $m_id; ?>"></i>

                    <!--投稿詳細-->
                    <section class="post">
                        <div class="icon-wrap">
                            <a href="userpage.php?u_id=<?php echo sanitize($dbPostUserInfo['id']); ?>">
                                <img class="user-icon" src="<?php echo showImg(sanitize($dbPostUserInfo['user_img'])); ?>">
                            </a>
                        </div>
                        <div class="post-wrap">
                            <div class="post-head">
                                <a href="userpage.php?u_id=<?php echo sanitize($dbPostUserInfo['id']); ?>" class="username"><?php echo sanitize($dbPostUserInfo['username']); ?></a>
                                <time><?php echo date('Y/m/d H:i', strtotime(sanitize($dbPostData['created_date']))); ?></time>
                            </div>
                            <p><?php echo (sanitize($dbPostData['contents'])); ?></p>
                            <?php if (!empty($dbPostData['post_img'])) : ?>
                                <div class="post-img-wrap">
                                    <img class="post-img" src="<?php echo sanitize($dbPostData['post_img']); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                    <!--コメント一覧-->
                    <?php
                    foreach ($dbCommentList as $key => $val) :
                        $dbCommentUserId = $val['user_id'];
                        $dbCommentUserInfo = getUser($dbCommentUserId);
                        ?>
                        <section class="comment">
                            <div class="icon-wrap">
                                <a href="userpage.php?u_id=<?php echo sanitize($dbCommentUserInfo['id']); ?>">
                                    <img class="user-icon" src="<?php echo showImg(sanitize($dbCommentUserInfo['user_img'])); ?>">
                                </a>
                            </div>
                            <div class="post-wrap">
                                <div class="post-head">
                                    <a href="userpage.php?u_id=<?php echo sanitize($dbCommentUserInfo['id']); ?>" class="username"><?php echo sanitize($dbCommentUserInfo['username']); ?></a>
                                    
                                    <time><?php echo date('Y/m/d H:i', strtotime(sanitize($val['created_date']))); ?></time>
                                </div>
                                <p>
                                    <?php echo nl2br(sanitize($val['comment'])); ?>
                                </p>
                            </div>
                        </section>
                    <?php
                endforeach;
                ?>


                </div>
            </div>
            <form action="" method="post" class="form">
                <div class="form-wrap">
                    <div class="err_msg">
                        <?php getErrMsg('common'); ?>
                    </div>
                    <label class="<?php if (!empty($err_msg['comment'])) echo 'err'; ?>">
                        スレッド入力
                        <textarea id="js-countup" name="comment" cols=63 rows=12><?php if (!empty($_POST['contents'])) {
                                                                                        echo sanitize($_POST['contents']);
                                                                                    } ?></textarea>
                    </label>
                    <p class="counter-text"><span id="js-countup-view">0</span>/200</p>
                    <div class="err_msg">
                        <?php getErrMsg('comment'); ?>
                    </div>
                    <div class="btn-container btn-right">
                        <input type="submit" name="submit" class="btn-primary btn-mid" value="送信">
                    </div>
                </div>
            </form>
        </div>
    </main>






    <!-- footer -->
    <?php
    require('footer.php');
    ?>