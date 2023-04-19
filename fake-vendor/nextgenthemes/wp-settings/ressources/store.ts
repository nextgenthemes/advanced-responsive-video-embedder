import { writable } from 'svelte/store';

declare global {
	interface Window {
		wp;
		jQuery;
	}
}

const url = new URL( window.location.href );
const pageQueryVal = url.searchParams.get( 'page' );

if ( ! pageQueryVal ) {
	throw 'Need page url arg';
}

const data = window[ pageQueryVal ];
export const settings = data.settings as Record< string, OptionProps >;
export const sections = data.sections as Record< string, string >;
export const options = writable( data.options );
export const nonce = data.nonce as string;
export const restURL = data.rest_url as string;
export const premiumSections = data.premium_sections as string[];

export const isSaving = writable( false );
export const message = writable( '' );

interface OptionProps {
	label: string;
	tag: string;
	type: string;
	default: number | string | boolean;
	description?: string;
	descriptionlink?: string;
	descriptionlinktext?: string;
	placeholder?: string;
	options?: Record< string, string >;
}
