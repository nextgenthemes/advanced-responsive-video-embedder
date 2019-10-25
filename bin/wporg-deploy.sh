#!/bin/bash
set -Eeuxo pipefail # https://vaneyckt.io/posts/safer_bash_scripts_with_set_euxo_pipefail/

# Copyright (c) Nicolas Jonas
# License GPL 3.0
#
# Based on: https://github.com/10up/action-wordpress-plugin-deploy/blob/develop/entrypoint.sh
# Copyright (c) 2019 Helen Hou-Sandi
# License MIT

readonly GIT_WORKSPACE="$PWD"
readonly DIRNAME=${PWD##*/}
readonly ASSETS_DIR=".assets-wp-repo"
readonly DEPLOY_DIR="$GIT_WORKSPACE/build/deploy"
readonly SVN_DIR="$GIT_WORKSPACE/build/svn"
readonly SVN_URL="http://plugins.svn.wordpress.org/$DIRNAME/"

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
	echo "need DEPLOY_REF env var not set"
	exit 1
fi

if [ -z "${DEPLOY_ZIPFILE+x}" ]; then
	readonly DEPLOY_ZIPFILE="$GIT_WORKSPACE/build/zip/$DIRNAME-$DEPLOY_REF.zip"
	echo "ZIPFILE env var not set. Using default $DEPLOY_ZIPFILE"
fi

if [ ! -f "$DEPLOY_ZIPFILE" ]; then
	echo "Zip $DEPLOY_ZIPFILE does not exist"
	exit 1
fi

rm -r --force "$SVN_DIR" "$DEPLOY_DIR"

# Check if the git tag actually exists https://stackoverflow.com/a/17793125/2847723
if ! git rev-parse "$DEPLOY_REF^{tag}" --quiet; then
    echo "WARNING Git tag not found"
fi

# We assume the tag is already released on (wp.org) if the SVN tag dir exists
if svn ls "${SVN_URL}tags/$DEPLOY_REF" --depth empty --quiet; then
	echo "TAG already exists on SVN remote"
	exit 1
fi

if grep '\* GitHub' "$DIRNAME".php ; then
	echo "Never push anything with Github updater headers to wp.org"
	#exit 1
fi

echo "➤ Checking out .org repository..."
# Tagging will be handled on the SVN level
mkdir --parents "$SVN_DIR"

(
cd "$SVN_DIR"

# Checkout just trunk and assets for efficiency
svn checkout --depth immediates "$SVN_URL" "$SVN_DIR"
svn update --set-depth infinity assets
svn update --set-depth infinity trunk

echo "➤ Extract zip to trunk ..."

unzip -q "$DEPLOY_ZIPFILE" -d "$SVN_DIR"
rm -rf trunk
mv "$DIRNAME" trunk

echo "➤ Move $ASSETS_DIR to svn /assets ..."

# Just move the assets to the svn dir
rm -rf assets 
mv "$GIT_WORKSPACE/$ASSETS_DIR" assets

# Fix screenshots getting force downloaded when clicking them
# https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/
svn propset svn:mime-type image/png assets/*.png || true
svn propset svn:mime-type image/jpeg assets/*.jpg || true

# Add everything and commit to SVN
# The force flag ensures we recurse into subdirectories even if they are already added
echo "➤ Preparing files..."
svn add . --force --quiet

# SVN delete all deleted files
svn status | grep '^\!' | sed 's/! *//' | xargs -I% svn rm % --quiet || true

# Copy tag locally to make this a single commit
echo "➤ Copying tag..."
svn cp "trunk" "tags/$DEPLOY_REF" --quiet

svn status

echo "➤ Committing files..."

echo "➤ ENDING HERE FOR TESTING..."; exit 0

svn commit -m "Update to version $DEPLOY_REF"

echo "✓ Plugin deployed!"
)

rm -r --force "$SVN_DIR" "$DEPLOY_DIR"

