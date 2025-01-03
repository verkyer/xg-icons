<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="<?php echo htmlspecialchars($logoImg); ?>" type="image/x-icon">
</head>
<body>
    <header class="header">
        <div class="logo-title">
            <img src="<?php echo htmlspecialchars($logoImg); ?>" alt="Logo" class="logo">
            <h1><?php echo htmlspecialchars($siteName); ?></h1>
        </div>
        <button id="theme-toggle" class="theme-toggle-button">🌙</button>
    </header>

    <div class="search-container">
        <select class="group-select" onchange="filterIcons()">
            <option value="all">全部</option>
            <?php
            foreach ($categories as $category) {
                if ($category !== '.' && $category !== '..' && is_dir("$realBaseDir/$category")) {
                    echo "<option value='" . htmlspecialchars($category) . "'>" . htmlspecialchars($category) . "</option>";
                }
            }
            ?>
        </select>
        <input type="text" class="search-input" placeholder="输入图标名称..." oninput="filterIcons()">
    </div>

    <div id="gallery" class="gallery">
        <?php
        foreach ($iconData as $category => $icons) {
            if (empty($icons)) continue;
            echo "<div class='icon-group' data-group='" . htmlspecialchars($category) . "'>";
            echo "<h2 class='group-title'>" . htmlspecialchars($category) . "</h2>";
            echo "<div class='icons'>";
            foreach ($icons as $icon) {
                $imagePath = $icon['imagePath'];
                $iconName = $icon['iconName'];
                $category = $icon['category'];
                echo "<div class='icon' data-group='" . htmlspecialchars($category) . "' onclick=\"copyToClipboard('$imagePath', this)\">";
                echo "<img src='$imagePath' alt='" . htmlspecialchars($iconName) . "' loading='lazy' width='64' height='64' decoding='async' onerror=\"this.onerror=null; this.src='placeholder.png';\">";
                echo "<div class='icon-name'>" . htmlspecialchars($iconName) . "</div>";
                echo "</div>";
            }
            echo "</div></div>";
        }
        ?>
    </div>

    <footer class="footer">
        <?php echo $copyright; ?>
    </footer>

    <script src="script.js"></script>
</body>
</html>
