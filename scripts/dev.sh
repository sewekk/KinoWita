#!/usr/bin/env bash

trap 'kill 0' SIGINT SIGTERM EXIT

php bin/console tailwind:build --watch &
symfony server:start