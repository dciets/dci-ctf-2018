#!/bin/sh

docker build -t sqlitutobasics ./sqlitutobasics
docker build -t sqlitutofilters ./sqlitutofilters
docker build -t spammy ./spammy

docker-compose up -d
