<nav class="navbar navbar-expand-lg noselec">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fi fi-br-menu-burger"></i>
            </button>
            <img src="/proyecto-lacruz-j/src/assets/images/logo2.png" class="navbar-logo" alt="logo">
            <a class="navbar-brand" href="index.php?c=loginControlador&m=home">J.Lacruz</a>
        </div>
        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span class="user-name ms-2">
                        <?php echo htmlspecialchars($_SESSION['usuario']['username'] ?? 'Usuario'); ?>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="index.php?c=usuarioControlador&m=perfil">
                            <i class="fas fa-user me-2"></i>
                            Mi Perfil
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>