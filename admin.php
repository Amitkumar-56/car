<?php
require __DIR__ . '/db.php';
session_start();
$msg = '';
$formEmail = '';
$isAuth = isset($_SESSION['user_id']);
$section = $_GET['section'] ?? 'dashboard';
$q = trim($_GET['q'] ?? '');

if (isset($_SESSION['flash'])) {
    $msg = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

function upload_image($field) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return '';
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed, true)) return '';
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
    $path = $dir . '/' . $name;
    if (is_uploaded_file($_FILES[$field]['tmp_name']) && move_uploaded_file($_FILES[$field]['tmp_name'], $path)) {
        return 'uploads/' . $name;
    }
    return '';
}

$hasUsers = false;
try {
    $row = $pdo->query("SELECT COUNT(*) AS c FROM users")->fetch();
    $hasUsers = isset($row['c']) ? ((int)$row['c'] > 0) : false;
} catch (Throwable $e) {
    $hasUsers = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'login' && !$isAuth) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $formEmail = htmlspecialchars($email, ENT_QUOTES);
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password) {
            $st = $pdo->prepare("SELECT id,password_hash FROM users WHERE email=?");
            $st->execute([$email]);
            $u = $st->fetch();
            if ($u && password_verify($password, $u['password_hash'])) {
                $_SESSION['user_id'] = (int)$u['id'];
                $isAuth = true;
                $_SESSION['flash'] = 'Logged in';
                header('Location: admin.php');
                exit;
            } else {
                $msg = 'Invalid credentials';
            }
        } else {
            $msg = 'Enter email and password';
        }
    }
    if ($action === 'setup_admin' && !$hasUsers) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $st = $pdo->prepare("INSERT INTO users (email,password_hash) VALUES (?,?)");
            $st->execute([$email,$hash]);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $isAuth = true;
            $hasUsers = true;
            $_SESSION['flash'] = 'Admin created';
            header('Location: admin.php');
            exit;
        } else {
            $msg = 'Invalid email or password';
        }
    }
    if ($action === 'logout' && $isAuth) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        $isAuth = false;
        $_SESSION['flash'] = 'Logged out';
        header('Location: admin.php');
        exit;
    }

    if ($isAuth) {
        if ($action === 'import_ref') {
            $modelsMost = ['Kia Seltos','BMW M5','Audi A4','Kia Carens','Defender','BMW X7'];
            $modelsLatest = ['Kia Seltos','Hyundai Venue','MG Hector'];
            $ins = $pdo->prepare("INSERT INTO cars (name,image,type,active) VALUES (?,?,?,1)");
            foreach ($modelsMost as $m) {
                $chk = $pdo->prepare("SELECT id FROM cars WHERE name=? AND type='most_searched'");
                $chk->execute([$m]);
                if (!$chk->fetch()) $ins->execute([$m,'','most_searched']);
            }
            foreach ($modelsLatest as $m) {
                $chk = $pdo->prepare("SELECT id FROM cars WHERE name=? AND type='latest'");
                $chk->execute([$m]);
                if (!$chk->fetch()) $ins->execute([$m,'','latest']);
            }
            $msg = 'Reference cars imported';
        }
        if ($action === 'del_submission') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $pdo->prepare("DELETE FROM submissions WHERE id=?")->execute([$id]);
                $msg = 'Deleted';
            }
        }
        if ($action === 'set_title') {
            $title = trim($_POST['title'] ?? '');
            if ($title) {
                $stmt = $pdo->prepare("INSERT INTO site_settings (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)");
                $stmt->execute(['site_title',$title]);
                $msg = 'Saved';
            }
        }
        if ($action === 'add_banner') {
            $title = trim($_POST['banner_title'] ?? '');
            $url = trim($_POST['banner_image_url'] ?? '');
            $img = '';
            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                $img = $url;
            } else {
                $img = upload_image('banner_image');
            }
            if ($title && $img) {
                $pdo->prepare("INSERT INTO banners (title,image) VALUES (?,?)")->execute([$title,$img]);
                $msg = 'Added';
            }
        }
        if ($action === 'add_car') {
            $name = trim($_POST['car_name'] ?? '');
            $type = $_POST['car_type'] ?? '';
            $category = $_POST['car_category'] ?? 'SUV';
            $url = trim($_POST['car_image_url'] ?? '');
            $img = '';
            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                $img = $url;
            } else {
                $img = upload_image('car_image');
            }
            if ($name && in_array($type,['most_searched','latest'],true) && $img) {
                $pdo->prepare("INSERT INTO cars (name,image,type,category) VALUES (?,?,?,?)")->execute([$name,$img,$type,$category]);
                $msg = 'Added';
            }
        }
        if ($action === 'add_upcoming') {
            $name = trim($_POST['up_name'] ?? '');
            $badge = trim($_POST['up_badge'] ?? '');
            $launch = trim($_POST['up_launch'] ?? '');
            $pmin = (float)($_POST['up_pmin'] ?? 0);
            $pmax = (float)($_POST['up_pmax'] ?? 0);
            $url = trim($_POST['up_image_url'] ?? '');
            $img = '';
            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                $img = $url;
            } else {
                $img = upload_image('up_image');
            }
            if ($name && $img) {
                $pdo->prepare("INSERT INTO upcoming_cars (name,image,price_min,price_max,badge,launch_date) VALUES (?,?,?,?,?,?)")
                    ->execute([$name,$img,$pmin,$pmax,$badge,$launch ?: null]);
                $msg = 'Added';
            }
        }
        if ($action === 'del_upcoming') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $pdo->prepare("DELETE FROM upcoming_cars WHERE id=?")->execute([$id]);
                $msg = 'Deleted';
            }
        }
        if ($action === 'del_banner') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $pdo->prepare("DELETE FROM banners WHERE id=?")->execute([$id]);
                $msg = 'Deleted';
            }
        }
        if ($action === 'del_car') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $pdo->prepare("DELETE FROM cars WHERE id=?")->execute([$id]);
                $msg = 'Deleted';
            }
        }
        if ($action === 'toggle_banner') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $pdo->prepare("UPDATE banners SET active=1-active WHERE id=?")->execute([$id]);
                $msg = 'Toggled';
            }
        }
        if ($action === 'toggle_car') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $pdo->prepare("UPDATE cars SET active=1-active WHERE id=?")->execute([$id]);
                $msg = 'Toggled';
            }
        }
    }
}
$title = 'Car Portal';
$s = $pdo->prepare("SELECT v FROM site_settings WHERE k=?");
$s->execute(['site_title']);
$r = $s->fetch();
if ($r && $r['v']) $title = $r['v'];
$banners = $pdo->query("SELECT * FROM banners ORDER BY id DESC")->fetchAll();
$cars = [];
if ($section === 'cars' && $q !== '') {
    $stCars = $pdo->prepare("SELECT * FROM cars WHERE name LIKE ? ORDER BY id DESC");
    $stCars->execute(['%'.$q.'%']);
    $cars = $stCars->fetchAll();
} else {
    $cars = $pdo->query("SELECT * FROM cars ORDER BY id DESC")->fetchAll();
}
$userEmail = '';
if ($isAuth) {
    $stMe = $pdo->prepare("SELECT email FROM users WHERE id=?");
    $stMe->execute([$_SESSION['user_id']]);
    $me = $stMe->fetch();
    if ($me && !empty($me['email'])) $userEmail = $me['email'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
<title>Admin Panel - <?php echo htmlspecialchars($title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Custom Responsive Styles */
* {
  box-sizing: border-box;
}

/* Mobile Sidebar */
.mobile-sidebar {
  position: fixed;
  top: 0;
  left: -300px;
  width: 280px;
  height: 100vh;
  background: white;
  z-index: 1000;
  transition: left 0.3s ease;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  overflow-y: auto;
}

.mobile-sidebar.open {
  left: 0;
}

.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 999;
  display: none;
}

.mobile-overlay.show {
  display: block;
}

/* Mobile Search */
.mobile-search-container {
  transition: all 0.3s ease;
}

/* Responsive Cards */
@media (max-width: 640px) {
  .mobile-stats-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 10px !important;
  }
  
  .mobile-image-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 10px !important;
  }
  
  .mobile-table-container {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
  }
  
  .mobile-table {
    min-width: 600px !important;
    font-size: 12px !important;
  }
  
  .mobile-content-card {
    margin: 8px !important;
    padding: 12px !important;
  }
}

