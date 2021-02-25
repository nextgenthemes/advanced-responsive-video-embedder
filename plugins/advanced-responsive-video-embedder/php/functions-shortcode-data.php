<?php
namespace Nextgenthemes\ARVE;

function url_query_array( $url ) {

	$url = wp_parse_url( $url );

	if ( empty( $url['query'] ) ) {
		return array();
	}

	parse_str( $url['query'], $url_params );

	return $url_params;
}

function get_language_name_from_code( $lang_code ) {
	// based on:
	// https://github.com/drupal/drupal/blob/9c7b7f7be26a1b9179010851e6800424c524acc9/core/lib/Drupal/Core/Language/LanguageManager.php#L224
	// Copyright Drupal and contributors
	// License GPL 2.0 or later
	//
	// The "Left-to-right marker" comments and the enclosed UTF-8 markers are to
	// make otherwise strange looking PHP syntax natural (to not be displayed in
	// right to left). See https://www.drupal.org/node/128866#comment-528929.
	$lang = array(
		'af'          => array( 'Afrikaans', 'Afrikaans' ),
		'am'          => array( 'Amharic', 'አማርኛ' ),
		'ar'          => array( 'Arabic', /* Left-to-right marker "‭" */ 'العربية', 'RTL' ),
		'ast'         => array( 'Asturian', 'Asturianu' ),
		'az'          => array( 'Azerbaijani', 'Azərbaycanca' ),
		'be'          => array( 'Belarusian', 'Беларуская' ),
		'bg'          => array( 'Bulgarian', 'Български' ),
		'bn'          => array( 'Bengali', 'বাংলা' ),
		'bo'          => array( 'Tibetan', 'བོད་སྐད་' ),
		'bs'          => array( 'Bosnian', 'Bosanski' ),
		'ca'          => array( 'Catalan', 'Català' ),
		'cs'          => array( 'Czech', 'Čeština' ),
		'cy'          => array( 'Welsh', 'Cymraeg' ),
		'da'          => array( 'Danish', 'Dansk' ),
		'de'          => array( 'German', 'Deutsch' ),
		'dz'          => array( 'Dzongkha', 'རྫོང་ཁ' ),
		'el'          => array( 'Greek', 'Ελληνικά' ),
		'en'          => array( 'English', 'English' ),
		'en-x-simple' => array( 'Simple English', 'Simple English' ),
		'eo'          => array( 'Esperanto', 'Esperanto' ),
		'es'          => array( 'Spanish', 'Español' ),
		'et'          => array( 'Estonian', 'Eesti' ),
		'eu'          => array( 'Basque', 'Euskera' ),
		'fa'          => array( 'Persian, Farsi', /* Left-to-right marker "‭" */ 'فارسی', 'RTL' ),
		'fi'          => array( 'Finnish', 'Suomi' ),
		'fil'         => array( 'Filipino', 'Filipino' ),
		'fo'          => array( 'Faeroese', 'Føroyskt' ),
		'fr'          => array( 'French', 'Français' ),
		'fy'          => array( 'Frisian, Western', 'Frysk' ),
		'ga'          => array( 'Irish', 'Gaeilge' ),
		'gd'          => array( 'Scots Gaelic', 'Gàidhlig' ),
		'gl'          => array( 'Galician', 'Galego' ),
		'gsw-berne'   => array( 'Swiss German', 'Schwyzerdütsch' ),
		'gu'          => array( 'Gujarati', 'ગુજરાતી' ),
		'he'          => array( 'Hebrew', /* Left-to-right marker "‭" */ 'עברית', 'RTL' ),
		'hi'          => array( 'Hindi', 'हिन्दी' ),
		'hr'          => array( 'Croatian', 'Hrvatski' ),
		'ht'          => array( 'Haitian Creole', 'Kreyòl ayisyen' ),
		'hu'          => array( 'Hungarian', 'Magyar' ),
		'hy'          => array( 'Armenian', 'Հայերեն' ),
		'id'          => array( 'Indonesian', 'Bahasa Indonesia' ),
		'is'          => array( 'Icelandic', 'Íslenska' ),
		'it'          => array( 'Italian', 'Italiano' ),
		'ja'          => array( 'Japanese', '日本語' ),
		'jv'          => array( 'Javanese', 'Basa Java' ),
		'ka'          => array( 'Georgian', 'ქართული ენა' ),
		'kk'          => array( 'Kazakh', 'Қазақ' ),
		'km'          => array( 'Khmer', 'ភាសាខ្មែរ' ),
		'kn'          => array( 'Kannada', 'ಕನ್ನಡ' ),
		'ko'          => array( 'Korean', '한국어' ),
		'ku'          => array( 'Kurdish', 'Kurdî' ),
		'ky'          => array( 'Kyrgyz', 'Кыргызча' ),
		'lo'          => array( 'Lao', 'ພາສາລາວ' ),
		'lt'          => array( 'Lithuanian', 'Lietuvių' ),
		'lv'          => array( 'Latvian', 'Latviešu' ),
		'mg'          => array( 'Malagasy', 'Malagasy' ),
		'mk'          => array( 'Macedonian', 'Македонски' ),
		'ml'          => array( 'Malayalam', 'മലയാളം' ),
		'mn'          => array( 'Mongolian', 'монгол' ),
		'mr'          => array( 'Marathi', 'मराठी' ),
		'ms'          => array( 'Bahasa Malaysia', 'بهاس ملايو' ),
		'my'          => array( 'Burmese', 'ဗမာစကား' ),
		'ne'          => array( 'Nepali', 'नेपाली' ),
		'nl'          => array( 'Dutch', 'Nederlands' ),
		'nb'          => array( 'Norwegian Bokmål', 'Norsk, bokmål' ),
		'nn'          => array( 'Norwegian Nynorsk', 'Norsk, nynorsk' ),
		'oc'          => array( 'Occitan', 'Occitan' ),
		'pa'          => array( 'Punjabi', 'ਪੰਜਾਬੀ' ),
		'pl'          => array( 'Polish', 'Polski' ),
		'pt-pt'       => array( 'Portuguese, Portugal', 'Português, Portugal' ),
		'pt-br'       => array( 'Portuguese, Brazil', 'Português, Brasil' ),
		'ro'          => array( 'Romanian', 'Română' ),
		'ru'          => array( 'Russian', 'Русский' ),
		'sco'         => array( 'Scots', 'Scots' ),
		'se'          => array( 'Northern Sami', 'Sámi' ),
		'si'          => array( 'Sinhala', 'සිංහල' ),
		'sk'          => array( 'Slovak', 'Slovenčina' ),
		'sl'          => array( 'Slovenian', 'Slovenščina' ),
		'sq'          => array( 'Albanian', 'Shqip' ),
		'sr'          => array( 'Serbian', 'Српски' ),
		'sv'          => array( 'Swedish', 'Svenska' ),
		'sw'          => array( 'Swahili', 'Kiswahili' ),
		'ta'          => array( 'Tamil', 'தமிழ்' ),
		'ta-lk'       => array( 'Tamil, Sri Lanka', 'தமிழ், இலங்கை' ),
		'te'          => array( 'Telugu', 'తెలుగు' ),
		'th'          => array( 'Thai', 'ภาษาไทย' ),
		'tr'          => array( 'Turkish', 'Türkçe' ),
		'tyv'         => array( 'Tuvan', 'Тыва дыл' ),
		'ug'          => array( 'Uyghur', 'Уйғур' ),
		'uk'          => array( 'Ukrainian', 'Українська' ),
		'ur'          => array( 'Urdu', /* Left-to-right marker "‭" */ 'اردو', 'RTL' ),
		'vi'          => array( 'Vietnamese', 'Tiếng Việt' ),
		'xx-lolspeak' => array( 'Lolspeak', 'Lolspeak' ),
		'zh-hans'     => array( 'Chinese, Simplified', '简体中文' ),
		'zh-hant'     => array( 'Chinese, Traditional', '繁體中文' ),
	);

	return $lang[ $lang_code ][1];
}
