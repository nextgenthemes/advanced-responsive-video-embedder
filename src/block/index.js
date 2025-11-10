/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */

import metadata from './block.json';
import { Edit } from './edit';

const { registerBlockType } = window.wp.blocks;

registerBlockType( metadata, {
	edit: Edit,
	// save() is intentionally omitted because we're using ServerSideRender
	// which handles the frontend rendering on the server
	save: () => null,
} );
