<?php
require __DIR__ . '/includes/bootstrap.php';

if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$page = isset($_GET['page']) ? (string) $_GET['page'] : (isset($_SESSION['user']) ? 'sales' : 'login');
$legacyPages = array('dashboard' => 'sales', 'transactions' => 'sales', 'reports' => 'sales', 'profile' => 'sales', 'users' => 'accounts');
if (isset($legacyPages[$page])) {
    $page = $legacyPages[$page];
}
$allowedPages = array('login', 'accounts', 'products', 'inventory', 'pos', 'sales', 'catalog');
if (!in_array($page, $allowedPages, true)) {
    $page = isset($_SESSION['user']) ? 'sales' : 'login';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/actions.php';
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
if ($page !== 'catalog' && $page !== 'login' && $user === null) {
    setNotice('Please log in to access the staff system.', 'error');
    redirectTo('login');
}
if ($page === 'login' && $user !== null) {
    redirectTo('sales');
}
if (in_array($page, array('products', 'inventory', 'accounts'), true)) {
    requireAdmin();
}

$notice = isset($_SESSION['notice']) ? $_SESSION['notice'] : null;
unset($_SESSION['notice']);
$products = $store['products'];
$transactions = array_reverse($store['transactions']);
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$cartItems = array();
$cartTotal = 0;
foreach ($cart as $cartId => $cartQuantity) {
    foreach ($products as $product) {
        if ($product['id'] === $cartId) {
            $subtotal = (float) $product['price'] * (int) $cartQuantity;
            $cartItems[] = array('product' => $product, 'quantity' => (int) $cartQuantity, 'subtotal' => $subtotal);
            $cartTotal += $subtotal;
            break;
        }
    }
}
$todaySales = 0;
$todayTransactions = 0;
$totalUnits = 0;
$productSales = array();
foreach ($store['transactions'] as $transaction) {
    if (substr($transaction['date'], 0, 10) === date('Y-m-d')) {
        $todaySales += (float) $transaction['total'];
        $todayTransactions++;
    }
    foreach ($transaction['items'] as $item) {
        $totalUnits += (int) $item['quantity'];
        if (!isset($productSales[$item['product_id']])) {
            $productSales[$item['product_id']] = array('name' => $item['name'], 'quantity' => 0, 'revenue' => 0);
        }
        $productSales[$item['product_id']]['quantity'] += (int) $item['quantity'];
        $productSales[$item['product_id']]['revenue'] += (float) $item['line_total'];
    }
}
uasort($productSales, function ($a, $b) {
    return $b['quantity'] - $a['quantity'];
});
$lowStock = array_values(array_filter($products, function ($product) {
    return (int) $product['stock'] <= 10;
}));
$editProduct = null;
if ($page === 'products' && isset($_GET['edit'])) {
    foreach ($products as $product) {
        if ($product['id'] === (string) $_GET['edit']) {
            $editProduct = $product;
            break;
        }
    }
}
$pageTitles = array('accounts' => 'Authentication & Accounts', 'products' => 'Products & Catalog', 'inventory' => 'Inventory', 'pos' => 'Point of Sale', 'sales' => 'Sales Records & Reports', 'catalog' => 'Product Catalog');

if ($page === 'login') {
    require __DIR__ . '/includes/login-page.php';
    exit;
}

require __DIR__ . '/includes/app-layout-start.php';
require __DIR__ . '/pages/' . $page . '.php';
require __DIR__ . '/includes/app-layout-end.php';