document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.icon img');

    // 确保图片加载逻辑更健壮
    images.forEach(img => {
        if (img.complete && img.naturalWidth > 0) {
            // 图片已加载
            img.classList.add('loaded');
        } else {
            // 为未加载的图片绑定事件
            img.addEventListener('load', () => {
                console.log(`图片加载成功：${img.src}`);
                img.classList.add('loaded');
            });

            img.addEventListener('error', () => {
                console.error(`图片加载失败，切换到占位图：${img.src}`);
                img.src = 'placeholder.png'; // 加载失败时切换到占位图
                img.classList.add('error');
            });
        }
    });

    // 深色模式和浅色模式切换逻辑
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme) {
        body.classList.add(savedTheme);
        themeToggle.innerHTML = savedTheme === 'dark-mode' ? '🌞' : '🌙';
    } else {
        body.classList.add('light-mode');
        themeToggle.innerHTML = '🌙';
    }

    themeToggle.style.fontSize = '18px'; // 设置主题切换按钮中的 emoji 字号

    themeToggle.addEventListener('click', () => {
        const isDarkMode = body.classList.contains('dark-mode');
        body.classList.toggle('dark-mode', !isDarkMode);
        body.classList.toggle('light-mode', isDarkMode);
        themeToggle.innerHTML = isDarkMode ? '🌙' : '🌞';
        localStorage.setItem('theme', isDarkMode ? 'light-mode' : 'dark-mode');
    });

    // 回到顶部按钮
    const backToTopButton = document.createElement('button');
    backToTopButton.classList.add('back-to-top-button');
    backToTopButton.textContent = '👆';
    backToTopButton.style.fontSize = '18px'; // 设置回到顶部按钮中的 emoji 字号
    document.body.appendChild(backToTopButton);

    window.addEventListener('scroll', () => {
        backToTopButton.style.display = window.scrollY > 200 ? 'flex' : 'none';
    });

    backToTopButton.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // 图标过滤逻辑
    function filterIcons() {
        const searchInput = document.querySelector('.search-input').value.toLowerCase();
        const groupSelect = document.querySelector('.group-select').value;
        const groups = document.querySelectorAll('.icon-group');

        groups.forEach(group => {
            const icons = group.querySelectorAll('.icon');
            let groupHasVisibleIcons = false;

            icons.forEach(icon => {
                const iconName = icon.querySelector('.icon-name').textContent.toLowerCase();
                const iconGroup = icon.getAttribute('data-group');
                const matchesSearch = iconName.includes(searchInput);
                const matchesGroup = groupSelect === 'all' || iconGroup === groupSelect;

                if (matchesSearch && matchesGroup) {
                    icon.style.display = 'flex';
                    groupHasVisibleIcons = true;

                    // 强制重新加载未显示的图片
                    const img = icon.querySelector('img');
                    if (!img.complete || img.naturalWidth === 0) {
                        console.warn(`重新加载图片：${img.src}`);
                        img.src = img.src; // 重新加载图片
                    }
                } else {
                    icon.style.display = 'none';
                }
            });

            const groupTitle = group.querySelector('.group-title');
            groupTitle.style.display = groupHasVisibleIcons ? 'block' : 'none';
        });
    }

    // 防抖函数
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    const debouncedFilter = debounce(filterIcons, 300);
    document.querySelector('.search-input').addEventListener('input', debouncedFilter);
    document.querySelector('.group-select').addEventListener('change', debouncedFilter);

    // 点击图标复制链接到剪贴板
    function copyToClipboard(imagePath, element) {
        const fullLink = new URL(imagePath, window.location.origin).href;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(fullLink).then(() => {
                showTooltip(element, '已复制!');
            }).catch(err => {
                console.error('复制失败:', err);
                showTooltip(element, '复制失败');
            });
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = fullLink;
            textArea.style.position = 'fixed';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                showTooltip(element, '已复制!');
            } catch (err) {
                console.error('复制失败:', err);
                showTooltip(element, '复制失败');
            } finally {
                document.body.removeChild(textArea);
            }
        }
    }

    function showTooltip(element, message) {
        let tooltip = element.querySelector('.tooltip');
        if (!tooltip) {
            tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            element.appendChild(tooltip);
        }
        tooltip.textContent = message;
        tooltip.style.opacity = '1';
        setTimeout(() => {
            tooltip.style.opacity = '0';
        }, 800);
    }

    // 为每个图标绑定点击事件
    const icons = document.querySelectorAll('.icon');
    icons.forEach(icon => {
        const imagePath = icon.querySelector('img').src;
        icon.addEventListener('click', () => copyToClipboard(imagePath, icon));
    });
});
