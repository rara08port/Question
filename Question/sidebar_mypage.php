<!-- サイドバー -->

<div class="sidebar">
    <?php debug('sidebar：' . print_r($userData, true)); ?>
    <div class="prof-icon-wrap">
        <img class="prof-icon" src="<?php echo showImg(sanitize($userData['user_img'])); ?>">
    </div>
    <ul class="prof">
        <li class="username">
            <?php echo sanitize($userData['username']); ?>
        </li>
        <li class="user-msg">
            <?php echo sanitize($userData['msg']); ?>
        </li>
    </ul>
    <ul class="menu">
        <li><a href="profEdit.php?u_id=<?php echo $_SESSION['user_id'] ?>">プロフィール編集</a></li>
        <li><a href="logout.php">ログアウト</a></li>
        <li><a href="passEdit.php?u_id=<?php echo $_SESSION['user_id'] ?>">パスワード変更</a></li>
        <li><a href="withdraw.php?u_id=<?php echo $_SESSION['user_id'] ?>">退会</a></li>
    </ul>

</div>