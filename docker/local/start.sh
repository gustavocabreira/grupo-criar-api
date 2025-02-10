#!/bin/sh

set -e

echo "Starting..."

export USER_ID=$(id -u)
export USER_GROUP=$(id -g)
export HOST_USER=$(whoami)

docker compose up -d --build --remove-orphans

echo "Started!"
