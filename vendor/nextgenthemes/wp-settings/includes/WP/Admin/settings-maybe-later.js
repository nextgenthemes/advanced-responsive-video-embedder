eddLicenseActionGet: () => {
	const context = getContext();
	const config = getConfig();
	const url = new URL( context.eddStoreUrl ); // Site with EDD Software Licensing activated.

	const urlParams = new URLSearchParams( {
		edd_action: context.eddAction,
		license: state.options[ context.key ], // License key
		item_id: context.eddProductId, // Product ID
		url: config.homeUrl, // Domain the request is coming from.
	} );

	url.search = urlParams.toString();

	helpers.debugJson( url.toString() );

	fetch( url.toString(), {
		mode: 'no-cors',
	} )
		.then( ( response ) => {
			l( 'response', response );
			if ( response.ok ) {
				return response.json();
			}
			return Promise.reject( response );
		} )
		.then( ( data ) => {
			// Software Licensing has a valid response to parse
			console.log( 'Successful response', data );
		} )
		.catch( ( error ) => {
			// Error handling.
			console.log( 'Error', error );
		} );
},
eddLicenseActionPost: () => {
	const context = getContext();
	const config = getConfig();

	const formData = new FormData();
	formData.append( 'edd_action', context.eddAction ); // Valid actions are activate_license, deactivate_license, get_version, check_license
	formData.append( 'license', state.options[ context.key ] ); // License key
	formData.append( 'item_id', context.eddProductId ); // Product ID
	formData.append( 'url', config.homeUrl ); // If you disable URL checking you do not need this entry.

	// Site with Software Licensing activated.
	fetch( context.eddStoreUrl, {
		mode: 'no-cors',
		method: 'POST',
		body: formData,
	} )
		.then( ( response ) => {
			if ( response.ok ) {
				return response.json();
			}
			return Promise.reject( response );
		} )
		.then( ( data ) => {
			// Software Licensing has a valid response to parse
			console.log( 'Successful response', data );
		} )
		.catch( ( error ) => {
			// Error handling.
			console.log( 'Error', error );
		} );
},
