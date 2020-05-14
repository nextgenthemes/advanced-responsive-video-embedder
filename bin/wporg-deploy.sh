#!/bin/bash
set -Eeuxo pipefail # https://vaneyckt.io/posts/safer_bash_scripts_with_set_euxo_pipefail/

# Copyright (c) Nicolas Jonas
# License GPL 3.0
#
# Based on: https://github.com/10up/action-wordpress-plugin-deploy/blob/develop/entrypoint.sh
# Copyright (c) 2019 Helen Hou-Sandi
# License MIT

if [ -f "./functions.php" ]; then
	readonly TYPE="theme"
elif [ -f "./${PWD##*/}.php" ]; then
	readonly TYPE="plugin"
else
	echo "Could not detect theme or plugin project"
	exit 1
fi

# Does it even make sense for VERSION to be editable in a workflow definition?
if [[ -z "$VERSION" ]]; then
	VERSION="${GITHUB_REF#refs/tags/}"
	VERSION="${VERSION#v}"
fi

readonly SLUG=${PWD##*/}
readonly ASSETS_DIR=".assets-wp-repo"
readonly DEPLOY_DIR="$PWD/build/deploy"
readonly SVN_DIR="$PWD/build/svn"
readonly SVN_URL="http://plugins.svn.wordpress.org/${SLUG}/"

rm -r --force "$SVN_DIR" "$DEPLOY_DIR"

# Check if the git tag actually exists https://stackoverflow.com/a/17793125/2847723
if ! git rev-parse "$VERSION^{tag}" --quiet; then
    echo "Git tag not found"
	exit 1
fi

# We assume the tag is already released on (wp.org) if the SVN tag dir exists
if svn ls "${SVN_URL}tags/$VERSION" --depth empty --quiet; then
	echo "TAG already exists on SVN remote"
	exit 1
fi

if grep '\* GitHub' "$SLUG".php ; then
	echo "Never push anything with Github updater headers to wp.org"
	exit 1
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

	echo "➤ Copying files..."

	unzip -q "$ZIPFILE" -d "$SVN_DIR"
	rm -rf trunk
	mv "$SLUG" trunk

	# Copy dotorg assets to /assets
	rsync -r --checksum "$PWD/$ASSETS_DIR/" assets/ --delete

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
	svn cp "trunk" "tags/$VERSION" --quiet

	svn status

	echo "➤ Committing files..."

	if [[ $DO_NOT_COMMIT ]]; then
		echo "➤ ENDING HERE FOR TESTING..."
		exit 1
	fi

	svn commit -m "Update to version $VERSION"

	echo "✓ Plugin deployed!"
)

rm -r --force "$SVN_DIR" "$DEPLOY_DIR"

