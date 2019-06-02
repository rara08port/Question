<header>
    <h1>Q</h1>
    <nav id="top-nav">
        <ul>
            <?php
            if (empty($_SESSION['user_id'])) {
                ?>
                <li><a href="signup.php">登録</a></li>
                <li><a href="login.php">ログイン</a></li>
            <?php
        } else {
            ?>
                <li class="home-icon"><a href="mypage.php?u_id=<?php echo $_SESSION['user_id'] ?>"><i class="fas fa-home fa-3x"></i></a></li>
            <?php
        }
        ?>
        </ul>
    </nav>
</header>