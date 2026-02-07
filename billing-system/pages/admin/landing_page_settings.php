<?php
/**
 * ランディングページ設定
 * 管理者がランディングページのコンテンツ・画像を編集
 */

session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/AuthManager.php';

// 認証チェック
$authManager = new AuthManager();
if (!$authManager->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// アップロードディレクトリ
$uploadBase = __DIR__ . '/../../../order/uploads';
$uploadDirs = ['hero', 'logo', 'gallery', 'partners'];
foreach ($uploadDirs as $dir) {
    $path = "$uploadBase/$dir";
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

// テーブル作成
try {
    $db->exec("CREATE TABLE IF NOT EXISTS landing_page_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_label VARCHAR(200),
        setting_type VARCHAR(50) DEFAULT 'text',
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS landing_page_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_type VARCHAR(50) NOT NULL,
        image_path VARCHAR(500) NOT NULL,
        image_title VARCHAR(200),
        image_alt VARCHAR(200),
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS landing_page_testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(200),
        person_name VARCHAR(100),
        person_role VARCHAR(100),
        testimonial_text TEXT,
        photo_path VARCHAR(500),
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS landing_page_faq (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question VARCHAR(500) NOT NULL,
        answer TEXT NOT NULL,
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS landing_page_menus (
        id INT AUTO_INCREMENT PRIMARY KEY,
        menu_name VARCHAR(200) NOT NULL,
        menu_description TEXT,
        menu_price INT DEFAULT 0,
        menu_image VARCHAR(500),
        menu_tag VARCHAR(100),
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // デフォルト設定
    $defaults = [
        ['hero_title', 'おいしいお弁当を<br>あなたの職場へ', 'ヒーロータイトル', 'textarea', 1],
        ['hero_subtitle', '毎日の昼食をもっと楽しく、もっと手軽に', 'ヒーローサブタイトル', 'text', 2],
        ['hero_cta_text', '無料で始める', 'CTAボタンテキスト', 'text', 3],
        ['hero_cta_sub', '初月無料キャンペーン中', 'CTAサブテキスト', 'text', 4],
        // 実績数字
        ['stat1_value', '150+', '実績1 数値', 'text', 5],
        ['stat1_label', '導入企業数', '実績1 ラベル', 'text', 6],
        ['stat2_value', '50,000+', '実績2 数値', 'text', 7],
        ['stat2_label', '月間配食数', '実績2 ラベル', 'text', 8],
        ['stat3_value', '98%', '実績3 数値', 'text', 9],
        ['stat3_label', '満足度', '実績3 ラベル', 'text', 10],
        // 特徴
        ['feature1_title', 'かんたん注文', '特徴1 タイトル', 'text', 11],
        ['feature1_desc', 'スマホから3タップで注文完了。忙しい朝でもサクッと注文できます。', '特徴1 説明', 'textarea', 12],
        ['feature1_icon', 'touch_app', '特徴1 アイコン', 'icon', 13],
        ['feature2_title', '栄養バランス', '特徴2 タイトル', 'text', 14],
        ['feature2_desc', '管理栄養士監修のメニューで、健康的な食生活をサポートします。', '特徴2 説明', 'textarea', 15],
        ['feature2_icon', 'favorite', '特徴2 アイコン', 'icon', 16],
        ['feature3_title', '職場へお届け', '特徴3 タイトル', 'text', 17],
        ['feature3_desc', 'お昼時にオフィスまでお届け。外出不要でランチタイムを有効活用。', '特徴3 説明', 'textarea', 18],
        ['feature3_icon', 'local_shipping', '特徴3 アイコン', 'icon', 19],
        // ご利用の流れ
        ['step1_title', 'アカウント登録', 'ステップ1 タイトル', 'text', 20],
        ['step1_desc', '企業コードとメールアドレスで簡単登録。1分で完了します。', 'ステップ1 説明', 'textarea', 21],
        ['step2_title', 'メニューを選ぶ', 'ステップ2 タイトル', 'text', 22],
        ['step2_desc', '日替わり・週替わりメニューから好きなお弁当を選択。前日までに注文できます。', 'ステップ2 説明', 'textarea', 23],
        ['step3_title', '職場でお受け取り', 'ステップ3 タイトル', 'text', 24],
        ['step3_desc', 'お昼時にオフィスまでお届け。あとは美味しくいただくだけ。', 'ステップ3 説明', 'textarea', 25],
        // メニューセクション
        ['menu_title', '人気のお弁当', 'メニュータイトル', 'text', 30],
        ['menu_subtitle', '毎日届く、手作りの味わい', 'メニューサブタイトル', 'text', 31],
        ['menu_price_text', '1食あたり', 'メニュー価格表示', 'text', 32],
        ['menu_price_from', '480', '最低価格', 'text', 33],
        // ギャラリー
        ['gallery_title', '本日のお弁当', 'ギャラリーセクションタイトル', 'text', 40],
        ['gallery_subtitle', '毎日届く、手作りの味わい', 'ギャラリーサブタイトル', 'text', 41],
        ['testimonial_title', 'ご利用企業様の声', 'お客様の声タイトル', 'text', 45],
        // 会社情報
        ['company_name', 'Smiley Kitchen', '会社名', 'text', 50],
        ['company_address', '', '住所', 'text', 51],
        ['contact_phone', '', '電話番号', 'text', 52],
        ['contact_email', '', 'メールアドレス', 'text', 53],
        ['business_hours', '平日 9:00〜18:00', '営業時間', 'text', 54],
        // CTA
        ['cta_title', '今日から始めませんか？', 'CTAセクションタイトル', 'text', 55],
        ['cta_subtitle', '登録は無料。すぐに注文を始められます。', 'CTAセクションサブタイトル', 'text', 56],
        // デザイン
        ['primary_color', '#5D8A4A', 'メインカラー', 'color', 60],
        ['accent_color', '#E8B86D', 'アクセントカラー', 'color', 61],
        // 表示設定
        ['show_stats', '1', '実績数字を表示', 'toggle', 70],
        ['show_gallery', '1', 'ギャラリーを表示', 'toggle', 71],
        ['show_testimonials', '1', 'お客様の声を表示', 'toggle', 72],
        ['show_partners', '1', 'パートナーロゴを表示', 'toggle', 73],
        ['show_faq', '1', 'FAQを表示', 'toggle', 74],
        ['show_company_info', '1', '会社情報を表示', 'toggle', 75],
        ['show_sticky_cta', '1', 'モバイル固定CTAを表示', 'toggle', 76],
    ];

    $stmt = $db->prepare("INSERT IGNORE INTO landing_page_settings (setting_key, setting_value, setting_label, setting_type, display_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($defaults as $default) {
        $stmt->execute($default);
    }
} catch (Exception $e) {
    $error = 'テーブル作成エラー: ' . $e->getMessage();
}

// 画像アップロード処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'upload_image' && isset($_FILES['image'])) {
        $type = $_POST['image_type'] ?? 'gallery';
        $title = $_POST['image_title'] ?? '';
        $file = $_FILES['image'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($ext, $allowed)) {
                $filename = uniqid() . '_' . time() . '.' . $ext;
                $targetPath = "$uploadBase/$type/$filename";

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $relativePath = "uploads/$type/$filename";
                    $stmt = $db->prepare("INSERT INTO landing_page_images (image_type, image_path, image_title, image_alt) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$type, $relativePath, $title, $title]);
                    $message = '画像をアップロードしました';
                } else {
                    $error = 'ファイルの保存に失敗しました';
                }
            } else {
                $error = '対応していないファイル形式です';
            }
        } else {
            $error = 'アップロードエラー';
        }
    }

    if ($action === 'delete_image' && isset($_POST['image_id'])) {
        $imageId = (int)$_POST['image_id'];
        $stmt = $db->prepare("SELECT image_path FROM landing_page_images WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $filePath = "$uploadBase/../" . $image['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $stmt = $db->prepare("DELETE FROM landing_page_images WHERE id = ?");
            $stmt->execute([$imageId]);
            $message = '画像を削除しました';
        }
    }

    if ($action === 'add_testimonial') {
        $stmt = $db->prepare("INSERT INTO landing_page_testimonials (company_name, person_name, person_role, testimonial_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['company_name'] ?? '',
            $_POST['person_name'] ?? '',
            $_POST['person_role'] ?? '',
            $_POST['testimonial_text'] ?? ''
        ]);
        $message = 'お客様の声を追加しました';
    }

    if ($action === 'delete_testimonial' && isset($_POST['testimonial_id'])) {
        $stmt = $db->prepare("DELETE FROM landing_page_testimonials WHERE id = ?");
        $stmt->execute([(int)$_POST['testimonial_id']]);
        $message = 'お客様の声を削除しました';
    }

    if ($action === 'add_faq') {
        $stmt = $db->prepare("INSERT INTO landing_page_faq (question, answer) VALUES (?, ?)");
        $stmt->execute([
            $_POST['faq_question'] ?? '',
            $_POST['faq_answer'] ?? ''
        ]);
        $message = 'FAQを追加しました';
    }

    if ($action === 'delete_faq' && isset($_POST['faq_id'])) {
        $stmt = $db->prepare("DELETE FROM landing_page_faq WHERE id = ?");
        $stmt->execute([(int)$_POST['faq_id']]);
        $message = 'FAQを削除しました';
    }

    if ($action === 'add_menu') {
        $menuImage = '';
        if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['menu_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowed)) {
                $filename = uniqid() . '_' . time() . '.' . $ext;
                $targetPath = "$uploadBase/gallery/$filename";
                if (move_uploaded_file($_FILES['menu_image']['tmp_name'], $targetPath)) {
                    $menuImage = "uploads/gallery/$filename";
                }
            }
        }
        $stmt = $db->prepare("INSERT INTO landing_page_menus (menu_name, menu_description, menu_price, menu_image, menu_tag) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['menu_name'] ?? '',
            $_POST['menu_description'] ?? '',
            (int)($_POST['menu_price'] ?? 0),
            $menuImage,
            $_POST['menu_tag'] ?? ''
        ]);
        $message = 'メニューを追加しました';
    }

    if ($action === 'delete_menu' && isset($_POST['menu_id'])) {
        $stmt = $db->prepare("SELECT menu_image FROM landing_page_menus WHERE id = ?");
        $stmt->execute([(int)$_POST['menu_id']]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($menu && !empty($menu['menu_image'])) {
            $filePath = "$uploadBase/../" . $menu['menu_image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $stmt = $db->prepare("DELETE FROM landing_page_menus WHERE id = ?");
        $stmt->execute([(int)$_POST['menu_id']]);
        $message = 'メニューを削除しました';
    }
}

// 設定更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings'])) {
    try {
        $stmt = $db->prepare("UPDATE landing_page_settings SET setting_value = ? WHERE setting_key = ?");
        foreach ($_POST['settings'] as $key => $value) {
            $stmt->execute([$value, $key]);
        }
        $message = '設定を保存しました';
    } catch (Exception $e) {
        $error = '保存エラー: ' . $e->getMessage();
    }
}

// データ取得
$settings = [];
$settingsMap = [];
try {
    $stmt = $db->query("SELECT * FROM landing_page_settings WHERE is_active = 1 ORDER BY display_order");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($settings as $s) {
        $settingsMap[$s['setting_key']] = $s;
    }
} catch (Exception $e) {}

$images = ['hero' => [], 'logo' => [], 'gallery' => [], 'partners' => []];
try {
    $stmt = $db->query("SELECT * FROM landing_page_images WHERE is_active = 1 ORDER BY display_order, id DESC");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $img) {
        $images[$img['image_type']][] = $img;
    }
} catch (Exception $e) {}

