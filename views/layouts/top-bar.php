<?php
use Router\Router;

$nav_user_name = $nav_user_name ?? current_user_name();
$nav_role_label = $nav_role_label ?? (auth_role() ?? '');
$nav_home_url = $nav_home_url ?? Router::route('/');
$nav_extra_links = $nav_extra_links ?? [];
$nav_current_path = $nav_current_path ?? parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="top-bar">
    <div class="top-bar-content">
        <a href="<?= h($nav_home_url) ?>" class="brand-link" aria-label="RetraiteFlow — Accueil">
            <i class="fas fa-church" aria-hidden="true"></i>
            <span class="brand-text">RetraiteFlow</span>
        </a>

        <div class="top-bar-actions">
            <nav class="top-bar-links" id="topBarLinks" aria-label="Navigation principale">
                <?php foreach ($nav_extra_links as $link):
                    $linkPath = parse_url($link['url'], PHP_URL_PATH) ?: $link['url'];
                    $isActive = ($nav_current_path === $linkPath);
                ?>
                    <a href="<?= h($link['url']) ?>"
                       class="nav-link<?= $isActive ? ' active' : '' ?>"
                       <?= $isActive ? 'aria-current="page"' : '' ?>>
                        <i class="<?= h($link['icon'] ?? 'fas fa-link') ?>" aria-hidden="true"></i>
                        <span><?= h($link['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="theme-toggle-wrap">
                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Activer le thème sombre">
                    <i class="theme-toggle-icon fas fa-sun" aria-hidden="true"></i>
                </button>
            </div>

            <div class="user-menu-container">
                <button class="user-menu-btn" id="userMenuBtn" type="button" aria-label="Menu utilisateur">
                    <div class="user-avatar">
                        <span id="userInitials"><?= h(mb_strtoupper(mb_substr((string) $nav_user_name, 0, 1))) ?></span>
                    </div>
                    <span class="user-name" id="userName"><?= h($nav_user_name) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-info">
                        <div class="user-info-avatar">
                            <span id="userInfoInitials"><?= h(mb_strtoupper(mb_substr((string) $nav_user_name, 0, 1))) ?></span>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-name" id="userInfoName"><?= h($nav_user_name) ?></div>
                            <div class="user-info-role" id="userInfoRole"><?= h($nav_role_label) ?></div>
                        </div>
                    </div>
                    <div class="user-menu-divider"></div>
                    <a href="<?= Router::route('/profile') ?>" class="user-menu-item">
                        <i class="fas fa-user-circle"></i>
                        <span>Profil</span>
                    </a>
                    <a href="<?= Router::route('/settings') ?>" class="user-menu-item">
                        <i class="fas fa-sliders-h"></i>
                        <span>Paramètres</span>
                    </a>
                    <div class="user-menu-divider"></div>
                    <a href="<?= Router::route('/logout') ?>" class="user-menu-item" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>

            <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="Ouvrir le menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    <nav class="mobile-menu-panel" id="mobileMenuPanel" aria-label="Navigation mobile">
        <?php foreach ($nav_extra_links as $link):
            $linkPath = parse_url($link['url'], PHP_URL_PATH) ?: $link['url'];
            $isActive = ($nav_current_path === $linkPath);
        ?>
            <a href="<?= h($link['url']) ?>"
               class="mobile-nav-link<?= $isActive ? ' active' : '' ?>"
               <?= $isActive ? 'aria-current="page"' : '' ?>>
                <i class="<?= h($link['icon'] ?? 'fas fa-link') ?>" aria-hidden="true"></i>
                <span><?= h($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</div>
