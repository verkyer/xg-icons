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