@media (min-width: 641px) and (max-width: 768px) {
  .mobile-image-grid {
    grid-template-columns: repeat(3, 1fr) !important;
  }
  
  .mobile-stats-grid {
    grid-template-columns: repeat(3, 1fr) !important;
  }
}

@media (min-width: 769px) {
  .mobile-image-grid {
    grid-template-columns: repeat(4, 1fr) !important;
  }
}

/* Form Responsive */
.mobile-form-grid {
  display: grid;
  gap: 16px;
}

@media (max-width: 768px) {
  .mobile-form-grid {
    grid-template-columns: 1fr !important;
  }
  
  .mobile-form-grid .grid-cols-2 {
    grid-template-columns: 1fr !important;
  }
}

/* Sidebar Links Hover */
.sidebar-link {
  transition: all 0.2s ease;
}

.sidebar-link:hover {
  transform: translateX(5px);
}

/* Table Row Hover */
.table-row-hover:hover {
  background-color: #f9fafb;
}

/* Button Transitions */
.btn-transition {
  transition: all 0.2s ease;
}

.btn-transition:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Image Container */
.img-container {
  position: relative;
  overflow: hidden;
}

.img-container img {
  transition: transform 0.3s ease;
}

.img-container:hover img {
  transform: scale(1.05);
}

