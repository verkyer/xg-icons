<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XiaoGe icons</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <img src="favicon.ico" alt="Logo" class="logo">
        <h1>XiaoGe icons</h1>
        <button id="theme-toggle" class="theme-toggle-button">🌙</button>
    </div>
    <div class="search-container">
        <select class="group-select" onchange="filterIcons()">
            <option value="all">全部</option>
            <?php
            $baseDir = 'images';
            // 使用绝对路径，避免目录遍历漏洞
            $realBaseDir = realpath($baseDir);

            if ($realBaseDir === false) {
                die("Error: Invalid base directory."); // 错误处理
            }
            
            $categories = @scandir($realBaseDir); // 添加@抑制错误

            if ($categories === false) {
                die("Error: Could not read categories directory."); // 错误处理
            }

            foreach ($categories as $category) {
                if ($category !== '.' && $category !== '..' && is_dir("$realBaseDir/$category")) {
                     // 使用 htmlspecialchars 确保安全
                    echo "<option value='" . htmlspecialchars($category) . "'>" . htmlspecialchars($category) . "</option>";
                }
            }
            ?>
        </select>
        <input type="text" class="search-input" placeholder="输入图标名称..." oninput="filterIcons()">
    </div>
    <div id="gallery">
        <?php
        foreach ($categories as $category) {
             if ($category !== '.' && $category !== '..' && is_dir("$realBaseDir/$category")) {
                echo "<div class='icon-group' data-group='" . htmlspecialchars($category) . "'>";
                echo "<h2 class='group-title'>" . htmlspecialchars($category) . "</h2>";
                echo "<div class='icons'>";

                $images = @scandir("$realBaseDir/$category"); // 添加@抑制错误
                 if ($images === false) {
                      echo "<p>Error: Could not read images in category: " . htmlspecialchars($category) . "</p>"; // 错误处理
                      continue;
                 }
                
                foreach ($images as $image) {
                    if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $image)) {
                        $imagePath = "$baseDir/$category/$image";
                        $iconName = pathinfo($image, PATHINFO_FILENAME);
                        echo "<div class='icon' data-group='" . htmlspecialchars($category) . "' onclick=\"copyToClipboard('$imagePath', this)\">";
                       
                        // 添加 loading="lazy" 实现图片懒加载
                        echo "<img src='$imagePath' alt='" . htmlspecialchars($image) . "' loading='lazy'>";
                       
                        echo "<div class='icon-name'>" . htmlspecialchars($iconName) . "</div>";
                        echo "</div>";
                    }
                }

                echo "</div></div>";
            }
        }
        ?>
    </div>
    <div class="footer">
        Created by <a href="https://github.com/verkyer/xg-icons" target="_blank" rel="noopener noreferrer">@xg-icons</a>.
    </div>
    <script src="script.js"></script>
</body>
</html>
