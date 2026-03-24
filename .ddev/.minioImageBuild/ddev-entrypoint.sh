#!/bin/sh
# ddev-generated
mkdir -p ~/.minio/certs/CAs
cp -ar /mnt/ddev-global-cache/mkcert/* ~/.minio/certs/CAs

/usr/bin/docker-entrypoint.sh "$@"
