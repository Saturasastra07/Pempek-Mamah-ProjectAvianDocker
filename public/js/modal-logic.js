let currentPrice = 0;
let currentQty = 1;

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

function openModalMakan(data) {
    extraSidePrice = 0;
    selectedExtras = {};
    renderSummary();

    console.log("Membuka modal untuk kategori:", data.category_id);
    currentPrice = parseInt(data.price);
    currentQty = 1;
    localStorage.setItem('activeModalData', JSON.stringify(data));

    document.getElementById('modalQty').innerText = currentQty;
    document.getElementById('modalName').innerText = data.name;
    document.getElementById('modalPrice').innerText = formatRupiah(currentPrice);
    document.getElementById('modalTotalPrice').innerText = formatRupiah(currentPrice * currentQty);
    document.getElementById('modalImage').src = data.image;
    document.getElementById('modalDesc').innerText = data.desc;
    document.getElementById('modalCalories').innerText = data.calories || 0;
    document.getElementById('modalProtein').innerText = data.protein || '-';

    window.dispatchEvent(new CustomEvent('set-wishlist', {
        detail: {
            id: data.id,
            status: data.isWishlisted
        }
    }));

    const extraDiv = document.getElementById('extraOption');
    const tempDiv = document.getElementById('temperatureOption');
    const toppingItems = document.querySelectorAll('.topping-item');

    if(extraDiv) extraDiv.classList.add('hidden');
    if(tempDiv) tempDiv.classList.add('hidden');
    toppingItems.forEach(item => {
        item.classList.add('hidden');
        const cb = item.querySelector('input[type="checkbox"]');
        if(cb) cb.checked = false;
    });

    const sideItems = document.querySelectorAll('.side-item');
    sideItems.forEach(item => item.classList.add('hidden'));

    let foodShown = 0;
    const maxRandomFood = 3;
    sideItems.forEach(item => {
        const itemId = item.getAttribute('data-id');
        const isDrink = item.getAttribute('data-is-drink') === 'true';

        if (itemId == data.id) return;
        if (!isDrink) {
            item.classList.remove('hidden');
        } else if ( foodShown < maxRandomFood) {
            item.classList.remove('hidden');
            foodShown++;
        }
        if (!item.classList.contains('hidden')) {
            item.parentElement.appendChild(item);
        }
    });

    sideItems.forEach(item => {
        const itemId = item.getAttribute('data-id');
        const isDrink = item.getAttribute('data-is-drink') === 'true';
        if (isDrink && itemId != data.id) {
            item.classList.remove('hidden');
            item.parentElement.appendChild(item);
        }
    });

    const catId = parseInt(data.category_id);

    // 2. LOGIKA MINUMAN (ID 8: Dingin, ID 9: Hangat)
    if (catId === 8 || catId === 9) {
        if(tempDiv) {
            tempDiv.classList.remove('hidden');
            document.getElementById('iceOption').classList.toggle('hidden', catId !== 8);
            document.getElementById('hotOption').classList.toggle('hidden', catId !== 9);
            
            const radioId = (catId === 8) ? 'iceOption' : 'hotOption';
            const radio = document.getElementById(radioId).querySelector('input');
            if(radio) radio.checked = true;
        }
    } 
    // 3. LOGIKA TOPPING
    else if ((catId >= 1 && catId <= 7) || catId === 4) {
        if(extraDiv) {
            const isTekwan = data.name.toLowerCase().includes('tekwan');
            const targetTop = isTekwan ? 11 : 10; 
            
            let hasTopping = false;
            toppingItems.forEach(item => {
                const itemCat = parseInt(item.getAttribute('data-category'));
                const itemName = item.querySelector('p').innerText.toLowerCase();

                if (isTekwan) {
                    if ((itemCat === 11 || itemName.includes('tahu')) && !itemName.includes('kulit')) {
                        item.classList.remove('hidden');
                        hasTopping = true;
                    }
                } else {
                    if (itemCat === 10) {
                        item.classList.remove('hidden');
                        hasTopping = true;
                    }
                }
            });

            if (hasTopping) extraDiv.classList.remove('hidden');
        }
    }

    updateUI();
    loadReviews(data.id);

    const modal = document.getElementById('modalPremium');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    if (window.lucide) lucide.createIcons();
}

function updateQty(change) {
    currentQty += change;
    if (currentQty < 1) {
        currentQty = 1;
    }
    document.getElementById('modalQty').innerText = currentQty;
    document.getElementById('modalTotalPrice').innerText = formatRupiah(currentPrice * currentQty);
}

let selectedExtras = {};
let extraSidePrice = 0;

