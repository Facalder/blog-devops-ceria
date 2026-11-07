#!/usr/bin/env bash

set -euo pipefail

GITHUB_TOKEN="${GITHUB_TOKEN:-${GH_TOKEN:-}}"
OWNER="${GITHUB_OWNER:-Facalder}"
REPO="${GITHUB_REPO:-blog-devops-ceria}"

if [ -z "$GITHUB_TOKEN" ]; then
    echo "Error: GITHUB_TOKEN or GH_TOKEN environment variable is not set."
    echo "Usage: GITHUB_TOKEN=your_pat ./scripts/setup-rulesets.sh"
    exit 1
fi

apply_ruleset() {
    local file="$1"
    local name="$2"

    echo "Applying ruleset: $name from $file..."
    local response
    response=$(curl -s -w "\n%{http_code}" -X POST \
        -H "Accept: application/vnd.github+json" \
        -H "Authorization: Bearer $GITHUB_TOKEN" \
        -H "X-GitHub-Api-Version: 2022-11-28" \
        "https://api.github.com/repos/$OWNER/$REPO/rulesets" \
        -d @"$file")

    local status_code
    status_code=$(echo "$response" | tail -n1)
    local body
    body=$(echo "$response" | sed '$d')

    if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
        echo "Ruleset '$name' successfully created (HTTP $status_code)."
    else
        echo "Failed to create ruleset '$name' (HTTP $status_code):"
        echo "$body"
    fi
}

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"

apply_ruleset "$ROOT_DIR/.github/rulesets/main-ruleset.json" "Main Branch Protection"
apply_ruleset "$ROOT_DIR/.github/rulesets/dev-ruleset.json" "Dev Branch Protection"
