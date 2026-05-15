#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${1:-http://127.0.0.1:80}"
ZIP_PATH="${2:-./.build/store-build/KskHeptacomAdminOpenAuth-HEAD.zip}"

export ACCESS_TOKEN
ACCESS_TOKEN=$(curl -X POST "${BASE_URL}/api/oauth/token" \
    --fail --silent --show-error \
    -H 'Accept: application/json' \
    -H 'Content-Type: application/json' \
    --data-raw '{"grant_type":"password","client_id":"administration","scopes":"write","username":"admin","password":"shopware"}' \
    | jq -r .access_token)

curl -X POST --fail -o /dev/stderr -v \
    -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' \
    "${BASE_URL}/api/_action/extension/upload" -F "file=@${ZIP_PATH}"

curl -X POST --fail -o /dev/stderr -v \
    -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' -H 'Content-Type: application/json' \
    "${BASE_URL}/api/_action/extension/refresh"

curl -X POST -v \
    -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' -H 'Content-Type: application/json' \
    --trace-ascii curl-trace.log -o curl-response.log \
    "${BASE_URL}/api/_action/extension/install/plugin/KskHeptacomAdminOpenAuth"

curl -X POST --fail -o /dev/stderr -v \
    -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' -H 'Content-Type: application/json' \
    "${BASE_URL}/api/_action/extension/refresh"

curl -X PUT --fail -o /dev/stderr -v \
    -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' -H 'Content-Type: application/json' \
    "${BASE_URL}/api/_action/extension/activate/plugin/KskHeptacomAdminOpenAuth"

PROVIDERS=$(curl --fail --silent --show-error \
    -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' \
    "${BASE_URL}/api/_action/heptacom_admin_open_auth_provider/list" \
    | jq -r '.data[]')
echo "Available providers: ${PROVIDERS}"
test -n "${PROVIDERS}"

for PROVIDER_KEY in ${PROVIDERS}; do
    echo "Creating client for provider: ${PROVIDER_KEY}"
    CLIENT_ID=$(curl --fail --silent --show-error -X POST \
        -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' -H 'Content-Type: application/json' \
        --data-raw "{\"provider_key\":\"${PROVIDER_KEY}\"}" \
        "${BASE_URL}/api/_action/heptacom_admin_open_auth_provider/factorize" \
        | jq -r '.data.id')
    echo "Created client ID: ${CLIENT_ID}"
    test -n "${CLIENT_ID}"
    test "${CLIENT_ID}" != "null"
    curl --fail --silent --show-error -X PATCH \
        -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' -H 'Content-Type: application/json' \
        --data-raw '{"active":true,"login":true}' \
        "${BASE_URL}/api/heptacom-admin-open-auth-client/${CLIENT_ID}"
done

CLIENT_LIST=$(curl --fail --silent --show-error \
    -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' \
    "${BASE_URL}/api/_admin/open-auth/client/list" \
    | jq -r '.data[].id')
echo "Clients in list: ${CLIENT_LIST}"

PROVIDER_COUNT=$(echo "${PROVIDERS}" | wc -w | tr -d ' ')
CLIENT_COUNT=$(echo "${CLIENT_LIST}" | wc -w | tr -d ' ')
echo "Expected ${PROVIDER_COUNT} clients, found ${CLIENT_COUNT}"
test "${PROVIDER_COUNT}" -eq "${CLIENT_COUNT}"
