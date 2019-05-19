FROM webdevops/php-nginx:7.3
RUN apt-get update && apt-get install -y ssmtp && apt-get clean
ADD . /app/
ENV TABBY_DB_DSN=sqlite:/tmp/db.sqlite
ENV TABBY_DB_USER=''
ENV TABBY_DB_PASSWORD=''
#ENV TABBY_DOMAIN
ENV TABBY_APPLICATION_EMAIL=''
ENV TABBY_ADMIN_EMAIL=''
ENV TABBY_REMIND_DAYS=5
ENV TABBY_BASE_PATH=/
ENV TABBY_PROTOCOL=https
# ENV TABBY_SMTP_SERVER
ENV TABBY_SMTP_USER=''
ENV TABBY_SMTP_PASSWORD=''
ADD ./docker/config.php /app/config.php
ADD ./docker/ssmtp.sh /opt/docker/provision/entrypoint.d/20-ssmtp.sh
ADD ./docker/setup.php /opt/docker/provision/entrypoint.d/40-tabby-setup.php
RUN echo "php /opt/docker/provision/entrypoint.d/40-tabby-setup.php" > /opt/docker/provision/entrypoint.d/40-tabby-setup.sh
RUN echo "0 0 * * * application /usr/bin/env php /app/cron.php" > /opt/docker/etc/cron/tabby
RUN chmod +x /opt/docker/provision/entrypoint.d/20-ssmtp.sh /opt/docker/provision/entrypoint.d/40-tabby-setup.sh
