<div id="main-header">
    <div id="nav">
        <?php if (isset($_SESSION['username'])): ?>
            <ul>
                <li>
                    <span class="headerlink">WELCOME, <span id="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span></span>
                </li>
                <li>
                    <a class="headerlink" href="myaccount.php">MY LISTINGS</a>   
                </li>
                <li>
                    <a class="headerlink" href="myaccount.php">NEW LISTING</a>
                </li>
                <?php if ($_SESSION['role_id'] === 3): ?>
                    <li>
                        <a class="headerlink" href="admincontrol.php">ADMIN SETTINGS</a>
                    </li>
                <?php endif; ?>
                <li>
                    <a class="headerlink" href="logout.php" onclick="return confirmSignOut()">SIGN-OUT</a>
                </li>
            </ul>
        <?php else: ?>
            <ul>
                <li>
                    <a class="headerlink" href="index.php">HOME</a>
                </li>
                <li>
                    <a class="headerlink" href="login.php">LOGIN</a>
                </li>
                <li>
                    <a class="headerlink" href="signup.php">SIGN-UP</a>
                </li>
                <li>
                    <a class="headerlink" href="about.php">ABOUT</a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
    <a href="index.php"><img id="logo" src="images/logo.png"></a>
    <h3 id="future">Find your future.</h3>
</div>