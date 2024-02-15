FROM registry.access.redhat.comubi8ubi8.1

RUN yum --disableplugin=subscription-manager -y module enable php7.3 
  && yum --disableplugin=subscription-manager -y install httpd php 
  && yum --disableplugin=subscription-manager clean all

ADD index.php varwwwhtml

RUN sed -i 'sListen 80Listen 8080' etchttpdconfhttpd.conf 
  && sed -i 'slisten.acl_users = apache,nginxlisten.acl_users =' etcphp-fpm.dwww.conf 
  && mkdir runphp-fpm 
  && chgrp -R 0 varloghttpd varrunhttpd runphp-fpm 
  && chmod -R g=u varloghttpd varrunhttpd runphp-fpm

EXPOSE 8080
USER 1001
CMD php-fpm & httpd -D FOREGROUND