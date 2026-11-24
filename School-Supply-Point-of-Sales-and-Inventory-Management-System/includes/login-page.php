<?php
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SupplyPoint</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible+Next:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">
    <main class="login-layout">
        <section class="login-panel">
            <div class="login-card">
                <div class="mobile-brand"><span class="brand-mark">S</span> SupplyPoint</div>
                <span class="eyebrow orange">WELCOME BACK</span>
                <h2>Sign in to your account</h2>
                <p class="muted">Enter your staff login details to continue.</p>
                <?php if ($notice): ?><div class="notice <?php echo e($notice['type']); ?>"><?php echo e($notice['message']); ?></div><?php endif; ?>
                <form method="post" class="stack-form">
                    <input type="hidden" name="csrf" value="<?php echo e($_SESSION['csrf']); ?>">
                    <input type="hidden" name="action" value="login">
                    <label>Username<input name="username" type="text" autocomplete="username" placeholder="Enter your username" required></label>
                    <label>Password<div class="password-control"><input id="loginPassword" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required><button class="password-toggle" type="button" data-toggle-password aria-controls="loginPassword" aria-label="Show password" aria-pressed="false">Show</button></div></label>
                    <button class="button button-primary button-wide" type="submit">Sign in <span>→</span></button>
                </form>
                <a class="catalog-link" href="index.php?page=catalog">Browse the public product catalog →</a>
            </div>
        </section>

    </main>
<script src="assets/app.js"></script>
</body>
</html>
