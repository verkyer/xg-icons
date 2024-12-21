<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header">
    <img src="<?php echo htmlspecialchars($logoImg); ?>" alt="Logo" class="logo">  <!-- 将 logoPath 改为 logoImg -->
    <h1><?php echo htmlspecialchars($siteName); ?></h1>
    <button id="theme-toggle" class="theme-toggle-button">🌙</button>
</div>
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
<div id="gallery">
    <?php
     foreach ($iconData as $category => $icons) {
          if(empty($icons)) continue;
        echo "<div class='icon-group' data-group='" . htmlspecialchars($category) . "'>";
        echo "<h2 class='group-title'>" . htmlspecialchars($category) . "</h2>";
        echo "<div class='icons'>";
           foreach ($icons as $icon) {
                $imagePath = $icon['imagePath'];
                $iconName = $icon['iconName'];
                $category = $icon['category'];
                  echo "<div class='icon' data-group='" . htmlspecialchars($category) . "' onclick=\"copyToClipboard('$imagePath', this)\">";
                  echo "<img src='$imagePath' alt='" . htmlspecialchars($iconName) . "' loading='lazy'>";
                  echo "<div class='icon-name'>" . htmlspecialchars($iconName) . "</div>";
                  echo "</div>";
            }
           echo "</div></div>";
     }
    ?>
</div>
<div class="footer">
    <?php echo $copyright; ?>
</div>
<script src="script.js"></script>
</body>
</html>
