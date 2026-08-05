#!/bin/bash
set -Eeuo pipefail

cd "$(dirname "$0")/.."

wp @stage arve block_json > src/block/block.json.new
mv src/block/block.json.new src/block/block.json

./bin/build-readme.php

pnpm run build-assets

../wp-settings/bin/build-and-update.sh
