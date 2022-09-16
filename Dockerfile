FROM php:7.2-apache

RUN apt-get update
RUN apt-get install -y python3 python3-pip
RUN apt-get install -y git
RUN python3 -m pip install --upgrade pip
COPY ./requirements.txt requirements.txt
RUN pip3 install -r requirements.txt
RUN rm requirements.txt

# Install Google Translate Client
ENV GOOGLE_APPLICATION_CREDENTIALS /var/www/html/config/auth/google.json
RUN chown www-data:www-data -R /usr/local/bin
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html
RUN chown www-data:www-data -R /var/www
WORKDIR /var/www/html

CMD chmod +x entrypoint.sh && ./entrypoint.sh