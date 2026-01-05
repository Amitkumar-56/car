document.addEventListener("DOMContentLoaded", function () {
  var t = document.querySelector(".nav-toggle");
  var n = document.querySelector(".nav");
  if (t && n) {
    t.addEventListener("click", function () {
      if (n.classList.contains("hidden")) {
        n.classList.remove("hidden");
        var o = document.getElementById("navOverlay");
        if (o) o.classList.remove("hidden");
      } else {
        n.classList.add("hidden");
        var o2 = document.getElementById("navOverlay");
        if (o2) o2.classList.add("hidden");
      }
    });
    n.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", function () {
        n.classList.add("hidden");
        var o3 = document.getElementById("navOverlay");
        if (o3) o3.classList.add("hidden");
      });
    });
  }

  var search = document.getElementById("siteSearch");
  if (search) {
    var cards = document.querySelectorAll("[data-name]");
    search.addEventListener("input", function () {
      var q = search.value.toLowerCase();
      cards.forEach(function (el) {
        var name = (el.getAttribute("data-name") || "").toLowerCase();
        if (name.indexOf(q) >= 0) {
          el.classList.remove("hidden");
        } else {
          el.classList.add("hidden");
        }
      });
    });
  }
  var formSection = document.getElementById("form");
  var formMsg = formSection ? formSection.querySelector(".alert-msg") : null;
  if (formSection && formMsg) {
    setTimeout(function () {
      formSection.scrollIntoView({ behavior: "smooth", block: "start" });
    }, 100);
  }
  var tabs = document.querySelectorAll(".category-tab");
  if (tabs.length) {
    var mostTitle = document.getElementById("mostTitle");
    var colorMap = {
      SUV: "text-emerald-800",
      Hatchback: "text-indigo-800",
      Sedan: "text-rose-800",
      MUV: "text-amber-800",
      Luxury: "text-purple-800",
    };
    var allColors = [
      "text-emerald-800",
      "text-indigo-800",
      "text-rose-800",
      "text-amber-800",
      "text-purple-800",
    ];
    tabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        var cat = tab.getAttribute("data-cat");
        tabs.forEach(function (t) {
          t.classList.remove(
            "border-emerald-600",
            "text-emerald-700",
            "bg-emerald-50"
          );
        });
        tab.classList.add(
          "border-emerald-600",
          "text-emerald-700",
          "bg-emerald-50"
        );
        tab.classList.add("animate-pulse");
        setTimeout(function () {
          tab.classList.remove("animate-pulse");
        }, 300);
        document.querySelectorAll("[data-cat]").forEach(function (card) {
          var c = card.getAttribute("data-cat");
          if (c === cat) {
            card.classList.remove("hidden");
          } else {
            card.classList.add("hidden");
          }
        });
        if (mostTitle) {
          mostTitle.classList.remove.apply(mostTitle.classList, allColors);
          var cls = colorMap[cat] || "text-emerald-800";
          mostTitle.classList.add(cls);
          mostTitle.classList.add("animate-bounce");
          setTimeout(function () {
            mostTitle.classList.remove("animate-bounce");
          }, 500);
        }
      });
    });
  }
  var overlay = document.getElementById("navOverlay");
  var mobileNav = document.querySelector(".nav");
  if (overlay && mobileNav) {
    overlay.addEventListener("click", function () {
      mobileNav.classList.add("hidden");
      overlay.classList.add("hidden");
    });
  }
  var modal = document.getElementById("cardModal");
  var modalOverlay = document.getElementById("cardModalOverlay");
  var modalPanel = document.getElementById("modalPanel");
  var modalTitle = document.getElementById("modalTitle");
  var modalImg = document.getElementById("modalImg");
  var modalImgPh = document.getElementById("modalImgPlaceholder");
  var modalClose = document.getElementById("modalClose");
  function openModal(name, img) {
    modalTitle.textContent = name || "Details";
    if (img) {
      modalImg.src = img;
      modalImg.classList.remove("hidden");
      modalImgPh.classList.add("hidden");
    } else {
      modalImg.src = "";
      modalImg.classList.add("hidden");
      modalImgPh.classList.remove("hidden");
    }
    modal.classList.remove("hidden");
    modalPanel.classList.remove("opacity-0", "scale-95");
    modalPanel.classList.add("opacity-100", "scale-100");
  }
  function closeModal() {
    modalPanel.classList.remove("opacity-100", "scale-100");
    modalPanel.classList.add("opacity-0", "scale-95");
    setTimeout(function () {
      modal.classList.add("hidden");
    }, 150);
  }
  if (modal && modalOverlay && modalClose) {
    modalOverlay.addEventListener("click", closeModal);
    modalClose.addEventListener("click", closeModal);
  }
  document.querySelectorAll(".car-card").forEach(function (card) {
    card.addEventListener("click", function () {
      document.querySelectorAll(".car-card").forEach(function (c) {
        c.classList.remove("ring-2", "ring-emerald-600");
      });
      card.classList.add("ring-2", "ring-emerald-600");
      var name = card.getAttribute("data-name");
      var img = card.getAttribute("data-image");
      openModal(name, img);
    });
  });
  var mostTitle = document.getElementById("mostTitle");
  if (mostTitle) {
    mostTitle.addEventListener("click", function () {
      var first = document.querySelector("#most .car-card");
      if (first) {
        var name = first.getAttribute("data-name");
        var img = first.getAttribute("data-image");
        openModal(name, img);
      }
    });
  }
});
