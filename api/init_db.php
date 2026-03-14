<?php
require_once __DIR__ . '/config.php';

// Create tables
$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    );

    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL,
        image_path TEXT,
        type TEXT DEFAULT 'ready-made'
    );

    CREATE TABLE IF NOT EXISTS cart_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        quantity INTEGER DEFAULT 1,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    );

    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        total_amount REAL NOT NULL,
        status TEXT DEFAULT 'Processing',
        FOREIGN KEY (user_id) REFERENCES users(id)
    );

    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        quantity INTEGER NOT NULL,
        total_price REAL NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    );

    CREATE TABLE IF NOT EXISTS promo_codes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT UNIQUE NOT NULL,
        discount_percentage REAL NOT NULL,
        valid_from DATETIME NOT NULL,
        valid_to DATETIME NOT NULL,
        usage_limit INTEGER DEFAULT 1
    );

    CREATE TABLE IF NOT EXISTS promo_code_usages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        promo_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        order_id INTEGER NOT NULL,
        used_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (promo_id) REFERENCES promo_codes(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (order_id) REFERENCES orders(id)
    );
");

// Seed products if empty
$count = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
if ($count == 0) {
    $products = [
        ['BEYOND STARS', 60, 'notebooks photos/BEYOND STARS.png'],
        ['Create', 60, 'notebooks photos/Create.png'],
        ['Daily Diary', 60, 'notebooks photos/Daily Diary.png'],
        ['JUST FOCUS', 60, 'notebooks photos/JUST FOCUS.png'],
        ['MY THOUGHTS', 60, 'notebooks photos/MY THOUGHTS.png'],
        ['Pretty Little Notes', 60, 'notebooks photos/Pretty Little Notes.png'],
        ['THOUGHTS OR WAVES', 60, 'notebooks photos/THOUGHTS OR WAVES.png'],
        ['Visça Barca', 60, 'notebooks photos/Visça Barca.png'],
        ['Where ideas begin', 60, 'notebooks photos/Where ideas begin.png'],
        ['Dreamy Wings', 60, 'notebooks photos/Dreamy Wings.png'],
    ];

    $stmt = $db->prepare("INSERT INTO products (name, price, image_path) VALUES (?, ?, ?)");
    foreach ($products as $p) {
        $stmt->execute($p);
    }
    echo "Seeded " . count($products) . " products.\n";
}

// Seed a test promo code
$promoCount = $db->query("SELECT COUNT(*) FROM promo_codes")->fetchColumn();
if ($promoCount == 0) {
    $stmt = $db->prepare("INSERT INTO promo_codes (code, discount_percentage, valid_from, valid_to, usage_limit) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['TEST50', 50.0, date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime('+30 days')), 100]);
    echo "Seeded test promo code: TEST50\n";
}

echo "Database initialized successfully.\n";
