<?php
namespace Nextgenthemes\ARVE;

function get_default_aspect_ratio( $aspect_ratio, array $a ) {

	if ( ! empty( $aspect_ratio ) ) {
		return $aspect_ratio;
	}

	if ( ! empty( $a['oembed_data']->width ) && ! empty( $a['oembed_data']->height ) ) {
		return $a['oembed_data']->width . ':' . $a['oembed_data']->height;
	}

	$properties = get_host_properties();

	return $properties[ $a['provider'] ]['aspect_ratio'];
}

function aspect_ratio_fixes( $aspect_ratio, $provider, $mode ) {

	if ( 'dailymotionlist' === $provider ) {
		switch ( $mode ) {
			case 'normal':
			case 'lazyload':
				$aspect_ratio = '640:370';
				break;
			default:
				$aspect_ratio = $aspect_ratio;
				break;
		}
	}

	return $aspect_ratio;
}

function get_html5_attributes() {

	return [ 'mp4', 'm4v', 'webm', 'ogv', 'ogg', 'ogm' ];
}

function url_query_array( $url ) {

	$url = wp_parse_url( $url );

	if ( empty( $url['query'] ) ) {
		return [];
	}

	parse_str( $url['query'], $url_params );

	return $url_params;
}

function id_fixes( $id, $provider ) {

	if ( 'liveleak' === $provider
		&& ! \Nextgenthemes\Utils\starts_with( $id, 'i=' )
		&& ! \Nextgenthemes\Utils\starts_with( $id, 'f=' )
	) {
		$id = 'i=' . $id;
	}

	return $id;
}

function maxwidth_when_aligned( $maxwidth, $align ) {

	$options = options();

	if ( $maxwidth < 100 && in_array( $align, [ 'left', 'right', 'center' ], true ) ) {
		$maxwidth = (int) $options['align_maxwidth'];
	}

	return $maxwidth;
}

