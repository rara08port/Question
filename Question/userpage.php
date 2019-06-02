<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザーページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';
if ($u_id == $_SESSION['user_id']) {
    header("Location:mypage.php?u_id=" . $_SESSION['user_id']);
}
// DBからユーザーデータを取得
$userData = getUser($u_id);
debug('取得したユーザー情報：' . print_r($userData, true));

$dbPostList = getUserPostList($u_id);
debug('取得したユーザー投稿情報：' . print_r($dbPostList, true));
$dbGoodPostList = getUserGoodPostList($u_id);
debug('取得したユーザー投稿情報：' . print_r($dbGoodPostList, true));

?>
<?php
require('head.php');
?>

<body>
    <!-- ヘッダー -->
    <?php require('header.php'); ?>

    <!-- メインコンテンツ -->
    <main>
        <div class="sp-prof-info">
            <div class="prof-icon-wrap">
                <img class="prof-icon" src="<?php echo showImg(sanitize($userData['user_img'])); ?>">
            </div>
            <div class="sp-username"><?php echo sanitize($userData['username']); ?></div>
            <!-- メッセージがある場合のみ表示 -->
            <?php if (!empty($userData['msg'])) { ?>
            <div class="sp-user-msg">
                <p><?php echo sanitize($userData['msg']); ?></p>
            </div>
            <?php 
        } ?>
          

        </div>
        <div class="mypage-wrap">
            <section class="my-contents">
                <section class="bordAll">
                    <div class="bord-btn-container">
                        <a href="bordAll.php">スレッド一覧へGO</a>
                    </div>
                </section>
                <section class="post-list">
                    <h3 class="title">書き込み一覧</h3>
                    <?php
                    if (!empty($dbPostList)) {
                        foreach ($dbPostList as $key => $val) {
                            ?>
                    <div class="bordlist-body">
                        <a href="bordDetail.php?m_id=<?php echo sanitize($val['id']); ?>">
                            <p><?php echo sanitize($val['contents']); ?> </p>
                        </a>
                    </div>
                    <?php

                }
            }
            ?>
                </section>
                <section class="good-list">
                    <h3 class="title">お気に入り一覧</h3>
                    <?php
                    if (!empty($dbGoodPostList)) {
                        foreach ($dbGoodPostList as $key => $val) {
                            ?>
                    <div class="bordlist-body">
                        <a href="bordDetail.php?m_id=<?php echo sanitize($val['id']); ?>">
                            <p><?php echo sanitize($val['contents']); ?> </p>
                        </a>
                    </div>
                    <?php

                }
            }
            ?>
                </section>
            </section>
            <?php require('sidebar_userpage.php'); ?>
        </div>
    </main>

    <!-- フッター -->
    <?php require('footer.php'); ?> 