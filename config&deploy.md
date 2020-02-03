## Config

### php.ini
cgi.fix_pathinfo=0
extension=curl
extension=fileinfo
extension=gd2
extension=mbstring
extension=openssl
extension=pdo_mysql

### .env

APP_ENV=production
APP_DEBUG=false
APP_URL=http://dev.run.nunet.cn

## Deploy

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