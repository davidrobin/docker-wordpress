# docker-wordpress
Get a worpdress instance working on top of Docker, to easily export/import your up to date instance elsewhere.

## 1. Install Docker

### 1.1. On your computer:
[https://docs.docker.com/get-started/get-docker/](https://docs.docker.com/get-started/get-docker/) 

### 1.2. On a local/remote server:
[https://docs.docker.com/engine/install/](https://docs.docker.com/engine/install/) 

## 2. Install the docker image

Create a new folder, download the `docker-compose.yml` file in it

Set some variables in this file:

1. DB_ROOT_PASSWORD
2. DB_PASSWORD

Execute the following command from this new folder: `sudo docker compose up -d`

The first init should take some minutes, then you should get a successful message.

## 3. Useful commands

### 3.1. Docker commands

List existing images\
 `sudo docker image list`

List existing containers\
 `sudo docker container list`
 
List existing volumes\
 `sudo docker volume list`

Update your Docker applications following a change in your `docker-compose.yml` file\
`sudo docker compose pull && sudo docker compose up -d`

Delete containters & volumes associated from `docker-compose.yml` file\
`sudo docker compose down -v`

Run shell prompt from WordPress container\
`sudo docker exec -it $(sudo docker ps -aqf "name=^wordpress-application") sh`

Run shell prompt from MariaDB container\
`sudo docker exec -it $(sudo docker ps -aqf "name=^wordpress-database") sh`

Run shell prompt from WP-CLI container\
`sudo docker compose run --rm wp-cli bash`

Run WP-CLI command from WP-CLI container\
`sudo docker compose run --rm wp-cli wp core install --title="Headless WordPress" --url="http://localhost:8000" --admin_user="admin" --admin_email="admin@local.host"`

### 3.2.Generic commands
Check if the port used by Docker is listening\
`sudo lsof -iTCP:8000 -sTCP:LISTEN`

## 4. (OPTIONAL) Nginx as a reverse proxy

### 4.1. Update your Nginx setup

Adapt your setup from `nginx-location.conf` file

### 4.2. Edit wp-config.php file in the container

Execute this command to add necessary lines to `wp-config.php` file
> Adapt container name & WordPress site URL

`sudo docker container exec -it $(sudo docker ps -aqf "name=^wp-app_...") bash -c "wpUrl='https://domain.tld/wp-subdomain/'; sed -i \"2i define('WP_HOME', '\$wpUrl');\ndefine('WP_SITEURL', '\$wpUrl');\" /var/www/html/wp-config.php"`

Check that lines have been correctly added in `wp-config.php` file\
` sudo docker container exec -it $(sudo docker ps -aqf "name=^wp-app_fr-chez-ali") head /var/www/html/wp-config.php`

or

Execute this command\
`sudo vi /var/lib/docker/volumes/wp_wp_data/_data/wp-config.php`

Then add those lines (bear in mind to change URL with your proxified site URL):

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

### 4.3. Give ownership to www-data user for html folder
In order to avoid filesystem errors when installing extensions, for example

Access command line from the container\
`sudo docker container exec -it $(sudo docker ps -aqf "name=^wordpress-application") sh`

Give ownership recursively\
`chown -R www-data:www-data /var/www/html`

## 5. (OPTIONAL) Update volume content properly
In case you've made change locally and you want to update your applications on your remote server for instance

### On source host

Create Wordpress application's volume archive\
`sudo docker cp $(sudo docker ps -qf "name=^wordpress-application"):/var/www/html . && tar -zc --exclude='wp-config.php' -f wordpress_application.tar.gz html`

Create MariaDB application's volume archive\
`sudo docker cp $(sudo docker ps -qf "name=^wordpress-database"):/var/lib/mysql . && sudo tar -zcf wordpress_database.tar.gz mysql`

Upload archives to destination host\
`rsync -avhP wordpress_application.tar.gz wordpress_database.tar.gz -e "ssh -p $SSH_PORT" drbn@$SSH_DEST_IP_OR_HOSTNAME:`

### On destination host

Expand WordPress application's volume archive\
`sudo tar -xf wordpress_application.tar.gz && sudo docker cp html $(sudo docker ps -qf "name=^wordpress-application"):/var/www`

Expand MariaDB application's volume archive\
`sudo tar -xf wordpress_database.tar.gz && sudo docker cp mysql $(sudo docker ps -qf "name=^wordpress-database"):/var/lib`

Give ownership recursively to wordpress aplication's volume\
`sudo docker exec $(sudo docker ps -qf "name=^wordpress-application") chown -R www-data:www-data /var/www/html`

Finally, restart containers\
`sudo docker restart $(sudo docker ps -qf "name=^wordpress-application") && sudo docker restart $(sudo docker ps -qf "name=^wordpress-database")`

## 6. (OPTIONAL) Share a local folder from host <> container

#### 6.1. Edit docker-compose.yml file
Add the line below, replace `{host_path}:{container_path}` from `docker-compose.yml` file:
```
...
wordpress:
    image: wordpress:latest
    volumes:
      - wp_data:/var/www/html
      - {host_path}:{container_path}
    ports:
...
```

#### 6.2. Update applications

Run this command from the folder containing `docker-compose.yml` file:

`sudo docker compose pull && sudo docker compose up -d`

## 7. Appendix

#### 7.1. Folders on Linux

WordPress volume\
`/var/lib/docker/volumes/*_wp_data/_data/`

MariaDB volume\
`/var/lib/docker/volumes/*_db_data/_data/`
