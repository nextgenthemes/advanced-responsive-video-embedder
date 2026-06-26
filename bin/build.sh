#!/bin/bash
set -Eeuo pipefail

wp @sdev arve block_json > src/block/block.json.new
mv src/block/block.json.new src/block/block.json
./bin/build-readme.php
pnpm run --filter @nextgenthemes/advanced-responsive-video-embedder build:assets
