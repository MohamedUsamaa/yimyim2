document.addEventListener("DOMContentLoaded", () => {
  // Mobile Sidebar Functionality
  const mobileMenuBtn = document.getElementById("mobileMenuBtn");
  const sidebarWrapper = document.getElementById("sidebarWrapper");
  const closeBtnSidebar = document.getElementById("closeBtnSidebar");
  const sidebarOverlay = document.getElementById("sidebarOverlay");

  if (mobileMenuBtn && sidebarWrapper) {
    const toggleSidebar = () => {
      sidebarWrapper.classList.toggle("active");

      if (sidebarOverlay) sidebarOverlay.classList.toggle("active");
      mobileMenuBtn.classList.toggle("active");
    };

    mobileMenuBtn.addEventListener("click", toggleSidebar);
    if (closeBtnSidebar) closeBtnSidebar.addEventListener("click", toggleSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener("click", toggleSidebar);

    // Close sidebar when clicking a navigation link
    const navLinksList = document.querySelectorAll(".nav-links a");
    navLinksList.forEach(link => {
      link.addEventListener("click", () => {
        if (sidebarWrapper.classList.contains("active")) {
          toggleSidebar();
        }
      });
    });
  }

  const searchBtn = document.getElementById("searchBtn");
  const searchPopup = document.getElementById("searchPopup");

  if (searchBtn && searchPopup) {
    searchBtn.addEventListener("click", (event) => {
      event.stopPropagation(); // يمنع إخفاء النافذة فور الضغط على الأيقونة
      searchPopup.classList.toggle("show");
    });

    // عند الضغط في أي مكان خارج نافذة البحث: تخفيها
    document.addEventListener("click", (event) => {
      if (!searchPopup.contains(event.target) && !searchBtn.contains(event.target)) {
        searchPopup.classList.remove("show");
      }
    });
  }

  const userBtn = document.getElementById("userBtn");
  if (userBtn) {
    userBtn.addEventListener("click", () => {
      const isLoggedIn = localStorage.getItem("isLoggedIn");

      if (isLoggedIn === "true") {
        window.location.href = "dashboard.html"; // إلى لوحة التحكم
      } else {
        window.location.href = "login.html"; // إلى صفحة تسجيل الدخول
      }
    });
  }

  const cartBtn = document.getElementById("cartBtn");
  if (cartBtn) {
    cartBtn.addEventListener("click", function () {
      window.location.href = "cart.html";
    });
  }

  const favoriteBtn = document.getElementById("favoriteBtn");
  if (favoriteBtn) {
    favoriteBtn.addEventListener("click", function () {
      window.location.href = "wishlist.html";
    });
  }
});

// دالة البحث
function performSearch() {
  const query = document.getElementById("searchInput").value;
  if (query.trim() !== '') {
    window.location.href = 'shop.html?search=' + encodeURIComponent(query);
  } else {
    alert('يرجى كتابة كلمة للبحث عنها');
  }
}



// إظهار النافذة عند الضغط على الزر
const openPopup = document.getElementById("openPopup");
if (openPopup) {
  openPopup.addEventListener("click", function () {
    document.getElementById("popup").style.display = "flex";
  });
}

// إغلاق النافذة عند الضغط على زر ×
const closeBtn = document.getElementById("close");
if (closeBtn) {
  closeBtn.addEventListener("click", function () {
    document.getElementById("popup").style.display = "none";
  });
}

// إغلاق النافذة عند الضغط خارج الصندوق
window.addEventListener("click", function (event) {
  var popup = document.getElementById("popup");
  if (popup && event.target === popup) {
    popup.style.display = "none";
  }
});







if (document.querySelector(".mySwiper")) {
  var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1.2, // Default for mobile to show next card partially
    centeredSlides: true,
    spaceBetween: 15,
    loop: true,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
    breakpoints: {
      500: { slidesPerView: 2, centeredSlides: false, spaceBetween: 20 },
      768: { slidesPerView: 3, centeredSlides: false, spaceBetween: 20 },
      1024: { slidesPerView: 4, centeredSlides: false, spaceBetween: 20 },
      1280: { slidesPerView: 5, centeredSlides: false, spaceBetween: 20 },
    }
  });
}


