#!/bin/bash



n=1

while true; do
    echo $n
    ((n++))
    sleep 1
    if [ $n -ge 11 ]
    then
        break
    fi
done