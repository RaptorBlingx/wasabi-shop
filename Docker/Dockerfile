FROM prestashop/prestashop:8.0
# RUN apt update

# Crea un collegamento simbolico da project/src a var/www/html
RUN rm -rf /var/www/html &&\
    ln -s /project/src/ /var/www/html

# # Installazione pacchetti
RUN docker-php-ext-install mysqli 

WORKDIR /var/www/html




