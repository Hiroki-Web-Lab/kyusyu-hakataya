// DOM要素の取得
const menuToggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('#nav');
const navLinks = document.querySelectorAll('#nav a');
const storeNames = document.querySelectorAll('.store-name');

// モバイルメニューの開閉
function toggleMobileMenu() {
    nav.classList.toggle('open');
    menuToggle.classList.toggle('active');
}

// メニューリンククリック時の処理
function handleNavClick(e) {
    e.preventDefault();
    const targetId = e.target.getAttribute('href');
    const targetElement = document.querySelector(targetId);
    
    if (targetElement) {
        // スムーススクロール
        targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
    
    // モバイルメニューを閉じる
    if (nav.classList.contains('open')) {
        nav.classList.remove('open');
        menuToggle.classList.remove('active');
    }
}

// 店舗情報のアコーディオン機能
function toggleStoreDetails(e) {
    const storeItem = e.target.closest('.store-item');
    const storeDetails = storeItem.querySelector('.store-details');
    
    // 他の店舗情報を閉じる
    document.querySelectorAll('.store-details').forEach(details => {
        if (details !== storeDetails) {
            details.style.display = 'none';
        }
    });
    
    // 現在の店舗情報を開閉
    if (storeDetails.style.display === 'none' || storeDetails.style.display === '') {
        storeDetails.style.display = 'block';
    } else {
        storeDetails.style.display = 'none';
    }
}

// スムーススクロール用の関数
function smoothScrollTo(targetId) {
    const targetElement = document.querySelector(targetId);
    if (targetElement) {
        targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// スクロール時のヘッダー背景変更
function handleScroll() {
    const header = document.querySelector('#header');
    if (window.scrollY > 100) {
        header.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        header.style.backdropFilter = 'blur(10px)';
    } else {
        header.style.backgroundColor = '#fff';
        header.style.backdropFilter = 'none';
    }
}

// アニメーション用のIntersection Observer
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // アニメーション対象要素を監視
    const animatedElements = document.querySelectorAll('.card, .store-item, .company-info, .management-policy');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// ページ読み込み完了時の初期化
document.addEventListener('DOMContentLoaded', function() {
    // イベントリスナーの設定
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleMobileMenu);
    }
    
    // ナビゲーションリンクのクリックイベント
    navLinks.forEach(link => {
        link.addEventListener('click', handleNavClick);
    });
    
    // 店舗名のクリックイベント（アコーディオン）
    storeNames.forEach(storeName => {
        storeName.addEventListener('click', toggleStoreDetails);
    });
    
    // スクロールイベント
    window.addEventListener('scroll', handleScroll);
    
    // アニメーション設定
    setupScrollAnimations();
    
    // 初期状態で店舗詳細を非表示
    document.querySelectorAll('.store-details').forEach(details => {
        details.style.display = 'none';
    });
    
    // ヒーローボタンのクリックイベント
    const heroButton = document.querySelector('.primary-button');
    if (heroButton) {
        heroButton.addEventListener('click', function(e) {
            e.preventDefault();
            smoothScrollTo('#stores');
        });
    }

    // Contact: quick links set select value
    document.querySelectorAll('.contact-quick-links a[data-type]').forEach(link => {
        link.addEventListener('click', () => {
            const type = link.getAttribute('data-type');
            const select = document.getElementById('type');
            if (select) select.value = type;
        });
    });
});

// ウィンドウリサイズ時の処理
window.addEventListener('resize', function() {
    // デスクトップサイズになったらモバイルメニューを閉じる
    if (window.innerWidth > 600) {
        nav.classList.remove('open');
        menuToggle.classList.remove('active');
    }
});

// ページの読み込み完了時に実行される関数
window.addEventListener('load', function() {
    // ローディングアニメーション（必要に応じて）
    document.body.style.opacity = '1';
});

// エラーハンドリング
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
});

// パフォーマンス最適化：スクロールイベントのthrottle
function throttle(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// スクロールイベントをthrottle化
const throttledScrollHandler = throttle(handleScroll, 10);
window.removeEventListener('scroll', handleScroll);
window.addEventListener('scroll', throttledScrollHandler);
