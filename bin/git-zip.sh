#!/bin/bash
set -Eeuxo pipefail # https://vaneyckt.io/posts/safer_bash_scripts_with_set_euxo_pipefail/

readonly DIR=${PWD}
readonly DIRNAME=${PWD##*/}

if [ -z "$1" ]; then
	echo "need git ref as first argument to this script"
	exit 1
fi

if [ -z "${2+x}" ]; then
	readonly ZIPFILE="${PWD}/build/zip/$DIRNAME-$1.zip"
else
	readonly ZIPFILE="$2"
fi

readonly ZIPPATH="$(dirname "$ZIPFILE")"

mkdir -p "$ZIPPATH/$DIRNAME/"
# Nice clean zip thanks to export-ignore rules from .gitattributes
git archive --format=zip --prefix="$DIRNAME"/ --output="$ZIPFILE" "$1"

# Let composer install only whats needed for distribution, removing and dev deps like phpunit, phpcs ...
composer install --quiet --no-dev --no-interaction

npm install --quiet
npm run production --quiet
mkdir dist -p # just for plugin that do not use it yet we create a empty folder to prevent errors
mkdir vendor -p # just for plugin that do not use it yet we create a empty folder to prevent errors
cp -r {dist,vendor} "$ZIPPATH/$DIRNAME/"

(
	cd "$ZIPPATH"
	# Put the compressed files in dist and the php deps in vendor into our zip for ditribution
	zip -urq "$ZIPFILE" "$DIRNAME"/dist
	zip -urq "$ZIPFILE" "$DIRNAME"/vendor
	rm -rf "$DIRNAME"
)

if [ -z ${CI_COMMIT_REF_SLUG+x} ]; then # NOT Gitlab
	composer install --quiet --no-interaction # When running local, get all the dev deps back in. Composer has them cached.
fi

# one
