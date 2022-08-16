FROM wordpress:latest

RUN a2enmod ssl
RUN a2enmod headers
RUN apt update && apt install -y openssl
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/apache-selfsigned.key -out /etc/ssl/certs/apache-selfsigned.crt -subj "/C=ES/ST=Barcelona/L=Spain/O=Còdec/OU=Developers/CN=hortadav.codeccoop.org"
COPY .ssl/ssl-params.conf /etc/apache2/conf-available/ssl-params.conf
RUN ln -s /etc/apache2/conf-available/ssl-params.conf /etc/apache2/conf-enabled/ssl-params.conf
COPY .ssl/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN ln -s /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf

RUN echo "ServerName hortadav.codeccoop.org" >> /etc/apache2/apache2.conf
