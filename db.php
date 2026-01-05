<?php
$dbHost = 'sql111.infinityfree.com';
$dbUser = 'if0_40825026';
$dbPass = 'Amit1447';
$dbName = 'if0_40825026_cars_assessment';

try {
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Database connection error.';
    exit;
}
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
      id INT AUTO_INCREMENT PRIMARY KEY,
      k VARCHAR(100) UNIQUE,
      v TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS banners (
      id INT AUTO_INCREMENT PRIMARY KEY,
      title VARCHAR(200),
      image VARCHAR(255),
      active TINYINT DEFAULT 1,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS cars (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(200),
      image VARCHAR(255),
      type ENUM('most_searched','latest'),
      category VARCHAR(50) DEFAULT 'SUV',
      active TINYINT DEFAULT 1,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS submissions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100),
      phone VARCHAR(20),
      email VARCHAR(100),
      address TEXT,
      options VARCHAR(100),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(150) UNIQUE,
      password_hash VARCHAR(255),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $stmt = $pdo->prepare("INSERT INTO site_settings (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)");
    $stmt->execute(['site_title','Car Portal']);
    $pdo->exec("CREATE TABLE IF NOT EXISTS upcoming_cars (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(200),
      image VARCHAR(255),
      price_min DECIMAL(12,2) DEFAULT 0,
      price_max DECIMAL(12,2) DEFAULT 0,
      badge VARCHAR(50),
      launch_date DATE,
      active TINYINT DEFAULT 1,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    try {
        $check = $pdo->prepare("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cars' AND COLUMN_NAME = 'category'");
        $check->execute();
        $row = $check->fetch();
        if ((int)($row['c'] ?? 0) === 0) {
            $pdo->exec("ALTER TABLE cars ADD COLUMN category VARCHAR(50) DEFAULT 'SUV'");
        }
    } catch (Throwable $e2) {}
} catch (Throwable $e) {
}
?>