function addSideToTotal(price, name) {
    extraSidePrice += parseInt(price);
    if (selectedExtras[name]) {
        selectedExtras[name].qty += 1;
    } else {
        selectedExtras[name] = {
            price: parseInt(price),
            qty: 1
        };
    }
    renderSummary();
    updateUI();
    showToast(`Berhasil menambah ${name}`);
}

function renderSummary() {
    const summaryContainer = document.getElementById('orderSummary');
    const listContainer = document.getElementById('summaryList');
    const keys = Object.keys(selectedExtras);
    
    if (keys.length > 0) {
        summaryContainer.classList.remove('hidden');
        listContainer.innerHTML = keys.map((name) => {
            const item = selectedExtras[name];
            const qtyDisplay = item.qty > 1 ? ` <span class="text-[#e91e63] font-black mr-1.5">(${item.qty})</span>` : '';
            
            return `
                <div class="flex items-center gap-1.5 bg-white border border-pink-100 px-3 py-1 rounded-full shadow-sm animate-fade-in">
                    <span class="text-[10px] font-bold text-gray-700">${name}${qtyDisplay}</span>
                    <button onclick="removeExtra('${name}')" class="text-pink-500 hover:text-pink-700">
                        <i data-lucide="x-circle" class="w-3 h-3"></i>
                    </button>
                </div>
            `;
        }).join('');
        
        if (window.lucide) lucide.createIcons();
    } else {
        summaryContainer.classList.add('hidden');
    }
}

function removeExtra(name) {
    const item = selectedExtras[name];
    if (item) {
        extraSidePrice -= item.price;
        
        if (item.qty > 1) {
            item.qty -= 1;
        } else {
            delete selectedExtras[name];
        }
    }
    
    renderSummary();
    updateUI();
}

function updateUI() {
    let extraPrice = 0;
    const selectedToppings = document.querySelectorAll('.topping-item input[type="checkbox"]:checked');
    
    selectedToppings.forEach(cb => {
        extraPrice += parseInt(cb.value) || 0;
    });

    let total = (currentPrice + extraPrice +  extraSidePrice) * currentQty;
    
    document.getElementById('modalQty').innerText = currentQty;
    document.getElementById('modalPrice').innerText = formatRupiah(currentPrice);
    document.getElementById('modalTotalPrice').innerText = formatRupiah(total);
}

function addToCart() {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
        const activeData = JSON.parse(localStorage.getItem('activeModalData'));
        if (!activeData) return;

        const productId = activeData.id;
        const qty = currentQty;

        const selectedToppings = [];
        document.querySelectorAll('.topping-item input[type="checkbox"]:checked').forEach(cb => {
            const parent = cb.closest('.topping-item');
            const toppingNameElement = parent.querySelector('h4') || parent.querySelector('p');
            const toppingName = toppingNameElement ? toppingNameElement.innerText.trim() : 'Topping';
            const toppingPrice = cb.value;

            selectedToppings.push({
                name: toppingName,
                price: parseInt(toppingPrice)
            });
        });

    const selectedSides = Object.keys(selectedExtras).map(name => {
        return {
            name: name,
            qty: selectedExtras[name].qty,
            price: selectedExtras[name].price
        };
    });

    const requestData = {
        product_id: productId,
        quantity: qty,
        toppings: selectedToppings,
        extra_sides: selectedSides,
    };

    const btnCart = event ? event.currentTarget : document.querySelector('#modalPremium button[onclick^="addToCart"]');
    if (!btnCart) return;
    const originalText = btnCart.innerHTML;
    btnCart.innerHTML = `<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Menyimpan...`;
    btnCart.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },

        body: JSON.stringify(requestData) 
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Update badge global
            document.querySelectorAll('.global-cart-badge').forEach(badge => {
                badge.innerText = result.cart_count;
                badge.style.display = 'block'; 
            });

            // closeModal(); 
            
            // Opsional: ganti alert dengan toast kalau punya
            console.log("Berhasil masuk keranjang!");
        } else {
            alert(result.message || "Gagal menambah ke keranjang");
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
    })
    .finally(() => {
        btnCart.innerHTML = originalText;
        btnCart.disabled = false;
        if (window.lucide) lucide.createIcons();
    });
}

