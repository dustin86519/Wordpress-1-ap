<?php
/**
 * WordPress 核心設定檔 — 雲端自動化與最高安全完全體版
 */

// ========================================================
// 🌟 核心控制點：WP Offload Media 自動化預設設定 (對齊你的截圖)
// ========================================================
define( 'AS3CF_SETTINGS', serialize( array(
    'provider'          => 'aws',
    'use-server-roles'  => true,                                        // 強制開啟 AWS 原生 IAM Role 模式
    'bucket'            => getenv('AWS_S3_BUCKET') ?: die('Missing AWS_S3_BUCKET'),
    'copy-to-s3'        => true,                                        // 核心功能：上傳媒體時自動同步到 S3
    'serve-from-s3'     => true,                                        // 核心功能：自動把前端圖片網址改成 S3 網址
    'remove-local-file' => false,                                       // 是否刪除 AP 機本地檔案 (建議先留 false)
    'delivery-provider' => 'none'                                       // 直接走 S3 原生網址，不透過 CloudFront
) ) );

// ========================================================
// 1. 資料庫連線設定（動態讀取環境變數）
// ========================================================
define( 'DB_NAME',     getenv('DB_NAME')        ?: die('Missing DB_NAME') );
define( 'DB_USER',     getenv('DB_USER')        ?: die('Missing DB_USER') );
define( 'DB_PASSWORD', getenv('DB_PASSWORD')    ?: die('Missing DB_PASSWORD') );
define( 'DB_HOST',     getenv('DB_MASTER_HOST') ?: die('Missing DB_MASTER_HOST') );
define( 'DB_CHARSET',  'utf8' );
define( 'DB_COLLATE',  '' );

// ========================================================
// 2. 唯一識別密鑰（Salts）— 從環境變數讀取，不可硬寫
// ========================================================
define( 'AUTH_KEY',         getenv('WP_AUTH_KEY')         ?: die('Missing WP_AUTH_KEY') );
define( 'SECURE_AUTH_KEY',  getenv('WP_SECURE_AUTH_KEY')  ?: die('Missing WP_SECURE_AUTH_KEY') );
define( 'LOGGED_IN_KEY',    getenv('WP_LOGGED_IN_KEY')    ?: die('Missing WP_LOGGED_IN_KEY') );
define( 'NONCE_KEY',        getenv('WP_NONCE_KEY')        ?: die('Missing WP_NONCE_KEY') );
define( 'AUTH_SALT',        getenv('WP_AUTH_SALT')        ?: die('Missing WP_AUTH_SALT') );
define( 'SECURE_AUTH_SALT', getenv('WP_SECURE_AUTH_SALT') ?: die('Missing WP_SECURE_AUTH_SALT') );
define( 'LOGGED_IN_SALT',   getenv('WP_LOGGED_IN_SALT')   ?: die('Missing WP_LOGGED_IN_SALT') );
define( 'NONCE_SALT',       getenv('WP_NONCE_SALT')       ?: die('Missing WP_NONCE_SALT') );

$table_prefix = 'wp_';

// ========================================================
// 3. WordPress 核心效能、安全與核心優化設定
// ========================================================
define( 'WP_DEBUG', false );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'CONCATENATE_SCRIPTS', false );

// 完全禁用後台安裝/刪除外掛與主題的權限，切換為唯讀防駭模式 (「安裝外掛」選單會隱藏)
define( 'DISALLOW_FILE_MODS', true );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}
require_once ABSPATH . 'wp-settings.php';