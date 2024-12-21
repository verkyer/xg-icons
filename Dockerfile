FROM php:8.2-apache

WORKDIR /var/www/html

# 将 web 文件夹下的内容复制到容器中
COPY web/ /var/www/html/

EXPOSE 80
