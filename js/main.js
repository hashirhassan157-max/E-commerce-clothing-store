// js/main.js
document.addEventListener('DOMContentLoaded', () => {

    const toastContainer = document.getElementById('toast-notification');

    // Function to show a toast notification
    function showToast(message, isSuccess = true) {
        toastContainer.textContent = message;
        toastContainer.className = 'toast-notification show';
        if (!isSuccess) {
            toastContainer.style.backgroundColor = '#dc3545'; // Red for error
        } else {
            toastContainer.style.backgroundColor = '#333'; // Default dark
        }

        setTimeout(() => {
            toastContainer.className = 'toast-notification';
        }, 3000);
    }

    // Function to update cart and wishlist counts in header
    function updateHeaderCounts(cartCount, wishlistCount) {
        if (cartCount !== null) {
            document.getElementById('cart-count').textContent = cartCount;
        }
        if (wishlistCount !== null) {
            document.getElementById('wishlist-count').textContent = wishlistCount;
        }
    }

    // --- 1. Add to Cart (from grid) ---
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = e.currentTarget.dataset.productId;
            handleCartAction('add', productId, 1);
        });
    });

    // --- 2. Add to Cart (from Product Detail Page) ---
    const pdpCartForm = document.getElementById('add-to-cart-form');
    if (pdpCartForm) {
        pdpCartForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(pdpCartForm);
            const productId = formData.get('product_id');
            const quantity = formData.get('quantity');
            handleCartAction('add', productId, quantity);
        });
    }

    // --- 3. Add/Remove from Wishlist (from grid & PDP) ---
    document.querySelectorAll('.btn-add-to-wishlist, .btn-add-to-wishlist-pdp').forEach(button => {
        button.addEventListener('click', (e) => {
            const currentButton = e.currentTarget;
            const productId = currentButton.dataset.productId;
            
            // Determine action based on class
            let action;
            if (currentButton.classList.contains('in-wishlist')) {
                action = 'remove';
            } else {
                action = 'add';
            }
            
            handleWishlistAction(action, productId, currentButton);
        });
    });

    // --- 4. Remove from Wishlist (from wishlist page) ---
    document.querySelectorAll('.btn-remove-from-wishlist').forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = e.currentTarget.dataset.productId;
            handleWishlistAction('remove', productId, e.currentTarget);
        });
    });


    // --- Reusable Fetch Function for Cart Actions ---
    function handleCartAction(action, productId, quantity) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        fetch('api/cart_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Item added to cart!');
                updateHeaderCounts(data.cart_count, null);
            } else {
                showToast(data.message || 'Action failed.', false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', false);
        });
    }

    // --- Reusable Fetch Function for Wishlist Actions ---
    function handleWishlistAction(action, productId, buttonElement) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('product_id', productId);

        fetch('api/wishlist_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Wishlist updated!');
                updateHeaderCounts(null, data.wishlist_count);
                
                // LIVE TOGGLE LOGIC
                if (buttonElement) {
                    const icon = buttonElement.querySelector('i');
                    if (action === 'add') {
                        buttonElement.classList.add('in-wishlist');
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        
                        if (buttonElement.classList.contains('btn-add-to-wishlist-pdp')) {
                            buttonElement.innerHTML = '<i class="fa-solid fa-heart"></i> Added to Wishlist';
                        }
                    } else if (action === 'remove') {
                        buttonElement.classList.remove('in-wishlist');
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');

                        if (buttonElement.classList.contains('btn-add-to-wishlist-pdp')) {
                            buttonElement.innerHTML = '<i class="fa-regular fa-heart"></i> Add to Wishlist';
                        }
                    }
                }

                // If removing from wishlist page, remove the item element
                if (action === 'remove' && buttonElement.closest('.wishlist-item')) {
                    buttonElement.closest('.wishlist-item').remove();
                    if (document.querySelectorAll('.wishlist-item').length === 0) {
                        document.querySelector('.wishlist-container').innerHTML = `
                            <h1 class="page-title">Your Wishlist</h1>
                            <div class="empty-page-message">
                                <p>Your wishlist is empty.</p>
                                <a href="index.php" class="btn btn-primary">Discover Products</a>
                            </div>`;
                    }
                }
            } else {
                showToast(data.message || 'Please log in to use the wishlist.', false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', false);
        });
    }
    
    // --- 5. Checkout Page - Payment Method Toggle ---
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const cardDetails = document.getElementById('payment-card-details');
            if (e.target.value === 'card') {
                cardDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
            }
        });
    });
    
    // --- 6. Product Detail Page - Thumbnail Click ---
    const mainImage = document.getElementById('main-product-image');
    const thumbnails = document.querySelectorAll('.thumbnail-image');

    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', () => {
                mainImage.src = thumb.src;
                thumbnails.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            });
        });
    }

    // --- 7. Mobile Menu Toggle ---
    const mobileMenuBtn = document.getElementById('nav-toggle-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });
    }

    // --- 8. Search Overlay Toggle ---
    const searchBtn = document.querySelector('.mobile-search-btn');
    const searchOverlay = document.getElementById('search-overlay');
    const closeSearchBtn = document.getElementById('close-search');

    if (searchBtn && searchOverlay) {
        searchBtn.addEventListener('click', () => {
            searchOverlay.classList.add('active');
        });
    }

    if (closeSearchBtn && searchOverlay) {
        closeSearchBtn.addEventListener('click', () => {
            searchOverlay.classList.remove('active');
        });
    }


    // --- 9. Hero Carousel Logic (New Fade Effect) ---
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    if (slides.length > 0) {
        let currentSlide = 0;
        const slideInterval = 5000; // Change slide every 5 seconds
        let autoSlide;

        // Function to show a specific slide
        function goToSlide(n) {
            // Remove active class from all
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Handle wrapping
            currentSlide = (n + slides.length) % slides.length;

            // Add active class to new current
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        // Next Slide Function
        function nextSlide() {
            goToSlide(currentSlide + 1);
        }

        // Prev Slide Function
        function prevSlide() {
            goToSlide(currentSlide - 1);
        }

        // Event Listeners for Buttons
        if (nextBtn) nextBtn.addEventListener('click', () => {
            nextSlide();
            resetTimer();
        });

        if (prevBtn) prevBtn.addEventListener('click', () => {
            prevSlide();
            resetTimer();
        });

        // Event Listeners for Dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
                resetTimer();
            });
        });

        // Auto Play Timer
        function startTimer() {
            autoSlide = setInterval(nextSlide, slideInterval);
        }

        function resetTimer() {
            clearInterval(autoSlide);
            startTimer();
        }

        // Initialize
        startTimer();
    }
});