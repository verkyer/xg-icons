FROM php:8.2-apache

WORKDIR /var/www/html

# 声明构建参数
ARG LOGO_IMG=favicon.ico
ARG SITE_NAME=xg-icons
ARG COPYRIGHT="Created by <a href=\"https://github.com/verkyer/xg-icons\" target=\"_blank\" rel=\"noopener noreferrer\">@xg-icons</a>."

# 将构建参数设置为环境变量
ENV LOGO_IMG=$LOGO_IMG
ENV SITE_NAME=$SITE_NAME
ENV COPYRIGHT=$COPYRIGHT

# 将 web 文件夹下的内容复制到容器中
COPY web/ /var/www/html/

EXPOSE 80
