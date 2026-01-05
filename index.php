<?php
require __DIR__ . '/db.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_form') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $options = $_POST['options'] ?? [];
    $opt = implode(',', array_intersect($options, ['Hatchback','Sedan','SUV']));
    if ($name && $phone && filter_var($email, FILTER_VALIDATE_EMAIL) && $address && $opt) {
        $stmt = $pdo->prepare("INSERT INTO submissions (name, phone, email, address, options) VALUES (?,?,?,?,?)");
        $stmt->execute([$name,$phone,$email,$address,$opt]);
        $msg = 'Submitted';
    } else {
        $msg = 'Invalid';
    }
}
$title = 'Car Portal';
$s = $pdo->prepare("SELECT v FROM site_settings WHERE k=?");
$s->execute(['site_title']);
$r = $s->fetch();
if ($r && $r['v']) $title = $r['v'];
$banners = $pdo->query("SELECT * FROM banners WHERE active=1 ORDER BY id DESC")->fetchAll();
$most = $pdo->query("SELECT * FROM cars WHERE active=1 AND type='most_searched' ORDER BY id DESC")->fetchAll();
$latest = $pdo->query("SELECT * FROM cars WHERE active=1 AND type='latest' ORDER BY id DESC")->fetchAll();
$upcoming = [];
try { $upcoming = $pdo->query("SELECT * FROM upcoming_cars WHERE active=1 ORDER BY id DESC")->fetchAll(); } catch (Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title><?php echo htmlspecialchars($title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
// Custom Tailwind configuration
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
          800: '#075985',
          900: '#0c4a6e',
        },
        secondary: {
          50: '#fdf4ff',
          100: '#fae8ff',
          200: '#f5d0fe',
          300: '#f0abfc',
          400: '#e879f9',
          500: '#d946ef',
          600: '#c026d3',
          700: '#a21caf',
          800: '#86198f',
          900: '#701a75',
        }
      },
      fontFamily: {
        'sans': ['Inter', 'system-ui', 'sans-serif'],
        'display': ['Poppins', 'system-ui', 'sans-serif'],
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.4s ease-out',
        'slide-down': 'slideDown 0.3s ease-out',
        'scale-in': 'scaleIn 0.3s ease-out',
        'float': 'float 3s ease-in-out infinite',
        'pulse-gentle': 'pulseGentle 2s ease-in-out infinite',
        'shimmer': 'shimmer 2s infinite',
        'bounce-gentle': 'bounceGentle 1s infinite',
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
        slideDown: {
          '0%': { transform: 'translateY(-20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        scaleIn: {
          '0%': { transform: 'scale(0.95)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        pulseGentle: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.8' },
        },
        shimmer: {
          '0%': { backgroundPosition: '-1000px 0' },
          '100%': { backgroundPosition: '1000px 0' },
        },
        bounceGentle: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-5px)' },
        }
      }
    }
  }
}
</script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap');

* {
  box-sizing: border-box;
  -webkit-tap-highlight-color: transparent;
}

body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
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
  background: #c1c1c1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Selection Color */
::selection {
  background-color: rgba(14, 165, 233, 0.3);
  color: inherit;
}

/* Card Hover Effects */
.hover-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.hover-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.6s ease;
}

.hover-card:hover::before {
  left: 100%;
}

.hover-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Button Hover Effects */
.hover-btn {
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.hover-btn::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 5px;
  height: 5px;
  background: rgba(255, 255, 255, 0.5);
  opacity: 0;
  border-radius: 100%;
  transform: scale(1, 1) translate(-50%);
  transform-origin: 50% 50%;
}

.hover-btn:focus:not(:active)::after {
  animation: ripple 1s ease-out;
}

@keyframes ripple {
  0% {
    transform: scale(0, 0);
    opacity: 0.5;
  }
  100% {
    transform: scale(20, 20);
    opacity: 0;
  }
}

