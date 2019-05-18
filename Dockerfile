FROM webdevops/php-nginx:7.3
#RUN apt-get update && apt-get install -y libpq-dev && apt-get clean
#RUN docker-php-ext-install -j$(nproc) pdo_pgsql
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
ADD ./docker/config.php /app/config.php
ADD ./docker/setup.php /opt/docker/provision/entrypoint.d/40-tabby-setup.php
RUN echo "php /opt/docker/provision/entrypoint.d/40-tabby-setup.php" > /opt/docker/provision/entrypoint.d/40-tabby-setup.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/40-tabby-setup.sh
