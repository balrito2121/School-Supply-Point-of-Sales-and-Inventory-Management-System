<?php
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(isset($pageTitles[$page]) ? $pageTitles[$page] . ' | SupplyPoint' : 'SupplyPoint'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible+Next:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="index.php?page=sales"><span class="brand-mark">S</span><span>Supply<span class="brand-light-text">Point</span></span></a>
            <div class="store-label">SCHOOL STORE</div>
            <nav class="main-nav">
                <?php if ($user): ?>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a class="<?php echo $page === 'accounts' ? 'active' : ''; ?>" href="index.php?page=accounts"><span class="nav-icon"><?php echo icon('users'); ?></span>Authentication &amp; Accounts</a>
                    <?php endif; ?>
                    <a class="<?php echo in_array($page, array('products', 'catalog'), true) ? 'active' : ''; ?>" href="index.php?page=<?php echo $user['role'] === 'admin' ? 'products' : 'catalog'; ?>"><span class="nav-icon"><?php echo icon('products'); ?></span>Products &amp; Catalog</a>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a class="<?php echo $page === 'inventory' ? 'active' : ''; ?>" href="index.php?page=inventory"><span class="nav-icon"><?php echo icon('inventory'); ?></span>Inventory</a>
                    <?php endif; ?>
                    <a class="<?php echo $page === 'pos' ? 'active' : ''; ?>" href="index.php?page=pos"><span class="nav-icon"><?php echo icon('pos'); ?></span>Point of Sale</a>
                    <a class="<?php echo $page === 'sales' ? 'active' : ''; ?>" href="index.php?page=sales"><span class="nav-icon"><?php echo icon('reports'); ?></span>Sales Records &amp; Reports</a>
                <?php endif; ?>
                <?php if (!$user): ?><a class="<?php echo $page === 'catalog' ? 'active' : ''; ?>" href="index.php?page=catalog"><span class="nav-icon"><?php echo icon('catalog'); ?></span>Product Catalog</a><?php endif; ?>
            </nav>
            <?php if ($user): ?>
                <div class="sidebar-bottom">
                    <div class="user-card">
                        <div class="avatar"><?php echo e(strtoupper(substr($user['name'], 0, 1))); ?></div>
                        <div><strong><?php echo e($user['name']); ?></strong><span><?php echo e(ucfirst($user['role'])); ?></span></div>
                    </div>
                    <form method="post" data-logout-form><input type="hidden" name="csrf" value="<?php echo e($_SESSION['csrf']); ?>"><input type="hidden" name="action" value="logout"><button class="logout-button" type="submit"><?php echo icon('logout'); ?><span>Sign out</span></button></form>
                </div>
            <?php else: ?>
                <div class="sidebar-bottom"><a class="login-sidebar-link" href="index.php?page=login">Staff sign in →</a></div>
            <?php endif; ?>
        </aside>
        <main class="main-content">
            <header class="topbar"><div><span class="topbar-label">SCHOOL SUPPLY STORE</span><h1><?php echo e(isset($pageTitles[$page]) ? $pageTitles[$page] : 'SupplyPoint'); ?></h1></div><div class="topbar-date"><?php echo date('D, M j, Y'); ?><span class="date-dot"></span></div></header>
            <div class="content">
                <?php if ($notice): ?><div class="notice <?php echo e($notice['type']); ?>"><?php echo e($notice['message']); ?></div><?php endif; ?>