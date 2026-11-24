<?php
require __DIR__ . '/icons.php';
session_start();
date_default_timezone_set('Asia/Manila');

$dataDirectory = dirname(__DIR__) . '/data';
$dataFile = $dataDirectory . '/store.json';

if (!is_dir($dataDirectory) && !mkdir($dataDirectory, 0775, true) && !is_dir($dataDirectory)) {
    throw new RuntimeException('The data folder could not be created.');
}

if (!file_exists($dataFile)) {
    $initialData = array(
        'users' => array(
            array('username' => 'admin', 'name' => 'Store Administrator', 'role' => 'admin', 'password' => password_hash('admin123', PASSWORD_DEFAULT)),
            array('username' => 'cashier', 'name' => 'Store Cashier', 'role' => 'cashier', 'password' => password_hash('cashier123', PASSWORD_DEFAULT))
        ),
        'products' => array(
            array('id' => 'P-1001', 'name' => 'Composition Notebook', 'category' => 'Paper Supplies', 'price' => 35.00, 'stock' => 48, 'description' => 'A ruled notebook for everyday school notes.'),
            array('id' => 'P-1002', 'name' => 'Blue Ballpen', 'category' => 'Writing Tools', 'price' => 12.00, 'stock' => 85, 'description' => 'Smooth-writing blue ink ballpoint pen.'),
            array('id' => 'P-1003', 'name' => 'Pencil Set', 'category' => 'Writing Tools', 'price' => 25.00, 'stock' => 32, 'description' => 'A set of two pencils with erasers.'),
            array('id' => 'P-1004', 'name' => 'Colored Paper', 'category' => 'Art Supplies', 'price' => 45.00, 'stock' => 18, 'description' => 'Assorted colors for school projects.'),
            array('id' => 'P-1005', 'name' => 'Glue Stick', 'category' => 'Art Supplies', 'price' => 30.00, 'stock' => 7, 'description' => 'Easy-to-use glue stick for paper crafts.')
        ),
        'transactions' => array()
    );
    if (file_put_contents($dataFile, json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) {
        throw new RuntimeException('The starter data could not be saved.');
    }
}

$storedData = file_get_contents($dataFile);
$store = json_decode($storedData, true);
if (!is_array($store) || !isset($store['users'], $store['products'], $store['transactions'])) {
    throw new RuntimeException('The store data file is unreadable or has an invalid format.');
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money($amount)
{
    return '₱' . number_format((float) $amount, 2);
}

function saveStore($store, $file)
{
    $json = json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false || file_put_contents($file, $json, LOCK_EX) === false) {
        throw new RuntimeException('Changes could not be saved. Check that the data folder is writable.');
    }
}

function redirectTo($page)
{
    header('Location: index.php?page=' . urlencode($page));
    exit;
}

function setNotice($message, $type)
{
    $_SESSION['notice'] = array('message' => $message, 'type' => $type);
}

function requireAdmin()
{
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        setNotice('That page is available to administrators only.', 'error');
        redirectTo('sales');
    }
}
