#!/bin/bash

# Docker Compose management script for Laravel + Vue.js project

COMPOSE_FILE="docker-compose.yml"

case "$1" in
    start)
        echo "Creating folders..."
        mkdir -p ./www/backend/storage ./www/backend/bootstrap/cache
        echo "Setting up and starting Docker containers..."
        docker-compose -f $COMPOSE_FILE down && docker-compose -f $COMPOSE_FILE up -d --build
        echo "Containers started. Backend at http://localhost:8000, Frontend at http://localhost:8080"
        ;;
    stop)
        echo "Stopping Docker containers..."
        docker-compose -f $COMPOSE_FILE down
        echo "Containers stopped."
        ;;
    clean)
        echo "Stopping containers and removing images and volumes..."
        docker-compose -f $COMPOSE_FILE down --volumes --rmi all
        echo "Cleanup complete."
        ;;
    *)
        echo "Usage: $0 {start|stop|clean}"
        echo "  start: Build and start the containers"
        echo "  stop: Stop the containers"
        echo "  clean: Stop containers and remove all images and volumes"
        exit 1
        ;;
esac