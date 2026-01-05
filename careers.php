<?php
require __DIR__ . '/db.php';
$title = 'Car Portal';
$s = $pdo->prepare("SELECT v FROM site_settings WHERE k=?");
$s->execute(['site_title']);
$r = $s->fetch();
$title = ($r && $r['v']) ? $r['v'] : $title;

// Define job openings array (can be moved to database later)
$jobOpenings = [
    [
        'title' => 'Frontend Engineer',
        'department' => 'Engineering',
        'type' => 'Full-time',
        'location' => 'Remote',
        'experience' => '2+ years',
        'skills' => ['Tailwind CSS', 'JavaScript', 'React', 'Vue.js', 'Responsive Design'],
        'description' => 'Build beautiful, responsive interfaces for our automotive platform.'
    ],
    [
        'title' => 'PHP Developer',
        'department' => 'Backend',
        'type' => 'Full-time',
        'location' => 'Hybrid',
        'experience' => '3+ years',
        'skills' => ['PHP', 'MySQL', 'PDO', 'REST API', 'Performance'],
        'description' => 'Develop robust backend systems for car portal management.'
    ],
    [
        'title' => 'Product Designer',
        'department' => 'Design',
        'type' => 'Full-time',
        'location' => 'Remote',
        'experience' => '3+ years',
        'skills' => ['Figma', 'UI/UX', 'Prototyping', 'User Research'],
        'description' => 'Design intuitive experiences for car enthusiasts.'
    ],
    [
        'title' => 'DevOps Engineer',
        'department' => 'Infrastructure',
        'type' => 'Full-time',
        'location' => 'Remote',
        'experience' => '2+ years',
        'skills' => ['Docker', 'AWS', 'CI/CD', 'Linux'],
        'description' => 'Build and maintain our deployment infrastructure.'
    ],
    [
        'title' => 'Content Writer',
        'department' => 'Marketing',
        'type' => 'Contract',
        'location' => 'Remote',
        'experience' => '1+ years',
        'skills' => ['SEO', 'Copywriting', 'Automotive', 'Content Strategy'],
        'description' => 'Create engaging content about cars and automotive trends.'
    ],
    [
        'title' => 'QA Engineer',
        'department' => 'Engineering',
        'type' => 'Full-time',
        'location' => 'Hybrid',
        'experience' => '2+ years',
        'skills' => ['Testing', 'Automation', 'PHPUnit', 'Selenium'],
        'description' => 'Ensure quality across our automotive platform.'
    ]
];

// Get total applications count (can be from database)
$totalApplications = 126;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title>Careers - <?php echo htmlspecialchars($title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
// Custom Tailwind configuration for Careers page
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
        }
      },
      fontFamily: {
        'sans': ['Inter', 'system-ui', 'sans-serif'],
        'display': ['Poppins', 'system-ui', 'sans-serif'],
      },
      animation: {
        'fade-in': 'fadeIn 0.6s ease-out',
        'slide-up': 'slideUp 0.5s ease-out',
        'slide-down': 'slideDown 0.3s ease-out',
        'pulse-subtle': 'pulseSubtle 2s ease-in-out infinite',
        'bounce-subtle': 'bounceSubtle 0.5s ease-in-out infinite',
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
        pulseSubtle: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.9' },
        },
        bounceSubtle: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-4px)' },
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
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
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
  
  .mobile-job-grid {
    grid-template-columns: 1fr !important;
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
  
  .mobile-job-card {
    padding: 1.25rem !important;
  }
}

/* Tablet Optimizations */
@media (min-width: 641px) and (max-width: 1024px) {
  .tablet-padding {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
  }
  
  .tablet-grid-2 {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1.5rem !important;
  }
  
  .tablet-job-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1.5rem !important;
  }
}

/* Desktop Optimizations */
@media (min-width: 1025px) {
  .desktop-container {
    max-width: 1280px !important;
    margin-left: auto !important;
    margin-right: auto !important;
  }
  
  .desktop-grid-3 {
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 2rem !important;
  }
}

