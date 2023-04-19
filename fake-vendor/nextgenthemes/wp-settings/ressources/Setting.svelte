<script lang="ts">
	import { options, settings, sections, restURL, nonce, message } from "./store";
	import Labeltext from './Labeltext.svelte';
	const { log } = console;

	export let optionKey;
	const description = settings[optionKey].description;
	const label = settings[optionKey].label;
	const type = settings[optionKey].type;
	const selectOptions = settings[optionKey].options;

	const premiumUrl = 'https://nextgenthemes.com/plugins/arve-' + settings[optionKey].tag;
	const sectionLabel = sections[ settings[optionKey].tag ];

	let isSaving = false;
	let textInputTimeout;

	function debouncedSaveOptions() {
		if (textInputTimeout) {
			clearTimeout(textInputTimeout)
		}
		textInputTimeout = setTimeout(saveOptions, 300)
	}

	function saveOptions( refreshAfterSave = false ) {

		if ( isSaving ) {
			$message = 'trying to save too fast';
			return;
		}

		// set the state so that another save cannot happen while processing
		isSaving = true;

		$message = 'Saving...';

		// Make a POST request to the REST API route that we registered in our PHP file
		window.jQuery.ajax( {
			url: restURL + '/save',
			method: 'POST',
			data: $options,

			// set the nonce in the request header
			beforeSend( request ) {
				request.setRequestHeader( 'X-WP-Nonce', nonce );
			},

			// callback to run upon successful completion of our request
			success: () => {

				log('success');

				$message = 'Options saved';
				setTimeout( () => ( $message = '' ), 1000 );
			},

			// callback to run if our request caused an error
			error: ( errorData ) => {
				$message = errorData.responseText;
				refreshAfterSave = false;
			},

			// when our request is complete (successful or not), reset the state to indicate we are no longer saving
			complete: () => {

				log('complete');

				isSaving = false;
				if ( refreshAfterSave ) {
					refreshAfterSave = false;
					window.location.reload();
				}
			},
		} );
	}
</script>

<div>
	<p>
		{#if 'string' === type}

			<label>
				<Labeltext {optionKey} />
				<input type="text" class="large-text" bind:value={$options[optionKey]} on:input={ () => { debouncedSaveOptions() }} />
			</label>

		{:else if 'boolean' === type}

			<label>
				<input type=checkbox bind:checked={$options[optionKey]} on:change={ () => { saveOptions() }}>
				<Labeltext {optionKey} />
			</label>

		{:else if 'select' === type}

			<label>
				<Labeltext {optionKey} />
				<select bind:value={$options[optionKey]} on:change={ () => { saveOptions() }}>
					{#each Object.entries(settings[optionKey].options) as [ selectKey, selectLabel ] }
						<option value={selectKey}>
							{selectLabel}
						</option>
					{/each}
				</select>
			</label>

		{:else if 'integer' === type}

			<label>
				<Labeltext {optionKey} />
				<input type="number" bind:value={$options[optionKey]} on:input={ () => { debouncedSaveOptions() }} />
			</label>

		{:else}

			<h3>Error: {type} not implemented</h3>

		{/if}

	</p>
	{#if description }
		<p>
			{description}
		</p>
	{/if}
	<hr>
</div>

<style lang="scss">
</style>
