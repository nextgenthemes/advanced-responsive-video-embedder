#!/bin/bash
set -Eeuox pipefail

cd "$(dirname "$0")/.."

deno task build

git add build/
git commit -m "build" || true

opencode run --model opencode/big-pickle 'commit package'
