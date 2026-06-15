FROM wordpress:7.0-fpm-alpine

RUN apk add --no-cache unzip wget
WORKDIR /usr/src/wordpress

# 1. 複製自訂 PHP 最佳化參數
COPY php-local.ini /usr/local/etc/php/conf.d/php-local.ini

# 2. 將預設好的 wp-config.php 打包進根目錄資產庫
COPY wp-config.php /usr/src/wordpress/wp-config.php

# 3. 下載 WP Offload Media Lite
RUN wget -q https://downloads.wordpress.org/plugin/amazon-s3-and-cloudfront.latest-stable.zip -O wp-offload-media.zip \
    && unzip wp-offload-media.zip \
    && mkdir -p /usr/src/wordpress/wp-content/plugins \
    && mv amazon-s3-and-cloudfront /usr/src/wordpress/wp-content/plugins/ \
    && rm wp-offload-media.zip

# 4. 下載 LudicrousDB
RUN wget -q https://github.com/stuttter/ludicrousdb/archive/refs/heads/master.zip -O ludicrousdb.zip \
    && unzip ludicrousdb.zip \
    && mkdir -p /usr/src/wordpress/wp-content \
    && mv ludicrousdb-master/ludicrousdb/drop-ins/db.php /usr/src/wordpress/wp-content/ \
    && rm -rf ludicrousdb.zip ludicrousdb-master

# 🔥【關鍵修正】db-config.php 必須複製到 wp-content 目錄下，LudicrousDB 才能被正確激活！
COPY db-config.php /usr/src/wordpress/wp-content/db-config.php

# 5. 複製繁體中文語言包
COPY languages /usr/src/wordpress/wp-content/languages

# ========================================================
# 6. 🔥【修正核心】利用 mu-plugins 機制，強制自動啟用 WP Offload Media
# 修正為官方正確入口檔名 wordpress-s3.php，並改用 printf 單行格式避免 Docker 多行換行出錯
# ========================================================
RUN mkdir -p /usr/src/wordpress/wp-content/mu-plugins \
    && printf "<?php\nadd_action('init', function() {\n    if (!function_exists('activate_plugin')) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }\n    \$plugin = 'amazon-s3-and-cloudfront/wordpress-s3.php';\n    if (!is_plugin_active(\$plugin)) { activate_plugin(\$plugin); }\n});\n" > /usr/src/wordpress/wp-content/mu-plugins/auto-activate.php

# 7. 統一調整目錄權限
RUN chown -R www-data:www-data /usr/src/wordpress

# 8. 【環境解鎖】強行關閉 PHP-FPM 清空環境變數機制，確保 getenv() 能順利抓到變數
RUN echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

# 9. 將工作目錄切回官方預設
WORKDIR /var/www/html

EXPOSE 9000