// phpcs:disable
function get_language_name_from_code( $lang_code ) {
	// This list is based on languages available from localize.drupal.org. See
	// http://localize.drupal.org/issues for information on how to add languages
	// there.
	//
	// The "Left-to-right marker" comments and the enclosed UTF-8 markers are to
	// make otherwise strange looking PHP syntax natural (to not be displayed in
	// right to left). See https://www.drupal.org/node/128866#comment-528929.
	$lang = [
		'af' => [ 'Afrikaans', 'Afrikaans' ],
		'am' => [ 'Amharic', 'አማርኛ' ],
		'ar' => [ 'Arabic', /* Left-to-right marker "‭" */ 'العربية', 'RTL' ],
		'ast' => [ 'Asturian', 'Asturianu' ],
		'az' => [ 'Azerbaijani', 'Azərbaycanca' ],
		'be' => [ 'Belarusian', 'Беларуская' ],
		'bg' => [ 'Bulgarian', 'Български' ],
		'bn' => [ 'Bengali', 'বাংলা' ],
		'bo' => [ 'Tibetan', 'བོད་སྐད་' ],
		'bs' => [ 'Bosnian', 'Bosanski' ],
		'ca' => [ 'Catalan', 'Català' ],
		'cs' => [ 'Czech', 'Čeština' ],
		'cy' => [ 'Welsh', 'Cymraeg' ],
		'da' => [ 'Danish', 'Dansk' ],
		'de' => [ 'German', 'Deutsch' ],
		'dz' => [ 'Dzongkha', 'རྫོང་ཁ' ],
		'el' => [ 'Greek', 'Ελληνικά' ],
		'en' => [ 'English', 'English' ],
		'en-x-simple' => [ 'Simple English', 'Simple English' ],
		'eo' => [ 'Esperanto', 'Esperanto' ],
		'es' => [ 'Spanish', 'Español' ],
		'et' => [ 'Estonian', 'Eesti' ],
		'eu' => [ 'Basque', 'Euskera' ],
		'fa' => [ 'Persian, Farsi', /* Left-to-right marker "‭" */ 'فارسی', 'RTL' ],
		'fi' => [ 'Finnish', 'Suomi' ],
		'fil' => [ 'Filipino', 'Filipino' ],
		'fo' => [ 'Faeroese', 'Føroyskt' ],
		'fr' => [ 'French', 'Français' ],
		'fy' => [ 'Frisian, Western', 'Frysk' ],
		'ga' => [ 'Irish', 'Gaeilge' ],
		'gd' => [ 'Scots Gaelic', 'Gàidhlig' ],
		'gl' => [ 'Galician', 'Galego' ],
		'gsw-berne' => [ 'Swiss German', 'Schwyzerdütsch' ],
		'gu' => [ 'Gujarati', 'ગુજરાતી' ],
		'he' => [ 'Hebrew', /* Left-to-right marker "‭" */ 'עברית', 'RTL' ],
		'hi' => [ 'Hindi', 'हिन्दी' ],
		'hr' => [ 'Croatian', 'Hrvatski' ],
		'ht' => [ 'Haitian Creole', 'Kreyòl ayisyen' ],
		'hu' => [ 'Hungarian', 'Magyar' ],
		'hy' => [ 'Armenian', 'Հայերեն' ],
		'id' => [ 'Indonesian', 'Bahasa Indonesia' ],
		'is' => [ 'Icelandic', 'Íslenska' ],
		'it' => [ 'Italian', 'Italiano' ],
		'ja' => [ 'Japanese', '日本語' ],
		'jv' => [ 'Javanese', 'Basa Java' ],
		'ka' => [ 'Georgian', 'ქართული ენა' ],
		'kk' => [ 'Kazakh', 'Қазақ' ],
		'km' => [ 'Khmer', 'ភាសាខ្មែរ' ],
		'kn' => [ 'Kannada', 'ಕನ್ನಡ' ],
		'ko' => [ 'Korean', '한국어' ],
		'ku' => [ 'Kurdish', 'Kurdî' ],
		'ky' => [ 'Kyrgyz', 'Кыргызча' ],
		'lo' => [ 'Lao', 'ພາສາລາວ' ],
		'lt' => [ 'Lithuanian', 'Lietuvių' ],
		'lv' => [ 'Latvian', 'Latviešu' ],
		'mg' => [ 'Malagasy', 'Malagasy' ],
		'mk' => [ 'Macedonian', 'Македонски' ],
		'ml' => [ 'Malayalam', 'മലയാളം' ],
		'mn' => [ 'Mongolian', 'монгол' ],
		'mr' => [ 'Marathi', 'मराठी' ],
		'ms' => [ 'Bahasa Malaysia', 'بهاس ملايو' ],
		'my' => [ 'Burmese', 'ဗမာစကား' ],
		'ne' => [ 'Nepali', 'नेपाली' ],
		'nl' => [ 'Dutch', 'Nederlands' ],
		'nb' => [ 'Norwegian Bokmål', 'Norsk, bokmål' ],
		'nn' => [ 'Norwegian Nynorsk', 'Norsk, nynorsk' ],
		'oc' => [ 'Occitan', 'Occitan' ],
		'pa' => [ 'Punjabi', 'ਪੰਜਾਬੀ' ],
		'pl' => [ 'Polish', 'Polski' ],
		'pt-pt' => [ 'Portuguese, Portugal', 'Português, Portugal' ],
		'pt-br' => [ 'Portuguese, Brazil', 'Português, Brasil' ],
		'ro' => [ 'Romanian', 'Română' ],
		'ru' => [ 'Russian', 'Русский' ],
		'sco' => [ 'Scots', 'Scots' ],
		'se' => [ 'Northern Sami', 'Sámi' ],
		'si' => [ 'Sinhala', 'සිංහල' ],
		'sk' => [ 'Slovak', 'Slovenčina' ],
		'sl' => [ 'Slovenian', 'Slovenščina' ],
		'sq' => [ 'Albanian', 'Shqip' ],
		'sr' => [ 'Serbian', 'Српски' ],
		'sv' => [ 'Swedish', 'Svenska' ],
		'sw' => [ 'Swahili', 'Kiswahili' ],
		'ta' => [ 'Tamil', 'தமிழ்' ],
		'ta-lk' => [ 'Tamil, Sri Lanka', 'தமிழ், இலங்கை' ],
		'te' => [ 'Telugu', 'తెలుగు' ],
		'th' => [ 'Thai', 'ภาษาไทย' ],
		'tr' => [ 'Turkish', 'Türkçe' ],
		'tyv' => [ 'Tuvan', 'Тыва дыл' ],
		'ug' => [ 'Uyghur', 'Уйғур' ],
		'uk' => [ 'Ukrainian', 'Українська' ],
		'ur' => [ 'Urdu', /* Left-to-right marker "‭" */ 'اردو', 'RTL' ],
		'vi' => [ 'Vietnamese', 'Tiếng Việt' ],
		'xx-lolspeak' => [ 'Lolspeak', 'Lolspeak' ],
		'zh-hans' => [ 'Chinese, Simplified', '简体中文' ],
		'zh-hant' => [ 'Chinese, Traditional', '繁體中文' ],
	];

	return $lang[ $lang_code ][1];
}
// phpcs:enable
