<?php
require_once __DIR__ . '/config.php';

// ══════════════════════════════════════════
// CATEGORIES
// ══════════════════════════════════════════

function getAllCategories(bool $activeOnly = true): array {
    $where = $activeOnly ? "WHERE is_active = 1" : "";
    $st = db()->query("SELECT *, (SELECT COUNT(*) FROM products WHERE category_id = categories.id AND is_active=1) AS product_count FROM categories $where ORDER BY sort_order ASC, id ASC");
    return $st->fetchAll();
}

function getCategoryBySlug(string $slug): ?array {
    $st = db()->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
    $st->execute([$slug]);
    return $st->fetch() ?: null;
}

function getCategoryById(int $id): ?array {
    $st = db()->prepare("SELECT * FROM categories WHERE id = ?");
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function createCategory(array $data): int {
    $slug = uniqueSlug('categories', slugify($data['name']));
    $st = db()->prepare("INSERT INTO categories (name, slug, icon, description, sort_order, is_active) VALUES (?,?,?,?,?,?)");
    $st->execute([$data['name'], $slug, $data['icon'] ?? '🔧', $data['description'] ?? '', $data['sort_order'] ?? 0, $data['is_active'] ?? 1]);
    return (int)db()->lastInsertId();
}

function updateCategory(int $id, array $data): void {
    $fields = [];
    $vals   = [];
    foreach (['name','icon','description','sort_order','is_active'] as $k) {
        if (array_key_exists($k, $data)) { $fields[] = "$k=?"; $vals[] = $data[$k]; }
    }
    if (!$fields) return;
    $vals[] = $id;
    db()->prepare("UPDATE categories SET " . implode(',', $fields) . " WHERE id=?")->execute($vals);
}

function deleteCategory(int $id): void {
    db()->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
}

// ══════════════════════════════════════════
// PRODUCTS
// ══════════════════════════════════════════

function getProducts(array $opts = []): array {
    $where  = ['p.is_active = 1'];
    $params = [];

    if (!empty($opts['category_id'])) { $where[] = 'p.category_id = ?'; $params[] = $opts['category_id']; }
    if (!empty($opts['featured']))    { $where[] = 'p.is_featured = 1'; }
    if (!empty($opts['search'])) {
        $where[] = '(p.name LIKE ? OR p.compat LIKE ? OR c.name LIKE ?)';
        $s = '%' . $opts['search'] . '%';
        $params = array_merge($params, [$s,$s,$s]);
    }

    $sort = match ($opts['sort'] ?? 'default') {
        'price-asc'  => 'p.price ASC',
        'price-desc' => 'p.price DESC',
        'rating'     => 'p.rating DESC',
        'newest'     => 'p.created_at DESC',
        default      => 'p.is_featured DESC, p.sort_order ASC, p.id DESC',
    };

    $limit  = isset($opts['limit'])  ? "LIMIT "  . (int)$opts['limit']  : '';
    $offset = isset($opts['offset']) ? "OFFSET " . (int)$opts['offset'] : '';
    $w = $where ? "WHERE " . implode(' AND ', $where) : '';

    $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon,
                   (SELECT image_path FROM product_images WHERE product_id=p.id AND is_cover=1 LIMIT 1) AS cover_image,
                   (SELECT image_path FROM product_images WHERE product_id=p.id ORDER BY is_cover DESC, sort_order ASC LIMIT 1) AS first_image
            FROM products p
            JOIN categories c ON p.category_id = c.id
            $w ORDER BY $sort $limit $offset";

    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function getProductBySlug(string $slug): ?array {
    $st = db()->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
                         FROM products p JOIN categories c ON p.category_id=c.id
                         WHERE p.slug=? AND p.is_active=1 LIMIT 1");
    $st->execute([$slug]);
    $prod = $st->fetch();
    if (!$prod) return null;
    $prod['images'] = getProductImages((int)$prod['id']);
    return $prod;
}

function getProductById(int $id): ?array {
    $st = db()->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug
                         FROM products p JOIN categories c ON p.category_id=c.id
                         WHERE p.id=? LIMIT 1");
    $st->execute([$id]);
    $prod = $st->fetch();
    if (!$prod) return null;
    $prod['images'] = getProductImages($id);
    return $prod;
}

function getProductImages(int $productId): array {
    $st = db()->prepare("SELECT * FROM product_images WHERE product_id=? ORDER BY is_cover DESC, sort_order ASC");
    $st->execute([$productId]);
    return $st->fetchAll();
}

function createProduct(array $data): int {
    $slug = uniqueSlug('products', slugify($data['name']));
    $st = db()->prepare("INSERT INTO products (category_id,name,slug,description,price,old_price,compat,rating,reviews_count,is_active,is_featured,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $st->execute([
        $data['category_id'], $data['name'], $slug,
        $data['description'] ?? '', $data['price'], $data['old_price'] ?? null,
        $data['compat'] ?? '', $data['rating'] ?? 4.5, $data['reviews_count'] ?? 0,
        $data['is_active'] ?? 1, $data['is_featured'] ?? 0, $data['sort_order'] ?? 0,
    ]);
    return (int)db()->lastInsertId();
}

function updateProduct(int $id, array $data): void {
    $allowed = ['category_id','name','description','price','old_price','compat','rating','reviews_count','is_active','is_featured','sort_order'];
    $fields = []; $vals = [];
    foreach ($allowed as $k) {
        if (array_key_exists($k, $data)) { $fields[] = "$k=?"; $vals[] = $data[$k]; }
    }
    if (!$fields) return;
    $vals[] = $id;
    db()->prepare("UPDATE products SET " . implode(',', $fields) . " WHERE id=?")->execute($vals);
}

function deleteProduct(int $id): void {
    // delete images from disk
    $imgs = getProductImages($id);
    foreach ($imgs as $img) {
        $path = UPLOAD_DIR . basename($img['image_path']);
        if (file_exists($path)) unlink($path);
    }
    db()->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
}

function addProductImage(int $productId, string $path, bool $isCover = false, int $sort = 0): int {
    $st = db()->prepare("INSERT INTO product_images (product_id,image_path,is_cover,sort_order) VALUES (?,?,?,?)");
    $st->execute([$productId, $path, $isCover ? 1 : 0, $sort]);
    return (int)db()->lastInsertId();
}

function deleteProductImage(int $imgId): void {
    $st = db()->prepare("SELECT image_path FROM product_images WHERE id=?");
    $st->execute([$imgId]);
    $row = $st->fetch();
    if ($row) {
        $p = UPLOAD_DIR . basename($row['image_path']);
        if (file_exists($p)) unlink($p);
    }
    db()->prepare("DELETE FROM product_images WHERE id=?")->execute([$imgId]);
}

function setCoverImage(int $productId, int $imgId): void {
    db()->prepare("UPDATE product_images SET is_cover=0 WHERE product_id=?")->execute([$productId]);
    db()->prepare("UPDATE product_images SET is_cover=1 WHERE id=?")->execute([$imgId]);
}

// ── Upload image file
function uploadProductImage(array $file): string {
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array($file['type'], $allowed)) throw new Exception('Invalid file type.');
    if ($file['size'] > 10 * 1024 * 1024) throw new Exception('File too large (max 10MB).');
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $name = uniqid('prod_', true) . '.' . $ext;
    $dest = UPLOAD_DIR . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) throw new Exception('Upload failed.');
    return UPLOAD_URL . $name;
}

// ── Unique slug
function uniqueSlug(string $table, string $base): string {
    $slug = $base; $i = 1;
    while (true) {
        $st = db()->prepare("SELECT id FROM $table WHERE slug=? LIMIT 1");
        $st->execute([$slug]);
        if (!$st->fetch()) return $slug;
        $slug = $base . '-' . $i++;
    }
}

// ── Dashboard stats
function getDashboardStats(): array {
    $pdo = db();
    return [
        'products'   => (int)$pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn(),
        'categories' => (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE is_active=1")->fetchColumn(),
        'featured'   => (int)$pdo->query("SELECT COUNT(*) FROM products WHERE is_featured=1 AND is_active=1")->fetchColumn(),
        'images'     => (int)$pdo->query("SELECT COUNT(*) FROM product_images")->fetchColumn(),
    ];
}

function imgUrl(?string $path): string {
    if (!$path) return PLACEHOLDER_IMG;
    if (str_starts_with($path,'http')) return $path;
    return BASE_URL . '/' . ltrim($path, '/');
}