function updateCartBadgeRealtime(newCount) {
    const badges = document.querySelectorAll('.global-cart-badge');
    
    badges.forEach(badge => {
        if (newCount > 0) {
            badge.innerText = newCount;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    });
}

function loadReviews(productId) {
    console.log("1. Memulai fetch untuk ID:", productId);
    const container = document.getElementById('reviewContainer');
    
    if (!container) {
        console.error("ERROR: ID 'reviewContainer' tidak ditemukan di HTML!");
        return;
    }

    container.innerHTML = '<p class="text-[10px] text-gray-400 p-4 italic text-center w-full">Memuat ulasan...</p>';

    fetch(`/get-reviews/${productId}`, { cache: "no-store" })
        .then(response => {
            console.log("2. Response status:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("3. Data diterima:", data);
            container.innerHTML = ''; 

            if (!data || data.length === 0) {
                console.log("4. Data kosong.");
                container.innerHTML = '<p class="text-[10px] text-gray-400 p-4 italic text-center w-full">Belum ada ulasan.</p>';
                return;
            }

            let htmlContent = '';
            data.forEach((review, index) => {
                let starsHtml = '';
                const fullStars = Math.floor(review.rating);
                const hasHalfStar = review.rating % 1 >= 0.5;

                for (let i = 1; i <= 5; i++) {
                    if (i <= fullStars) {
                        starsHtml += `<i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>`;
                    } else if (i === fullStars + 1 && hasHalfStar) {
                        starsHtml += `<i data-lucide="star-half" class="w-3 h-3 fill-amber-400 text-amber-400"></i>`;
                    } else {
                        starsHtml += `<i data-lucide="star" class="w-3 h-3 text-gray-200"></i>`;
                    }
                }

                htmlContent += `
                    <div class="min-w-[85%] bg-white/60 backdrop-blur-md border border-white/60 p-4 rounded-3xl shadow-lg mb-2 relative overflow-hidden">
                        <div class="flex items-center gap-3 mb-3">
                            <img src="/img/users/${review.user_photo}" class="w-10 h-10 rounded-full object-cover border-2 border-white/50" onerror="this.src='https://ui-avatars.com/api/?name=${review.user_name}'">
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <p class="text-[13px] font-bold text-gray-900">${review.user_name}</p>
                                    <span class="text-[9px] font-medium text-gray-400 mt-0.5">${review.created_at}</span>
                                </div>
                                <div class="flex items-center gap-0.5 mt-0.5">
                                    ${starsHtml}
                                </div>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-600 leading-relaxed italic line-clamp-3">
                            "${review.comment}"
                        </p>
                    </div>
                `;
            });
            
            container.innerHTML = htmlContent;
            setTimeout(() => {
                if (window.lucide) lucide.createIcons();
                startAutoScroll();
            }, 50);
        })
        .catch(err => {
            console.error('6. FETCH ERROR:', err);
            container.innerHTML = '<p class="text-[10px] text-red-400 p-4 italic text-center w-full">Yah, gagal memuat ulasan. Coba lagi yuk</p>';
        });
}

function startAutoScroll() {
    const container = document.getElementById('reviewContainer');
    let animationFrame;
    let isUserInteracting = false;
    let speed = 0.4;

    function step() {
        if (isUserInteracting) return;

        container.scrollLeft += speed;
        if (container.scrollLeft + container.offsetWidth >= container.scrollWidth - 1) {
            container.scrollTo({ left: 0, behavior: 'smooth' });
            setTimeout(() => {
                animationFrame = requestAnimationFrame(step);
            }, 3000);
            return;
        }

        animationFrame = requestAnimationFrame(step);
    }

    function start() {
        cancelAnimationFrame(animationFrame);
        animationFrame = requestAnimationFrame(step);
    }

    function stop() {
        isUserInteracting = true;
        cancelAnimationFrame(animationFrame);
    }

    const resume = () => {
        isUserInteracting = false;
        setTimeout(start, 2000);
    };

    container.addEventListener('mousedown', stop);
    container.addEventListener('touchstart', stop);
    container.addEventListener('mouseup', resume);
    container.addEventListener('touchend', resume);

    start();
}

function closeModal() {
    const modal = document.getElementById('modalPremium');
    const scrollContainer = modal.querySelector('.overflow-y-auto');
    if (scrollContainer) {
        localStorage.setItem('modalScrollPos', scrollContainer.scrollTop);
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
    localStorage.removeItem('activeModalData');
}

window.addEventListener('DOMContentLoaded', () => {
    const savedModal = localStorage.getItem('activeModalData');
    const savedScroll = localStorage.getItem('modalScrollPos');
    
    if (savedModal) {
        const data = JSON.parse(savedModal);
        openModalMakan(data);
        setTimeout(() => {
            const scrollContainer = document.querySelector('#modalPremium .overflow-y-auto');
            if (scrollContainer && savedScroll) {
                scrollContainer.scrollTo({
                    top: parseInt(savedScroll),
                    behavior: 'instant'
                });
            }
        }, 300); 
    }
});
