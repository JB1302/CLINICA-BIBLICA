# Usamos Debian 12 (bookworm) para tener libaio1
FROM php:8.2-apache-bookworm

# Dependencias
RUN apt-get update && apt-get install -y unzip libaio1 && rm -rf /var/lib/apt/lists/*

# Copia los zips del Instant Client 23.26
COPY instantclient/instantclient-basiclite-linux.x64-23.26.0.0.0.zip /tmp/
COPY instantclient/instantclient-sdk-linux.x64-23.26.0.0.0.zip       /tmp/

# Instalación del Instant Client (forzar overwrite -o y silencioso -q)
RUN mkdir -p /opt/oracle && \
    unzip -q -o /tmp/instantclient-basiclite-linux.x64-23.26.0.0.0.zip -d /opt/oracle && \
    unzip -q -o /tmp/instantclient-sdk-linux.x64-23.26.0.0.0.zip       -d /opt/oracle && \
    rm -f /tmp/instantclient-*.zip && \
    echo "/opt/oracle/instantclient_23_26" > /etc/ld.so.conf.d/oracle-instantclient.conf && \
    ldconfig

# Variables de entorno
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_23_26
ENV ORACLE_HOME=/opt/oracle/instantclient_23_26

# Instalar y habilitar OCI8 para PHP (no interactivo)
RUN echo "instantclient,/opt/oracle/instantclient_23_26" | pecl install oci8 && \
    docker-php-ext-enable oci8

# Opcional: zona horaria y mod_rewrite
RUN echo "date.timezone=America/Costa_Rica" > /usr/local/etc/php/conf.d/timezone.ini
RUN a2enmod rewrite
