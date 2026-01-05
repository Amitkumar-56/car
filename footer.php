<?php
// expects $title to be set by the including page
?>
<style>
@keyframes footerScroll{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.footer-scroll{animation:footerScroll 18s linear infinite}

/* Mobile responsive styles */
@media (max-width: 640px) {
  .footer-scroll {
    animation-duration: 12s !important;
    font-size: 11px !important;
    padding: 2px 0 !important;
  }
  
  .footer-main-content {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
  }
  
  .footer-contact span {
    font-size: 10px !important;
    padding: 4px 8px !important;
  }
  
  .footer-bottom {
    padding: 6px 0 !important;
    font-size: 10px !important;
  }
  
  .mobile-quick-actions {
    padding: 6px !important;
  }
  
  .mobile-quick-actions div {
    width: 28px !important;
    height: 28px !important;
    font-size: 14px !important;
  }
  
  .mobile-quick-actions span {
    font-size: 9px !important;
  }
}
</style>
<footer class="fixed bottom-0 left-0 right-0 bg-gradient-to-r from-blue-50 to-indigo-50 text-gray-800 shadow-lg border-t border-gray-200" style="height: auto; min-height: fit-content;">
  <!-- Scrolling news ticker - LIGHT -->
  <div class="border-b border-gray-300 bg-gray-100/80 py-1">
    <div class="max-w-7xl mx-auto px-2 overflow-hidden">
      <div class="flex gap-3 md:gap-4 footer-scroll whitespace-nowrap">
        <span class="text-blue-700 text-xs font-medium">🚗 SUV under 10L</span>
        <span class="text-purple-700 text-xs font-medium">⚡ New EVs</span>
        <span class="text-green-700 text-xs font-medium">🆕 Compact SUVs</span>
        <span class="text-amber-700 text-xs font-medium">💰 Best Mileage</span>
        <span class="text-cyan-700 text-xs font-medium">💡 Compare Prices</span>
        <span class="text-blue-700 text-xs font-medium">🚙 SUV under 10L</span>
        <span class="text-purple-700 text-xs font-medium">⚡ New EVs</span>
        <span class="text-green-700 text-xs font-medium">🆕 Compact SUVs</span>
        <span class="text-amber-700 text-xs font-medium">💰 Best Mileage</span>
        <span class="text-cyan-700 text-xs font-medium">💡 Compare Prices</span>
      </div>
    </div>
  </div>
  
  <!-- Main footer content - LIGHT -->
  <div class="max-w-7xl mx-auto px-2 py-2 footer-main-content">
    <div class="flex flex-col md:flex-row items-center justify-between gap-2">
      <!-- Logo/Title - LIGHT -->
      <div class="font-bold text-sm md:text-base text-blue-800 mb-1 md:mb-0"><?php echo htmlspecialchars($title ?? 'Car Portal'); ?></div>
      
      <!-- Navigation Links - LIGHT -->
      <div class="flex flex-wrap justify-center gap-1 footer-links">
        <a class="px-2 py-1 rounded bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs transition-all duration-200 border border-blue-200" href="index.php#most">Most</a>
        <a class="px-2 py-1 rounded bg-purple-100 hover:bg-purple-200 text-purple-800 text-xs transition-all duration-200 border border-purple-200" href="index.php#latest">Latest</a>
        <a class="px-2 py-1 rounded bg-pink-100 hover:bg-pink-200 text-pink-800 text-xs transition-all duration-200 border border-pink-200" href="index.php#form">Help</a>
        <a class="px-2 py-1 rounded bg-cyan-100 hover:bg-cyan-200 text-cyan-800 text-xs transition-all duration-200 border border-cyan-200" href="about.php">About</a>
        <a class="px-2 py-1 rounded bg-amber-100 hover:bg-amber-200 text-amber-800 text-xs transition-all duration-200 border border-amber-200" href="careers.php">Careers</a>
      </div>
      
      <!-- Contact Info - LIGHT -->
      <div class="flex flex-wrap justify-center gap-1 footer-contact mt-1 md:mt-0">
        <span class="px-2 py-1 rounded bg-blue-100/80 text-blue-800 text-xs flex items-center gap-1 border border-blue-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          amitk73262@gmail.com
        </span>
        <span class="px-2 py-1 rounded bg-purple-100/80 text-purple-800 text-xs flex items-center gap-1 border border-purple-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
          </svg>
          +91-9648065956
        </span>
      </div>
    </div>
  </div>
  
  <!-- Footer Bottom - LIGHT -->
  <div class="border-t border-gray-300 bg-gray-100/60 py-1 footer-bottom">
    <div class="max-w-7xl mx-auto px-2">
      <div class="text-xs text-gray-600 flex flex-col md:flex-row items-center justify-between gap-1">
        <div class="flex items-center gap-1">
          <span>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($title ?? 'Car Portal'); ?></span>
          <span class="hidden md:inline">•</span>
          <span class="text-gray-500 hidden md:inline">All rights reserved</span>
        </div>
        <div class="flex items-center gap-1">
          <span class="text-gray-500">Built with</span>
          <span class="bg-gradient-to-r from-blue-400 to-indigo-500 text-white px-1.5 py-0.5 rounded text-xs font-medium">Tailwind</span>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Mobile quick actions - LIGHT -->
  <div class="md:hidden bg-gradient-to-r from-blue-100 to-indigo-100 p-1 mobile-quick-actions border-t border-gray-300">
    <div class="flex justify-around items-center">
      <a href="index.php#most" class="flex flex-col items-center text-xs text-blue-800">
        <div class="w-6 h-6 flex items-center justify-center bg-blue-200 rounded mb-0.5">
          <span class="text-xs">🔥</span>
        </div>
        <span>Most</span>
      </a>
      <a href="index.php#latest" class="flex flex-col items-center text-xs text-purple-800">
        <div class="w-6 h-6 flex items-center justify-center bg-purple-200 rounded mb-0.5">
          <span class="text-xs">🆕</span>
        </div>
        <span>Latest</span>
      </a>
      <a href="index.php#form" class="flex flex-col items-center text-xs text-pink-800">
        <div class="w-6 h-6 flex items-center justify-center bg-pink-200 rounded mb-0.5">
          <span class="text-xs">💬</span>
        </div>
        <span>Help</span>
      </a>
      <a href="tel:+919648065956" class="flex flex-col items-center text-xs text-cyan-800">
        <div class="w-6 h-6 flex items-center justify-center bg-cyan-200 rounded mb-0.5">
          <span class="text-xs">📞</span>
        </div>
        <span>Call</span>
      </a>
    </div>
  </div>
</footer>

<script>
// Auto-hide footer on scroll for mobile
document.addEventListener('DOMContentLoaded', function() {
  let footer = document.querySelector('footer');
  let lastScrollTop = 0;
  
  window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (window.innerWidth <= 768) {
      if (scrollTop > lastScrollTop) {
        // Scrolling down - hide footer
        footer.style.transform = 'translateY(100%)';
        footer.style.transition = 'transform 0.3s ease';
      } else {
        // Scrolling up - show footer
        footer.style.transform = 'translateY(0)';
      }
    } else {
      footer.style.transform = 'translateY(0)';
    }
    
    lastScrollTop = scrollTop;
  });
  
  // Make call when mobile call button is clicked
  document.querySelector('a[href^="tel:"]').addEventListener('click', function(e) {
    if (window.innerWidth <= 768) {
      // On mobile, let the default tel: link work
      return true;
    } else {
      // On desktop, show phone number
      e.preventDefault();
      alert('Call: +91-9648065956');
    }
  });
});
</script>