<?php
use Automattic\LudicrousDB\LudicrousDB;

$wpdb = new LudicrousDB();

// ========================================================
// 資料庫讀寫分離設定 (LudicrousDB) - 動態讀取環境變數
// ========================================================

# Master 寫入庫
$wpdb->add_database( array(
    'host'     => (getenv('DB_MASTER_HOST') ?: die('Missing DB_MASTER_HOST')) . ':3306',
    'user'     => getenv('DB_USER')     ?: die('Missing DB_USER'),
    'password' => getenv('DB_PASSWORD') ?: die('Missing DB_PASSWORD'),
    'name'     => getenv('DB_NAME')     ?: die('Missing DB_NAME'),
    'write'    => 1,
    'read'     => 0,
    'dataset'  => 'global',
) );

# Slave 讀取庫
$wpdb->add_database( array(
    'host'     => (getenv('DB_SLAVE_HOST') ?: die('Missing DB_SLAVE_HOST')) . ':3306',
    'user'     => getenv('DB_USER')     ?: die('Missing DB_USER'),
    'password' => getenv('DB_PASSWORD') ?: die('Missing DB_PASSWORD'),
    'name'     => getenv('DB_NAME')     ?: die('Missing DB_NAME'),
    'write'    => 0,
    'read'     => 1,
    'dataset'  => 'global',
) );