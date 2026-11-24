<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string) $_POST['action'] : '';
    $token = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';

    if (!hash_equals($_SESSION['csrf'], $token)) {
        setNotice('Your form expired. Please try again.', 'error');
        redirectTo(isset($_SESSION['user']) ? 'sales' : 'login');
    }

    if ($action === 'login') {
        $username = trim(isset($_POST['username']) ? (string) $_POST['username'] : '');
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
        $matchedUser = null;
        foreach ($store['users'] as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                $matchedUser = $user;
                break;
            }
        }
        if ($matchedUser === null) {
            setNotice('Incorrect username or password.', 'error');
            redirectTo('login');
        }
        session_regenerate_id(true);
        $_SESSION['user'] = array('username' => $matchedUser['username'], 'name' => $matchedUser['name'], 'role' => $matchedUser['role']);
        $_SESSION['cart'] = array();
        setNotice('Welcome back, ' . $matchedUser['name'] . '!', 'success');
        redirectTo('sales');
    }

    if ($action === 'logout') {
        $_SESSION = array();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

    if (!isset($_SESSION['user'])) {
        setNotice('Please log in to continue.', 'error');
        redirectTo('login');
    }

    $userRole = $_SESSION['user']['role'];
    $adminActions = array('save_product', 'delete_product', 'adjust_stock', 'create_user');
    if (in_array($action, $adminActions, true) && $userRole !== 'admin') {
        setNotice('Only administrators can make that change.', 'error');
        redirectTo('sales');
    }

    if ($action === 'create_user') {
        $name = trim(isset($_POST['name']) ? (string) $_POST['name'] : '');
        $username = strtolower(trim(isset($_POST['username']) ? (string) $_POST['username'] : ''));
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? (string) $_POST['confirm_password'] : '';
        $role = isset($_POST['role']) ? (string) $_POST['role'] : '';

        if ($name === '' || !preg_match('/^[a-z0-9._-]{3,30}$/', $username) || !in_array($role, array('cashier', 'staff'), true)) {
            setNotice('Enter a name, a valid username (3–30 letters, numbers, dots, dashes, or underscores), and a valid staff role.', 'error');
            redirectTo('accounts');
        }
        if (strlen($password) < 8 || $password !== $confirmPassword) {
            setNotice('The password must be at least 8 characters and match the confirmation.', 'error');
            redirectTo('accounts');
        }
        foreach ($store['users'] as $account) {
            if (strtolower($account['username']) === $username) {
                setNotice('That username is already in use.', 'error');
                redirectTo('accounts');
            }
        }

        $store['users'][] = array(
            'username' => $username,
            'name' => $name,
            'role' => $role,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        );
        saveStore($store, $dataFile);
        setNotice('The staff account has been created.', 'success');
        redirectTo('accounts');
    }

    if ($action === 'save_product') {
        $id = trim(isset($_POST['product_id']) ? (string) $_POST['product_id'] : '');
        $name = trim(isset($_POST['name']) ? (string) $_POST['name'] : '');
        $category = trim(isset($_POST['category']) ? (string) $_POST['category'] : '');
        $description = trim(isset($_POST['description']) ? (string) $_POST['description'] : '');
        $price = filter_var(isset($_POST['price']) ? $_POST['price'] : null, FILTER_VALIDATE_FLOAT);
        $stock = filter_var(isset($_POST['stock']) ? $_POST['stock'] : null, FILTER_VALIDATE_INT);
        if ($name === '' || $category === '' || $price === false || $price <= 0 || $stock === false || $stock < 0) {
            setNotice('Enter a product name, category, valid price, and non-negative stock quantity.', 'error');
            redirectTo('products');
        }

        $found = false;
        foreach ($store['products'] as &$product) {
            if ($id !== '' && $product['id'] === $id) {
                $product['name'] = $name;
                $product['category'] = $category;
                $product['description'] = $description;
                $product['price'] = (float) $price;
                $product['stock'] = (int) $stock;
                $found = true;
                break;
            }
        }
        unset($product);
        if (!$found) {
            $newId = 'P-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $store['products'][] = array('id' => $newId, 'name' => $name, 'category' => $category, 'description' => $description, 'price' => (float) $price, 'stock' => (int) $stock);
        }
        saveStore($store, $dataFile);
        setNotice($found ? 'Product details updated.' : 'Product added to the catalog.', 'success');
        redirectTo('products');
    }

    if ($action === 'delete_product') {
        $id = isset($_POST['product_id']) ? (string) $_POST['product_id'] : '';
        $store['products'] = array_values(array_filter($store['products'], function ($product) use ($id) {
            return $product['id'] !== $id;
        }));
        saveStore($store, $dataFile);
        setNotice('Product removed.', 'success');
        redirectTo('products');
    }

    if ($action === 'adjust_stock') {
        $id = isset($_POST['product_id']) ? (string) $_POST['product_id'] : '';
        $quantity = filter_var(isset($_POST['quantity']) ? $_POST['quantity'] : null, FILTER_VALIDATE_INT);
        $mode = isset($_POST['mode']) ? (string) $_POST['mode'] : '';
        $updated = false;
        if ($quantity !== false && $quantity >= 0 && in_array($mode, array('add', 'set'), true)) {
            foreach ($store['products'] as &$product) {
                if ($product['id'] === $id) {
                    $product['stock'] = $mode === 'add' ? $product['stock'] + $quantity : $quantity;
                    $updated = true;
                    break;
                }
            }
            unset($product);
        }
        if (!$updated) {
            setNotice('Choose a product and enter a valid quantity.', 'error');
            redirectTo('inventory');
        }
        saveStore($store, $dataFile);
        setNotice($mode === 'add' ? 'Stock received and inventory updated.' : 'Stock quantity updated.', 'success');
        redirectTo('inventory');
    }

    if ($action === 'add_to_cart') {
        $id = isset($_POST['product_id']) ? (string) $_POST['product_id'] : '';
        $quantity = filter_var(isset($_POST['quantity']) ? $_POST['quantity'] : null, FILTER_VALIDATE_INT);
        $selectedProduct = null;
        foreach ($store['products'] as $product) {
            if ($product['id'] === $id) {
                $selectedProduct = $product;
                break;
            }
        }
        if ($selectedProduct === null || $quantity === false || $quantity < 1) {
            setNotice('Choose a product and enter a quantity of at least one.', 'error');
            redirectTo('pos');
        }
        $inCart = isset($_SESSION['cart'][$id]) ? (int) $_SESSION['cart'][$id] : 0;
        if ($inCart + $quantity > $selectedProduct['stock']) {
            setNotice('There is not enough stock for that quantity.', 'error');
            redirectTo('pos');
        }
        $_SESSION['cart'][$id] = $inCart + $quantity;
        setNotice('Item added to the cart.', 'success');
        redirectTo('pos');
    }

    if ($action === 'update_cart') {
        $id = isset($_POST['product_id']) ? (string) $_POST['product_id'] : '';
        $quantity = filter_var(isset($_POST['quantity']) ? $_POST['quantity'] : null, FILTER_VALIDATE_INT);
        $productStock = null;
        foreach ($store['products'] as $product) {
            if ($product['id'] === $id) {
                $productStock = (int) $product['stock'];
                break;
            }
        }
        if ($quantity === false || $quantity < 1) {
            unset($_SESSION['cart'][$id]);
        } elseif ($productStock === null || $quantity > $productStock) {
            setNotice('The requested quantity is not available.', 'error');
            redirectTo('pos');
        } else {
            $_SESSION['cart'][$id] = $quantity;
        }
        redirectTo('pos');
    }

    if ($action === 'remove_from_cart') {
        $id = isset($_POST['product_id']) ? (string) $_POST['product_id'] : '';
        unset($_SESSION['cart'][$id]);
        redirectTo('pos');
    }

    if ($action === 'checkout') {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
        $lineItems = array();
        $total = 0;
        $stockError = false;
        foreach ($cart as $id => $quantity) {
            foreach ($store['products'] as $product) {
                if ($product['id'] === $id) {
                    if ((int) $quantity < 1 || (int) $quantity > (int) $product['stock']) {
                        $stockError = true;
                    } else {
                        $lineTotal = (float) $product['price'] * (int) $quantity;
                        $lineItems[] = array('product_id' => $id, 'name' => $product['name'], 'quantity' => (int) $quantity, 'price' => (float) $product['price'], 'line_total' => $lineTotal);
                        $total += $lineTotal;
                    }
                    break;
                }
            }
        }
        $payment = filter_var(isset($_POST['amount_paid']) ? $_POST['amount_paid'] : null, FILTER_VALIDATE_FLOAT);
        $method = isset($_POST['payment_method']) ? (string) $_POST['payment_method'] : '';
        if (count($lineItems) === 0 || $stockError) {
            setNotice('The cart is empty or stock has changed. Please check the cart and try again.', 'error');
            redirectTo('pos');
        }
        if ($payment === false || $payment < $total || !in_array($method, array('Cash', 'GCash', 'Card'), true)) {
            setNotice('Payment must cover the total. Check the amount and payment method.', 'error');
            redirectTo('pos');
        }
        foreach ($lineItems as $line) {
            foreach ($store['products'] as &$product) {
                if ($product['id'] === $line['product_id']) {
                    $product['stock'] -= $line['quantity'];
                    break;
                }
            }
            unset($product);
        }
        $transactionId = 'SS-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $store['transactions'][] = array(
            'id' => $transactionId,
            'date' => date('Y-m-d H:i:s'),
            'cashier' => $_SESSION['user']['name'],
            'items' => $lineItems,
            'total' => $total,
            'payment_method' => $method,
            'amount_paid' => (float) $payment,
            'change' => (float) $payment - $total
        );
        saveStore($store, $dataFile);
        $_SESSION['cart'] = array();
        setNotice('Sale completed. Transaction ' . $transactionId . ' has been recorded.', 'success');
        redirectTo('sales');
    }

    setNotice('That action was not recognized.', 'error');
    redirectTo('sales');
}

