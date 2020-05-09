## Config

### dev.run.conf

``` sh
server {
        listen 80; 
        root /home/dev.run/PopRun-b/public;
        index index.html index.htm index.php;
        server_name dev.run.nunet.cn;
        # 404 重要
        location / {
               try_files $uri $uri/ /index.php?$query_string;
        }
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php7.2-fpm.sock;
        }
        location ~ /\.ht {
                deny all;
        }
}
```

### nginx.conf
client_max_body_size 30m; 

### php.ini

``` she
cgi.fix_pathinfo=0
upload_max_filesize 20m

extension=curl
extension=fileinfo
extension=gd2
extension=mbstring
extension=openssl
extension=pdo_mysql
```

### my.cnf

[client]
default-character-set = utf8mb4
[mysql]
default-character-set = utf8mb4
[mysqld]
character-set-client-handshake = FALSE
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
init_connect='SET NAMES utf8mb4'

ALTER DATABASE poprun CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
ALTER TABLE table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE table_name CHANGE column_name VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

### .env

``` she
APP_ENV=production
APP_DEBUG=false
APP_URL=http://dev.run.nunet.cn

WX_APPID=
WX_SECRET=

TIMEZONE=Asia/Shanghai
DB_DATABASE=poprun
DB_USERNAME=root
DB_PASSWORD=root
```

## Deploy

``` she
/home/dev.run/  git clone git@github.com:Patrick-Jun/PopRun-b.git
cd PopRun-b
cp ../.env ./

composer install  #first time
php artisan key:generate  #first time
php artisan migrate  #first time
php artisan up

crontab -e
* * * * * php  /home/dev.run/PopRun-b/artisan schedule:run >> /dev/null 2>&1

chown -R www-data:www-data /home/dev.run/PopRun-b
chmod -R 775 resources

systemctl restart nginx
systemctl restart php7.2-fpm
```

## Update

``` she
cd /home/dev.run/PopRun-b
git pull origin master
```