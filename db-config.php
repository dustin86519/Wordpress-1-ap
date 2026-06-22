<?php
use Automattic\LudicrousDB\LudicrousDB;

$wpdb = new LudicrousDB();

// ========================================================
// 資料庫讀寫分離設定 (LudicrousDB) - 動態讀取 Docker 環境變數
// ========================================================

# 1. 取得資料庫基本憑證（優先讀取環境變數，若無則走預設值）
$db_user     = getenv('DB_USER')     ?: 'wp_user';
$db_password = getenv('DB_PASSWORD') ?: 'wp_password';
$db_name     = getenv('DB_NAME')     ?: 'wordpress_db';

# 2. 取得 Master / Slave 的主機位置
# 🎯 完美對齊你的 Docker -e 參數，且後補預設值直接走 Route 53 私有網域
$master_host = getenv('DB_MASTER_HOST') ?: 'master-db.cloud.wordpress.project';
$slave_host  = getenv('DB_SLAVE_HOST')  ?: 'slave-db.cloud.wordpress.project';


# ========================================================
# 路由配置：Master 寫入庫 (Write Only)
# ========================================================
$wpdb->add_database( array(
    'host'     => $master_host . ':3306',
    'user'     => $db_user,
    'password' => $db_password,
    'name'     => $db_name,
    'write'    => 1,  // 🟢 允許寫入
    'read'     => 0,  // ❌ 不負責讀取（登入、發文專用）
    'dataset'  => 'global',
) );


# ========================================================
# 路由配置：Slave 讀取庫 (Read Only)
# ========================================================
$wpdb->add_database( array(
    'host'     => $slave_host . ':3306',
    'user'     => $db_user,
    'password' => $db_password,
    'name'     => $db_name,
    'write'    => 0,  // ❌ 不允許寫入（防範日常流量寫入 Slave）
    'read'     => 1,  // 🟢 允許讀取（日常瀏覽文章專用）
    'dataset'  => 'global',
) );