/* Category Tab Hover */
.category-tab {
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.category-tab:hover {
  transform: translateY(-2px);
}

.category-tab.active {
  background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
}

/* Form Input Focus */
.form-input:focus {
  box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

/* Banner Animation */
.banner-slide {
  animation: slideIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Loading Skeleton */
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

/* Mobile Optimizations */
@media (max-width: 640px) {
  .mobile-car-grid {
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)) !important;
    gap: 12px !important;
  }
  
  .mobile-banner {
    height: 200px !important;
  }
  
  .mobile-modal {
    width: 95% !important;
    margin: 10px !important;
    max-height: 85vh !important;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch !important;
  }
  
  .mobile-category-tabs {
    display: flex !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    scrollbar-width: none !important;
    padding-bottom: 12px !important;
    margin-bottom: 16px !important;
  }
  
  .mobile-category-tabs::-webkit-scrollbar {
    display: none !important;
  }
  
  .mobile-category-tabs button {
    flex-shrink: 0 !important;
    white-space: nowrap !important;
    font-size: 14px !important;
    padding: 8px 16px !important;
  }
  
  .mobile-section-padding {
    padding-left: 16px !important;
    padding-right: 16px !important;
    padding-top: 32px !important;
    padding-bottom: 32px !important;
  }
  
  .mobile-form-grid {
    grid-template-columns: 1fr !important;
    gap: 16px !important;
  }
  
  .mobile-text-xl {
    font-size: 1.25rem !important;
    line-height: 1.75rem !important;
  }
  
  .mobile-text-lg {
    font-size: 1.125rem !important;
    line-height: 1.75rem !important;
  }
}

/* Tablet Optimizations */
@media (min-width: 641px) and (max-width: 1024px) {
  .tablet-car-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)) !important;
    gap: 20px !important;
  }
  
  .tablet-section-padding {
    padding-left: 24px !important;
    padding-right: 24px !important;
  }
}

/* Desktop Optimizations */
@media (min-width: 1025px) {
  .desktop-container {
    max-width: 1280px !important;
    margin-left: auto !important;
    margin-right: auto !important;
  }
  
  .desktop-car-grid {
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)) !important;
    gap: 24px !important;
  }
}

