
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/rental_playbox/index.php">Rental Playbox</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/rental_playbox/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/rental_playbox/view/user/sewa_ps4.php">Sewa PS4</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/rental_playbox/view/user/dashboard.php">Dashboard</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= $_SESSION['user']['nama'] ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/rental_playbox/view/user/dashboard.php">Dashboard</a></li>
                            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="/rental_playbox/view/admin/dashboard.php">Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/rental_playbox/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/rental_playbox/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rental_playbox/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>