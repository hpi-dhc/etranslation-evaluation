#!/bin/bash

docker stop $(cat docker.cid) && rm docker.cid
