<?php

declare(strict_types = 1);

/**
 * @see https://github.com/wp-shortcake/Shortcake/blob/master/dev.php
 *
 * @param array{
 *     label: string,                     // UI label for the shortcode (required).
 *     listItemImage?: string,            // Dashicon class or HTML markup for the icon.
 *     post_type?: string[],              // Post types where the UI should appear.
 *     inner_content?: array{
 *         label: string,                 // Label for the inner‑content field.
 *         description?: string           // Optional description.
 *     },
 *     attrs: array<int, array{
 *         label: string,                 // Field label.
 *         attr: string,                  // Attribute name.
 *         type: string,                  // Field type (text, checkbox, textarea, radio, select,
 *                                        // email, url, number, date, post_select, attachment,
 *                                        // color, term_select, user_select, etc.).
 *         description?: string,          // Optional field description.
 *         encode?: bool,                 // Whether the value should be encoded.
 *         multiple?: bool,               // For fields that support multiple selections.
 *         libraryType?: string[],        // For attachment fields – allowed media types.
 *         addButton?: string,            // Text for the media library button.
 *         frameTitle?: string,           // Title for the media library modal.
 *         query?: array<string, mixed>,  // WP_Query args for post_select fields.
 *         taxonomy?: string,             // Taxonomy slug for term_select fields.
 *         options?: array<int, array{
 *             value?: string,            // Option value (empty string for placeholder).
 *             label?: string,            // Option label.
 *             // For optgroup support:
 *             options?: array<int, array{
 *                 value: string,
 *                 label: string
 *             }>
 *         }>,
 *         meta?: array<string, mixed>    // Arbitrary HTML attributes for the field.
 *     }>
 * } $shortcode_ui_args
 */
function shortcode_ui_register_for_shortcode( string $shortcode, array $shortcode_ui_args ): void {}