document.querySelectorAll('.star').forEach(starContainer => {
  const stars = starContainer.querySelectorAll('i');

  stars.forEach(star => {
    star.addEventListener('click', () => {
      let rating = star.getAttribute('data-value');

      stars.forEach(s => {
        s.classList.remove('active');
        if (s.getAttribute('data-value') <= rating) {
          s.classList.add('active');
        }
      });
    });
  });
});

// Product Linking Logic (Dynamically routes to product.html without changing other HTML)
document.addEventListener("DOMContentLoaded", () => {
  const productContainers = document.querySelectorAll('.pro, .product');
  productContainers.forEach(container => {
    container.style.cursor = 'pointer';
    container.addEventListener('click', function (e) {
      // Prevent routing if the user clicked an input, button, or link inside the container
      if (
        e.target.tagName === 'A' ||
        e.target.tagName === 'INPUT' ||
        e.target.tagName === 'BUTTON' ||
        e.target.closest('a') ||
        (e.target.tagName === 'I' && e.target.closest('.star')) ||
        e.target.closest('.wishlist-btn')
      ) {
        return;
      }

      const titleEl = this.querySelector('h3');
      const priceEl = this.querySelector('h4');
      const imgEl = this.querySelector('img');

      const title = titleEl ? titleEl.innerText.trim() : '';
      const price = priceEl ? priceEl.innerText.trim() : '';
      const img = imgEl ? imgEl.getAttribute('src') : '';

      window.location.href = `product.html?title=${encodeURIComponent(title)}&price=${encodeURIComponent(price)}&img=${encodeURIComponent(img)}`;
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  // تصفية المنتجات في صفحة المتجر (أو أي صفحة تحتوي على منتجات) بناءً على كلمة البحث في الرابط
  const urlParams = new URLSearchParams(window.location.search);
  const searchQuery = urlParams.get('search');

  if (searchQuery) {
    const products = document.querySelectorAll('.product, .pro');
    if (products.length > 0) {
      const lowerQuery = searchQuery.toLowerCase();
      let found = false;
      let productsContainer = null;

      products.forEach(product => {
        if (!productsContainer) {
          productsContainer = product.parentElement;
        }
        const titleEl = product.querySelector('h3');
        if (titleEl && titleEl.innerText.toLowerCase().includes(lowerQuery)) {
          // نظهر المنتج
          product.style.display = '';
          found = true;
        } else {
          // نخفي المنتج
          product.style.display = 'none';
        }
      });

      if (!found && productsContainer) {
        const msg = document.createElement('h3');
        msg.innerText = 'لا توجد منتجات تطابق بحثك عن: ' + searchQuery;
        msg.style.textAlign = 'center';
        msg.style.width = '100%';
        msg.style.marginTop = '40px';
        msg.style.color = '#777';
        productsContainer.appendChild(msg);
      }

      // إزالة ستايل الإخفاء المؤقت لتظهر المنتجات المتطابقة
      const hideStyle = document.getElementById('search-hide-style');
      if (hideStyle) {
        hideStyle.remove();
      }
    }
  }
});

// --- Wishlist Logic ---
document.addEventListener("DOMContentLoaded", () => {
  // 1. إضافة تنسيقات زر القلب مبرمجاً لتشمل كل الصفحات
  const heartStyles = `
    <style>
      .wishlist-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.2rem;
        color: #ccc;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 10;
        outline: none;
      }
      .wishlist-btn:hover {
        background: #fff;
        color: #ff92a2;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
      }
      .wishlist-btn.active {
        color: #ff4757;
      }
      .wishlist-btn.active i {
        animation: heartPop 0.4s ease-out forwards;
      }
      @keyframes heartPop {
        0% { transform: scale(1); }
        50% { transform: scale(1.4); }
        100% { transform: scale(1); }
      }
    </style>
  `;
  document.head.insertAdjacentHTML("beforeend", heartStyles);

  // 2. حقن زر القلب في جميع كروت المنتجات
  const products = document.querySelectorAll('.pro, .product');
  products.forEach(product => {
    // تجنب الإضافة المكررة إذا وجد
    if (product.querySelector('.wishlist-btn')) return;

    const titleEl = product.querySelector('h3');
    const priceEl = product.querySelector('h4') || product.querySelector('.new-price');
    const imgEl = product.querySelector('img');

    if (!titleEl || !priceEl || !imgEl) return;

    // تأكد أن الكارت نسبته position: relative لكي يعمل الموضع بشكل صحيح
    if (window.getComputedStyle(product).position === 'static') {
      product.style.position = 'relative';
    }

    const title = titleEl.innerText.trim();
    const price = priceEl.innerText.trim();
    const img = imgEl.getAttribute('src');

    const wishlistBtn = document.createElement('button');
    wishlistBtn.className = 'wishlist-btn';

    // التحقق هل المنتج في المفضلة أولاً
    let wishlistStr = localStorage.getItem('yim_wishlist');
    let wishlistArr = wishlistStr ? JSON.parse(wishlistStr) : [];
    const isInWishlist = wishlistArr.some(item => item.title === title);

    if (isInWishlist) {
      wishlistBtn.classList.add('active');
      wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
    } else {
      wishlistBtn.innerHTML = '<i class="far fa-heart"></i>';
    }

    product.appendChild(wishlistBtn);

    // حدث الضغط على الزر للإضافة/الحذف
    wishlistBtn.addEventListener('click', (e) => {
      e.stopPropagation(); // منع الانتقال لصفحة التفاصيل
      e.preventDefault();

      let currentStr = localStorage.getItem('yim_wishlist');
      let currentArr = currentStr ? JSON.parse(currentStr) : [];
      let index = currentArr.findIndex(item => item.title === title);

      if (index > -1) {
        // حذفه من المفضلة
        currentArr.splice(index, 1);
        wishlistBtn.classList.remove('active');
        wishlistBtn.innerHTML = '<i class="far fa-heart"></i>';
      } else {
        // إضافته للمفضلة
        currentArr.push({
          title: title,
          price: price,
          img: img,
          addedAt: new Date().toISOString()
        });
        wishlistBtn.classList.add('active');
        wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
      }
      localStorage.setItem('yim_wishlist', JSON.stringify(currentArr));
    });
  });

  // 3. حقن زر القلب في صفحة تفاصيل المنتج (product.html)
  const productDetailsImgWrapper = document.querySelector('.product-image-wrapper');
  if (productDetailsImgWrapper && !productDetailsImgWrapper.querySelector('.wishlist-btn')) {
    const titleEl = document.getElementById('productTitle');
    const priceEl = document.getElementById('productPrice');
    const imgEl = document.getElementById('productImage');

    if (titleEl && imgEl) {
      if (window.getComputedStyle(productDetailsImgWrapper).position === 'static') {
        productDetailsImgWrapper.style.position = 'relative';
      }

      const title = titleEl.innerText.trim();
      const priceStr = priceEl ? priceEl.innerText.trim() : 'EGP 60';
      const img = imgEl.getAttribute('src');

      const wishlistBtn = document.createElement('button');
      wishlistBtn.className = 'wishlist-btn';
      wishlistBtn.style.top = '25px';
      wishlistBtn.style.right = '25px';
      wishlistBtn.style.width = '45px';
      wishlistBtn.style.height = '45px';
      wishlistBtn.style.fontSize = '1.4rem';

      let wishlistStr = localStorage.getItem('yim_wishlist');
      let wishlistArr = wishlistStr ? JSON.parse(wishlistStr) : [];
      const isInWishlist = wishlistArr.some(item => item.title === title);

      if (isInWishlist) {
        wishlistBtn.classList.add('active');
        wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
      } else {
        wishlistBtn.innerHTML = '<i class="far fa-heart"></i>';
      }

      productDetailsImgWrapper.appendChild(wishlistBtn);

      wishlistBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();

        let currentStr = localStorage.getItem('yim_wishlist');
        let currentArr = currentStr ? JSON.parse(currentStr) : [];
        let index = currentArr.findIndex(item => item.title === title);

        if (index > -1) {
          currentArr.splice(index, 1);
          wishlistBtn.classList.remove('active');
          wishlistBtn.innerHTML = '<i class="far fa-heart"></i>';
        } else {
          currentArr.push({
            title: title,
            price: priceStr,
            img: img,
            addedAt: new Date().toISOString()
          });
          wishlistBtn.classList.add('active');
          wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
        }
        localStorage.setItem('yim_wishlist', JSON.stringify(currentArr));
      });
    }
  }
});

// --- Cart Logic ---
document.addEventListener('click', (e) => {
  const cartBtn = e.target.closest('.cart, a[href="cart.html"], .btn-add, .add-to-cart-btn, .btn-buy, a[href="checkout.html"]');
  if (cartBtn) {
    // Avoid triggering if it's the navbar cart icon itself
    if (cartBtn.closest('.icons') && cartBtn.id === 'cartBtn') return;

    // Check if it's a Buy Now action
    const isBuyNow = cartBtn.classList.contains('btn-buy') || cartBtn.getAttribute('href') === 'checkout.html';

    // Check where the click happened
    const isProductPage = cartBtn.closest('.product-container');
    const isCard = cartBtn.closest('.pro, .product, tr');

    e.preventDefault();

    let title = '', price = 0, img = '', qty = 1;

    if (isProductPage) {
      title = document.getElementById('productTitle')?.innerText.trim() || '';
      let prStr = document.getElementById('productPrice')?.innerText.trim() || '';
      price = parseFloat(prStr.replace(/[^0-9.]/g, '')) || 0;
      img = document.getElementById('productImage')?.getAttribute('src') || '';
      qty = parseInt(document.getElementById('qtyInput')?.value) || 1;
    } else if (isCard) {
      // In a regular card or wishlist
      let titleEl = isCard.querySelector('h3, .product-info');
      if (titleEl) {
        // Clean title if inside wishlist logic
        title = titleEl.innerText.replace('🗑', '').split('\n')[0].trim();
      }

      let prStr = isCard.querySelector('h4, .new-price')?.innerText.trim() || '';
      price = parseFloat(prStr.replace(/[^0-9.]/g, '')) || 0;
      img = isCard.querySelector('img')?.getAttribute('src') || '';
      const inputQty = isCard.querySelector('input[type="number"]');
      if (inputQty) qty = parseInt(inputQty.value) || 1;
    }

    if (title && price > 0) {
      let cartStr = localStorage.getItem('yim_cart');
      let cartArr = cartStr ? JSON.parse(cartStr) : [];
      let idx = cartArr.findIndex(item => item.title === title);

      if (idx > -1) {
        cartArr[idx].qty += qty;
      } else {
        cartArr.push({ title, price, img, qty });
      }

      localStorage.setItem('yim_cart', JSON.stringify(cartArr));

      if (isBuyNow) {
        window.location.href = 'checkout.html';
      } else {
        showCartToast('تمت الإضافة إلى السلة بنجاح!');
      }
    } else {
      // If we clicked a Buy Now / Cart link somewhere without product data context, just navigate
      if (isBuyNow) {
        window.location.href = 'checkout.html';
      } else if (cartBtn.getAttribute('href') === 'cart.html') {
        window.location.href = 'cart.html';
      }
    }
  }
});

function showCartToast(msg) {
  let oldToast = document.getElementById('cart-toast-msg');
  if (oldToast) oldToast.remove();

  let toast = document.createElement('div');
  toast.id = 'cart-toast-msg';
  toast.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 8px;"></i>' + msg;
  toast.style.position = 'fixed';
  toast.style.bottom = '20px';
  toast.style.left = '50%';
  toast.style.transform = 'translateX(-50%) translateY(20px)';
  toast.style.background = '#ffb6c1';
  toast.style.color = '#fff';
  toast.style.padding = '12px 25px';
  toast.style.borderRadius = '50px';
  toast.style.boxShadow = '0 5px 15px rgba(255, 182, 193, 0.4)';
  toast.style.zIndex = '9999';
  toast.style.fontSize = '15px';
  toast.style.fontWeight = 'bold';
  toast.style.opacity = '0';
  toast.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';

  document.body.appendChild(toast);

  // الدخول
  setTimeout(() => {
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
  }, 10);

  // الخروج
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-50%) translateY(20px)';
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}
