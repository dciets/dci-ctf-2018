#!/bin/sh

docker-compose down
sleep 2
sudo rm -r ./datadir/*

docker build -t bots ./bots
docker build -t flagbook ./flagbook
docker build -t sqlitutobasics ./sqlitutobasics
docker build -t sqlitutofilters ./sqlitutofilters
docker build -t spammy ./spammy

docker-compose up -d db
sleep 20
docker-compose up
