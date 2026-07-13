#!/bin/bash
set -Eeuo pipefail

cd "$(dirname "$0")/.."

wp @sdev arve block_json > src/block/block.json.new
mv src/block/block.json.new src/block/block.json

./bin/build-readme.php

deno task build-assets

../wp-settings/bin/build-and-update.sh
