#!/bin/bash
set -Eeuo pipefail

wp @sdev arve block_json > src/block/block.json.new
mv src/block/block.json.new src/block/block.json

./bin/build-readme.php

if [[ "${npm_execpath:-}" == *deno ]]; then
	deno run build-assets
else
	pnpm run build-assets
fi
