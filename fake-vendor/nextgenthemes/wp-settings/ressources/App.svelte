<script lang="ts">
	import { options, settings, sections, message } from "./store";
	import { onMount } from 'svelte';
	import Setting from "./Setting.svelte";

	let activeSection = 'all';
	
	function showSection( sectionKey: string ) {
		activeSection = sectionKey;
	}

	onMount(async () => {
		injectFromTemplates();
	});

	function injectFromTemplates() {

		const templates = document.querySelectorAll<HTMLTemplateElement>( 'template[data-ngt-svelte-target]');

		templates.forEach( ( template ) => {

			const target = document.querySelector( template.dataset.ngtSvelteTarget );

			if ( ! target ) {
				return;
			}

			if ( template.dataset.append ) {
				target.append(template.content.cloneNode(true));
			} else {
				target.prepend(template.content.cloneNode(true));
			}
		} );
	}

	function uploadImage( optionKey ) {
		const vueThis = this;
		const image = window.wp
			.media( {
				title: 'Upload Image',
				multiple: false,
			} )
			.open()
			.on( 'select', function () {
				// This will return the selected image from the Media Uploader, the result is an object
				const uploadedImage = image.state().get( 'selection' ).first();
				// We convert uploadedImage to a JSON object to make accessing it easier
				const attachmentID = uploadedImage.toJSON().id;
				$options[ optionKey ] = attachmentID;
			} );
	}
</script>

<h2 class="nav-tab-wrapper">
	<button class="nav-tab" class:nav-tab-active={ 'all' === activeSection } on:click={ () => showSection( 'all' ) }>
		All
	</button>
	{#each Object.entries(sections) as [ sectionKey, sectionLabel ] }
		<button class="nav-tab" class:nav-tab-active={ sectionKey === activeSection } on:click={ () => showSection( sectionKey ) }>
			{sectionLabel}
		</button>
	{/each}
</h2>

<div class="ngt-settings-grid">

	<div class="ngt-settings-grid__content">

		{#each Object.entries(sections) as [ sectionKey, sectionLabel ] }
		
			<div class="ngt-section ngt-section--{sectionKey}" hidden={ 'all' != activeSection && sectionKey != activeSection }>
		
				<h1 hidden={ 'all' != activeSection }>{sectionLabel}</h1>
		
				{#each Object.keys($options) as optionKey }

					{#if settings[optionKey].tag === sectionKey}
			
						<Setting {optionKey} />

					{/if}<!-- currect section -->
				
				{/each}
		
			</div>
				
		{/each}<!-- sections -->

		<p>
			<strong>{$message}&nbsp;</strong>
		</p>

		<p>{JSON.stringify($options, 0, 2)}</p>
		
	</div>

	<div class="ngt-settings-grid__sidebar"></div>
</div>

<style lang="scss">
	[hidden] {
		display: none !important;
	}

	:global(.wrap--nextgenthemes) {
		max-width: 1100px;
		margin-right: auto;
		margin-left: auto;

		.nav-tab {
			cursor: pointer;
		}

		.nav-tab:not(.nav-tab--ngt-highlight) {
			font-weight: 400;
		}
	}

	.ngt-settings-grid {
		display: grid;
		grid-template-areas:
			"content"
			"sidebar";

		@media (min-width: 750px) {
			grid-column-gap: 2rem;
			grid-template-areas: "content sidebar";
			grid-template-columns: minmax(300px, 1fr) 300px;
		}

		@media (min-width: 1200px) {
			grid-column-gap: 3rem;
			grid-template-columns: minmax(300px, 1fr) 420px;
		}
	}

	.ngt-settings-grid__content {
		grid-area: content;
	}

	.ngt-settings-grid__sidebar {
		grid-area: sidebar;
	}

	:global(.ngt-sidebar-box) {
		padding: 1rem;
		margin-top: 1rem;
		margin-bottom: 1rem;
		background: #fff;
		border-left: 4px solid hsl(125.5, 44%, 49%);
		box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);

		h3:first-child {
			margin-top: 0;
		}

		ul {
			padding-left: 1em;
			list-style: square;
		}

		li {
			padding-left: .7em;
			margin-bottom: 1em;
		}

		@media (min-width: 1200px) {
			padding: 3rem;
		}
	}

	:global(.ngt-debug-textarea) {
		width: 100%;
		height: 1000px;
		max-height: calc(100vh - 150px);
		margin-top: 2em;
		font-family: monospace;
	}
</style>

