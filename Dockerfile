FROM registry.access.redhat.com/ubi8/ubi:latest as BUILDER

ARG PHP_VERSION=7.4
ARG SOURCE_NAME

USER root

ENV PATH=/opt/app-root/bin/:$PATH
RUN yum update -y --disablerepo=* --enablerepo=ubi-8-appstream --enablerepo=ubi-8-baseos \
 && yum -y module enable php:$PHP_VERSION \
 && yum clean all \
 && rm -rf /var/cache/yum \
 && rm -rf /var/log/*

RUN yum install -y --disablerepo=* --enablerepo=ubi-8-appstream --enablerepo=ubi-8-baseos nginx npm nodejs nodejs-devel autoconf automake binutils make git wget curl php php-fpm php-cli php-pgsql php-devel php-xml php-json php-pdo php-mysqlnd php-bcmath php-gd php-xmlrpc php-soap php-mbstring \
 && yum clean all \
 && rm -rf /var/cache/yum \
 && rm -rf /var/log/*

RUN rm -rf /var/www/html \
 && mkdir -p /var/www/html \
 && mkdir -p /opt/app-root/{bin,src} \
 && mkdir -p /var/{log,run}/{nginx,php-fpm} \
 && touch /var/log/nginx/error.log \
 && chown -R 1001:1001 /opt/app-root \
 && chown -R 1001:1001 /var/www/ \
 && chown -R 1001:1001 /var/{log,run}/{nginx,php-fpm}/ \
 && chmod -R 777 /var/{log,run}/{nginx,php-fpm}/ \
 && chmod -R 777 /var/www/

COPY container-root/ /
#COPY app-src/ /var/www/html/
#COPY .git/refs/heads/main /var/www/html/storage/.gitchecksum

USER 1001

WORKDIR "/var/www/html"

CMD [ "sh", "/start.sh" ]
