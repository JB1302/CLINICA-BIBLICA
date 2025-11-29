FROM php:8.2-apache-bookworm

# Cliente Oracle ya compilado
RUN apt-get update && \
    apt-get install -y unzip libaio1 libaio-dev php-pear php-dev build-essential \
                       gcc make autoconf pkg-config && \
    rm -rf /var/lib/apt/lists/*

# Instant Client
COPY instantclient/*.zip /tmp/
RUN mkdir -p /opt/oracle && \
    unzip -q -o /tmp/instantclient-basiclite-linux.x64-23.26.0.0.0.zip -d /opt/oracle && \
    unzip -q -o /tmp/instantclient-sdk-linux.x64-23.26.0.0.0.zip       -d /opt/oracle && \
    echo "/opt/oracle/instantclient_23_26" > /etc/ld.so.conf.d/oracle-instantclient.conf && \
    ldconfig

ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_23_26
ENV ORACLE_HOME=/opt/oracle/instantclient_23_26

# 👉 Versión 3.2.1 funciona con PHP 8.2
RUN echo "instantclient,/opt/oracle/instantclient_23_26" | pecl install oci8-3.2.1 && \
    docker-php-ext-enable oci8

RUN echo "date.timezone=America/Costa_Rica" > /usr/local/etc/php/conf.d/timezone.ini
RUN a2enmod rewrite
