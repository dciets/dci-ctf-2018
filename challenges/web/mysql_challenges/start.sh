#!/bin/sh

docker build -t sqlitutobasics ./sqlitutobasics
docker build -t sqlitutofilters ./sqlitutofilters
docker build -t spammy ./spammy
docker build -t closedsource ./closed_source

docker-compose up -d