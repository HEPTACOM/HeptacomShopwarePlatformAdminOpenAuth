# ddev-generated
FROM minio/minio:RELEASE.2024-07-31T05-46-26Z

COPY ddev-entrypoint.sh /usr/bin/ddev-entrypoint.sh

RUN chmod +x /usr/bin/ddev-entrypoint.sh

ENTRYPOINT ["/usr/bin/ddev-entrypoint.sh"]
