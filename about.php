<?php
require __DIR__ . '/db.php';
$title = 'Car Portal';
$s = $pdo->prepare("SELECT v FROM site_settings WHERE k=?");
$s->execute(['site_title']);
$r = $s->fetch();
if ($r && $r['v']) $title = $r['v'];

// Get stats for dynamic content
$stats = [
    'banners' => (int)($pdo->query("SELECT COUNT(*) as count FROM banners WHERE active=1")->fetch()['count'] ?? 0),
    'cars' => (int)($pdo->query("SELECT COUNT(*) as count FROM cars WHERE active=1")->fetch()['count'] ?? 0),
    'upcoming' => (int)($pdo->query("SELECT COUNT(*) as count FROM upcoming_cars WHERE active=1")->fetch()['count'] ?? 0),
    'submissions' => (int)($pdo->query("SELECT COUNT(*) as count FROM submissions")->fetch()['count'] ?? 0),
];

// Get recent cars for showcase
$recentCars = $pdo->query("SELECT name, image, category, type FROM cars WHERE active=1 ORDER BY id DESC LIMIT 6")->fetchAll();

// Get team members (you can add this to database later)
$teamMembers = [
    ['name' => 'Alex Johnson', 'role' => 'CEO & Founder', 'image' => '👨‍💼', 'color' => 'from-blue-100 to-blue-50'],
    ['name' => 'Sarah Miller', 'role' => 'Head of Operations', 'image' => '👩‍💼', 'color' => 'from-purple-100 to-purple-50'],
    ['name' => 'Mike Chen', 'role' => 'Lead Developer', 'image' => '👨‍💻', 'color' => 'from-green-100 to-green-50'],
    ['name' => 'Emma Davis', 'role' => 'Customer Support', 'image' => '👩‍💻', 'color' => 'from-pink-100 to-pink-50'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title>About Us - <?php echo htmlspecialchars($title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
// Custom Tailwind configuration with light colors
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          200: '#bae6fd',
          300: '#7dd3fc',
          400: '#38bdf8',
          500: '#0ea5e9',
          600: '#0284c7',
          700: '#0369a1',
        }
      },
      fontFamily: {
        'sans': ['Inter', 'system-ui', 'sans-serif'],
      },
      animation: {
        'fade-in': 'fadeIn 0.6s ease-out',
        'slide-up': 'slideUp 0.5s ease-out',
        'slide-left': 'slideLeft 0.5s ease-out',
        'pulse-subtle': 'pulseSubtle 2s ease-in-out infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideLeft: {
          '0%': { transform: 'translateX(-20px)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },
        pulseSubtle: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.9' },
        }
      }
    }
  }
}
</script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
  box-sizing: border-box;
  -webkit-tap-highlight-color: transparent;
}

body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
  color: #374151;
  overflow-x: hidden;
}

/* Smooth scrolling */
html {
  scroll-behavior: smooth;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Glass Effect */
.glass-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
}

/* Gradient Text */
.gradient-text {
  background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Card Hover Effects */
.hover-lift {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

/* Responsive Typography */
.responsive-heading {
  font-size: clamp(1.75rem, 4vw, 2.75rem);
  line-height: 1.2;
}

.responsive-subheading {
  font-size: clamp(1.125rem, 2.5vw, 1.75rem);
  line-height: 1.4;
}

/* Mobile Optimizations */
@media (max-width: 640px) {
  .mobile-padding {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
    padding-top: 1.5rem !important;
    padding-bottom: 1.5rem !important;
  }
  
  .mobile-margin {
    margin-left: 1rem !important;
    margin-right: 1rem !important;
  }
  
  .mobile-grid-1 {
    grid-template-columns: 1fr !important;
    gap: 1rem !important;
  }
  
  .mobile-grid-2 {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 0.75rem !important;
  }
  
  .mobile-flex-col {
    flex-direction: column !important;
  }
  
  .mobile-text-center {
    text-align: center !important;
  }
  
  .mobile-full-width {
    width: 100% !important;
  }
  
  .mobile-car-card {
    width: 100% !important;
    margin-bottom: 1rem !important;
  }
  
  .mobile-team-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1rem !important;
  }
  
  .mobile-table-container {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
  }
  
  .mobile-table {
    min-width: 600px !important;
    font-size: 0.875rem !important;
  }
}

/* Tablet Optimizations */
@media (min-width: 641px) and (max-width: 1024px) {
  .tablet-padding {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
  }
  
  .tablet-grid-3 {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1.5rem !important;
  }
  
  .tablet-team-grid {
    grid-template-columns: repeat(3, 1fr) !important;
  }
}

/* Desktop Optimizations */
@media (min-width: 1025px) {
  .desktop-container {
    max-width: 1280px !important;
    margin-left: auto !important;
    margin-right: auto !important;
  }
  
  .desktop-grid-4 {
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 2rem !important;
  }
  
  .desktop-grid-3 {
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 1.5rem !important;
  }
}

/* Loading Animation */
.loading-pulse {
  animation: pulseSubtle 2s ease-in-out infinite;
}

/* Feature Card Styling */
.feature-card {
  background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.98) 100%);
  border: 1px solid rgba(229, 231, 235, 0.8);
  transition: all 0.3s ease;
}

