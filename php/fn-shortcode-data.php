<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

/**
 * @return array <string, any>
 */
function url_query_array( string $url ): array {

	$url = wp_parse_url( $url );

	if ( empty( $url['query'] ) ) {
		return array();
	}

	parse_str( $url['query'], $url_params );

	return $url_params;
}

function get_language_name_from_code( string $lang_code ): string {
	// based on:
	// https://github.com/drupal/drupal/blob/9c7b7f7be26a1b9179010851e6800424c524acc9/core/lib/Drupal/Core/Language/LanguageManager.php#L224
	// Copyright Drupal and contributors
	// License GPL 2.0 or later
	//
	// The "Left-to-right marker" comments and the enclosed UTF-8 markers are to
	// make otherwise strange looking PHP syntax natural (to not be displayed in
	// right to left). See https://www.drupal.org/node/128866#comment-528929.
	$lang = [
		'af'          => [ 'Afrikaans', 'Afrikaans' ],
		'am'          => [ 'Amharic', 'አማርኛ' ],
		'ar'          => [ 'Arabic', /* RTL */ 'العربية', 'RTL' ],
		'ast'         => [ 'Asturian', 'Asturianu' ],
		'az'          => [ 'Azerbaijani', 'Azərbaycanca' ],
		'be'          => [ 'Belarusian', 'Беларуская' ],
		'bg'          => [ 'Bulgarian', 'Български' ],
		'bn'          => [ 'Bengali', 'বাংলা' ],
		'bo'          => [ 'Tibetan', 'བོད་སྐད་' ],
		'br'          => [ 'Breton', 'Breton' ],
		'bs'          => [ 'Bosnian', 'Bosanski' ],
		'ca'          => [ 'Catalan', 'Català' ],
		'cs'          => [ 'Czech', 'Čeština' ],
		'cy'          => [ 'Welsh', 'Cymraeg' ],
		'da'          => [ 'Danish', 'Dansk' ],
		'de'          => [ 'German', 'Deutsch' ],
		'dz'          => [ 'Dzongkha', 'རྫོང་ཁ' ],
		'el'          => [ 'Greek', 'Ελληνικά' ],
		'en'          => [ 'English', 'English' ],
		'en-gb'       => [ 'English, British', 'English, British' ],
		'en-x-simple' => [ 'Simple English', 'Simple English' ],
		'eo'          => [ 'Esperanto', 'Esperanto' ],
		'es'          => [ 'Spanish', 'Español' ],
		'et'          => [ 'Estonian', 'Eesti' ],
		'eu'          => [ 'Basque', 'Euskera' ],
		'fa'          => [ 'Persian, Farsi', /* RTL */ 'فارسی', 'RTL' ],
		'fi'          => [ 'Finnish', 'Suomi' ],
		'fil'         => [ 'Filipino', 'Filipino' ],
		'fo'          => [ 'Faeroese', 'Føroyskt' ],
		'fr'          => [ 'French', 'Français' ],
		'fy'          => [ 'Frisian, Western', 'Frysk' ],
		'ga'          => [ 'Irish', 'Gaeilge' ],
		'gd'          => [ 'Scots Gaelic', 'Gàidhlig' ],
		'gl'          => [ 'Galician', 'Galego' ],
		'gsw-berne'   => [ 'Swiss German', 'Schwyzerdütsch' ],
		'gu'          => [ 'Gujarati', 'ગુજરાતી' ],
		'haw'         => [ 'Hawaiian', 'ʻŌlelo Hawaiʻi' ],
		'he'          => [ 'Hebrew', /* RTL */ 'עברית', 'RTL' ],
		'hi'          => [ 'Hindi', 'हिन्दी' ],
		'hr'          => [ 'Croatian', 'Hrvatski' ],
		'ht'          => [ 'Haitian Creole', 'Kreyòl ayisyen' ],
		'hu'          => [ 'Hungarian', 'Magyar' ],
		'hy'          => [ 'Armenian', 'Հայերեն' ],
		'id'          => [ 'Indonesian', 'Bahasa Indonesia' ],
		'is'          => [ 'Icelandic', 'Íslenska' ],
		'it'          => [ 'Italian', 'Italiano' ],
		'ja'          => [ 'Japanese', '日本語' ],
		'jv'          => [ 'Javanese', 'Basa Java' ],
		'ka'          => [ 'Georgian', 'ქართული ენა' ],
		'kk'          => [ 'Kazakh', 'Қазақ' ],
		'km'          => [ 'Khmer', 'ភាសាខ្មែរ' ],
		'kn'          => [ 'Kannada', 'ಕನ್ನಡ' ],
		'ko'          => [ 'Korean', '한국어' ],
		'ku'          => [ 'Kurdish', 'Kurdî' ],
		'ky'          => [ 'Kyrgyz', 'Кыргызча' ],
		'lo'          => [ 'Lao', 'ພາສາລາວ' ],
		'lt'          => [ 'Lithuanian', 'Lietuvių' ],
		'lv'          => [ 'Latvian', 'Latviešu' ],
		'mg'          => [ 'Malagasy', 'Malagasy' ],
		'mk'          => [ 'Macedonian', 'Македонски' ],
		'ml'          => [ 'Malayalam', 'മലയാളം' ],
		'mn'          => [ 'Mongolian', 'монгол' ],
		'mr'          => [ 'Marathi', 'मराठी' ],
		'ms'          => [ 'Bahasa Malaysia', 'بهاس ملايو' ],
		'mt'          => [ 'Maltese', 'Malti' ],
		'my'          => [ 'Burmese', 'ဗမာစကား' ],
		'ne'          => [ 'Nepali', 'नेपाली' ],
		'nl'          => [ 'Dutch', 'Nederlands' ],
		'nb'          => [ 'Norwegian Bokmål', 'Norsk, bokmål' ],
		'nn'          => [ 'Norwegian Nynorsk', 'Norsk, nynorsk' ],
		'oc'          => [ 'Occitan', 'Occitan' ],
		'or'          => [ 'Odia', 'ଓଡିଆ' ],
		'os'          => [ 'Ossetian', 'Ossetian' ],
		'pa'          => [ 'Punjabi', 'ਪੰਜਾਬੀ' ],
		'pl'          => [ 'Polish', 'Polski' ],
		'prs'         => [ 'Persian, Afghanistan', /* RTL */ 'دری', 'RTL' ],
		'ps'          => [ 'Pashto', /* RTL */ 'پښتو', 'RTL' ],
		'pt'          => [ 'Portuguese, International', 'Português, Internacional' ],
		'pt-pt'       => [ 'Portuguese, Portugal', 'Português, Portugal' ],
		'pt-br'       => [ 'Portuguese, Brazil', 'Português, Brasil' ],
		'rhg'         => [ 'Rohingya', 'Ruáinga' ],
		'rm-rumgr'    => [ 'Rumantsch Grischun', 'Rumantsch Grischun' ],
		'ro'          => [ 'Romanian', 'Română' ],
		'ru'          => [ 'Russian', 'Русский' ],
		'rw'          => [ 'Kinyarwanda', 'Kinyarwanda' ],
		'sco'         => [ 'Scots', 'Scots' ],
		'se'          => [ 'Northern Sami', 'Sámi' ],
		'si'          => [ 'Sinhala', 'සිංහල' ],
		'sk'          => [ 'Slovak', 'Slovenčina' ],
		'sl'          => [ 'Slovenian', 'Slovenščina' ],
		'sq'          => [ 'Albanian', 'Shqip' ],
		'sr'          => [ 'Serbian', 'Српски' ],
		'sv'          => [ 'Swedish', 'Svenska' ],
		'sw'          => [ 'Swahili', 'Kiswahili' ],
		'ta'          => [ 'Tamil', 'தமிழ்' ],
		'ta-lk'       => [ 'Tamil, Sri Lanka', 'தமிழ், இலங்கை' ],
		'te'          => [ 'Telugu', 'తెలుగు' ],
		'th'          => [ 'Thai', 'ภาษาไทย' ],
		'tr'          => [ 'Turkish', 'Türkçe' ],
		'tyv'         => [ 'Tuvan', 'Тыва дыл' ],
		'ug'          => [ 'Uyghur', /* RTL */ 'ئۇيغۇرچە', 'RTL' ],
		'uk'          => [ 'Ukrainian', 'Українська' ],
		'ur'          => [ 'Urdu', /* RTL */ 'اردو', 'RTL' ],
		'vi'          => [ 'Vietnamese', 'Tiếng Việt' ],
		'xx-lolspeak' => [ 'Lolspeak', 'Lolspeak' ],
		'zh-hans'     => [ 'Chinese, Simplified', '简体中文' ],
		'zh-hant'     => [ 'Chinese, Traditional', '繁體中文' ],
	];

	return $lang[ $lang_code ][1];
}
