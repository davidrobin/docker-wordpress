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

Execute the following command from this new folder: `docker compose up -d`

The first init should take some minutes, then you should get a successful message.

## 3. Useful commands

Run this command to check if the port used by Docker is listening: `lsof -iTCP:8000 -sTCP:LISTEN`
