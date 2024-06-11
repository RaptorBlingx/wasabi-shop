## Install

1. Ensure you have docker installed before continue
2. Run following command to build docker containers and pull images

```sh
sudo docker compose -f ./Docker/docker-compose.yml  up --build -d
```
3. Run following command to import default database dump

```sh
docker exec -it wasabi-db sh -c "mysql -proot wasabi < PS-Wasabi-Default.sql"
```