# docker-wordpress
Get a worpdress instance working on top of Docker, to easily export/import your up to date instance elsewhere.

## 1. Install Docker

### 1.1. On your computer:
[https://docs.docker.com/engine/install/debian/](https://docs.docker.com/get-started/get-docker/)

### 1.2. On a local/remote server:
[https://docs.docker.com/engine/install/](https://docs.docker.com/engine/install/) 

## 2. Install the docker image

Create a new folder, download the `docker-compose.yml` file in it

Set some variables in this file:

1. MYSQL_ROOT_PASSWORD
2. MYSQL_PASSWORD
3. WORDPRESS_DB_PASSWORD

Execute the following command from this new folder: `sudo docker compose up -d`

The first init should take some minutes, then you should get a successful message.

## 3. Useful commands

Run this command to check if the port used by Docker is listening: `sudo lsof -iTCP:8000 -sTCP:LISTEN`

## 4. (OPTIONAL) Nginx as a reverse proxy

### 4.1. Edit wp-config.php file in the container

#### 4.1.1. Access command line from the container

sudo docker container exec -it $(sudo docker container ls | grep "wordpress:latest" | cut -d" " -f1) bash

#### 4.1.2. Install nano or vim

`apt install nano` or `apt install vim`

#### 4.1.2. Edit /var/www/html/wp-config.php file

`vi /var/www/html/wp-config.php`

Add those lines (bear in mind to change URL with your proxified site URL):

```
...
define('FORCE_SSL_ADMIN', true);

if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $_SERVER['HTTPS'] = 'on';
  $_SERVER['SERVER_PORT'] = 443;
}

define('WP_HOME','https://example.com/site/');
define('WP_SITEURL','https://example.com/site/');
...
```
