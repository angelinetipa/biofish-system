<!-- Header -->
<div class="header">
    <div class="header-left">
        <div class="logo">🐟</div>
        <div class="header-title">
            <h1><?php echo APP_NAME; ?> MONITOR</h1>
            <p><?php echo APP_DESCRIPTION; ?></p>
        </div>
    </div>
    <div class="header-right">
        <div class="user-info">
            <div class="user-name"><?php echo get_user_name(); ?></div>
            <div class="user-role"><?php echo ucfirst(get_user_role()); ?></div>
        </div>
        <button class="logout-btn" onclick="showLogoutModal()">Logout</button>
    </div>
</div>