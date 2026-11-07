#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"

if [ -z "${GITHUB_TOKEN:-}" ] && [ -f "$ROOT_DIR/.env" ]; then
    TOKEN_FROM_ENV=$(grep -E '^(GITHUB_TOKEN|GH_TOKEN)=' "$ROOT_DIR/.env" | head -n1 | cut -d'=' -f2- | tr -d '"' | tr -d "'" | tr -d ' ')
    if [ -n "$TOKEN_FROM_ENV" ]; then
        GITHUB_TOKEN="$TOKEN_FROM_ENV"
    fi
fi

GITHUB_TOKEN="${GITHUB_TOKEN:-${GH_TOKEN:-}}"
OWNER="${GITHUB_OWNER:-Facalder}"
REPO="${GITHUB_REPO:-blog-devops-ceria}"

if [ -z "$GITHUB_TOKEN" ]; then
    echo "Error: GITHUB_TOKEN or GH_TOKEN is not set and was not found in .env."
    echo "Usage: GITHUB_TOKEN=your_pat ./scripts/setup-rulesets.sh"
    exit 1
fi

get_existing_ruleset_id() {
    local target_name="$1"
    curl -s -H "Accept: application/vnd.github+json" \
        -H "Authorization: Bearer $GITHUB_TOKEN" \
        -H "X-GitHub-Api-Version: 2022-11-28" \
        "https://api.github.com/repos/$OWNER/$REPO/rulesets" | \
        python3 -c "import sys, json
data = json.load(sys.stdin)
target = '$target_name'
found_id = ''
if isinstance(data, list):
    for r in data:
        if r.get('name') == target:
            found_id = str(r.get('id'))
            break
print(found_id)
"
}

apply_ruleset() {
    local file="$1"
    local name="$2"

    local existing_id
    existing_id=$(get_existing_ruleset_id "$name")

    local response
    local status_code
    local body

    if [ -n "$existing_id" ]; then
        echo "Ruleset '$name' already exists with ID: $existing_id. Updating via PUT..."
        response=$(curl -s -w "\n%{http_code}" -X PUT \
            -H "Accept: application/vnd.github+json" \
            -H "Authorization: Bearer $GITHUB_TOKEN" \
            -H "X-GitHub-Api-Version: 2022-11-28" \
            "https://api.github.com/repos/$OWNER/$REPO/rulesets/$existing_id" \
            -d @"$file")
    else
        echo "Creating new ruleset '$name' via POST..."
        response=$(curl -s -w "\n%{http_code}" -X POST \
            -H "Accept: application/vnd.github+json" \
            -H "Authorization: Bearer $GITHUB_TOKEN" \
            -H "X-GitHub-Api-Version: 2022-11-28" \
            "https://api.github.com/repos/$OWNER/$REPO/rulesets" \
            -d @"$file")
    fi

    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')

    if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
        echo "Ruleset '$name' successfully synchronized (HTTP $status_code)."
    else
        echo "Failed to synchronize ruleset '$name' (HTTP $status_code):"
        echo "$body"
        exit 1
    fi
}

apply_ruleset "$ROOT_DIR/.github/rulesets/main-ruleset.json" "Protect main branch"
apply_ruleset "$ROOT_DIR/.github/rulesets/dev-ruleset.json" "Protect dev branch"
