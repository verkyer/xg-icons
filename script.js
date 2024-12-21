document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const backToTopButton = document.createElement('button');
    backToTopButton.classList.add('back-to-top-button');
    backToTopButton.textContent = '👆';
    document.body.appendChild(backToTopButton);

    const body = document.body;

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        body.classList.add(savedTheme);
        themeToggle.textContent = savedTheme === 'dark-mode' ? '🌞' : '🌙';
    } else {
        body.classList.add('light-mode');
        themeToggle.textContent = '🌙';
    }

    themeToggle.addEventListener('click', function() {
        if (body.classList.contains('dark-mode')) {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            themeToggle.textContent = '🌙';
            localStorage.setItem('theme', 'light-mode');
        } else {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            themeToggle.textContent = '🌞';
            localStorage.setItem('theme', 'dark-mode');
        }
    });

    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            backToTopButton.style.display = 'flex';
        } else {
            backToTopButton.style.display = 'none';
        }
    });

    backToTopButton.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});


function copyToClipboard(imagePath, element) {
    // 使用绝对路径
   const fullLink = new URL(imagePath, window.location.origin).href;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(fullLink).then(() => {
            showTooltip(element, '已复制!');
        }, (err) => {
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
        }

        document.body.removeChild(textArea);
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
    }, 2000);
}

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
          } else {
            icon.style.display = 'none';
          }
      });

       const groupTitle = group.querySelector('.group-title');
       groupTitle.style.display = groupHasVisibleIcons ? 'block' : 'none';

    });
}
