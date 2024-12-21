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

if ($realBaseDir === false) {
    trigger_error("Invalid base directory.", E_USER_ERROR);
    die();
}

// 获取所有分类
$categories = @scandir($realBaseDir);
if ($categories === false) {
    trigger_error("Could not read categories directory.", E_USER_ERROR);
    die();
}

// 获取所有的图标数据
function getIconsData($realBaseDir, $baseDir, $categories) {
    $iconData = [];
     foreach ($categories as $category) {
        if ($category !== '.' && $category !== '..' && is_dir("$realBaseDir/$category")) {
             $images = @scandir("$realBaseDir/$category");
            if ($images === false) {
                trigger_error("Could not read images in category: " . htmlspecialchars($category),E_USER_WARNING);
                 continue;
            }
            $categoryIcons = [];
            foreach ($images as $image) {
                 if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $image)) {
                    $imagePath = $baseDir . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $image;
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

$iconData = getIconsData($realBaseDir, $baseDir, $categories);

// 网站名称、logo 和版权信息配置
$siteName = getenv('SITE_NAME') ?: 'XiaoGe icons'; // 网站名称
$logoImg = getenv('LOGO_IMG') ?: 'favicon.ico';  // logo图片
$copyright = getenv('COPYRIGHT') ?: 'Created by <a href="https://github.com/verkyer/xg-icons" target="_blank" rel="noopener noreferrer">@xg-icons</a>.'; // 版权信息

// 引入模板文件，并传递数据
require 'template.php';