.feature-card:hover {
  border-color: rgba(14, 165, 233, 0.3);
  background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(255,255,255,1) 100%);
}

/* Stat Card Styling */
.stat-card {
  background: white;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.stat-card:hover {
  border-color: #0ea5e9;
  transform: translateY(-2px);
}

/* Table Styling */
.data-table {
  border-collapse: separate;
  border-spacing: 0;
}

.data-table th {
  background: #f9fafb;
  font-weight: 600;
  color: #374151;
  border-bottom: 2px solid #e5e7eb;
}

.data-table td {
  border-bottom: 1px solid #f3f4f6;
}

.data-table tr:hover {
  background-color: #f9fafb;
}

/* Button Styling */
.btn-primary {
  background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
  color: white;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
}

.btn-light {
  background: white;
  border: 1px solid #e5e7eb;
  color: #374151;
  transition: all 0.3s ease;
}

.btn-light:hover {
  background: #f9fafb;
  border-color: #d1d5db;
}

/* Section Backgrounds */
.section-light {
  background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
}

.section-very-light {
  background: linear-gradient(135deg, #fefefe 0%, #f5f5f5 100%);
}

/* Animation Delays */
.animation-delay-100 { animation-delay: 100ms; }
.animation-delay-200 { animation-delay: 200ms; }
.animation-delay-300 { animation-delay: 300ms; }
.animation-delay-400 { animation-delay: 400ms; }
</style>
</head>
<body class="min-h-screen">
<!-- Floating Background Elements -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
  <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-100 rounded-full opacity-20 animate-pulse-subtle"></div>
  <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-100 rounded-full opacity-20 animate-pulse-subtle" style="animation-delay: 1s"></div>
</div>

<?php include __DIR__ . '/header.php'; ?>

<main class="pb-20">
  <!-- Hero Section -->
  <section class="pt-8 pb-12 mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="mb-6">
        <a href="index.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors mb-6 btn-light px-4 py-2 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Back to Home</span>
        </a>
        
        <div class="glass-card rounded-2xl p-8 mb-8 animate-fade-in">
          <div class="flex items-start justify-between mobile-flex-col">
            <div class="mb-6 md:mb-0">
              <h1 class="responsive-heading font-bold text-gray-800 mb-3">
                About <span class="gradient-text"><?php echo htmlspecialchars($title); ?></span>
              </h1>
              <p class="text-gray-600 text-lg mb-6 max-w-3xl">
                Your trusted destination for discovering the most searched, latest, and upcoming cars. 
                We combine clean design with powerful features to help you find your perfect vehicle.
              </p>
              
              <div class="flex flex-wrap gap-3 mobile-flex-col mobile-full-width">
                <a href="index.php#most" class="btn-primary px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2">
                  <span>Most Searched Cars</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </a>
                <a href="index.php#latest" class="btn-light px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2">
                  <span>Latest Releases</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </a>
                <a href="admin.php" class="btn-light px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2">
                  <span>Admin Panel</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </a>
              </div>
            </div>
            
            <!-- Stats -->
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm min-w-[280px]">
              <h3 class="font-semibold text-gray-700 mb-4">Portal Stats</h3>
              <div class="grid mobile-grid-2 desktop-grid-4 gap-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-blue-600"><?php echo $stats['banners']; ?></div>
                  <div class="text-sm text-gray-500">Active Banners</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-green-600"><?php echo $stats['cars']; ?></div>
                  <div class="text-sm text-gray-500">Total Cars</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-orange-600"><?php echo $stats['upcoming']; ?></div>
                  <div class="text-sm text-gray-500">Upcoming</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-purple-600"><?php echo $stats['submissions']; ?></div>
                  <div class="text-sm text-gray-500">Submissions</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-12 bg-white mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-3">
          Our <span class="gradient-text">Core Features</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Designed with user experience and functionality in mind
        </p>
      </div>
      
      <div class="grid mobile-grid-1 tablet-grid-3 desktop-grid-4 gap-6">
        <!-- Feature 1 -->
        <div class="feature-card rounded-xl p-6 hover-lift animate-slide-up animation-delay-100">
          <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center mb-4">
            <span class="text-2xl">🔍</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Most Searched Cars</h3>
          <p class="text-gray-600 text-sm">
            Curated list of popular models with advanced filtering by category.
          </p>
        </div>
        
        <!-- Feature 2 -->
        <div class="feature-card rounded-xl p-6 hover-lift animate-slide-up animation-delay-200">
          <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-green-50 to-green-100 flex items-center justify-center mb-4">
            <span class="text-2xl">✨</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Latest Releases</h3>
          <p class="text-gray-600 text-sm">
            Stay updated with newly launched car models and their specifications.
          </p>
        </div>
        
        <!-- Feature 3 -->
        <div class="feature-card rounded-xl p-6 hover-lift animate-slide-up animation-delay-300">
          <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center mb-4">
            <span class="text-2xl">🚀</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Upcoming Models</h3>
          <p class="text-gray-600 text-sm">
            Track expected launch dates and estimated pricing of upcoming cars.
          </p>
        </div>
        
        <!-- Feature 4 -->
        <div class="feature-card rounded-xl p-6 hover-lift animate-slide-up animation-delay-400">
          <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 flex items-center justify-center mb-4">
            <span class="text-2xl">🛠️</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Admin Management</h3>
          <p class="text-gray-600 text-sm">
            Full control over banners, cars, and submissions through admin panel.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Recent Cars Showcase -->
  <?php if ($recentCars): ?>
  <section class="py-12 bg-gradient-to-b from-white to-gray-50 mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-3">
          Recently <span class="gradient-text">Added Cars</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Some of our latest additions to the portal
        </p>
      </div>
      
      <div class="grid mobile-grid-2 tablet-grid-3 desktop-grid-3 gap-6">
        <?php foreach ($recentCars as $index => $car): ?>
        <div class="bg-white rounded-xl overflow-hidden border border-gray-100 hover-lift animate-slide-up" style="animation-delay: <?php echo ($index % 3) * 100; ?>ms">
          <div class="h-40 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden">
            <?php if (!empty($car['image'])): ?>
              <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" 
                   class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
            <?php else: ?>
              <div class="text-center p-4">
                <div class="text-4xl mb-2">🚗</div>
                <p class="text-gray-400 text-sm">No Image</p>
              </div>
            <?php endif; ?>
          </div>
          <div class="p-4">
            <div class="flex items-center justify-between mb-2">
              <h3 class="font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($car['name']); ?></h3>
              <span class="text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-600 font-medium">
                <?php echo htmlspecialchars($car['type']); ?>
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500"><?php echo htmlspecialchars($car['category']); ?></span>
              <span class="text-xs text-gray-400">Recently Added</span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Team Section -->
  <section class="py-12 mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-3">
          Meet Our <span class="gradient-text">Team</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
          The dedicated people behind <?php echo htmlspecialchars($title); ?>
        </p>
      </div>
      
      <div class="grid mobile-team-grid tablet-team-grid desktop-grid-4 gap-6">
        <?php foreach ($teamMembers as $index => $member): ?>
        <div class="bg-white rounded-xl p-6 border border-gray-100 hover-lift animate-slide-left" style="animation-delay: <?php echo $index * 100; ?>ms">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br <?php echo $member['color']; ?> flex items-center justify-center text-2xl">
              <?php echo $member['image']; ?>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800"><?php echo $member['name']; ?></h3>
              <p class="text-sm text-gray-500"><?php echo $member['role']; ?></p>
            </div>
          </div>
          <p class="text-gray-600 text-sm">
            Dedicated to providing the best car discovery experience.
          </p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Data Table Section -->
  <section class="py-12 bg-white mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-3">
          Portal <span class="gradient-text">Statistics</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Detailed breakdown of our portal data
        </p>
      </div>
      
      <!-- Responsive Table Container -->
      <div class="mobile-table-container">
        <table class="w-full data-table mobile-table rounded-lg overflow-hidden border border-gray-200">
          <thead>
            <tr class="bg-gray-50">
              <th class="p-4 text-left font-semibold text-gray-700">Category</th>
              <th class="p-4 text-left font-semibold text-gray-700">Count</th>
              <th class="p-4 text-left font-semibold text-gray-700">Status</th>
              <th class="p-4 text-left font-semibold text-gray-700">Last Updated</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded bg-blue-100 flex items-center justify-center">
                    <span class="text-blue-600">🖼️</span>
                  </div>
                  <span class="font-medium text-gray-700">Active Banners</span>
                </div>
              </td>
              <td class="p-4">
                <span class="font-semibold text-blue-600"><?php echo $stats['banners']; ?></span>
              </td>
              <td class="p-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Active
                </span>
              </td>
              <td class="p-4 text-gray-500">Recently</td>
            </tr>
            
            <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded bg-green-100 flex items-center justify-center">
                    <span class="text-green-600">🚗</span>
                  </div>
                  <span class="font-medium text-gray-700">Total Cars</span>
                </div>
              </td>
              <td class="p-4">
                <span class="font-semibold text-green-600"><?php echo $stats['cars']; ?></span>
              </td>
              <td class="p-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Active
                </span>
              </td>
              <td class="p-4 text-gray-500">Recently</td>
            </tr>
            
            <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded bg-orange-100 flex items-center justify-center">
                    <span class="text-orange-600">🚀</span>
                  </div>
                  <span class="font-medium text-gray-700">Upcoming Cars</span>
                </div>
              </td>
              <td class="p-4">
                <span class="font-semibold text-orange-600"><?php echo $stats['upcoming']; ?></span>
              </td>
              <td class="p-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  Coming Soon
                </span>
              </td>
              <td class="p-4 text-gray-500">Recently</td>
            </tr>
            
            <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded bg-purple-100 flex items-center justify-center">
                    <span class="text-purple-600">📋</span>
                  </div>
                  <span class="font-medium text-gray-700">Form Submissions</span>
                </div>
              </td>
              <td class="p-4">
                <span class="font-semibold text-purple-600"><?php echo $stats['submissions']; ?></span>
              </td>
              <td class="p-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  Processing
                </span>
              </td>
              <td class="p-4 text-gray-500">Recently</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-12 mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="glass-card rounded-2xl p-8 md:p-12 text-center">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-4">
          Ready to Find Your <span class="gradient-text">Dream Car</span>?
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-8">
          Explore our extensive collection of cars, get notified about upcoming launches, 
          or manage your own content through our admin panel.
        </p>
        
        <div class="flex flex-wrap justify-center gap-4">
          <a href="index.php" class="btn-primary px-8 py-3 rounded-lg font-medium inline-flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Go to Homepage</span>
          </a>
          
          <a href="index.php#form" class="btn-light px-8 py-3 rounded-lg font-medium inline-flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Contact Us</span>
          </a>
          
          <a href="admin.php" class="btn-light px-8 py-3 rounded-lg font-medium inline-flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Admin Panel</span>
          </a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<!-- Back to Top Button -->
<button id="backToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-white border border-gray-200 rounded-full shadow-lg hover:bg-gray-50 transition-all duration-300 hidden items-center justify-center z-40">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
  </svg>
</button>

<script>
// Enhanced JavaScript for About Page
document.addEventListener('DOMContentLoaded', function() {
  // Back to Top Button
  const backToTop = document.getElementById('backToTop');
  
  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      backToTop.classList.remove('hidden');
      setTimeout(() => {
        backToTop.style.opacity = '1';
      }, 10);
    } else {
      backToTop.style.opacity = '0';
      setTimeout(() => {
        if (window.scrollY <= 300) {
          backToTop.classList.add('hidden');
        }
      }, 300);
    }
  });
  
  backToTop.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
  
  // Add ripple effect to buttons
  const buttons = document.querySelectorAll('.btn-primary, .btn-light');
  buttons.forEach(button => {
    button.addEventListener('click', function(e) {
      const x = e.clientX - e.target.getBoundingClientRect().left;
      const y = e.clientY - e.target.getBoundingClientRect().top;
      
      const ripple = document.createElement('span');
      ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
        left: ${x}px;
        top: ${y}px;
        width: 100px;
        height: 100px;
        margin-left: -50px;
        margin-top: -50px;
      `;
      
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 600);
    });
  });
  
  // Add CSS for ripple animation
  const style = document.createElement('style');
  style.textContent = `
    @keyframes ripple {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }
    .btn-primary, .btn-light {
      position: relative;
      overflow: hidden;
    }
  `;
  document.head.appendChild(style);
  
  // Table row click effect
  const tableRows = document.querySelectorAll('.data-table tr');
  tableRows.forEach(row => {
    row.addEventListener('click', function() {
      tableRows.forEach(r => r.classList.remove('bg-blue-50'));
      this.classList.add('bg-blue-50');
    });
  });
  
  // Stats animation on scroll
  const observerOptions = {
    threshold: 0.2,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const statNumbers = entry.target.querySelectorAll('.text-2xl');
        statNumbers.forEach(stat => {
          const finalValue = parseInt(stat.textContent);
          let startValue = 0;
          const duration = 1000;
          const step = finalValue / (duration / 16);
          
          const timer = setInterval(() => {
            startValue += step;
            if (startValue >= finalValue) {
              stat.textContent = finalValue;
              clearInterval(timer);
            } else {
              stat.textContent = Math.floor(startValue);
            }
          }, 16);
        });
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  const statsSection = document.querySelector('.bg-white.rounded-xl.p-6');
  if (statsSection) {
    observer.observe(statsSection);
  }
  
  // Parallax effect for floating elements
  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const floaters = document.querySelectorAll('.fixed .bg-blue-100, .fixed .bg-purple-100');
    
    floaters.forEach((floater, index) => {
      const speed = 0.5 + (index * 0.1);
      const yPos = -(scrolled * speed);
      floater.style.transform = `translateY(${yPos}px)`;
    });
  });
  
  // Mobile menu improvements (if header has mobile menu)
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobileMenu');
      if (mobileMenu) {
        mobileMenu.classList.toggle('hidden');
        mobileMenu.classList.toggle('animate-slide-down');
      }
    });
  }
  
  // Lazy load images
  const lazyImages = document.querySelectorAll('img');
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
          }
          imageObserver.unobserve(img);
        }
      });
    });
    
    lazyImages.forEach(img => {
      if (img.getAttribute('loading') === 'lazy') {
        imageObserver.observe(img);
      }
    });
  }
  
  // Touch gesture improvements for mobile
  let touchStart = 0;
  let touchEnd = 0;
  
  document.addEventListener('touchstart', e => {
    touchStart = e.changedTouches[0].screenY;
  }, { passive: true });
  
  document.addEventListener('touchend', e => {
    touchEnd = e.changedTouches[0].screenY;
    const diff = touchStart - touchEnd;
    
    // Swipe down to show back to top button
    if (diff < -50 && window.scrollY > 100) {
      backToTop.style.transform = 'scale(1.1)';
      setTimeout(() => {
        backToTop.style.transform = 'scale(1)';
      }, 200);
    }
  }, { passive: true });
});
</script>
</body>
</html>