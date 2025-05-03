<?php
require_once 'vendor/autoload.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// ================================
// ✅ CONFIGURATION
// ================================

$mode = 'all'; // Options: 'single', 'all', 'custom'
$buildDir = 'build/';
$minDir = $buildDir . 'min/';
$encDir = $buildDir . 'enc/';
$assetsDir = 'assets/';
$confirmReset = !in_array('--force', $argv); // ✅ Use --force to skip prompt

// ================================
// 📁 CHECK & RESET BUILD FOLDER
// ================================

function clearFolder($path) {
    if (!is_dir($path)) return;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
}

foreach ([$buildDir, $minDir, $encDir] as $dir) {
    if (!is_dir($dir)) {
        echo "📁 Folder '$dir' not found. Creating...\n";
        mkdir($dir, 0777, true);
    } else {
        if ($confirmReset) {
            echo "⚠️  '$dir' exists. Clear it? (y/n): ";
            $input = strtolower(trim(fgets(STDIN)));
            if ($input === 'y') {
                echo "🧹 Clearing $dir...\n";
                clearFolder($dir);
            } else {
                echo "✅ Keeping existing $dir\n";
            }
        } else {
            echo "🧹 --force used. Auto clearing $dir\n";
            clearFolder($dir);
        }
    }
}

// ================================
// 🔧 Twig Setup
// ================================

$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);

// ================================
// 📦 Load All JSON Data
// ================================

$data = [];
foreach (glob('data/*.json') as $file) {
    $key = basename($file, '.json');
    $data[$key] = json_decode(file_get_contents($file), true);
}

// ================================
// 🧽 Minify & 🔐 Encrypt Functions
// ================================

function minify_html($html) {
    return preg_replace([
        '/\>[^\S ]+/s',
        '/[^\S ]+\</s',
        '/(\s)+/s',
        '/<!--(.|\s)*?-->/'
    ], ['>', '<', '\1', ''], $html);
}

function encrypt_html($html) {
    $encoded = base64_encode($html);
    return '<script>document.write(atob("' . $encoded . '"));</script>';
}

// ================================
// 🛠️ RENDER MODES
// ================================

function ensureDir($path) {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

function save_outputs($slug, $html) {
    global $buildDir, $minDir, $encDir;

    $minified = minify_html($html);
    $encrypted = encrypt_html($minified);

    $buildFile = "{$buildDir}{$slug}.html";
    $minFile   = "{$minDir}{$slug}.html";
    $encFile   = "{$encDir}{$slug}.html";

    // Ensure directories exist
    ensureDir($buildFile);
    ensureDir($minFile);
    ensureDir($encFile);

    file_put_contents($buildFile, $html);
    file_put_contents($minFile, $minified);
    file_put_contents($encFile, $encrypted);

    echo "✔ Rendered: $slug → build/, min/, enc/\n";
}


if ($mode === 'single') {
    $html = $twig->render('page.twig', $data);
    save_outputs('index', $html);
}

if ($mode === 'all') {
    $base = 'templates/';
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));

    foreach ($files as $file) {
        if ($file->getExtension() !== 'twig') continue;

        $path = $file->getPathname();
        $templatePath = substr($path, strlen($base));
        $html = $twig->render($templatePath, $data);
        $slug = str_replace('.twig', '', $templatePath);
        save_outputs($slug, $html);
    }
}

if ($mode === 'custom') {
    $pages = [
        'home' => 'home.twig',
        'about' => 'pages/about.twig',
        'pricing' => 'pages/pricing.twig',
    ];

    foreach ($pages as $slug => $template) {
        $html = $twig->render($template, $data);
        save_outputs($slug, $html);
    }
}

// ================================
// 🗂️ Copy Assets
// ================================

function copyAssets($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $srcPath = "$src/$file";
            $dstPath = "$dst/$file";
            if (is_dir($srcPath)) {
                copyAssets($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
    }
}

foreach ([$buildDir, $minDir, $encDir] as $target) {
    copyAssets($assetsDir, $target . 'assets');
}
echo "📦 Assets copied to build/, min/, and enc/ folders\n";