/* Loading Animation */
.loading-pulse {
  animation: pulseSubtle 2s ease-in-out infinite;
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

.btn-outline {
  background: transparent;
  border: 2px solid #0ea5e9;
  color: #0ea5e9;
  transition: all 0.3s ease;
}

.btn-outline:hover {
  background: #0ea5e9;
  color: white;
}

/* Badge Styling */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.badge-primary {
  background: #dbeafe;
  color: #1e40af;
}

.badge-success {
  background: #d1fae5;
  color: #065f46;
}

.badge-warning {
  background: #fef3c7;
  color: #92400e;
}

.badge-info {
  background: #e0f2fe;
  color: #0369a1;
}

/* Section Backgrounds */
.section-light {
  background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
}

.section-very-light {
  background: linear-gradient(135deg, #fefefe 0%, #f5f5f5 100%);
}

/* Job Card Styling */
.job-card {
  background: white;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.job-card:hover {
  border-color: #0ea5e9;
  background: #f8fafc;
}

/* Skill Tag */
.skill-tag {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  background: #f3f4f6;
  color: #4b5563;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.skill-tag:hover {
  background: #e5e7eb;
  transform: translateY(-1px);
}

/* Modal Styling */
.modal-backdrop {
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

/* Animation Delays */
.animation-delay-100 { animation-delay: 100ms; }
.animation-delay-200 { animation-delay: 200ms; }
.animation-delay-300 { animation-delay: 300ms; }
.animation-delay-400 { animation-delay: 400ms; }

/* Floating Elements */
.floating {
  animation: bounceSubtle 3s ease-in-out infinite;
}
</style>
</head>
<body class="min-h-screen">
<!-- Floating Background Elements -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
  <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-100 rounded-full opacity-20 animate-pulse-subtle floating"></div>
  <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-100 rounded-full opacity-20 animate-pulse-subtle floating" style="animation-delay: 1s"></div>
  <div class="absolute top-1/4 left-1/4 w-40 h-40 bg-green-100 rounded-full opacity-20 animate-pulse-subtle floating" style="animation-delay: 2s"></div>
</div>

<?php include __DIR__ . '/header.php'; ?>

<main class="pb-20">
  <!-- Hero Section -->
  <section class="pt-8 pb-12 mobile-padding tablet-padding section-light">
    <div class="desktop-container">
      <div class="mb-6">
        <!-- Back Button -->
        <a href="index.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors mb-8">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Back to Home</span>
        </a>
        
        <!-- Hero Content -->
        <div class="glass-card rounded-2xl p-8 md:p-12 mb-8 animate-fade-in">
          <div class="flex flex-col lg:flex-row items-start justify-between gap-8">
            <div class="flex-1">
              <h1 class="responsive-heading font-bold text-gray-800 mb-4">
                Build the Future of <span class="gradient-text">Automotive Tech</span>
              </h1>
              <p class="text-gray-600 text-lg mb-6 max-w-3xl">
                Join our team to create innovative solutions for car enthusiasts worldwide. 
                We're looking for passionate individuals to help us revolutionize how people discover and interact with cars.
              </p>
              
              <!-- Stats -->
              <div class="flex flex-wrap gap-6 mb-8">
                <div class="text-center">
                  <div class="text-3xl font-bold text-blue-600"><?php echo count($jobOpenings); ?></div>
                  <div class="text-sm text-gray-500">Open Positions</div>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-bold text-green-600"><?php echo $totalApplications; ?></div>
                  <div class="text-sm text-gray-500">Applications</div>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-bold text-purple-600">100%</div>
                  <div class="text-sm text-gray-500">Remote Friendly</div>
                </div>
              </div>
              
              <!-- CTA Buttons -->
              <div class="flex flex-wrap gap-3">
                <a href="#job-openings" class="btn-primary px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2">
                  <span>View Open Positions</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                  </svg>
                </a>
                <a href="#why-join" class="btn-light px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2">
                  <span>Why Join Us</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </a>
              </div>
            </div>
            
            <!-- Hero Image/Illustration -->
            <div class="lg:w-96">
              <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 text-center">
                <div class="text-6xl mb-4">🚀</div>
                <h3 class="font-semibold text-gray-800 mb-2">Grow With Us</h3>
                <p class="text-gray-600 text-sm">
                  Be part of a team that's shaping the future of automotive discovery
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Why Join Us Section -->
  <section id="why-join" class="py-12 bg-white mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-3">
          Why <span class="gradient-text">Join Our Team</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
          We offer more than just a job - we offer a career with purpose
        </p>
      </div>
      
      <div class="grid mobile-grid-1 tablet-grid-2 desktop-grid-3 gap-6">
        <!-- Benefit 1 -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 hover-lift animate-slide-up animation-delay-100">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center mb-4 shadow-sm">
            <span class="text-2xl text-blue-600">💼</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Remote First</h3>
          <p class="text-gray-600 text-sm">
            Work from anywhere with flexible hours. We believe in output, not hours.
          </p>
        </div>
        
        <!-- Benefit 2 -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 hover-lift animate-slide-up animation-delay-200">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center mb-4 shadow-sm">
            <span class="text-2xl text-green-600">📈</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Career Growth</h3>
          <p class="text-gray-600 text-sm">
            Regular learning opportunities, mentorship, and clear progression paths.
          </p>
        </div>
        
        <!-- Benefit 3 -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 hover-lift animate-slide-up animation-delay-300">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center mb-4 shadow-sm">
            <span class="text-2xl text-purple-600">🏖️</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Work-Life Balance</h3>
          <p class="text-gray-600 text-sm">
            Unlimited PTO, wellness programs, and flexible scheduling.
          </p>
        </div>
        
        <!-- Benefit 4 -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 hover-lift animate-slide-up animation-delay-100">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center mb-4 shadow-sm">
            <span class="text-2xl text-orange-600">💻</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Latest Tech</h3>
          <p class="text-gray-600 text-sm">
            Work with modern tech stack and cutting-edge tools.
          </p>
        </div>
        
        <!-- Benefit 5 -->
        <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-2xl p-6 hover-lift animate-slide-up animation-delay-200">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center mb-4 shadow-sm">
            <span class="text-2xl text-pink-600">👥</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Great Team</h3>
          <p class="text-gray-600 text-sm">
            Collaborate with talented, passionate individuals.
          </p>
        </div>
        
        <!-- Benefit 6 -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-6 hover-lift animate-slide-up animation-delay-300">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center mb-4 shadow-sm">
            <span class="text-2xl text-indigo-600">💰</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Competitive Pay</h3>
          <p class="text-gray-600 text-sm">
            Industry-competitive salary with equity options.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Job Openings Section -->
  <section id="job-openings" class="py-12 bg-gradient-to-b from-white to-gray-50 mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 md:mb-16 gap-4">
        <div>
          <h2 class="responsive-subheading font-bold text-gray-800 mb-2">
            Current <span class="gradient-text">Job Openings</span>
          </h2>
          <p class="text-gray-600">
            <?php echo count($jobOpenings); ?> positions available across multiple departments
          </p>
        </div>
        
        <!-- Filter Dropdown -->
        <div class="flex gap-3">
          <select class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="all">All Departments</option>
            <option value="engineering">Engineering</option>
            <option value="design">Design</option>
            <option value="marketing">Marketing</option>
            <option value="infrastructure">Infrastructure</option>
          </select>
          
          <select class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="all">All Locations</option>
            <option value="remote">Remote</option>
            <option value="hybrid">Hybrid</option>
            <option value="onsite">On-site</option>
          </select>
        </div>
      </div>
      
      <!-- Job Cards Grid -->
      <div class="grid mobile-job-grid tablet-job-grid desktop-grid-3 gap-6">
        <?php foreach ($jobOpenings as $index => $job): ?>
        <div class="job-card rounded-2xl overflow-hidden hover-lift animate-slide-up" 
             style="animation-delay: <?php echo ($index % 3) * 100; ?>ms"
             data-department="<?php echo strtolower($job['department']); ?>"
             data-location="<?php echo strtolower($job['location']); ?>">
          
          <!-- Job Header -->
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div>
                <h3 class="font-bold text-lg text-gray-800 mb-1"><?php echo $job['title']; ?></h3>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <span class="badge badge-primary"><?php echo $job['department']; ?></span>
                  <span>•</span>
                  <span><?php echo $job['type']; ?></span>
                  <span>•</span>
                  <span><?php echo $job['location']; ?></span>
                </div>
              </div>
              <div class="text-3xl">
                <?php 
                  $icons = ['💻', '🎨', '🔧', '⚡', '✍️', '🧪'];
                  echo $icons[$index % count($icons)]; 
                ?>
              </div>
            </div>
            
            <!-- Job Description -->
            <p class="text-gray-600 text-sm mb-4"><?php echo $job['description']; ?></p>
            
            <!-- Experience -->
            <div class="flex items-center gap-2 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-sm text-gray-500">Experience: <?php echo $job['experience']; ?></span>
            </div>
            
            <!-- Skills -->
            <div class="mb-6">
              <div class="text-xs text-gray-500 mb-2">Required Skills:</div>
              <div class="flex flex-wrap gap-2">
                <?php foreach ($job['skills'] as $skill): ?>
                <span class="skill-tag"><?php echo $skill; ?></span>
                <?php endforeach; ?>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-2">
              <button class="flex-1 btn-primary py-2.5 rounded-lg font-medium text-sm view-details-btn" 
                      data-job-index="<?php echo $index; ?>">
                View Details
              </button>
              <button class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-50 apply-btn"
                      data-job-title="<?php echo $job['title']; ?>">
                Apply Now
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <!-- No Jobs Message -->
      <?php if (empty($jobOpenings)): ?>
      <div class="text-center py-12">
        <div class="text-5xl mb-4">📭</div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Open Positions</h3>
        <p class="text-gray-500 max-w-md mx-auto">
          We don't have any open positions at the moment. Please check back later or send us your resume for future opportunities.
        </p>
      </div>
      <?php endif; ?>
      
      <!-- Newsletter Signup -->
      <div class="mt-12 glass-card rounded-2xl p-8 text-center">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Don't see a perfect match?</h3>
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
          Join our talent community to get notified about new positions that match your skills.
        </p>
        <div class="max-w-md mx-auto">
          <form class="flex flex-col sm:flex-row gap-3">
            <input type="email" 
                   placeholder="Enter your email" 
                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="btn-primary px-6 py-3 rounded-lg font-medium">
              Join Talent Community
            </button>
          </form>
          <p class="text-xs text-gray-500 mt-3">
            We respect your privacy. Unsubscribe at any time.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Application Process -->
  <section class="py-12 bg-white mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-3">
          Our <span class="gradient-text">Hiring Process</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Simple, transparent, and designed to find the best talent
        </p>
      </div>
      
      <!-- Process Steps -->
      <div class="grid mobile-grid-1 tablet-grid-2 desktop-grid-4 gap-8">
        <!-- Step 1 -->
        <div class="text-center animate-fade-in">
          <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl text-blue-600">1</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Application</h3>
          <p class="text-gray-600 text-sm">
            Submit your application through our portal
          </p>
        </div>
        
        <!-- Step 2 -->
        <div class="text-center animate-fade-in animation-delay-100">
          <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl text-green-600">2</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Screening</h3>
          <p class="text-gray-600 text-sm">
            Initial review by our recruitment team
          </p>
        </div>
        
        <!-- Step 3 -->
        <div class="text-center animate-fade-in animation-delay-200">
          <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl text-purple-600">3</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Interviews</h3>
          <p class="text-gray-600 text-sm">
            Technical and cultural fit interviews
          </p>
        </div>
        
        <!-- Step 4 -->
        <div class="text-center animate-fade-in animation-delay-300">
          <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl text-orange-600">4</span>
          </div>
          <h3 class="font-semibold text-gray-800 mb-2">Offer</h3>
          <p class="text-gray-600 text-sm">
            Receive and sign your offer letter
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="py-12 bg-gradient-to-b from-white to-gray-50 mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="text-center mb-10 md:mb-16">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-3">
          Frequently Asked <span class="gradient-text">Questions</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Get answers to common questions about working with us
        </p>
      </div>
      
      <div class="max-w-3xl mx-auto">
        <!-- FAQ Items -->
        <div class="space-y-4">
          <!-- FAQ 1 -->
          <div class="bg-white border border-gray-200 rounded-xl p-6 hover-lift">
            <button class="w-full flex items-center justify-between text-left faq-toggle">
              <h3 class="font-semibold text-gray-800">What's the interview process like?</h3>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div class="mt-3 text-gray-600 hidden faq-content">
              Our process typically includes: initial screening, technical interview, 
              cultural fit discussion, and final offer. We aim to complete the process within 2-3 weeks.
            </div>
          </div>
          
          <!-- FAQ 2 -->
          <div class="bg-white border border-gray-200 rounded-xl p-6 hover-lift">
            <button class="w-full flex items-center justify-between text-left faq-toggle">
              <h3 class="font-semibold text-gray-800">Do you offer remote work?</h3>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div class="mt-3 text-gray-600 hidden faq-content">
              Yes! We're a remote-first company. Most of our positions are fully remote, 
              with occasional team meetups for collaboration.
            </div>
          </div>
          
          <!-- FAQ 3 -->
          <div class="bg-white border border-gray-200 rounded-xl p-6 hover-lift">
            <button class="w-full flex items-center justify-between text-left faq-toggle">
              <h3 class="font-semibold text-gray-800">What benefits do you offer?</h3>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div class="mt-3 text-gray-600 hidden faq-content">
              We offer competitive salaries, health insurance, unlimited PTO, 
              learning budgets, wellness programs, and equity options.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-12 mobile-padding tablet-padding">
    <div class="desktop-container">
      <div class="glass-card rounded-2xl p-8 md:p-12 text-center">
        <h2 class="responsive-subheading font-bold text-gray-800 mb-4">
          Ready to <span class="gradient-text">Join Our Team</span>?
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-8">
          Take the first step towards an exciting career in automotive technology. 
          Apply today or reach out to our recruitment team.
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <a href="#job-openings" class="btn-primary px-8 py-3 rounded-lg font-medium inline-flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Browse All Jobs</span>
          </a>
          
          <a href="mailto:careers@example.com" class="btn-light px-8 py-3 rounded-lg font-medium inline-flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Contact Recruitment</span>
          </a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<!-- Job Details Modal -->
<div id="jobModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm modal-backdrop transition-opacity duration-300"></div>
  <div class="flex items-center justify-center min-h-screen p-4">
    <div id="modalContent" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">
      <!-- Modal Header -->
      <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between z-10">
        <div>
          <h3 id="modalJobTitle" class="text-xl font-bold text-gray-800">Job Title</h3>
          <div id="modalJobMeta" class="text-sm text-gray-500 mt-1"></div>
        </div>
        <button id="modalClose" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Modal Body -->
      <div class="p-6">
        <div id="modalJobDescription" class="text-gray-600 mb-6"></div>
        
        <div class="mb-6">
          <h4 class="font-semibold text-gray-800 mb-3">Requirements</h4>
          <ul id="modalRequirements" class="list-disc list-inside text-gray-600 space-y-2"></ul>
        </div>
        
        <div class="mb-6">
          <h4 class="font-semibold text-gray-800 mb-3">What You'll Do</h4>
          <ul id="modalResponsibilities" class="list-disc list-inside text-gray-600 space-y-2"></ul>
        </div>
        
        <div class="mb-6">
          <h4 class="font-semibold text-gray-800 mb-3">Skills</h4>
          <div id="modalSkills" class="flex flex-wrap gap-2"></div>
        </div>
      </div>
      
      <!-- Modal Footer -->
      <div class="sticky bottom-0 bg-white border-t border-gray-200 p-6">
        <button id="modalApplyBtn" class="w-full btn-primary py-3 rounded-lg font-medium text-lg">
          Apply for this Position
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Back to Top Button -->
<button id="backToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-white border border-gray-200 rounded-full shadow-lg hover:bg-gray-50 transition-all duration-300 hidden items-center justify-center z-40">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
  </svg>
</button>

<script>
// Enhanced JavaScript for Careers Page
document.addEventListener('DOMContentLoaded', function() {
  // Job openings data
  const jobOpenings = <?php echo json_encode($jobOpenings); ?>;
  
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
  
  // Job Filtering
  const departmentFilter = document.querySelector('select:first-of-type');
  const locationFilter = document.querySelector('select:last-of-type');
  const jobCards = document.querySelectorAll('.job-card');
  
  function filterJobs() {
    const selectedDept = departmentFilter ? departmentFilter.value : 'all';
    const selectedLocation = locationFilter ? locationFilter.value : 'all';
    
    jobCards.forEach(card => {
      const cardDept = card.getAttribute('data-department');
      const cardLocation = card.getAttribute('data-location');
      
      const showByDept = selectedDept === 'all' || cardDept === selectedDept;
      const showByLocation = selectedLocation === 'all' || cardLocation === selectedLocation;
      
      if (showByDept && showByLocation) {
        card.style.display = 'block';
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, 10);
      } else {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        setTimeout(() => {
          card.style.display = 'none';
        }, 300);
      }
    });
  }
  
  if (departmentFilter) departmentFilter.addEventListener('change', filterJobs);
  if (locationFilter) locationFilter.addEventListener('change', filterJobs);
  
  // Job Details Modal
  const jobModal = document.getElementById('jobModal');
  const modalBackdrop = document.querySelector('.modal-backdrop');
  const modalContent = document.getElementById('modalContent');
  const modalClose = document.getElementById('modalClose');
  const modalJobTitle = document.getElementById('modalJobTitle');
  const modalJobMeta = document.getElementById('modalJobMeta');
  const modalJobDescription = document.getElementById('modalJobDescription');
  const modalRequirements = document.getElementById('modalRequirements');
  const modalResponsibilities = document.getElementById('modalResponsibilities');
  const modalSkills = document.getElementById('modalSkills');
  const modalApplyBtn = document.getElementById('modalApplyBtn');
  
  const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
  const applyButtons = document.querySelectorAll('.apply-btn');
  
  // Sample job details (can be extended)
  const jobDetails = {
    requirements: [
      'Strong problem-solving skills',
      'Excellent communication abilities',
      'Ability to work in a team environment',
      'Proactive attitude and self-motivation'
    ],
    responsibilities: [
      'Develop and maintain web applications',
      'Collaborate with cross-functional teams',
      'Participate in code reviews',
      'Contribute to technical documentation'
    ]
  };
  
  function openJobModal(jobIndex) {
    const job = jobOpenings[jobIndex];
    
    // Update modal content
    modalJobTitle.textContent = job.title;
    modalJobMeta.innerHTML = `
      <span class="badge badge-primary">${job.department}</span>
      <span class="mx-2">•</span>
      <span>${job.type}</span>
      <span class="mx-2">•</span>
      <span>${job.location}</span>
      <span class="mx-2">•</span>
      <span>${job.experience}</span>
    `;
    
    modalJobDescription.textContent = job.description;
    
    // Requirements
    modalRequirements.innerHTML = jobDetails.requirements
      .map(req => `<li>${req}</li>`)
      .join('');
    
    // Responsibilities
    modalResponsibilities.innerHTML = jobDetails.responsibilities
      .map(resp => `<li>${resp}</li>`)
      .join('');
    
    // Skills
    modalSkills.innerHTML = job.skills
      .map(skill => `<span class="skill-tag">${skill}</span>`)
      .join('');
    
    // Apply button
    modalApplyBtn.textContent = `Apply for ${job.title}`;
    modalApplyBtn.onclick = () => {
      window.location.href = `mailto:careers@example.com?subject=Application for ${job.title}`;
    };
    
    // Show modal with animation
    jobModal.classList.remove('hidden');
    setTimeout(() => {
      modalBackdrop.style.opacity = '1';
      modalContent.style.opacity = '1';
      modalContent.style.transform = 'scale(1)';
      document.body.style.overflow = 'hidden';
    }, 10);
  }
  
  function closeModal() {
    modalBackdrop.style.opacity = '0';
    modalContent.style.opacity = '0';
    modalContent.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
      jobModal.classList.add('hidden');
      document.body.style.overflow = '';
    }, 300);
  }
  
  // Event listeners for view details buttons
  viewDetailsButtons.forEach(button => {
    button.addEventListener('click', function() {
      const jobIndex = parseInt(this.getAttribute('data-job-index'));
      openJobModal(jobIndex);
    });
  });
  
  // Event listeners for apply buttons
  applyButtons.forEach(button => {
    button.addEventListener('click', function() {
      const jobTitle = this.getAttribute('data-job-title');
      window.location.href = `mailto:careers@example.com?subject=Application for ${jobTitle}`;
    });
  });
  
  // Close modal
  if (modalClose) modalClose.addEventListener('click', closeModal);
  if (modalBackdrop) modalBackdrop.addEventListener('click', closeModal);
  
  // Close modal on escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !jobModal.classList.contains('hidden')) {
      closeModal();
    }
  });
  
  // FAQ Toggle
  const faqToggles = document.querySelectorAll('.faq-toggle');
  
  faqToggles.forEach(toggle => {
    toggle.addEventListener('click', function() {
      const content = this.nextElementSibling;
      const icon = this.querySelector('svg');
      
      content.classList.toggle('hidden');
      icon.classList.toggle('rotate-180');
      
      // Close other FAQs
      faqToggles.forEach(otherToggle => {
        if (otherToggle !== this) {
          const otherContent = otherToggle.nextElementSibling;
          const otherIcon = otherToggle.querySelector('svg');
          otherContent.classList.add('hidden');
          otherIcon.classList.remove('rotate-180');
        }
      });
    });
  });
  
  // Newsletter form submission
  const newsletterForm = document.querySelector('form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const emailInput = this.querySelector('input[type="email"]');
      const email = emailInput.value.trim();
      
      if (email) {
        // Show success message
        const button = this.querySelector('button');
        const originalText = button.textContent;
        
        button.textContent = 'Subscribed!';
        button.classList.remove('btn-primary');
        button.classList.add('bg-green-500', 'hover:bg-green-600');
        
        emailInput.value = '';
        
        setTimeout(() => {
          button.textContent = originalText;
          button.classList.add('btn-primary');
          button.classList.remove('bg-green-500', 'hover:bg-green-600');
        }, 2000);
      }
    });
  }
  
  // Add ripple effect to buttons
  const buttons = document.querySelectorAll('.btn-primary, .btn-light, .btn-outline');
  buttons.forEach(button => {
    button.addEventListener('click', function(e) {
      // Create ripple element
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;
      
      ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
        left: ${x}px;
        top: ${y}px;
        width: ${size}px;
        height: ${size}px;
      `;
      
      this.style.position = 'relative';
      this.style.overflow = 'hidden';
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
  `;
  document.head.appendChild(style);
  
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
  
  // Parallax effect for floating elements
  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const floaters = document.querySelectorAll('.floating');
    
    floaters.forEach((floater, index) => {
      const speed = 0.3 + (index * 0.1);
      const yPos = -(scrolled * speed);
      floater.style.transform = `translateY(${yPos}px)`;
    });
  });
  
  // Mobile menu improvements
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
  
  // Count-up animation for stats
  const stats = document.querySelectorAll('.text-3xl.font-bold');
  const observerStats = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const stat = entry.target;
        const finalValue = parseInt(stat.textContent);
        let startValue = 0;
        const duration = 1500;
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
        
        observerStats.unobserve(stat);
      }
    });
  }, { threshold: 0.5 });
  
  stats.forEach(stat => observerStats.observe(stat));
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        window.scrollTo({
          top: target.offsetTop - 80,
          behavior: 'smooth'
        });
      }
    });
  });
});
</script>
</body>
</html>