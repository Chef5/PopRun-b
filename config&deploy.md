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

### php.ini

``` she
cgi.fix_pathinfo=0

extension=curl
extension=fileinfo
extension=gd2
extension=mbstring
extension=openssl
extension=pdo_mysql
```

### .env

``` she
APP_ENV=production
APP_DEBUG=false
APP_URL=http://dev.run.nunet.cn
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

chown -R www-data:www-data /home/dev.run/PopRun-b
chmod -R 775 resources

systemctl restart nginx
systemctl restart php7.2-fpm
```

