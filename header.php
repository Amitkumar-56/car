<?php
// expects $title to be set by the including page
?>
<style>
/* Custom styles for mobile header */
@media (max-width: 768px) {
  .mobile-search-container {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    padding: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 20;
  }
  
  .nav-toggle {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.3s ease;
  }
  
  .nav-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
  }
}
</style>
<header class="sticky top-0 bg-gradient-to-r from-blue-50 to-indigo-50 text-gray-800 shadow-lg z-40 border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4 py-3">
    <div class="flex items-center justify-between">
      <!-- Logo -->
      <div class="flex items-center space-x-3">
        <div class="font-bold text-xl md:text-2xl text-blue-700">
          <?php echo htmlspecialchars($title ?? 'Car Portal'); ?>
        </div>
      </div>
      
      <!-- Desktop Search and Navigation -->
      <div class="hidden lg:flex items-center space-x-6">
        <!-- Search Bar -->
        <div class="relative">
          <input id="siteSearch" 
                 class="pl-10 pr-4 py-2.5 w-80 rounded-full border border-gray-300 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                 type="search" 
                 placeholder="Search cars, brands, models...">
          <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        
        <!-- Desktop Navigation -->
        <nav class="flex items-center space-x-1">
          <a class="px-4 py-2.5 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 font-medium text-sm" href="index.php#most">
            <span class="mr-1">🔥</span> Most
          </a>
          <a class="px-4 py-2.5 rounded-lg text-gray-700 hover:bg-purple-100 hover:text-purple-700 transition-all duration-200 font-medium text-sm" href="index.php#latest">
            <span class="mr-1">🆕</span> Latest
          </a>
          <a class="px-4 py-2.5 rounded-lg text-gray-700 hover:bg-pink-100 hover:text-pink-700 transition-all duration-200 font-medium text-sm" href="index.php#form">
            <span class="mr-1">💬</span> Help
          </a>
          <a class="px-4 py-2.5 rounded-lg text-gray-700 hover:bg-cyan-100 hover:text-cyan-700 transition-all duration-200 font-medium text-sm" href="about.php">
            <span class="mr-1">ℹ️</span> About
          </a>
          <a class="px-4 py-2.5 rounded-lg text-gray-700 hover:bg-amber-100 hover:text-amber-700 transition-all duration-200 font-medium text-sm" href="careers.php">
            <span class="mr-1">💼</span> Careers
          </a>
          <a class="ml-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-medium text-sm shadow-sm" href="admin.php">
            Admin
          </a>
        </nav>
      </div>
      
      <!-- Mobile Right Section -->
      <div class="flex items-center space-x-3 lg:hidden">
        <!-- Mobile Search Button -->
        <button id="mobileSearchToggle" 
                class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors duration-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </button>
        
        <!-- Mobile Menu Toggle -->
        <button id="navToggle" 
                class="nav-toggle w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 hover:from-blue-200 hover:to-indigo-200 transition-all duration-300 shadow-sm">
          <svg id="menuIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg id="closeIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Mobile Search Container (Hidden by default) -->
    <div id="mobileSearchContainer" class="mobile-search-container hidden">
      <div class="relative">
        <input id="mobileSiteSearch" 
               class="w-full pl-12 pr-4 py-3 rounded-full border-2 border-blue-500 bg-white shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
               type="search" 
               placeholder="Search for cars, SUVs, EVs...">
        <svg class="absolute left-4 top-3.5 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <button id="closeMobileSearch" class="absolute right-4 top-3.5 text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="mt-3 flex flex-wrap gap-2">
        <span class="text-xs text-gray-500">Quick searches:</span>
        <button class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs rounded-full hover:bg-blue-200 transition-colors">SUV</button>
        <button class="px-3 py-1.5 bg-purple-100 text-purple-700 text-xs rounded-full hover:bg-purple-200 transition-colors">EV</button>
        <button class="px-3 py-1.5 bg-green-100 text-green-700 text-xs rounded-full hover:bg-green-200 transition-colors">Hatchback</button>
        <button class="px-3 py-1.5 bg-amber-100 text-amber-700 text-xs rounded-full hover:bg-amber-200 transition-colors">Under 10L</button>
      </div>
    </div>
    
    <!-- Mobile Navigation Menu (Hidden by default) -->
    <nav id="mobileNav" class="absolute left-0 right-0 top-full bg-white shadow-xl rounded-b-2xl z-30 hidden transform transition-all duration-300 opacity-0 scale-95 origin-top">
      <div class="p-4 space-y-1">
        <a class="flex items-center px-4 py-3.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 text-base font-medium" href="index.php#most">
          <span class="mr-3 text-xl">🔥</span>
          <div>
            <div class="font-semibold">Most Searched</div>
            <div class="text-xs text-gray-500">Popular car models</div>
          </div>
        </a>
        <a class="flex items-center px-4 py-3.5 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 text-base font-medium" href="index.php#latest">
          <span class="mr-3 text-xl">🆕</span>
          <div>
            <div class="font-semibold">Latest Cars</div>
            <div class="text-xs text-gray-500">New launches</div>
          </div>
        </a>
        <a class="flex items-center px-4 py-3.5 rounded-lg text-gray-700 hover:bg-pink-50 hover:text-pink-700 transition-all duration-200 text-base font-medium" href="index.php#form">
          <span class="mr-3 text-xl">💬</span>
          <div>
            <div class="font-semibold">Get Help</div>
            <div class="text-xs text-gray-500">Contact & support</div>
          </div>
        </a>
        <a class="flex items-center px-4 py-3.5 rounded-lg text-gray-700 hover:bg-cyan-50 hover:text-cyan-700 transition-all duration-200 text-base font-medium" href="about.php">
          <span class="mr-3 text-xl">ℹ️</span>
          <div>
            <div class="font-semibold">About Us</div>
            <div class="text-xs text-gray-500">Our story & mission</div>
          </div>
        </a>
        <a class="flex items-center px-4 py-3.5 rounded-lg text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-all duration-200 text-base font-medium" href="careers.php">
          <span class="mr-3 text-xl">💼</span>
          <div>
            <div class="font-semibold">Careers</div>
            <div class="text-xs text-gray-500">Join our team</div>
          </div>
        </a>
        <div class="pt-4 mt-4 border-t border-gray-200">
          <a class="flex items-center justify-center px-4 py-3.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 text-base font-semibold shadow-md" href="admin.php">
            <span class="mr-2">⚙️</span> Admin Panel
          </a>
        </div>
      </div>
    </nav>
    
    <!-- Overlay for mobile menu -->
    <div id="navOverlay" class="fixed inset-0 bg-black/40 z-20 hidden"></div>
  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const navToggle = document.getElementById('navToggle');
  const mobileNav = document.getElementById('mobileNav');
  const navOverlay = document.getElementById('navOverlay');
  const menuIcon = document.getElementById('menuIcon');
  const closeIcon = document.getElementById('closeIcon');
  const mobileSearchToggle = document.getElementById('mobileSearchToggle');
  const mobileSearchContainer = document.getElementById('mobileSearchContainer');
  const closeMobileSearch = document.getElementById('closeMobileSearch');
  const mobileSiteSearch = document.getElementById('mobileSiteSearch');
  
  let isMenuOpen = false;
  let isSearchOpen = false;
  
  // Toggle mobile navigation
  navToggle.addEventListener('click', function() {
    isMenuOpen = !isMenuOpen;
    
    if (isMenuOpen) {
      mobileNav.classList.remove('hidden', 'opacity-0', 'scale-95');
      mobileNav.classList.add('block', 'opacity-100', 'scale-100');
      navOverlay.classList.remove('hidden');
      menuIcon.classList.add('hidden');
      closeIcon.classList.remove('hidden');
      // Close search if open
      if (isSearchOpen) {
        closeSearch();
      }
    } else {
      mobileNav.classList.remove('block', 'opacity-100', 'scale-100');
      mobileNav.classList.add('hidden', 'opacity-0', 'scale-95');
      navOverlay.classList.add('hidden');
      menuIcon.classList.remove('hidden');
      closeIcon.classList.add('hidden');
    }
  });
  
  // Close menu when overlay is clicked
  navOverlay.addEventListener('click', function() {
    closeMenu();
    closeSearch();
  });
  
  // Toggle mobile search
  mobileSearchToggle.addEventListener('click', function() {
    isSearchOpen = !isSearchOpen;
    
    if (isSearchOpen) {
      mobileSearchContainer.classList.remove('hidden');
      mobileSearchContainer.classList.add('block');
      // Close menu if open
      if (isMenuOpen) {
        closeMenu();
      }
      // Focus on search input
      setTimeout(() => {
        mobileSiteSearch.focus();
      }, 100);
    } else {
      mobileSearchContainer.classList.remove('block');
      mobileSearchContainer.classList.add('hidden');
    }
  });
  
  // Close mobile search
  closeMobileSearch.addEventListener('click', closeSearch);
  
  // Quick search buttons
  document.querySelectorAll('#mobileSearchContainer button').forEach(button => {
    if (button.textContent !== '') {
      button.addEventListener('click', function() {
        mobileSiteSearch.value = this.textContent;
        mobileSiteSearch.focus();
        // Here you can trigger search
        console.log('Searching for:', this.textContent);
      });
    }
  });
  
  // Close menu function
  function closeMenu() {
    isMenuOpen = false;
    mobileNav.classList.remove('block', 'opacity-100', 'scale-100');
    mobileNav.classList.add('hidden', 'opacity-0', 'scale-95');
    navOverlay.classList.add('hidden');
    menuIcon.classList.remove('hidden');
    closeIcon.classList.add('hidden');
  }
  
  // Close search function
  function closeSearch() {
    isSearchOpen = false;
    mobileSearchContainer.classList.remove('block');
    mobileSearchContainer.classList.add('hidden');
  }
  
  // Close search on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      if (isSearchOpen) closeSearch();
      if (isMenuOpen) closeMenu();
    }
  });
  
  // Search functionality
  const siteSearch = document.getElementById('siteSearch');
  const performSearch = function(query) {
    if (query.trim() !== '') {
      // Implement your search logic here
      console.log('Searching for:', query);
      // You can redirect to search page or filter content
      alert(`Searching for: ${query}`);
    }
  };
  
  // Desktop search
  if (siteSearch) {
    siteSearch.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        performSearch(this.value);
      }
    });
  }
  
  // Mobile search
  if (mobileSiteSearch) {
    mobileSiteSearch.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        performSearch(this.value);
        closeSearch();
      }
    });
  }
});
</script>