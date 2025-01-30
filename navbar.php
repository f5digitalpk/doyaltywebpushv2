<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/">Your App</a>
        <div class="d-flex align-items-center">
            <a href="/notifications.php" class="text-white me-3 position-relative">
                <i class="fas fa-bell"></i>
                <?php if($unread_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $unread_count ?>
                    </span>
                <?php endif; ?>
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" 
                        type="button" 
                        data-bs-toggle="dropdown">
                    <i class="fas fa-user me-2"></i>
                    <?= htmlspecialchars($_SESSION['mobile']) ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>