$testimonials = [];
try {
    $stmt = $db->query("SELECT * FROM landing_page_testimonials WHERE is_active = 1 ORDER BY display_order, id DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$faqs = [];
try {
    $stmt = $db->query("SELECT * FROM landing_page_faq WHERE is_active = 1 ORDER BY display_order, id");
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$menus = [];
try {
    $stmt = $db->query("SELECT * FROM landing_page_menus WHERE is_active = 1 ORDER BY display_order, id");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// タブ
$activeTab = $_GET['tab'] ?? 'content';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ランディングページ設定 - 管理画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .page-header { background: linear-gradient(135deg, #5D8A4A 0%, #4A7A3A 100%); color: white; padding: 24px; margin-bottom: 24px; }
        .page-title { font-size: 24px; font-weight: 600; margin: 0; }
        .nav-tabs { border-bottom: 2px solid #dee2e6; }
        .nav-tabs .nav-link { border: none; color: #666; font-weight: 500; padding: 12px 24px; }
        .nav-tabs .nav-link.active { color: #5D8A4A; border-bottom: 3px solid #5D8A4A; background: none; }
        .settings-card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .settings-card h3 { font-size: 16px; font-weight: 600; color: #5D8A4A; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #E8F5E9; display: flex; align-items: center; gap: 8px; }
        .form-label { font-weight: 500; color: #555; font-size: 14px; }
        .form-control:focus { border-color: #5D8A4A; box-shadow: 0 0 0 0.2rem rgba(93, 138, 74, 0.25); }
        .icon-select { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
        .icon-option { width: 44px; height: 44px; border: 2px solid #e0e0e0; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .icon-option:hover, .icon-option.selected { border-color: #5D8A4A; background: #E8F5E9; }
        .btn-save { background: #5D8A4A; border: none; padding: 12px 32px; font-size: 16px; font-weight: 600; }
        .btn-save:hover { background: #4A7A3A; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 16px; }
        .image-card { position: relative; border-radius: 12px; overflow: hidden; aspect-ratio: 1; background: #f5f5f5; }
        .image-card img { width: 100%; height: 100%; object-fit: cover; }
        .image-card .delete-btn { position: absolute; top: 8px; right: 8px; background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .upload-zone { border: 2px dashed #ccc; border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s; background: #fafafa; }
        .upload-zone:hover { border-color: #5D8A4A; background: #f0f7ed; }
        .upload-zone.dragover { border-color: #5D8A4A; background: #E8F5E9; }
        .testimonial-card { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 16px; position: relative; }
        .testimonial-card .delete-btn { position: absolute; top: 12px; right: 12px; }
        .form-switch .form-check-input:checked { background-color: #5D8A4A; border-color: #5D8A4A; }
        .back-link { color: rgba(255,255,255,0.8); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px; font-size: 14px; }
        .back-link:hover { color: white; }
        .preview-btn { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; }
        .preview-btn:hover { background: rgba(255,255,255,0.3); color: white; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <a href="../" class="back-link">
                        <span class="material-icons-outlined" style="font-size: 18px;">arrow_back</span>
                        管理画面に戻る
                    </a>
                    <h1 class="page-title">ランディングページ設定</h1>
                </div>
                <a href="/order/" target="_blank" class="preview-btn">
                    <span class="material-icons-outlined" style="vertical-align: middle; font-size: 18px;">open_in_new</span>
                    プレビュー
                </a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- タブ -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link <?= $activeTab === 'content' ? 'active' : '' ?>" href="?tab=content">コンテンツ</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeTab === 'images' ? 'active' : '' ?>" href="?tab=images">画像・ロゴ</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeTab === 'menus' ? 'active' : '' ?>" href="?tab=menus">メニュー</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeTab === 'testimonials' ? 'active' : '' ?>" href="?tab=testimonials">お客様の声</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeTab === 'faq' ? 'active' : '' ?>" href="?tab=faq">FAQ</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeTab === 'design' ? 'active' : '' ?>" href="?tab=design">デザイン</a></li>
        </ul>

        <?php if ($activeTab === 'content'): ?>
        <!-- コンテンツタブ -->
        <form method="POST">
            <div class="settings-card">
                <h3><span class="material-icons-outlined">view_carousel</span>ヒーローセクション</h3>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">メインタイトル</label>
                        <textarea name="settings[hero_title]" class="form-control" rows="2"><?= htmlspecialchars($settingsMap['hero_title']['setting_value'] ?? '') ?></textarea>
                        <small class="text-muted">&lt;br&gt;で改行できます</small>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">サブタイトル</label>
                        <input type="text" name="settings[hero_subtitle]" class="form-control" value="<?= htmlspecialchars($settingsMap['hero_subtitle']['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">CTAボタンテキスト</label>
                        <input type="text" name="settings[hero_cta_text]" class="form-control" value="<?= htmlspecialchars($settingsMap['hero_cta_text']['setting_value'] ?? '無料で始める') ?>">
                    </div>
                </div>
            </div>

            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="settings-card">
                <h3><span class="material-icons-outlined">star</span>特徴 <?= $i ?></h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">タイトル</label>
                        <input type="text" name="settings[feature<?= $i ?>_title]" class="form-control" value="<?= htmlspecialchars($settingsMap["feature{$i}_title"]['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">アイコン</label>
                        <input type="hidden" name="settings[feature<?= $i ?>_icon]" id="icon_feature<?= $i ?>" value="<?= htmlspecialchars($settingsMap["feature{$i}_icon"]['setting_value'] ?? '') ?>">
                        <div class="icon-select" data-target="icon_feature<?= $i ?>">
                            <?php
                            $icons = ['touch_app', 'favorite', 'local_shipping', 'restaurant', 'schedule', 'smartphone', 'verified', 'eco', 'support_agent', 'payments', 'groups', 'thumb_up', 'emoji_food_beverage', 'delivery_dining'];
                            $currentIcon = $settingsMap["feature{$i}_icon"]['setting_value'] ?? '';
                            foreach ($icons as $icon): ?>
                            <div class="icon-option <?= $currentIcon === $icon ? 'selected' : '' ?>" data-icon="<?= $icon ?>">
                                <span class="material-icons-outlined"><?= $icon ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">説明文</label>
                        <textarea name="settings[feature<?= $i ?>_desc]" class="form-control" rows="2"><?= htmlspecialchars($settingsMap["feature{$i}_desc"]['setting_value'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            <?php endfor; ?>

            <div class="settings-card">
                <h3><span class="material-icons-outlined">analytics</span>実績数字（トラストバー）</h3>
                <div class="row g-3">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                    <div class="col-md-2">
                        <label class="form-label">数値<?= $i ?></label>
                        <input type="text" name="settings[stat<?= $i ?>_value]" class="form-control" value="<?= htmlspecialchars($settingsMap["stat{$i}_value"]['setting_value'] ?? '') ?>" placeholder="例: 150+">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ラベル<?= $i ?></label>
                        <input type="text" name="settings[stat<?= $i ?>_label]" class="form-control" value="<?= htmlspecialchars($settingsMap["stat{$i}_label"]['setting_value'] ?? '') ?>" placeholder="例: 導入企業数">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="settings-card">
                <h3><span class="material-icons-outlined">format_list_numbered</span>ご利用の流れ</h3>
                <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">ステップ<?= $i ?> タイトル</label>
                        <input type="text" name="settings[step<?= $i ?>_title]" class="form-control" value="<?= htmlspecialchars($settingsMap["step{$i}_title"]['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">ステップ<?= $i ?> 説明</label>
                        <input type="text" name="settings[step<?= $i ?>_desc]" class="form-control" value="<?= htmlspecialchars($settingsMap["step{$i}_desc"]['setting_value'] ?? '') ?>">
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <div class="settings-card">
                <h3><span class="material-icons-outlined">campaign</span>CTAセクション</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">CTAタイトル</label>
                        <input type="text" name="settings[cta_title]" class="form-control" value="<?= htmlspecialchars($settingsMap['cta_title']['setting_value'] ?? '今日から始めませんか？') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CTAサブタイトル</label>
                        <input type="text" name="settings[cta_subtitle]" class="form-control" value="<?= htmlspecialchars($settingsMap['cta_subtitle']['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CTAサブテキスト（ヒーロー下）</label>
                        <input type="text" name="settings[hero_cta_sub]" class="form-control" value="<?= htmlspecialchars($settingsMap['hero_cta_sub']['setting_value'] ?? '') ?>" placeholder="例: 初月無料キャンペーン中">
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h3><span class="material-icons-outlined">business</span>会社情報</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">会社名・サービス名</label>
                        <input type="text" name="settings[company_name]" class="form-control" value="<?= htmlspecialchars($settingsMap['company_name']['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">住所</label>
                        <input type="text" name="settings[company_address]" class="form-control" value="<?= htmlspecialchars($settingsMap['company_address']['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">電話番号</label>
                        <input type="text" name="settings[contact_phone]" class="form-control" value="<?= htmlspecialchars($settingsMap['contact_phone']['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">メールアドレス</label>
                        <input type="email" name="settings[contact_email]" class="form-control" value="<?= htmlspecialchars($settingsMap['contact_email']['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">営業時間</label>
                        <input type="text" name="settings[business_hours]" class="form-control" value="<?= htmlspecialchars($settingsMap['business_hours']['setting_value'] ?? '') ?>" placeholder="例: 平日 9:00〜18:00">
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success btn-save">
                    <span class="material-icons-outlined" style="vertical-align: middle;">save</span>
                    保存する
                </button>
            </div>
        </form>

        <?php elseif ($activeTab === 'images'): ?>
        <!-- 画像タブ -->
        <div class="settings-card">
            <h3><span class="material-icons-outlined">image</span>ヒーロー画像</h3>
            <p class="text-muted mb-3">トップに表示されるメイン画像（推奨: 1200x600px）</p>
            <div class="image-grid mb-3">
                <?php foreach ($images['hero'] as $img): ?>
                <div class="image-card">
                    <img src="/order/<?= htmlspecialchars($img['image_path']) ?>" alt="">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_image">
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('削除しますか？')">
                            <span class="material-icons-outlined" style="font-size: 18px;">close</span>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_image">
                <input type="hidden" name="image_type" value="hero">
                <div class="upload-zone" onclick="this.querySelector('input[type=file]').click()">
                    <span class="material-icons-outlined" style="font-size: 48px; color: #ccc;">cloud_upload</span>
                    <p class="mb-0 text-muted">クリックまたはドラッグ＆ドロップで画像をアップロード</p>
                    <input type="file" name="image" accept="image/*" style="display: none;" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <div class="settings-card">
            <h3><span class="material-icons-outlined">branding_watermark</span>ロゴ</h3>
            <p class="text-muted mb-3">ヘッダーに表示されるロゴ画像（推奨: 200x60px、透過PNG）</p>
            <div class="image-grid mb-3">
                <?php foreach ($images['logo'] as $img): ?>
                <div class="image-card" style="aspect-ratio: auto; padding: 20px; background: #f0f0f0;">
                    <img src="/order/<?= htmlspecialchars($img['image_path']) ?>" alt="" style="object-fit: contain;">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_image">
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('削除しますか？')">
                            <span class="material-icons-outlined" style="font-size: 18px;">close</span>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_image">
                <input type="hidden" name="image_type" value="logo">
                <div class="upload-zone" onclick="this.querySelector('input[type=file]').click()">
                    <span class="material-icons-outlined" style="font-size: 48px; color: #ccc;">cloud_upload</span>
                    <p class="mb-0 text-muted">ロゴ画像をアップロード</p>
                    <input type="file" name="image" accept="image/*" style="display: none;" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <div class="settings-card">
            <h3><span class="material-icons-outlined">photo_library</span>お弁当ギャラリー</h3>
            <p class="text-muted mb-3">商品写真（推奨: 正方形、600x600px）</p>
            <div class="image-grid mb-3">
                <?php foreach ($images['gallery'] as $img): ?>
                <div class="image-card">
                    <img src="/order/<?= htmlspecialchars($img['image_path']) ?>" alt="">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_image">
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('削除しますか？')">
                            <span class="material-icons-outlined" style="font-size: 18px;">close</span>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_image">
                <input type="hidden" name="image_type" value="gallery">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="image_title" class="form-control" placeholder="画像タイトル（任意）">
                    </div>
                </div>
                <div class="upload-zone" onclick="this.querySelector('input[type=file]').click()">
                    <span class="material-icons-outlined" style="font-size: 48px; color: #ccc;">cloud_upload</span>
                    <p class="mb-0 text-muted">商品写真をアップロード</p>
                    <input type="file" name="image" accept="image/*" style="display: none;" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <div class="settings-card">
            <h3><span class="material-icons-outlined">handshake</span>導入企業ロゴ</h3>
            <p class="text-muted mb-3">パートナー企業のロゴ（推奨: 180x60px、透過PNG）</p>
            <div class="image-grid mb-3">
                <?php foreach ($images['partners'] as $img): ?>
                <div class="image-card" style="aspect-ratio: 3/1; padding: 16px; background: #fff;">
                    <img src="/order/<?= htmlspecialchars($img['image_path']) ?>" alt="" style="object-fit: contain;">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_image">
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('削除しますか？')">
                            <span class="material-icons-outlined" style="font-size: 18px;">close</span>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_image">
                <input type="hidden" name="image_type" value="partners">
                <div class="upload-zone" onclick="this.querySelector('input[type=file]').click()">
                    <span class="material-icons-outlined" style="font-size: 48px; color: #ccc;">cloud_upload</span>
                    <p class="mb-0 text-muted">企業ロゴをアップロード</p>
                    <input type="file" name="image" accept="image/*" style="display: none;" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <?php elseif ($activeTab === 'testimonials'): ?>
        <!-- お客様の声タブ -->
        <div class="settings-card">
            <h3><span class="material-icons-outlined">format_quote</span>お客様の声を追加</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_testimonial">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">企業名</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">担当者名</label>
                        <input type="text" name="person_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">役職</label>
                        <input type="text" name="person_role" class="form-control" placeholder="例: 総務部長">
                    </div>
                    <div class="col-12">
                        <label class="form-label">コメント</label>
                        <textarea name="testimonial_text" class="form-control" rows="3" required placeholder="お客様からのコメントを入力してください"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <span class="material-icons-outlined" style="vertical-align: middle;">add</span>
                            追加する
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="settings-card">
            <h3><span class="material-icons-outlined">list</span>登録済みのお客様の声</h3>
            <?php if (empty($testimonials)): ?>
            <p class="text-muted">まだ登録がありません</p>
            <?php else: ?>
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete_testimonial">
                    <input type="hidden" name="testimonial_id" value="<?= $t['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger delete-btn" onclick="return confirm('削除しますか？')">
                        <span class="material-icons-outlined" style="font-size: 16px;">delete</span>
                    </button>
                </form>
                <p class="mb-2" style="font-size: 15px;">"<?= htmlspecialchars($t['testimonial_text']) ?>"</p>
                <p class="mb-0 text-muted" style="font-size: 14px;">
                    <strong><?= htmlspecialchars($t['company_name']) ?></strong>
                    <?php if ($t['person_name']): ?>
                    - <?= htmlspecialchars($t['person_name']) ?>
                    <?php endif; ?>
                    <?php if ($t['person_role']): ?>
                    (<?= htmlspecialchars($t['person_role']) ?>)
                    <?php endif; ?>
                </p>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php elseif ($activeTab === 'menus'): ?>
        <!-- メニュータブ -->
        <div class="settings-card">
            <h3><span class="material-icons-outlined">restaurant_menu</span>メニューを追加</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_menu">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">メニュー名</label>
                        <input type="text" name="menu_name" class="form-control" required placeholder="例: 日替わり弁当">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">価格（税込）</label>
                        <div class="input-group">
                            <input type="number" name="menu_price" class="form-control" placeholder="480">
                            <span class="input-group-text">円</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">タグ</label>
                        <input type="text" name="menu_tag" class="form-control" placeholder="例: 人気No.1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">画像</label>
                        <input type="file" name="menu_image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-12">
                        <label class="form-label">説明</label>
                        <textarea name="menu_description" class="form-control" rows="2" placeholder="メニューの説明"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <span class="material-icons-outlined" style="vertical-align: middle;">add</span>
                            追加する
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="settings-card">
            <h3><span class="material-icons-outlined">list</span>登録済みメニュー</h3>
            <?php if (empty($menus)): ?>
            <p class="text-muted">まだ登録がありません</p>
            <?php else: ?>
            <div class="row g-3">
                <?php foreach ($menus as $menu): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if ($menu['menu_image']): ?>
                        <img src="/order/<?= htmlspecialchars($menu['menu_image']) ?>" class="card-img-top" alt="" style="height: 150px; object-fit: cover;">
                        <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                            <span class="material-icons-outlined" style="font-size: 48px; color: #ccc;">restaurant</span>
                        </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <?php if ($menu['menu_tag']): ?>
                            <span class="badge bg-success mb-2"><?= htmlspecialchars($menu['menu_tag']) ?></span>
                            <?php endif; ?>
                            <h5 class="card-title"><?= htmlspecialchars($menu['menu_name']) ?></h5>
                            <?php if ($menu['menu_description']): ?>
                            <p class="card-text text-muted small"><?= htmlspecialchars($menu['menu_description']) ?></p>
                            <?php endif; ?>
                            <?php if ($menu['menu_price']): ?>
                            <p class="card-text fw-bold text-success"><?= number_format($menu['menu_price']) ?>円</p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_menu">
                                <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('削除しますか？')">
                                    <span class="material-icons-outlined" style="font-size: 16px;">delete</span>
                                    削除
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php elseif ($activeTab === 'faq'): ?>
        <!-- FAQタブ -->
        <div class="settings-card">
            <h3><span class="material-icons-outlined">help_outline</span>FAQを追加</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_faq">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">質問</label>
                        <input type="text" name="faq_question" class="form-control" required placeholder="例: 注文の締め切りは何時ですか？">
                    </div>
                    <div class="col-12">
                        <label class="form-label">回答</label>
                        <textarea name="faq_answer" class="form-control" rows="3" required placeholder="回答を入力してください"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <span class="material-icons-outlined" style="vertical-align: middle;">add</span>
                            追加する
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="settings-card">
            <h3><span class="material-icons-outlined">list</span>登録済みFAQ</h3>
            <?php if (empty($faqs)): ?>
            <p class="text-muted">まだ登録がありません</p>
            <?php else: ?>
            <?php foreach ($faqs as $faq): ?>
            <div class="testimonial-card">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete_faq">
                    <input type="hidden" name="faq_id" value="<?= $faq['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger delete-btn" onclick="return confirm('削除しますか？')">
                        <span class="material-icons-outlined" style="font-size: 16px;">delete</span>
                    </button>
                </form>
                <p class="mb-2 fw-bold" style="font-size: 15px;">Q. <?= htmlspecialchars($faq['question']) ?></p>
                <p class="mb-0 text-muted" style="font-size: 14px;">A. <?= htmlspecialchars($faq['answer']) ?></p>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php elseif ($activeTab === 'design'): ?>
        <!-- デザインタブ -->
        <form method="POST">
            <div class="settings-card">
                <h3><span class="material-icons-outlined">palette</span>カラー設定</h3>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">メインカラー</label>
                        <div class="d-flex gap-3 align-items-center">
                            <input type="color" name="settings[primary_color]" class="form-control form-control-color" value="<?= htmlspecialchars($settingsMap['primary_color']['setting_value'] ?? '#5D8A4A') ?>" style="width: 60px; height: 50px;">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($settingsMap['primary_color']['setting_value'] ?? '#5D8A4A') ?>" readonly style="max-width: 120px;">
                        </div>
                        <small class="text-muted">ボタン、見出しなどに使用</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">アクセントカラー</label>
                        <div class="d-flex gap-3 align-items-center">
                            <input type="color" name="settings[accent_color]" class="form-control form-control-color" value="<?= htmlspecialchars($settingsMap['accent_color']['setting_value'] ?? '#E8B86D') ?>" style="width: 60px; height: 50px;">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($settingsMap['accent_color']['setting_value'] ?? '#E8B86D') ?>" readonly style="max-width: 120px;">
                        </div>
                        <small class="text-muted">CTAボタン、強調に使用</small>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h3><span class="material-icons-outlined">toggle_on</span>セクション表示設定</h3>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="settings[show_stats]" value="1" id="showStats" <?= ($settingsMap['show_stats']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="showStats">実績数字を表示</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="settings[show_gallery]" value="1" id="showGallery" <?= ($settingsMap['show_gallery']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="showGallery">お弁当ギャラリーを表示</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="settings[show_testimonials]" value="1" id="showTestimonials" <?= ($settingsMap['show_testimonials']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="showTestimonials">お客様の声を表示</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="settings[show_partners]" value="1" id="showPartners" <?= ($settingsMap['show_partners']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="showPartners">導入企業ロゴを表示</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="settings[show_faq]" value="1" id="showFaq" <?= ($settingsMap['show_faq']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="showFaq">FAQを表示</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="settings[show_company_info]" value="1" id="showCompanyInfo" <?= ($settingsMap['show_company_info']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="showCompanyInfo">会社情報を表示</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="settings[show_sticky_cta]" value="1" id="showStickyCta" <?= ($settingsMap['show_sticky_cta']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="showStickyCta">モバイル固定CTAを表示</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h3><span class="material-icons-outlined">text_fields</span>セクションタイトル</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">ギャラリーセクションタイトル</label>
                        <input type="text" name="settings[gallery_title]" class="form-control" value="<?= htmlspecialchars($settingsMap['gallery_title']['setting_value'] ?? '本日のお弁当') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ギャラリーサブタイトル</label>
                        <input type="text" name="settings[gallery_subtitle]" class="form-control" value="<?= htmlspecialchars($settingsMap['gallery_subtitle']['setting_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">お客様の声セクションタイトル</label>
                        <input type="text" name="settings[testimonial_title]" class="form-control" value="<?= htmlspecialchars($settingsMap['testimonial_title']['setting_value'] ?? 'ご利用企業様の声') ?>">
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success btn-save">
                    <span class="material-icons-outlined" style="vertical-align: middle;">save</span>
                    保存する
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // アイコン選択
        document.querySelectorAll('.icon-select').forEach(container => {
            const targetId = container.dataset.target;
            const input = document.getElementById(targetId);
            container.querySelectorAll('.icon-option').forEach(option => {
                option.addEventListener('click', function() {
                    container.querySelectorAll('.icon-option').forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    input.value = this.dataset.icon;
                });
            });
        });

        // カラーピッカー連動
        document.querySelectorAll('input[type="color"]').forEach(colorInput => {
            const textInput = colorInput.parentElement.querySelector('input[type="text"]');
            if (textInput) {
                colorInput.addEventListener('input', function() {
                    textInput.value = this.value;
                });
            }
        });

        // ドラッグ＆ドロップ
        document.querySelectorAll('.upload-zone').forEach(zone => {
            zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
            zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.classList.remove('dragover');
                const input = zone.querySelector('input[type="file"]');
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            });
        });
    </script>
</body>
</html>
