<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) { echo json_encode([]); exit; }

$products = getProducts(['search' => $q, 'limit' => 8]);
$results  = [];

foreach ($products as $p) {
    $results[] = [
        'id'       => $p['id'],
        'name'     => $p['name'],
        'slug'     => $p['slug'],
        'price'    => $p['price'],
        'category' => $p['category_name'],
        'cover'    => $p['first_image'] ?? '',
    ];
}

echo json_encode($results);
