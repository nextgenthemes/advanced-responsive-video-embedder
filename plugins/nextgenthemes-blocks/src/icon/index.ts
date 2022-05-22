import json from './block.json';
import Edit from './edit';
import save from './save';
import { registerBlockType } from '@wordpress/blocks';
import { code as icon } from '@wordpress/icons';
import './style.css';

export {};
declare global {
	interface Window {
		ngtIconsPattern: string;
	}
}

// Destructure the json file to get the name and settings for the block
// For more information on how this works, see: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/Destructuring_assignment
const { name } = json;

// Register the block
registerBlockType( name, {
	icon,
	edit: Edit,
} );
