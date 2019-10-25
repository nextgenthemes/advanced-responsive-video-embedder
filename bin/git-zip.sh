#!/bin/bash
set -Eeuxo pipefail # https://vaneyckt.io/posts/safer_bash_scripts_with_set_euxo_pipefail/

readonly DIRNAME=${PWD##*/}
readonly GIT_WORKSPACE="$PWD"

if [ -f "./functions.php" ]; then
	readonly TYPE="theme"
elif [ -f "./$DIRNAME.php" ]; then
	readonly TYPE="plugin"
fi

if [ -z "${TYPE+x}" ]; then
	echo "Could not detect theme or plugin project"
	exit 1
fi

if [ -z "${DEPLOY_REF+x}" ]; then
	echo "Error! DEPLOY_REF env var not set."
	exit 1
fi

if [ -z "${DEPLOY_ZIPFILE+x}" ]; then

	if [ -z "${DEPLOY_REF_SHORT+x}" ]; then
		readonly DEPLOY_REF_SHORT="$DEPLOY_REF"
	fi

	readonly DEPLOY_ZIPFILE="$GIT_WORKSPACE/build/zip/$DIRNAME-$DEPLOY_REF_SHORT.zip"
	echo "DEPLOY_ZIPFILE env var not set. Using default"
fi

readonly DEPLOY_ZIPPATH="$(dirname "$DEPLOY_ZIPFILE")"

echo "DEPLOY_REF: $DEPLOY_REF"
echo "REPLOY_ZIPFILE: $DEPLOY_ZIPFILE"
echo "DEPLOY_ZIPPATH: $DEPLOY_ZIPPATH"

mkdir -p "$DEPLOY_ZIPPATH/$DIRNAME/"
# Nice clean zip thanks to export-ignore rules from .gitattributes
git archive --format=zip --prefix="$DIRNAME"/ --output="$DEPLOY_ZIPFILE" "$DEPLOY_REF"

# Let composer install only whats needed for distribution, removing and dev deps like phpunit, phpcs ...
composer install --quiet --no-dev --no-interaction

npm install --quiet
npm run production --quiet
mkdir dist -p # just for plugin that do not use it yet we create a empty folder to prevent errors
mkdir vendor -p # just for plugin that do not use it yet we create a empty folder to prevent errors
cp -r {dist,vendor} "$DEPLOY_ZIPPATH/$DIRNAME/"

(
	cd "$DEPLOY_ZIPPATH"
	# Put the compressed files in dist and the php deps in vendor into our zip for ditribution
	zip -urq "$DEPLOY_ZIPFILE" "$DIRNAME"/dist
	zip -urq "$DEPLOY_ZIPFILE" "$DIRNAME"/vendor
	rm -rf "$DIRNAME"
)

if [ -n "${DESKTOP_SESSION+set}" ]; then
	composer install --quiet --no-interaction # When running local, get all the dev deps back in. Composer has them cached.
fi
