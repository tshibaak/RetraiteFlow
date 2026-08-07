<?php
use Router\Router;

$nav_user_name = $nav_user_name ?? current_user_name();
$nav_role_label = $nav_role_label ?? (auth_role() ?? '');
$nav_home_url = $nav_home_url ?? Router::route('/');
$nav_extra_links = $nav_extra_links ?? [];
?>
<div class="top-bar">
    <div class="top-bar-content">
        <a href="<?= h($nav_home_url) ?>" class="brand-link">
            <i class="fas fa-church"></i>
            <span class="brand-text">RetraiteFlow</span>
        </a>

        <div class="top-bar-actions">
            <div class="top-bar-links" id="topBarLinks">
                <?php foreach ($nav_extra_links as $link): ?>
                    <a href="<?= h($link['url']) ?>" class="nav-link">
                        <i class="<?= h($link['icon'] ?? 'fas fa-link') ?>"></i>
                        <span><?= h($link['label']) ?></span>
                    </a>
                <?php endforeach; ?>
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

    <div class="mobile-menu-panel" id="mobileMenuPanel">
        <?php foreach ($nav_extra_links as $link): ?>
            <a href="<?= h($link['url']) ?>" class="mobile-nav-link">
                <i class="<?= h($link['icon'] ?? 'fas fa-link') ?>"></i>
                <span><?= h($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
