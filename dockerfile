# Usamos imagen PHP con Apache
FROM php:8.2-apache

# Actualizamos e instalamos dependencias básicas
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    wget \
    libaio-dev \
    libaio1t64 \
    && rm -rf /var/lib/apt/lists/* \
    && ln -s /usr/lib/x86_64-linux-gnu/libaio.so.1t64 /usr/lib/x86_64-linux-gnu/libaio.so.1

# Habilitamos mod_rewrite
RUN a2enmod rewrite

# Configuramos zona horaria
RUN echo "date.timezone=America/Costa_Rica" > /usr/local/etc/php/conf.d/timezone.ini

# Instalar Oracle Instant Client y OCI8
RUN wget https://download.oracle.com/otn_software/linux/instantclient/2350000/instantclient-basic-linux.x64-23.5.0.24.07.zip \
    && wget https://download.oracle.com/otn_software/linux/instantclient/2350000/instantclient-sdk-linux.x64-23.5.0.24.07.zip \
    && mkdir -p /opt/oracle \
    && unzip -o instantclient-basic-linux.x64-23.5.0.24.07.zip -d /opt/oracle \
    && unzip -n instantclient-sdk-linux.x64-23.5.0.24.07.zip -d /opt/oracle \
    && echo /opt/oracle/instantclient_23_5 > /etc/ld.so.conf.d/oracle-instantclient.conf \
    && ldconfig \
    && rm -f instantclient-*.zip

# Instalar extensión OCI8
RUN echo "instantclient,/opt/oracle/instantclient_23_5" | pecl install oci8 \
    && docker-php-ext-enable oci8

# Configurar variables de entorno
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_23_5:$LD_LIBRARY_PATH
ENV PATH=/opt/oracle/instantclient_23_5:$PATH

# Exponemos puerto 80
EXPOSE 80


