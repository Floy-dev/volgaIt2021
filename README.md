<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="laravle"></a></p>

<h1> Установка зависимостей</h1>

- Установка php-fpm
- Установка node
- Установка yarn
- Установка mySql
- Установка composer
- Установка nginx
- Установка git

<p> Используется ОС - Linux Ubuntu </p>
<hr>
1) Установка php7.4 и php-fpm7.4:

	sudo apt-get update
	sudo apt -y install software-properties-common
	sudo add-apt-repository ppa:ondrej/php
	sudo apt-get update
	sudo apt -y install php7.4

	После этого php будет установлен, осталось установить php-fpm и зависимости для него:
	sudo apt-get install -y php7.4-cli php7.4-json php7.4-common php7.4-mysql 
	php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-fpm
<hr>
2) Установка node

	cd ~
	curl -sL https://deb.nodesource.com/setup_14.x -o nodesource_setup.sh
	sudo bash nodesource_setup.sh
	sudo apt install nodejs
	node -v

	Здесь мы переходим в корневую директорию, с помощью curl берем файл с установкой
	ноды 14.х версии. Закидываем установщик в bash и устанавливаем ее. Далее можно 
	проверить версию. 
<hr>
3) Установка yarn

	curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
	echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
	sudo apt install --no-install-recommends yarn
<hr>
4) Установка mySql

	sudo apt install mysql-server
	sudo mysql_secure_installation // выбираем минимальный уровень защиты
	
	Устанавливаем пароль для ROOT пользователя mySql
	sudo mysql
	
	CREATE USER 'floy'@'localhost'IDENTIFIED WITH mysql_native_password BY 'floy2310';
	GRANT ALL PRIVILEGES ON *.* TO 'floy'@'localhost' WITH GRANT OPTION;
	FLUSH PRIVILEGES;
<hr>
5) Установка composer

	sudo apt install php-cli unzip
	cd ~
	curl -sS https://getcomposer.org/installer -o composer-setup.php
	HASH=`curl -sS https://composer.github.io/installer.sig`
	php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

	sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
<hr>
6) Установка nginx

	sudo apt install nginx	
<hr>
7) Установка git

	sudo apt install git

<h2>Установка проекта</h2>

1) Клонирование

   	cd ~
   	git clone https://github.com/Floy-dev/volgaIt2021.git volga
<hr>
2) Инициализация

   	cd ~
   	cd volga/
   	composer i
   	yarn
   	yarn watch
<hr>
3) Редактирование nginx conf

   	sudo vim /etc/hosts
   	Добавить следующее:
   	127.0.0.1	volga.local

   	sudo vim /etc/nginx/nginx.conf
   	Добавить следующее:

   	server {
           listen 80;
           server_name volga.local www.volga.local;
           root ~/volga/public;
           index index.php;

           client_max_body_size 300m;

           location / {
                   try_files $uri $uri/ /index.php?$query_string;
           }

           location ~ /images/cache/ {
                   try_files $uri /index.php?$query_string;
           }

           location ~ \.php$ {
                   try_files $uri $uri/ /index.php$query_string;
                   fastcgi_split_path_info ^(.+\.php)(/.+)$;
                   fastcgi_pass unix:/run/php/php7.4-fpm.sock;
                   fastcgi_index index.php;
                   include fastcgi_params;
                   fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                   fastcgi_param PATH_INFO $fastcgi_path_info;
           }
   	}
<hr>
4)  Перезапуск nginx и php-fpm

    	sudo systemctl restart nginx.service 
    	sudo systemctl restart php7.4-fpm.service 
<hr>
5) Создание в MySQL новую базу данных - volga

   	Использовать adminer | phpmyadmin на свое усмотрение
<hr>
6) Настройка подключения к БД

   	Скопировать файл .env.exampe > .env и изменить следующие:
   	DB_CONNECTION=mysql  
   	DB_HOST=127.0.0.1  
   	DB_PORT=3306  
   	DB_DATABASE=volga  
   	DB_USERNAME=ваш_username_от_бд 
   	DB_PASSWORD=ваш_password_от_бд   
<hr>

7) Готово - ©Floy TYZ - перейти на volga.local

<hr>