/* Scrollbar Styling */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
</head>
<body class="bg-gray-50 min-h-screen">
<!-- Mobile Overlay -->
<div id="mobileOverlay" class="mobile-overlay"></div>

<!-- Mobile Sidebar -->
<aside id="adminSidebarMobile" class="mobile-sidebar p-6">
  <div class="flex items-center justify-between mb-8">
    <div class="font-bold text-xl text-gray-800">Admin Panel</div>
    <button id="closeSidebarMobile" class="p-2 rounded-lg hover:bg-gray-100">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
  
  <nav class="space-y-2">
    <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='dashboard'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
       href="?section=dashboard">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
      </svg>
      <span class="font-medium">Dashboard</span>
    </a>
    
    <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='header'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
       href="?section=header">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
      </svg>
      <span class="font-medium">Header</span>
    </a>
    
    <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='banner'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
       href="?section=banner">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <span class="font-medium">Banner</span>
    </a>
    
    <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='cars'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
       href="?section=cars">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <span class="font-medium">Cars</span>
    </a>
    
    <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='upcoming'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
       href="?section=upcoming">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="font-medium">Upcoming</span>
    </a>
    
    <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='submissions'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
       href="?section=submissions">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <span class="font-medium">Submissions</span>
    </a>
  </nav>
  
  <div class="mt-auto pt-8 border-t border-gray-200">
    <?php if ($isAuth): ?>
    <form method="post">
      <input type="hidden" name="action" value="logout">
      <button class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 btn-transition" 
              type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        <span class="font-medium">Logout</span>
      </button>
    </form>
    <?php endif; ?>
  </div>
</aside>

