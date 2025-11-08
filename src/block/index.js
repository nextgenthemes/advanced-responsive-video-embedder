/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */

// Import the block.json file
import metadata from './block.json';
// Import the editor component
import { Edit } from './edit';

// Register the block
const { registerBlockType } = window.wp.blocks;

// Note: We're not using a save.js file because this block uses ServerSideRender
// to dynamically render the block on the frontend. This is necessary because
// the video embed functionality requires server-side processing to generate
// the appropriate iframe and responsive markup.
registerBlockType(metadata, {
    edit: Edit,
    // save() is intentionally omitted because we're using ServerSideRender
    // which handles the frontend rendering on the server
    save: () => null
});
