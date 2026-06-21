<?php
use Router\Router;

$nav_user_name = $nav_user_name ?? current_user_name();
$nav_role_label = $nav_role_label ?? ($_SESSION['role'] ?? '');
$nav_home_url = $nav_home_url ?? Router::route('/');
$nav_extra_links = $nav_extra_links ?? [];
?>
<div class="top-bar">
    <div class="top-bar-content">
        <div class="top-bar-left">
            <a href="<?= h($nav_home_url) ?>" class="brand-link">
                <i class="fas fa-church"></i>
                <span class="brand-text">RetraiteFlow</span>
            </a>
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
                    <span id="userInitials"><?= h(mb_strtoupper(mb_substr($nav_user_name, 0, 1))) ?></span>
                </div>
                <span class="user-name" id="userName"><?= h($nav_user_name) ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="user-menu-dropdown" id="userMenuDropdown">
                <div class="user-info">
                    <div class="user-info-avatar">
                        <span id="userInfoInitials"><?= h(mb_strtoupper(mb_substr($nav_user_name, 0, 1))) ?></span>
                    </div>
                    <div class="user-info-text">
                        <div class="user-info-name" id="userInfoName"><?= h($nav_user_name) ?></div>
                        <div class="user-info-role" id="userInfoRole"><?= h($nav_role_label) ?></div>
                    </div>
                </div>
                <div class="user-menu-divider"></div>
                <a href="<?= Router::route('/logout') ?>" class="user-menu-item" id="logoutBtn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </div>
</div>