<!-- Main Container -->
<div class="min-h-screen">
  <!-- Header -->
  <header class="sticky top-0 bg-white shadow-sm z-50 border-b border-gray-200">
    <div class="container mx-auto px-4 py-3">
      <div class="flex items-center justify-between">
        <!-- Left: Hamburger Menu + Title -->
        <div class="flex items-center gap-4">
          <button id="sidebarToggleMobile" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          
          <div>
            <div class="font-bold text-lg text-gray-800">Admin Panel</div>
            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($title); ?></div>
          </div>
        </div>
        
        <!-- Center: Desktop Search -->
        <div class="hidden lg:block flex-1 max-w-xl mx-4">
          <form action="" method="get" class="relative">
            <input type="hidden" name="section" value="cars">
            <input class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   type="search" 
                   name="q" 
                   placeholder="Search cars..." 
                   value="<?php echo htmlspecialchars($q); ?>">
            <div class="absolute left-3 top-2.5 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </form>
        </div>
        
        <!-- Right: User Info -->
        <div class="flex items-center gap-4">
          <!-- Mobile Search Button -->
          <button id="mobileSearchToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
          
          <?php if ($isAuth): ?>
          <div class="hidden md:flex items-center gap-3">
            <div class="text-right">
              <div class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($userEmail ?: 'Admin'); ?></div>
              <div class="text-xs text-gray-500">Administrator</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold">
              <?php echo strtoupper(substr($userEmail ?: 'A', 0, 1)); ?>
            </div>
            <form method="post">
              <input type="hidden" name="action" value="logout">
              <button class="hidden lg:block px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 btn-transition" 
                      type="submit">
                Logout
              </button>
            </form>
          </div>
          
          <a href="index.php" target="_blank" 
             class="hidden md:flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 btn-transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span class="text-sm font-medium">View Site</span>
          </a>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Mobile Search Container -->
      <div id="mobileSearchContainer" class="mt-3 hidden mobile-search-container">
        <form action="" method="get" class="relative">
          <input type="hidden" name="section" value="cars">
          <input class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                 type="search" 
                 name="q" 
                 placeholder="Search cars, brands, models..." 
                 value="<?php echo htmlspecialchars($q); ?>">
          <div class="absolute left-3 top-3.5 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </form>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="py-6">
    <div class="container mx-auto px-4">
      <?php if ($msg): ?>
      <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div class="text-green-800"><?php echo htmlspecialchars($msg); ?></div>
        </div>
        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <?php endif; ?>
      
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:block lg:col-span-1">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
            <div class="font-semibold text-lg text-gray-800 mb-6">Dashboard Sections</div>
            <nav class="space-y-2">
              <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='dashboard'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
                 href="?section=dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
              </a>
              
              <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='header'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
                 href="?section=header">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span>Header</span>
              </a>
              
              <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='banner'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
                 href="?section=banner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Banner</span>
              </a>
              
              <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='cars'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
                 href="?section=cars">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Cars</span>
              </a>
              
              <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='upcoming'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
                 href="?section=upcoming">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Upcoming</span>
              </a>
              
              <a class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $section==='submissions'?'bg-blue-50 text-blue-600 border-r-4 border-blue-600':'text-gray-700 hover:bg-gray-100'; ?>" 
                 href="?section=submissions">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Submissions</span>
              </a>
            </nav>
          </div>
        </aside>
        
        <!-- Main Content Area -->
        <section class="lg:col-span-3">
          <?php if ($isAuth): ?>
            <!-- DASHBOARD SECTION -->
            <?php if ($section==='dashboard'): ?>
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mobile-content-card">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                  <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($userEmail ?: 'Admin'); ?>! 👋</h1>
                    <p class="text-gray-600 mt-2">Manage your car portal website from here</p>
                  </div>
                  <div class="flex gap-2 mt-4 md:mt-0">
                    <button onclick="location.reload()" 
                            class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 btn-transition flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      Refresh
                    </button>
                  </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8 mobile-stats-grid">
                  <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-5 shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Banners</div>
                    <div class="text-2xl md:text-3xl font-bold"><?php echo count($banners); ?></div>
                    <div class="text-xs opacity-80 mt-2">
                      Active: <?php echo count(array_filter($banners, fn($b) => $b['active'])); ?>
                    </div>
                  </div>
                  
                  <div class="bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-xl p-5 shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Cars</div>
                    <div class="text-2xl md:text-3xl font-bold"><?php echo count($cars); ?></div>
                    <div class="text-xs opacity-80 mt-2">
                      Active: <?php echo count(array_filter($cars, fn($c) => $c['active'])); ?>
                    </div>
                  </div>
                  
                  <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-xl p-5 shadow-lg">
                    <div class="text-sm opacity-90 mb-1">Submissions</div>
                    <div class="text-2xl md:text-3xl font-bold">
                      <?php echo (int)($pdo->query("SELECT COUNT(*) AS c FROM submissions")->fetch()['c'] ?? 0); ?>
                    </div>
                    <div class="text-xs opacity-80 mt-2">
                      Today: <?php echo (int)($pdo->query("SELECT COUNT(*) AS c FROM submissions WHERE DATE(created_at)=CURDATE()")->fetch()['c'] ?? 0); ?>
                    </div>
                  </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-gray-50 rounded-xl p-6">
                  <h3 class="font-semibold text-gray-700 mb-4">Quick Actions</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <form method="post">
                      <input type="hidden" name="action" value="import_ref">
                      <button class="w-full px-4 py-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 btn-transition" 
                              type="submit">
                        Import Reference Cars
                      </button>
                    </form>
                    
                    <a href="index.php" target="_blank" 
                       class="w-full px-4 py-3 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 btn-transition flex items-center justify-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                      View Live Site
                    </a>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            
            <!-- HEADER SECTION -->
            <?php if ($section==='header'): ?>
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mobile-content-card">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Site Header Settings</h2>
                
                <form method="post" class="bg-gray-50 rounded-xl p-6">
                  <input type="hidden" name="action" value="set_title">
                  
                  <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Site Title</label>
                    <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           type="text" 
                           name="title" 
                           placeholder="Enter site title..." 
                           value="<?php echo htmlspecialchars($title); ?>" 
                           required>
                    <p class="text-sm text-gray-500 mt-2">This title will appear in the browser tab</p>
                  </div>
                  
                  <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 btn-transition font-medium" 
                          type="submit">
                    Save Changes
                  </button>
                </form>
              </div>
            <?php endif; ?>
            
            <!-- BANNER SECTION -->
            <?php if ($section==='banner'): ?>
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mobile-content-card">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                  <div>
                    <h2 class="text-2xl font-bold text-gray-800">Banner Management</h2>
                    <p class="text-gray-600 mt-1">Add and manage homepage banners</p>
                  </div>
                  <div class="flex gap-2 mt-4 md:mt-0">
                    <button onclick="location.reload()" 
                            class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 btn-transition">
                      Refresh
                    </button>
                  </div>
                </div>
                
                <!-- Add Banner Form -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                  <h3 class="font-medium text-gray-700 mb-4">Add New Banner</h3>
                  <form method="post" enctype="multipart/form-data" class="grid gap-4 mobile-form-grid">
                    <input type="hidden" name="action" value="add_banner">
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Banner Title</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="text" 
                             name="banner_title" 
                             placeholder="Enter banner title..." 
                             required>
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Image URL (Optional)</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="url" 
                             name="banner_image_url" 
                             placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Or Upload Image</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="file" 
                             name="banner_image" 
                             accept="image/*">
                    </div>
                    
                    <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 btn-transition font-medium" 
                            type="submit">
                      Add Banner
                    </button>
                  </form>
                </div>
                
                <!-- Import Reference Cars -->
                <form method="post" class="mb-8">
                  <input type="hidden" name="action" value="import_ref">
                  <button class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 btn-transition font-medium" 
                          type="submit">
                    Import Reference Cars
                  </button>
                </form>
                
                <!-- Banners List -->
                <h3 class="font-medium text-gray-700 mb-4">Existing Banners</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mobile-image-grid">
                  <?php foreach ($banners as $b): ?>
                  <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="img-container">
                      <?php
                        $img = $b['image'] ?? '';
                        $isUrl = $img && filter_var($img, FILTER_VALIDATE_URL);
                        $isLocal = $img && is_file(__DIR__ . '/' . $img);
                      ?>
                      <?php if ($isUrl || $isLocal): ?>
                        <img class="w-full h-40 object-cover" src="<?php echo htmlspecialchars($img); ?>" alt="">
                      <?php else: ?>
                        <div class="w-full h-40 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </div>
                      <?php endif; ?>
                    </div>
                    
                    <div class="p-4">
                      <h4 class="font-medium text-gray-800 text-sm truncate mb-2"><?php echo htmlspecialchars($b['title']); ?></h4>
                      
                      <div class="flex gap-2">
                        <form method="post" class="flex-1">
                          <input type="hidden" name="action" value="toggle_banner">
                          <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
                          <button class="w-full px-3 py-2 rounded-lg text-sm <?php echo $b['active']?'bg-green-100 text-green-700 hover:bg-green-200':'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>" 
                                  type="submit">
                            <?php echo $b['active'] ? 'Active' : 'Inactive'; ?>
                          </button>
                        </form>
                        
                        <form method="post" class="flex-1">
                          <input type="hidden" name="action" value="del_banner">
                          <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
                          <button class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm" 
                                  type="submit">
                            Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
            
            <!-- CARS SECTION -->
            <?php if ($section==='cars'): ?>
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mobile-content-card">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                  <div>
                    <h2 class="text-2xl font-bold text-gray-800">Car Management</h2>
                    <p class="text-gray-600 mt-1">Add and manage cars for Most Searched and Latest sections</p>
                  </div>
                  <div class="flex gap-2 mt-4 md:mt-0">
                    <form action="" method="get" class="flex items-center gap-2">
                      <input type="hidden" name="section" value="cars">
                      <input class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="search" 
                             name="q" 
                             placeholder="Search cars..." 
                             value="<?php echo htmlspecialchars($q); ?>">
                      <button class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 btn-transition" 
                              type="submit">
                        Search
                      </button>
                    </form>
                    <button onclick="location.reload()" 
                            class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 btn-transition">
                      Refresh
                    </button>
                  </div>
                </div>
                
                <!-- Add Car Form -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                  <h3 class="font-medium text-gray-700 mb-4">Add New Car</h3>
                  <form method="post" enctype="multipart/form-data" class="grid gap-4 mobile-form-grid">
                    <input type="hidden" name="action" value="add_car">
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Car Name</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="text" 
                             name="car_name" 
                             placeholder="Enter car name..." 
                             required>
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Section</label>
                      <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              name="car_type" 
                              required>
                        <option value="most_searched">Most Searched</option>
                        <option value="latest">Latest</option>
                      </select>
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Category</label>
                      <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              name="car_category" 
                              required>
                        <option value="SUV">SUV</option>
                        <option value="Hatchback">Hatchback</option>
                        <option value="Sedan">Sedan</option>
                        <option value="MUV">MUV</option>
                        <option value="Luxury">Luxury</option>
                      </select>
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Image URL (Optional)</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="url" 
                             name="car_image_url" 
                             placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Or Upload Image</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="file" 
                             name="car_image" 
                             accept="image/*">
                    </div>
                    
                    <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 btn-transition font-medium" 
                            type="submit">
                      Add Car
                    </button>
                  </form>
                </div>
                
                <!-- Cars List -->
                <h3 class="font-medium text-gray-700 mb-4">Existing Cars (<?php echo count($cars); ?>)</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mobile-image-grid">
                  <?php foreach ($cars as $c): ?>
                  <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="img-container">
                      <img class="w-full h-40 object-cover" src="<?php echo htmlspecialchars($c['image']); ?>" alt="">
                    </div>
                    
                    <div class="p-4">
                      <h4 class="font-medium text-gray-800 text-sm truncate mb-2"><?php echo htmlspecialchars($c['name']); ?></h4>
                      
                      <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded"><?php echo htmlspecialchars($c['type']); ?></span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded"><?php echo htmlspecialchars($c['category'] ?? ''); ?></span>
                      </div>
                      
                      <div class="flex gap-2">
                        <form method="post" class="flex-1">
                          <input type="hidden" name="action" value="toggle_car">
                          <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                          <button class="w-full px-3 py-2 rounded-lg text-sm <?php echo $c['active']?'bg-green-100 text-green-700 hover:bg-green-200':'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>" 
                                  type="submit">
                            <?php echo $c['active'] ? 'Active' : 'Inactive'; ?>
                          </button>
                        </form>
                        
                        <form method="post" class="flex-1">
                          <input type="hidden" name="action" value="del_car">
                          <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                          <button class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm" 
                                  type="submit">
                            Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
            
            <!-- UPCOMING CARS SECTION -->
            <?php if ($section==='upcoming'): ?>
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mobile-content-card">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                  <div>
                    <h2 class="text-2xl font-bold text-gray-800">Upcoming Cars</h2>
                    <p class="text-gray-600 mt-1">Manage upcoming car launches</p>
                  </div>
                  <div class="flex gap-2 mt-4 md:mt-0">
                    <button onclick="location.reload()" 
                            class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 btn-transition">
                      Refresh
                    </button>
                  </div>
                </div>
                
                <!-- Add Upcoming Car Form -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                  <h3 class="font-medium text-gray-700 mb-4">Add Upcoming Car</h3>
                  <form method="post" enctype="multipart/form-data" class="grid gap-4 mobile-form-grid">
                    <input type="hidden" name="action" value="add_upcoming">
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Car Name</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="text" 
                             name="up_name" 
                             placeholder="Enter car name..." 
                             required>
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Image URL (Optional)</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="url" 
                             name="up_image_url" 
                             placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Or Upload Image</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="file" 
                             name="up_image" 
                             accept="image/*">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">Min Price (₹)</label>
                        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               type="number" 
                               step="0.01" 
                               name="up_pmin" 
                               placeholder="500000" 
                               required>
                      </div>
                      <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">Max Price (₹)</label>
                        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               type="number" 
                               step="0.01" 
                               name="up_pmax" 
                               placeholder="1000000" 
                               required>
                      </div>
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Badge</label>
                      <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              name="up_badge">
                        <option value="">Select Badge</option>
                        <option value="New Variant">New Variant</option>
                        <option value="Facelift">Facelift</option>
                        <option value="Estimated">Estimated</option>
                        <option value="Upcoming">Upcoming</option>
                      </select>
                    </div>
                    
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Launch Date</label>
                      <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             type="date" 
                             name="up_launch">
                    </div>
                    
                    <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 btn-transition font-medium" 
                            type="submit">
                      Add Upcoming Car
                    </button>
                  </form>
                </div>
                
                <!-- Upcoming Cars List -->
                <h3 class="font-medium text-gray-700 mb-4">Upcoming Cars</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mobile-image-grid">
                  <?php
                    $upcoming = $pdo->query("SELECT * FROM upcoming_cars ORDER BY id DESC")->fetchAll();
                    foreach ($upcoming as $u):
                  ?>
                  <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="img-container">
                      <img class="w-full h-40 object-cover" src="<?php echo htmlspecialchars($u['image']); ?>" alt="">
                    </div>
                    
                    <div class="p-4">
                      <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-800 text-sm truncate"><?php echo htmlspecialchars($u['name']); ?></h4>
                        <?php if (!empty($u['badge'])): ?>
                          <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs rounded whitespace-nowrap"><?php echo htmlspecialchars($u['badge']); ?></span>
                        <?php endif; ?>
                      </div>
                      
                      <div class="text-gray-600 text-sm mb-2">
                        ₹<?php echo number_format((float)$u['price_min']/100000,2); ?> - <?php echo number_format((float)$u['price_max']/100000,2); ?> Lakh
                      </div>
                      
                      <?php if (!empty($u['launch_date'])): ?>
                        <div class="text-gray-500 text-xs mb-3 flex items-center gap-1">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          <?php echo htmlspecialchars($u['launch_date']); ?>
                        </div>
                      <?php endif; ?>
                      
                      <form method="post">
                        <input type="hidden" name="action" value="del_upcoming">
                        <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                        <button class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm" 
                                type="submit">
                          Delete
                        </button>
                      </form>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
            
            <!-- SUBMISSIONS SECTION -->
            <?php if ($section==='submissions'): ?>
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 mobile-content-card">
                <div class="p-6 border-b border-gray-200">
                  <h2 class="text-2xl font-bold text-gray-800">Form Submissions</h2>
                  <p class="text-gray-600 mt-1">View and manage form submissions from users</p>
                </div>
                
                <div class="p-6">
                  <div class="flex items-center justify-between mb-6">
                    <div class="text-gray-700 font-medium">All Submissions</div>
                    <div class="text-sm text-gray-500">
                      Total: <?php echo count($pdo->query("SELECT * FROM submissions")->fetchAll()); ?>
                    </div>
                  </div>
                  
                  <div class="mobile-table-container">
                    <table class="w-full mobile-table">
                      <thead class="bg-gray-100">
                        <tr>
                          <th class="text-left p-3 text-gray-700 font-medium text-sm">Name</th>
                          <th class="text-left p-3 text-gray-700 font-medium text-sm">Phone</th>
                          <th class="text-left p-3 text-gray-700 font-medium text-sm">Email</th>
                          <th class="text-left p-3 text-gray-700 font-medium text-sm">Options</th>
                          <th class="text-left p-3 text-gray-700 font-medium text-sm">Date</th>
                          <th class="text-left p-3 text-gray-700 font-medium text-sm">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $subs = $pdo->query("SELECT * FROM submissions ORDER BY id DESC")->fetchAll();
                          foreach ($subs as $srow):
                        ?>
                        <tr class="border-t border-gray-200 hover:bg-gray-50 table-row-hover">
                          <td class="p-3 text-gray-800"><?php echo htmlspecialchars($srow['name']); ?></td>
                          <td class="p-3 text-gray-800">
                            <a href="tel:<?php echo htmlspecialchars($srow['phone']); ?>" class="text-blue-600 hover:text-blue-800">
                              <?php echo htmlspecialchars($srow['phone']); ?>
                            </a>
                          </td>
                          <td class="p-3 text-gray-800">
                            <a href="mailto:<?php echo htmlspecialchars($srow['email']); ?>" class="text-blue-600 hover:text-blue-800">
                              <?php echo htmlspecialchars($srow['email']); ?>
                            </a>
                          </td>
                          <td class="p-3">
                            <div class="flex flex-wrap gap-1">
                              <?php 
                                $options = explode(',', $srow['options']);
                                foreach ($options as $option):
                              ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded"><?php echo htmlspecialchars(trim($option)); ?></span>
                              <?php endforeach; ?>
                            </div>
                          </td>
                          <td class="p-3 text-gray-600 text-sm">
                            <?php echo date('d M Y', strtotime($srow['created_at'])); ?>
                          </td>
                          <td class="p-3">
                            <form method="post">
                              <input type="hidden" name="action" value="del_submission">
                              <input type="hidden" name="id" value="<?php echo (int)$srow['id']; ?>">
                              <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm btn-transition" 
                                      type="submit">
                                Delete
                              </button>
                            </form>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($subs)): ?>
                        <tr>
                          <td colspan="6" class="p-6 text-center text-gray-500">
                            No submissions found
                          </td>
                        </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            
          <?php else: ?>
            <!-- Login/Setup Form -->
            <section class="max-w-md mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
              <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                  <?php echo $hasUsers ? '🔐' : '⚙️'; ?>
                </div>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo $hasUsers ? 'Admin Login' : 'Setup Admin Account'; ?></h3>
                <p class="text-gray-600 mt-2">
                  <?php echo $hasUsers ? 'Enter your credentials to access the admin panel' : 'Create the first admin account for your car portal'; ?>
                </p>
              </div>
              
              <form method="post" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $hasUsers ? 'login' : 'setup_admin'; ?>">
                
                <div>
                  <label class="block text-gray-700 text-sm font-medium mb-1">Email Address</label>
                  <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                         type="email" 
                         name="email" 
                         placeholder="admin@example.com" 
                         value="<?php echo $formEmail; ?>" 
                         required>
                </div>
                
                <div>
                  <label class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                  <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                         type="password" 
                         name="password" 
                         placeholder="Enter password" 
                         required>
                </div>
                
                <button class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 btn-transition font-medium" 
                        type="submit">
                  <?php echo $hasUsers ? 'Sign In' : 'Create Admin Account'; ?>
                </button>
              </form>
              
              <div class="mt-6 text-center text-sm text-gray-500">
                <a href="index.php" class="text-blue-600 hover:text-blue-800">← Back to Website</a>
              </div>
            </section>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Mobile sidebar toggle
  const sidebarToggle = document.getElementById('sidebarToggleMobile');
  const closeSidebar = document.getElementById('closeSidebarMobile');
  const adminSidebar = document.getElementById('adminSidebarMobile');
  const mobileOverlay = document.getElementById('mobileOverlay');
  
  function openSidebar() {
    adminSidebar.classList.add('open');
    mobileOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  
  function closeSidebarFunc() {
    adminSidebar.classList.remove('open');
    mobileOverlay.classList.remove('show');
    document.body.style.overflow = '';
  }
  
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', openSidebar);
  }
  
  if (closeSidebar) {
    closeSidebar.addEventListener('click', closeSidebarFunc);
  }
  
  if (mobileOverlay) {
    mobileOverlay.addEventListener('click', closeSidebarFunc);
  }
  
  // Mobile search toggle
  const mobileSearchToggle = document.getElementById('mobileSearchToggle');
  const mobileSearchContainer = document.getElementById('mobileSearchContainer');
  
  if (mobileSearchToggle && mobileSearchContainer) {
    mobileSearchToggle.addEventListener('click', function() {
      mobileSearchContainer.classList.toggle('hidden');
    });
  }
  
  // Close mobile sidebar when clicking on links
  document.querySelectorAll('#adminSidebarMobile a').forEach(link => {
    link.addEventListener('click', closeSidebarFunc);
  });
  
  // Auto-hide flash message after 5 seconds
  const flashMsg = document.querySelector('.bg-gradient-to-r.from-green-50.to-emerald-50');
  if (flashMsg) {
    setTimeout(() => {
      flashMsg.style.transition = 'opacity 0.5s ease';
      flashMsg.style.opacity = '0';
      setTimeout(() => flashMsg.remove(), 500);
    }, 5000);
  }
  
  // Close sidebar on window resize if desktop
  window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
      closeSidebarFunc();
    }
  });
  
  // Form validation enhancement
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.innerHTML = 'Processing...';
        submitBtn.disabled = true;
      }
    });
  });
});
</script>
</body>
</html>