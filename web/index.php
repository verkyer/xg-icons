<?php
// 错误处理函数
function errorHandler($errno, $errstr, $errfile, $errline) {
    echo "<p>Error: {$errstr} in {$errfile} on line {$errline}</p>";
    error_log("Error: {$errstr} in {$errfile} on line {$errline}", 0);
}
set_error_handler('errorHandler');

// 配置参数
$baseDir = 'images';
$realBaseDir = realpath($baseDir);

// 检查并获取所有分类
if ($realBaseDir === false || !is_dir($realBaseDir)) {
    trigger_error("Invalid base directory.", E_USER_ERROR);
    die();
}

$categories = @scandir($realBaseDir);
if ($categories === false) {
    trigger_error("Could not read categories directory.", E_USER_ERROR);
    die();
}

// 路径验证函数
function isValidPath($path, $baseDir) {
    $realPath = realpath($path);
    return $realPath !== false && strpos($realPath, realpath($baseDir)) === 0;
}

// 自定义文件名排序函数 (优化后)
function custom_filename_sort($a, $b) {
    // 提取文件名主要部分和数字后缀
    preg_match('/^(.*?)-(\d+)\./', $a, $matches_a);
    $base_a = isset($matches_a[1]) ? $matches_a[1] : pathinfo($a, PATHINFO_FILENAME); // 获取文件名主要部分
    $num_a = isset($matches_a[2]) ? intval($matches_a[2]) : 0;       // 获取数字后缀，没有则为 0

    preg_match('/^(.*?)-(\d+)\./', $b, $matches_b);
    $base_b = isset($matches_b[1]) ? $matches_b[1] : pathinfo($b, PATHINFO_FILENAME); // 获取文件名主要部分
    $num_b = isset($matches_b[2]) ? intval($matches_b[2]) : 0;       // 获取数字后缀，没有则为 0

    // 优先比较文件名主要部分
    $base_cmp = strcmp($base_a, $base_b);
    if ($base_cmp !== 0) {
        return $base_cmp; // 主要部分不同，按字符串顺序排序
    }

    // 如果文件名主要部分相同，则比较数字后缀
    return ($num_a < $num_b) ? -1 : (($num_a > $num_b) ? 1 : 0); // 数字小的排前面
}


// 获取图标数据
function getIconsData($realBaseDir, $baseDir, $categories) {
    $iconData = [];
    foreach ($categories as $category) {
        if ($category !== '.' && $category !== '..' && is_dir("$realBaseDir/$category")) {
            $images = @scandir("$realBaseDir/$category");
            if ($images === false) {
                trigger_error("Could not read images in category: " . htmlspecialchars($category), E_USER_WARNING);
                continue;
            }
            usort($images, 'custom_filename_sort'); // 使用 usort 和自定义排序函数
            $categoryIcons = [];
            foreach ($images as $image) {
                if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $image)) {
                    $imagePath = $baseDir . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $image;
                    if (!isValidPath($imagePath, $baseDir)) {
                        continue;
                    }
                    $iconName = pathinfo($image, PATHINFO_FILENAME);
                    $categoryIcons[] = [
                        'imagePath' => $imagePath,
                        'iconName' => $iconName,
                        'category' => $category
                    ];
                }
            }
            $iconData[$category] = $categoryIcons;
        }
    }
    return $iconData;
}

// 自定义异常类
class IconsException extends Exception {}

try {
    $iconData = getIconsData($realBaseDir, $baseDir, $categories);
    if (empty($iconData)) {
        throw new IconsException("没有找到任何图标");
    }
} catch (IconsException $e) {
    error_log($e->getMessage());
    echo "<div class='error-message'>{$e->getMessage()}</div>";
    die();
}

// 网站名称、logo 和版权信息配置
$siteName = getenv('SITE_NAME') ?: 'xg-icons'; // 网站名称
$logoImg = getenv('LOGO_IMG') ?: 'favicon.ico';  // logo图片
$copyright = getenv('COPYRIGHT') ?: 'Created by <a href="https://github.com/verkyer/xg-icons" target="_blank" rel="noopener noreferrer">@xg-icons</a>.'; // 版权信息

// 引入模板文件
require 'template.php';
?>