/* Glass Effect */
.glass-effect {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Gradient Text */
.gradient-text {
  background: linear-gradient(135deg, #0ea5e9 0%, #d946ef 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Floating Animation */
.floating {
  animation: float 6s ease-in-out infinite;
}

/* Responsive Typography */
.responsive-heading {
  font-size: clamp(1.5rem, 4vw, 2.5rem);
  line-height: 1.2;
}

.responsive-subheading {
  font-size: clamp(1rem, 2.5vw, 1.5rem);
  line-height: 1.4;
}

/* Loading Animation */
.loading-dots {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 20px;
}

.loading-dots div {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #0ea5e9;
  animation-timing-function: cubic-bezier(0, 1, 1, 0);
}

.loading-dots div:nth-child(1) {
  left: 8px;
  animation: loading-dots1 0.6s infinite;
}

.loading-dots div:nth-child(2) {
  left: 8px;
  animation: loading-dots2 0.6s infinite;
}

.loading-dots div:nth-child(3) {
  left: 32px;
  animation: loading-dots2 0.6s infinite;
}

.loading-dots div:nth-child(4) {
  left: 56px;
  animation: loading-dots3 0.6s infinite;
}

@keyframes loading-dots1 {
  0% { transform: scale(0); }
  100% { transform: scale(1); }
}

@keyframes loading-dots3 {
  0% { transform: scale(1); }
  100% { transform: scale(0); }
}

@keyframes loading-dots2 {
  0% { transform: translate(0, 0); }
  100% { transform: translate(24px, 0); }
}
</style>
</head>
<body class="min-h-screen antialiased">
<!-- Floating Background Elements -->
<div class="fixed inset-0 -z-10 overflow-hidden">
  <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-gentle"></div>
  <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-gentle animation-delay-2000"></div>
  <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-gentle animation-delay-4000"></div>
</div>

<?php include __DIR__ . '/header.php'; ?>

<main class="relative">
  <!-- Banner Section -->
  <section class="relative overflow-hidden">
    <div class="px-0">
      <?php if ($banners): ?>
        <div class="relative">
          <!-- Banner Slider -->
          <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide" id="bannerSlider">
            <?php foreach ($banners as $index => $b): ?>
              <?php
                $img = $b['image'] ?? '';
                $isUrl = $img && filter_var($img, FILTER_VALIDATE_URL);
                $isLocal = $img && is_file(__DIR__ . '/' . $img);
              ?>
              <div class="flex-shrink-0 w-full snap-start relative">
                <div class="relative h-[280px] md:h-[400px] lg:h-[500px] overflow-hidden">
                  <?php if ($isUrl || $isLocal): ?>
                    <img 
                      class="w-full h-full object-cover banner-slide" 
                      src="<?php echo htmlspecialchars($img); ?>" 
                      alt="<?php echo htmlspecialchars($b['title']); ?>"
                      loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                    >
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                  <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                      <div class="text-center">
                        <div class="text-4xl mb-2">🚗</div>
                        <p class="text-gray-600">Add banners in admin panel</p>
                      </div>
                    </div>
                  <?php endif; ?>
                  
                  <!-- Banner Content -->
                  <div class="absolute bottom-8 left-8 right-8 text-white">
                    <div class="max-w-2xl">
                      <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-4 responsive-heading">
                        <?php echo htmlspecialchars($b['title']); ?>
                      </h1>
                      <p class="text-lg md:text-xl opacity-90 mb-6">Find your perfect car today</p>
                      <a href="#most" class="inline-flex items-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-300 hover-btn shadow-lg">
                        <span>Explore Cars</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <!-- Banner Dots -->
          <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
            <?php foreach ($banners as $index => $b): ?>
              <button 
                class="w-2 h-2 rounded-full bg-white/50 hover:bg-white transition-all duration-300 banner-dot <?php echo $index === 0 ? 'bg-white w-4' : ''; ?>"
                data-slide="<?php echo $index; ?>"
              ></button>
            <?php endforeach; ?>
          </div>
          
          <!-- Navigation Arrows -->
          <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white p-2 rounded-full shadow-lg transition-all duration-300 hidden md:block" id="prevBanner">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white p-2 rounded-full shadow-lg transition-all duration-300 hidden md:block" id="nextBanner">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      <?php else: ?>
        <!-- Default Banner -->
        <div class="h-[300px] md:h-[400px] bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center relative overflow-hidden">
          <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-64 h-64 bg-white/10 rounded-full translate-x-1/2 translate-y-1/2"></div>
          </div>
          <div class="relative z-10 text-center text-white px-4">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 animate-fade-in">Welcome to <?php echo htmlspecialchars($title); ?></h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">Find your dream car with us</p>
            <a href="#most" class="inline-flex items-center gap-2 bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:scale-105 transition-transform duration-300 shadow-xl hover-btn">
              <span>Browse Cars</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Most Searched Cars Section -->
  <section id="most" class="py-12 md:py-16 px-4 mobile-section-padding tablet-section-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4 responsive-heading">
          <span class="gradient-text">Most Searched</span> Cars
        </h2>
        <p class="text-gray-600 text-lg md:text-xl max-w-2xl mx-auto">
          Discover the most popular cars that everyone is talking about
        </p>
      </div>
      
      <!-- Category Tabs -->
      <div class="flex justify-center mb-8 md:mb-12 mobile-category-tabs">
        <div class="inline-flex gap-2 bg-gray-100 p-1 rounded-xl">
          <?php foreach (['All','SUV','Hatchback','Sedan','MUV','Luxury'] as $cat): ?>
            <button 
              class="category-tab px-5 py-2.5 rounded-lg text-sm md:text-base font-medium transition-all duration-300 <?php echo $cat === 'All' ? 'active' : 'text-gray-700 hover:text-blue-600'; ?>" 
              data-cat="<?php echo $cat; ?>"
            >
              <?php echo $cat; ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
      
      <!-- Cars Grid -->
      <div class="grid mobile-car-grid tablet-car-grid desktop-car-grid">
        <?php if ($most): foreach ($most as $index => $c): ?>
          <div 
            class="group bg-white rounded-2xl overflow-hidden shadow-lg hover-card cursor-pointer animate-scale-in" 
            style="animation-delay: <?php echo $index * 100; ?>ms"
            data-name="<?php echo htmlspecialchars($c['name']); ?>" 
            data-image="<?php echo htmlspecialchars($c['image'] ?? ''); ?>" 
            data-cat="<?php echo htmlspecialchars($c['category'] ?? 'SUV'); ?>"
          >
            <div class="relative overflow-hidden h-48 md:h-56">
              <?php if (!empty($c['image'])): ?>
                <img 
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                  src="<?php echo htmlspecialchars($c['image']); ?>" 
                  alt="<?php echo htmlspecialchars($c['name']); ?>"
                  loading="lazy"
                >
              <?php else: ?>
                <div class="w-full h-full bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
                  <div class="text-center">
                    <div class="text-4xl mb-2">🚗</div>
                    <p class="text-gray-500 text-sm">No Image</p>
                  </div>
                </div>
              <?php endif; ?>
              
              <!-- Category Badge -->
              <div class="absolute top-4 left-4">
                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-blue-600 text-xs font-semibold rounded-full">
                  <?php echo htmlspecialchars($c['category'] ?? 'SUV'); ?>
                </span>
              </div>
              
              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
            
            <div class="p-5">
              <h3 class="font-bold text-lg md:text-xl text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                <?php echo htmlspecialchars($c['name']); ?>
              </h3>
              <p class="text-gray-600 text-sm mb-4">
                <?php echo htmlspecialchars($c['category'] ?? ''); ?> • Most Searched
              </p>
              <button class="w-full bg-blue-50 text-blue-600 py-2.5 rounded-lg font-medium hover:bg-blue-100 transition-colors duration-300 text-sm">
                View Details
              </button>
            </div>
          </div>
        <?php endforeach; else: ?>
          <div class="col-span-full text-center py-12">
            <div class="text-6xl mb-4">🚗</div>
            <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Cars Found</h3>
            <p class="text-gray-500">Add cars from the admin panel to display here</p>
          </div>
        <?php endif; ?>
      </div>
      
      <!-- View More Button -->
      <div class="text-center mt-10 md:mt-16">
        <a href="#form" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3.5 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 hover-btn shadow-lg hover:shadow-xl">
          <span>Get In Touch</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Latest Cars Section -->
  <section id="latest" class="py-12 md:py-16 bg-gradient-to-b from-white to-gray-50 mobile-section-padding tablet-section-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4 responsive-heading">
          Latest <span class="gradient-text">Car Releases</span>
        </h2>
        <p class="text-gray-600 text-lg md:text-xl max-w-2xl mx-auto">
          Check out the newest cars that have just hit the market
        </p>
      </div>
      
      <!-- Latest Cars Grid -->
      <div class="grid mobile-car-grid tablet-car-grid desktop-car-grid">
        <?php if ($latest): foreach ($latest as $index => $c): ?>
          <div 
            class="group bg-white rounded-2xl overflow-hidden shadow-lg hover-card cursor-pointer animate-scale-in glass-effect" 
            style="animation-delay: <?php echo $index * 100 + 200; ?>ms"
            data-name="<?php echo htmlspecialchars($c['name']); ?>" 
            data-image="<?php echo htmlspecialchars($c['image'] ?? ''); ?>"
          >
            <div class="relative overflow-hidden h-48 md:h-56">
              <?php if (!empty($c['image'])): ?>
                <img 
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                  src="<?php echo htmlspecialchars($c['image']); ?>" 
                  alt="<?php echo htmlspecialchars($c['name']); ?>"
                  loading="lazy"
                >
              <?php else: ?>
                <div class="w-full h-full bg-gradient-to-br from-purple-50 to-pink-100 flex items-center justify-center">
                  <div class="text-center">
                    <div class="text-4xl mb-2">✨</div>
                    <p class="text-gray-500 text-sm">Latest Release</p>
                  </div>
                </div>
              <?php endif; ?>
              
              <!-- New Badge -->
              <div class="absolute top-4 right-4">
                <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full animate-pulse-gentle">
                  NEW
                </span>
              </div>
              
              <!-- Gradient Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-purple-600/20 via-transparent to-transparent"></div>
            </div>
            
            <div class="p-5">
              <h3 class="font-bold text-lg md:text-xl text-gray-800 mb-3 group-hover:text-purple-600 transition-colors duration-300">
                <?php echo htmlspecialchars($c['name']); ?>
              </h3>
              <div class="flex items-center justify-between">
                <span class="text-gray-500 text-sm">Latest Model</span>
                <div class="flex items-center gap-1 text-yellow-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <span class="font-semibold">4.5</span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; else: ?>
          <div class="col-span-full text-center py-12">
            <div class="text-6xl mb-4">✨</div>
            <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Latest Cars</h3>
            <p class="text-gray-500">Add latest cars from the admin panel</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Upcoming Cars Section -->
  <?php if ($upcoming): ?>
  <section id="upcoming" class="py-12 md:py-16 mobile-section-padding tablet-section-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4 responsive-heading">
          <span class="gradient-text">Upcoming</span> Car Launches
        </h2>
        <p class="text-gray-600 text-lg md:text-xl max-w-2xl mx-auto">
          Be the first to know about exciting new cars coming soon
        </p>
      </div>
      
      <!-- Upcoming Cars Grid -->
      <div class="grid mobile-car-grid tablet-car-grid desktop-car-grid">
        <?php foreach ($upcoming as $index => $u): ?>
          <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-card animate-scale-in" style="animation-delay: <?php echo $index * 100 + 400; ?>ms">
            <div class="relative overflow-hidden h-48 md:h-56">
              <img 
                class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" 
                src="<?php echo htmlspecialchars($u['image']); ?>" 
                alt="<?php echo htmlspecialchars($u['name']); ?>"
                loading="lazy"
              >
              
              <!-- Badge -->
              <?php if (!empty($u['badge'])): ?>
                <div class="absolute top-4 left-4">
                  <span class="px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full animate-bounce-gentle">
                    <?php echo htmlspecialchars($u['badge']); ?>
                  </span>
                </div>
              <?php endif; ?>
              
              <!-- Launch Date -->
              <?php if (!empty($u['launch_date'])): ?>
                <div class="absolute top-4 right-4">
                  <span class="px-3 py-1 bg-black/70 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                    🗓️ <?php echo date('M Y', strtotime($u['launch_date'])); ?>
                  </span>
                </div>
              <?php endif; ?>
            </div>
            
            <div class="p-5">
              <h3 class="font-bold text-lg md:text-xl text-gray-800 mb-2"><?php echo htmlspecialchars($u['name']); ?></h3>
              
              <div class="mb-4">
                <div class="text-2xl font-bold text-gray-900 mb-1">
                  ₹<?php echo number_format((float)$u['price_min']/100000,2); ?> - <?php echo number_format((float)$u['price_max']/100000,2); ?> Lakh
                </div>
                <div class="text-gray-500 text-sm">Estimated Price</div>
              </div>
              
              <div class="space-y-2 mb-5">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>Launch: <?php echo !empty($u['launch_date']) ? date('d M Y', strtotime($u['launch_date'])) : 'Coming Soon'; ?></span>
                </div>
              </div>
              
              <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-lg font-semibold hover:from-orange-600 hover:to-orange-700 transition-all duration-300 hover-btn flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>Notify Me</span>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Contact Form Section -->
  <section id="form" class="py-12 md:py-16 bg-gradient-to-br from-blue-50 to-indigo-50 mobile-section-padding tablet-section-padding">
    <div class="desktop-container">
      <div class="max-w-3xl mx-auto">
        <div class="text-center mb-10 md:mb-16">
          <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4 responsive-heading">
            Get Your <span class="gradient-text">Dream Car</span>
          </h2>
          <p class="text-gray-600 text-lg md:text-xl">
            Fill out the form and our experts will contact you shortly
          </p>
        </div>
        
        <?php if ($msg): ?>
          <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-xl animate-slide-down">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div>
                <div class="font-semibold text-green-800">Success!</div>
                <div class="text-green-700"><?php echo htmlspecialchars($msg); ?></div>
              </div>
            </div>
          </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden glass-effect">
          <div class="p-6 md:p-8">
            <form method="post" class="space-y-6">
              <input type="hidden" name="action" value="submit_form">
              
              <!-- Car Type Selection -->
              <div class="space-y-3">
                <label class="block text-gray-700 font-medium">What type of car are you interested in?</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  <label class="flex items-center justify-center gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-300 has-checked:border-blue-500 has-checked:bg-blue-50 has-checked:ring-2 has-checked:ring-blue-200">
                    <input type="checkbox" name="options[]" value="Hatchback" class="sr-only peer">
                    <div class="w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center peer-checked:border-blue-500 peer-checked:bg-blue-500">
                      <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="font-medium text-gray-700 peer-checked:text-blue-600">Hatchback</span>
                  </label>
                  
                  <label class="flex items-center justify-center gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-300 has-checked:border-blue-500 has-checked:bg-blue-50 has-checked:ring-2 has-checked:ring-blue-200">
                    <input type="checkbox" name="options[]" value="Sedan" class="sr-only peer">
                    <div class="w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center peer-checked:border-blue-500 peer-checked:bg-blue-500">
                      <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="font-medium text-gray-700 peer-checked:text-blue-600">Sedan</span>
                  </label>
                  
                  <label class="flex items-center justify-center gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-300 has-checked:border-blue-500 has-checked:bg-blue-50 has-checked:ring-2 has-checked:ring-blue-200">
                    <input type="checkbox" name="options[]" value="SUV" class="sr-only peer">
                    <div class="w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center peer-checked:border-blue-500 peer-checked:bg-blue-500">
                      <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="font-medium text-gray-700 peer-checked:text-blue-600">SUV</span>
                  </label>
                </div>
              </div>
              
              <!-- Form Grid -->
              <div class="grid mobile-form-grid sm:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-gray-700 font-medium">Full Name *</label>
                  <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <input 
                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 form-input" 
                      type="text" 
                      name="name" 
                      placeholder="John Doe" 
                      required
                    >
                  </div>
                </div>
                
                <div class="space-y-2">
                  <label class="block text-gray-700 font-medium">Phone Number *</label>
                  <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <input 
                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 form-input" 
                      type="tel" 
                      name="phone" 
                      placeholder="+91 9876543210" 
                      required
                    >
                  </div>
                </div>
                
                <div class="space-y-2">
                  <label class="block text-gray-700 font-medium">Email Address *</label>
                  <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <input 
                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 form-input" 
                      type="email" 
                      name="email" 
                      placeholder="john@example.com" 
                      required
                    >
                  </div>
                </div>
                
                <div class="space-y-2">
                  <label class="block text-gray-700 font-medium">Address *</label>
                  <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <textarea 
                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 form-input resize-none" 
                      name="address" 
                      placeholder="Your complete address..." 
                      rows="3" 
                      required
                    ></textarea>
                  </div>
                </div>
              </div>
              
              <!-- Submit Button -->
              <div class="pt-4">
                <button 
                  type="submit" 
                  class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 hover-btn shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center gap-3 group"
                >
                  <span>Submit Inquiry</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </button>
              </div>
              
              <!-- Privacy Note -->
              <div class="text-center text-gray-500 text-sm pt-4">
                <p>Your information is safe with us. We never share your details with third parties.</p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<!-- Card Modal -->
<div id="cardModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
  <div id="cardModalOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300"></div>
  <div class="flex items-center justify-center min-h-screen p-4">
    <div id="modalPanel" class="relative bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 w-full max-w-lg mobile-modal">
      <!-- Modal Header -->
      <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <div>
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Car Details</h3>
            <p id="modalSubtitle" class="text-gray-500 text-sm">Complete information</p>
          </div>
        </div>
        <button id="modalClose" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Modal Content -->
      <div class="p-6">
        <div class="mb-6">
          <img id="modalImg" class="w-full h-64 object-cover rounded-xl shadow-lg" src="" alt="">
          <div id="modalImgPlaceholder" class="w-full h-64 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-lg flex items-center justify-center hidden">
            <div class="text-center">
              <div class="text-5xl mb-2">🚗</div>
              <p class="text-gray-500">No Image Available</p>
            </div>
          </div>
        </div>
        
        <div class="space-y-4">
          <div>
            <label class="block text-gray-500 text-sm mb-1">Car Name</label>
            <div id="modalCarName" class="text-xl font-bold text-gray-800"></div>
          </div>
          
          <div>
            <label class="block text-gray-500 text-sm mb-1">Category</label>
            <div id="modalCarCategory" class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium"></div>
          </div>
          
          <div>
            <label class="block text-gray-500 text-sm mb-1">Description</label>
            <p class="text-gray-700">This car is one of the most popular choices in its category. Known for its performance, comfort, and style.</p>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-xl">
              <div class="text-gray-500 text-sm mb-1">Fuel Type</div>
              <div class="font-semibold text-gray-800">Petrol</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl">
              <div class="text-gray-500 text-sm mb-1">Transmission</div>
              <div class="font-semibold text-gray-800">Automatic</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Modal Footer -->
      <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
        <div class="flex gap-3">
          <button class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors duration-300">
            Contact Dealer
          </button>
          <button class="flex-1 border-2 border-blue-600 text-blue-600 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-colors duration-300">
            Test Drive
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Back to Top Button -->
<button id="backToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 hover-btn hidden items-center justify-center z-40">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
  </svg>
</button>

<!-- Loading Screen -->
<div id="loadingScreen" class="fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-300">
  <div class="text-center">
    <div class="relative w-20 h-20 mx-auto mb-4">
      <div class="absolute inset-0 border-4 border-blue-100 rounded-full"></div>
      <div class="absolute inset-4 border-4 border-blue-500 rounded-full animate-spin border-t-transparent"></div>
    </div>
    <div class="text-gray-600 font-medium">Loading...</div>
  </div>
</div>

<script src="script.js"></script>
<script>
// Enhanced JavaScript for better animations and interactions
document.addEventListener('DOMContentLoaded', function() {
  // Loading Screen
  const loadingScreen = document.getElementById('loadingScreen');
  if (loadingScreen) {
    setTimeout(() => {
      loadingScreen.style.opacity = '0';
      setTimeout(() => {
        loadingScreen.style.display = 'none';
      }, 300);
    }, 1000);
  }
  
  // Banner Slider
  const bannerSlider = document.getElementById('bannerSlider');
  const bannerDots = document.querySelectorAll('.banner-dot');
  const prevBanner = document.getElementById('prevBanner');
  const nextBanner = document.getElementById('nextBanner');
  
  let currentSlide = 0;
  const totalSlides = bannerDots.length;
  
  function updateBannerSlider() {
    if (bannerSlider) {
      bannerSlider.scrollTo({
        left: currentSlide * window.innerWidth,
        behavior: 'smooth'
      });
      
      bannerDots.forEach((dot, index) => {
        dot.classList.toggle('bg-white', index === currentSlide);
        dot.classList.toggle('w-4', index === currentSlide);
        dot.classList.toggle('w-2', index !== currentSlide);
      });
    }
  }
  
  if (prevBanner) {
    prevBanner.addEventListener('click', () => {
      currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
      updateBannerSlider();
    });
  }
  
  if (nextBanner) {
    nextBanner.addEventListener('click', () => {
      currentSlide = (currentSlide + 1) % totalSlides;
      updateBannerSlider();
    });
  }
  
  bannerDots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      currentSlide = index;
      updateBannerSlider();
    });
  });
  
  // Auto slide banners
  let bannerInterval;
  function startBannerAutoSlide() {
    bannerInterval = setInterval(() => {
      currentSlide = (currentSlide + 1) % totalSlides;
      updateBannerSlider();
    }, 5000);
  }
  
  function stopBannerAutoSlide() {
    clearInterval(bannerInterval);
  }
  
  if (bannerSlider) {
    bannerSlider.addEventListener('mouseenter', stopBannerAutoSlide);
    bannerSlider.addEventListener('mouseleave', startBannerAutoSlide);
    bannerSlider.addEventListener('touchstart', stopBannerAutoSlide);
    bannerSlider.addEventListener('touchend', () => setTimeout(startBannerAutoSlide, 5000));
    
    startBannerAutoSlide();
  }
  
  // Category Tabs Filtering
  const categoryTabs = document.querySelectorAll('.category-tab[data-cat]');
  const carCards = document.querySelectorAll('.car-card');
  
  categoryTabs.forEach(tab => {
    tab.addEventListener('click', function() {
      const category = this.getAttribute('data-cat');
      
      // Update active tab
      categoryTabs.forEach(t => {
        t.classList.toggle('active', t === this);
        t.classList.toggle('text-gray-700', t !== this);
      });
      
      // Filter cars
      carCards.forEach(card => {
        if (category === 'All' || card.getAttribute('data-cat') === category) {
          card.style.display = 'block';
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
          }, 10);
        } else {
          card.style.opacity = '0';
          card.style.transform = 'scale(0.9)';
          setTimeout(() => {
            card.style.display = 'none';
          }, 300);
        }
      });
    });
  });
  
  // Card Modal
  const cardModal = document.getElementById('cardModal');
  const modalOverlay = document.getElementById('cardModalOverlay');
  const modalPanel = document.getElementById('modalPanel');
  const modalClose = document.getElementById('modalClose');
  const modalTitle = document.getElementById('modalTitle');
  const modalImg = document.getElementById('modalImg');
  const modalImgPlaceholder = document.getElementById('modalImgPlaceholder');
  const modalCarName = document.getElementById('modalCarName');
  const modalCarCategory = document.getElementById('modalCarCategory');
  
  document.querySelectorAll('.car-card').forEach(card => {
    card.addEventListener('click', function() {
      const carName = this.getAttribute('data-name');
      const carImage = this.getAttribute('data-image');
      const carCategory = this.getAttribute('data-cat');
      
      modalTitle.textContent = carName;
      modalCarName.textContent = carName;
      modalCarCategory.textContent = carCategory;
      
      if (carImage) {
        modalImg.src = carImage;
        modalImg.style.display = 'block';
        modalImgPlaceholder.style.display = 'none';
      } else {
        modalImg.style.display = 'none';
        modalImgPlaceholder.style.display = 'flex';
      }
      
      // Show modal with animation
      cardModal.classList.remove('hidden');
      setTimeout(() => {
        modalOverlay.style.opacity = '1';
        modalPanel.style.opacity = '1';
        modalPanel.style.transform = 'scale(1)';
      }, 10);
      
      // Prevent body scroll
      document.body.style.overflow = 'hidden';
    });
  });
  
  // Close modal
  function closeModal() {
    modalOverlay.style.opacity = '0';
    modalPanel.style.opacity = '0';
    modalPanel.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
      cardModal.classList.add('hidden');
      document.body.style.overflow = '';
    }, 300);
  }
  
  if (modalClose) modalClose.addEventListener('click', closeModal);
  if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
  
  // Close modal on escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !cardModal.classList.contains('hidden')) {
      closeModal();
    }
  });
  
  // Back to Top Button
  const backToTop = document.getElementById('backToTop');
  
  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      backToTop.classList.remove('hidden');
      backToTop.style.opacity = '1';
    } else {
      backToTop.style.opacity = '0';
      setTimeout(() => {
        if (window.scrollY <= 300) {
          backToTop.classList.add('hidden');
        }
      }, 300);
    }
  });
  
  if (backToTop) {
    backToTop.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }
  
  // Form validation and enhancement
  const form = document.querySelector('form');
  if (form) {
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    const labels = form.querySelectorAll('label.has-checked');
    
    checkboxes.forEach((checkbox, index) => {
      checkbox.addEventListener('change', function() {
        const label = labels[index];
        if (this.checked) {
          label.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
        } else {
          label.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
        }
      });
    });
    
    // Form submission animation
    form.addEventListener('submit', function(e) {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.innerHTML = `
          <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        `;
        submitBtn.disabled = true;
      }
    });
  }
  
  // Intersection Observer for animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  // Observe all sections
  document.querySelectorAll('section').forEach(section => {
    observer.observe(section);
  });
  
  // Mobile menu toggle for header (if exists)
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const mobileMenu = document.getElementById('mobileMenu');
  
  if (mobileMenuToggle && mobileMenu) {
    mobileMenuToggle.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  }
  
  // Touch gesture improvements
  let touchStartY = 0;
  let touchEndY = 0;
  
  document.addEventListener('touchstart', e => {
    touchStartY = e.changedTouches[0].screenY;
  }, { passive: true });
  
  document.addEventListener('touchend', e => {
    touchEndY = e.changedTouches[0].screenY;
    // Handle swipe up/down
    if (touchStartY - touchEndY > 50) {
      // Swipe up
    } else if (touchEndY - touchStartY > 50) {
      // Swipe down
    }
  }, { passive: true });
  
  // Performance optimizations for mobile
  if ('connection' in navigator && navigator.connection.saveData === true) {
    // Reduce animations for data saver mode
    document.body.classList.add('reduce-motion');
  }
  
  // Lazy load images
  const lazyImages = document.querySelectorAll('img[loading="lazy"]');
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src || img.src;
          imageObserver.unobserve(img);
        }
      });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));
  }
});
</script>
</body>
</html>