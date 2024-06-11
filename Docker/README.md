## Install

1. Ensure you have docker installed and running before continue
2. Run following command to build docker containers and pull images

    ```sh
    sudo docker compose -f ./Docker/docker-compose.yml  up --build -d
    ```
3. Run following command to import default database dump

    ```sh
    docker exec -it wasabi-db sh -c "mysql -u root -proot wasabi < PS-Wasabi-Default.sql"
    ```

4. Browse into `http://localhost:8080` and wait for the installation
5. Generate an `.htacces` file by enabling `Apache optimizations` in `Advanced parameters` > `Performace`