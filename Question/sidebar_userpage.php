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
</div>