<?php return [
	'alugha' => [
		'name'           => 'Alugha',
		'regex'          => '#https?://(www\\.)?alugha\\.com/(1/)?videos/(?<id>[a-z0-9_\\-]+)#i',
		'oembed'         => false,
		'embed_url'      => 'https://alugha.com/embed/web-player/?v=%s',
		'default_params' => 'nologo=1',
		'auto_thumbnail' => true,
		'tests'          => [
			0 => [
				'url' => 'https://alugha.com/videos/e269333a-579b-11e5-aa10-8587cc892389/share',
				'id'  => 'e269333a-579b-11e5-aa10-8587cc892389',
			],
			1 => [
				'url' => 'https://alugha.com/videos/e5ddd7d0-6b7c-11eb-b741-7b3016ed7f90',
				'id'  => 'e5ddd7d0-6b7c-11eb-b741-7b3016ed7f90',
			],
			2 => [
				'url' => 'https://alugha.com/videos/7cab9cd7-f64a-11e5-939b-c39074d29b86',
				'id'  => '7cab9cd7-f64a-11e5-939b-c39074d29b86',
			],
		],
	],
	'archiveorg' => [
		'name'           => 'Archive.org',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?archive\\.org/(details|embed)/(?<id>[0-9a-z\\-]+)#i',
		'embed_url'      => 'https://www.archive.org/embed/%s/',
		'default_params' => '',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://archive.org/details/arashyekt4_gmail_Cat',
				'id'  => 'arashyekt4',
			],
		],
	],
	'bannedvideo' => [
		'name'      => 'Banned.video',
		'oembed'    => false,
		'regex'     => '#https://banned\\.video/watch\\?id=(?<id>[a-z0-9]+)#i',
		'embed_url' => 'https://api.banned.video/embed/%s',
		'tests'     => [
			0 => [
				'url' => 'https://banned.video/watch?id=5ffe509f0d763c3dca0e8ad7',
				'id'  => '5ffe509f0d763c3dca0e8ad7',
			],
		],
	],
	'bitchute' => [
		'name'           => 'Bitchute',
		'oembed'         => false,
		'regex'          => '#https?://www\\.bitchute\\.com/(video|embed)/(?<id>[0-9a-z\\-]+)#i',
		'embed_url'      => 'https://www.bitchute.com/embed/%s/',
		'rebuild_url'    => 'https://www.bitchute.com/video/%s/',
		'default_params' => '',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://www.bitchute.com/video/eCctkmPpK8tq/',
				'id'  => 'eCctkmPpK8tq',
			],
		],
	],
	'mailru' => [
		'name'         => 'Mail.ru',
		'oembed'       => false,
		'regex'        => '#https?://my\\.mail\\.ru/video/embed/(?<id>[0-9]+)#i',
		'oembed'       => false,
		'embed_url'    => 'https://my.mail.ru/video/embed/%s',
		'requires_src' => true,
		'tests'        => [
			0 => [
				'url' => 'https://my.mail.ru/video/embed/1475383959813619758',
				'id'  => 1475383959813619758,
			],
		],
	],
	'brightcove' => [
		'name'         => 'Brightcove',
		'regex'        => '#https?://(players|link)\\.brightcove\\.net/(?<account_id>[0-9]+)/(?<brightcove_player>[a-z0-9]+)_(?<brightcove_embed>[a-z0-9]+)/index\\.html\\?videoId=(?<id>[0-9]+)#i',
		'oembed'       => false,
		'embed_url'    => 'https://players.brightcove.net/%s/%s_%s/index.html?videoId=%s',
		'requires_src' => true,
		'tests'        => [
			0 => [
				'url'               => 'https://players.brightcove.net/624246174001/BJXA5Px6f_default/index.html?videoId=5809251338001',
				'account_id'        => 624246174001,
				'brightcove_player' => 'BJXA5Px6f',
				'brightcove_embed'  => 'default',
				'id'                => 5809251338001,
			],
			1 => [
				'url'               => 'http://players.brightcove.net/1160438696001/default_default/index.html?videoId=4587535845001',
				'account_id'        => 1160438696001,
				'brightcove_player' => 'default',
				'brightcove_embed'  => 'default',
				'id'                => 4587535845001,
			],
			2 => [
				'url'               => 'http://players.brightcove.net/5107476400001/B1xUkhW8i_default/index.html?videoId=5371391223001',
				'account_id'        => 5107476400001,
				'brightcove_player' => 'B1xUkhW8i',
				'brightcove_embed'  => 'default',
				'id'                => 5371391223001,
			],
		],
	],
	'cantcensortruthcom' => [
		'name'      => 'cantcensortruth.com',
		'oembed'    => false,
		'regex'     => '#https://cantcensortruth\\.com/watch\\?id=(?<id>[a-z0-9]+)#i',
		'embed_url' => 'https://api.banned.video/embed/%s',
		'tests'     => [
			0 => [
				'url' => 'https://cantcensortruth.com/watch?id=601218b0de226411596203ae',
				'id'  => '601218b0de226411596203ae',
			],
		],
	],
	'comedycentral' => [
		'name'           => 'Comedy Central',
		'oembed'         => false,
		'regex'          => '#https?://media\\.mtvnservices\\.com/embed/mgid:arc:video:comedycentral\\.com:(?<id>[-a-z0-9]{36})#i',
		'embed_url'      => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:%s',
		'requires_src'   => true,
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:c80adf02-3e24-437a-8087-d6b77060571c',
				'id'  => 'c80adf02-3e24-437a-8087-d6b77060571c',
			],
			1 => [
				'url' => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:c3c1da76-96c2-48b4-b38d-8bb16fbf7a58',
				'id'  => 'c3c1da76-96c2-48b4-b38d-8bb16fbf7a58',
			],
		],
	],
	'dailymotion' => [
		'name'           => 'Dailymotion',
		'oembed'         => true,
		'regex'          => '#https?://(www\\.)?(dai\\.ly|dailymotion\\.com/video)/(?<id>[a-z0-9]+)#i',
		'embed_url'      => 'https://www.dailymotion.com/embed/video/%s',
		'rebuild_url'    => 'https://www.dailymotion.com/video/%s',
		'default_params' => 'logo=0&hideInfos=1&related=0',
		'auto_thumbnail' => true,
		'auto_title'     => true,
		'tests'          => [
			0 => [
				'url' => 'https://www.dailymotion.com/video/x41ia79_mass-effect-andromeda-gameplay-alpha_videogames',
				'id'  => 'x41ia79',
			],
			1 => [
				'url' => 'https://dai.ly/x3cwlqz',
				'id'  => 'x3cwlqz',
			],
		],
	],
	'dailymotion_playlist' => [
		'name'           => 'Dailymotion Playlist',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?dailymotion\\.com/playlist/(?<id>[a-z0-9]+)#i',
		'embed_url'      => 'https://www.dailymotion.com/embed/playlist/%s',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'http://www.dailymotion.com/playlist/x3yk8p_PHIL-MDS_nature-et-environnement-2011/1#video=xm3x45',
				'id'  => 'x3yk8p',
			],
		],
	],
	'dtube' => [
		'name'      => 'DTube',
		'oembed'    => false,
		'regex'     => '%https?://d\\.tube(/#!)?/v/(?<id>[^"]+)%i',
		'embed_url' => 'https://emb.d.tube/#!/%s',
		'tests'     => [
			0 => [
				'url' => 'https://d.tube/#!/v/exyle/bgc244pb',
				'id'  => 'exyle/bgc244pb',
			],
		],
	],
	'facebook' => [
		'name'           => 'Facebook',
		'oembed'         => false,
		'regex'          => '#(?<id>https?://([a-z]+\\.)?facebook\\.com/[-.a-z0-9]+/videos/[^\\s]+)#i',
		'url_encode_id'  => true,
		'embed_url'      => 'https://www.facebook.com/plugins/video.php?href=%s',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://www.facebook.com/TheKillingsOfTonyBlair/videos/vb.551089058285349/562955837098671/?type=2&theater',
				'id'  => 'https://www.facebook.com/TheKillingsOfTonyBlair/videos/vb.551089058285349/562955837098671/?type=2&theater',
			],
			1 => [
				'url' => 'https://web.facebook.com/XTvideo/videos/10153906059711871/',
				'id'  => 'https://web.facebook.com/XTvideo/videos/10153906059711871/',
			],
		],
	],
	'googledrive' => [
		'name'      => 'Google Drive',
		'oembed'    => false,
		'regex'     => '#https?://drive\\.google\\.com/file/d/(?<id>[^\\s/]+)#i',
		'embed_url' => 'https://drive.google.com/file/d/%s/preview',
		'tests'     => [
			0 => [
				'url' => 'https://drive.google.com/file/d/0BymXD1aD6QzJWkh4Q0hPRWlPYkk/edit',
				'id'  => '0BymXD1aD6QzJWkh4Q0hPRWlPYkk',
			],
		],
	],
	'html5' => [
		'name'         => 'mp4 or webm video files',
		'aspect_ratio' => false,
	],
	'iframe' => [
		'name'           => 'ARVE general iframe embed',
		'oembed'         => false,
		'embed_url'      => '%s',
		'default_params' => '',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://example.com/',
				'id'  => 'https://example.com/',
			],
		],
	],
	'ign' => [
		'name'           => 'IGN',
		'oembed'         => false,
		'regex'          => '#(?<id>https?://(www\\.)?ign\\.com/videos/[0-9]{4}/[0-9]{2}/[0-9]{2}/[0-9a-z\\-]+)#i',
		'embed_url'      => 'https://widgets.ign.com/video/embed/content.html?url=%s',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://ign.com/videos/2012/03/06/mass-effect-3-video-review',
				'id'  => 'https://ign.com/videos/2012/03/06/mass-effect-3-video-review',
			],
		],
	],
	'imdb' => [
		'name'         => 'IMDB',
		'requires_src' => true,
	],
	'kickstarter' => [
		'name'           => 'Kickstarter',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?kickstarter\\.com/projects/(?<id>[0-9a-z\\-]+/[-0-9a-z\\-]+)#i',
		'embed_url'      => 'https://www.kickstarter.com/projects/%s/widget/video.html',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://www.kickstarter.com/projects/obsidian/project-eternity?ref=discovery',
				'id'  => 'obsidian/project-eternity',
			],
			1 => [
				'url' => 'https://www.kickstarter.com/projects/trinandtonic/friendship-postcards?ref=category_featured',
				'id'  => 'trinandtonic/friendship-postcards',
			],
		],
	],
	'klatv' => [
		'name'           => 'kla.tv',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?kla(gemauer)?.tv/(?<id>[0-9]+)#i',
		'embed_url'      => 'https://www.kla.tv/index.php?a=showembed&vidid=%s',
		'url'            => true,
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'http://www.klagemauer.tv/9106',
				'id'  => 9106,
			],
			1 => [
				'url' => 'http://www.kla.tv/9122',
				'id'  => 9122,
			],
		],
	],
	'liveleak' => [
		# <iframe width="640" height="360" src="//www.liveleak.com/e/9UCAQ_1613786191" frameborder="0" allowfullscreen></iframe>
		# https://www.liveleak.com/view?t=svtYD_1613984945
		'name'           => 'LiveLeak',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?liveleak\\.com/(view|ll_embed)\\?(?<id>(f|i|t)=[0-9a-z\\_]+)#i',
		'embed_url'      => 'https://www.liveleak.com/ll_embed?%s',
		'default_params' => '',
		'auto_thumbnail' => true,
		'tests'          => [
			1 => [
				'url' => 'http://www.liveleak.com/view?f=c85bdf5e45b2',
				'id'  => 'f=c85bdf5e45b2',
			],
			2 => [
				'url' => 'https://www.liveleak.com/view?t=uGBX9_1579730411',
				'id'  => 't=uGBX9_1579730411',
			],
		],
		'test_ids'       => [
			0 => 'f=c85bdf5e45b2',
			1 => 'c85bdf5e45b2',
		],
	],
	'livestream' => [
		'name'           => 'Livestream.com',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?livestream\\.com/accounts/(?<id>[0-9]+/events/[0-9]+(/videos/[0-9]+)?)#i',
		'embed_url'      => 'https://livestream.com/accounts/%s/player',
		'default_params' => 'width=1280&height=720&enableInfoAndActivity=true&defaultDrawer=&mute=false',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://livestream.com/accounts/23470201/events/7021166',
				'id'  => '23470201/events/7021166',
			],
			1 => [
				'url' => 'https://livestream.com/accounts/467901/events/2015991/videos/17500857/player?width=640&height=360&enableInfo=true&defaultDrawer=&autoPlay=true&mute=false',
				'id'  => '467901/events/2015991/videos/17500857',
			],
		],
	],
	'metacafe' => [
		'name'           => 'Metacafe',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?metacafe\\.com/(watch|fplayer)/(?<id>[0-9]+)#i',
		'embed_url'      => 'http://www.metacafe.com/embed/%s/',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'http://www.metacafe.com/watch/11433151/magical-handheld-fireballs/',
				'id'  => 11433151,
			],
			1 => [
				'url' => 'http://www.metacafe.com/watch/11322264/everything_wrong_with_robocop_in_7_minutes/',
				'id'  => 11322264,
			],
		],
	],
	'myspace' => [
		'name'           => 'myspace',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?myspace\\.com/.+/(?<id>[0-9]+)#i',
		'embed_url'      => 'https://media.myspace.com/play/video/%s',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://myspace.com/myspace/video/dark-rooms-the-shadow-that-looms-o-er-my-heart-live-/109471212',
				'id'  => 109471212,
			],
		],
	],
	'ooyala' => [
		'name'         => 'ooyala',
		'requires_src' => true,
	],
	'qq' => [
		'name'           => 'v.qq.com',
		'oembed'         => false,
		'regex'          => '#https?://v\.qq\.com/.+?(?<id>[a-z0-9]+).html#i',
		'embed_url'      => 'https://v.qq.com/txp/iframe/player.html?vid=%s',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://v.qq.com/x/page/u0863pgoecf.html',
				'id'  => 'u0863pgoecf',
			],
			1 => [
				'url' => 'https://v.qq.com/x/cover/zf2z0xpqcculhcz/y0016tj0qvh.html',
				'id'  => 'y0016tj0qvh',
			],
		],
	],
	'rumble' => [
		'name'           => 'Rumble.com',
		'oembed'         => false,
		'regex'          => '#https://rumble\\.com/(embed/)?(?<id>[^-/]+)#i',
		'embed_url'      => 'https://rumble.com/embed/%s/',
		'default_params' => '',
		'auto_thumbnail' => true,
		'auto_title'     => true,
		'tests'          => [
			0 => [
				'url' => 'https://rumble.com/vd6thp-gigantic-galapagos-tortoise-casually-strolls-through-a-restaurant.html',
				'id'  => 'vd6thp',
			],
			1 => [
				'url' => 'https://rumble.com/ve2ez5-airplane-returns-to-denver-after-engine-malfunction.html?foo=bar',
				'id'  => 've2ez5',
			],
		],
	],
	'rutube' => [
		'name'        => 'RuTube.ru',
		'oembed'      => false,
		'regex'       => '#https?://(www\\.)?rutube\\.ru/play/embed/(?<id>[0-9]+)#i',
		'embed_url'   => 'https://rutube.ru/play/embed/%s',
		'tests'       => [
			0 => [
				'url' => 'https://rutube.ru/play/embed/9822149',
				'id'  => '9822149',
			],
		],
		'embed_codes' => [
			0 => [
				'url'  => 'https://rutube.ru/video/0c24c646267beb3091a52c43a46214b5/',
				'code' => '<iframe width="720" height="405" src="//rutube.ru/play/embed/9822149" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>',
			],
		],
	],
	'snotr' => [
		'name'           => 'Snotr',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?snotr\\.com/(video|embed)/(?<id>[0-9]+)#i',
		'embed_url'      => 'https://www.snotr.com/embed/%s',
		'rebuild_url'    => 'https://www.snotr.com/video/%s',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://www.snotr.com/video/12314/How_big_a_truck_blind_spot_really_is',
				'id'  => 12314,
			],
		],
	],
	'ted' => [
		'name'           => 'TED Talks',
		'oembed'         => true,
		'regex'          => '#https?://(www\\.)?ted\\.com/talks/(?<id>[a-z0-9_]+)#i',
		'embed_url'      => 'https://embed.ted.com/talks/%s',
		'rebuild_url'    => 'https://www.ted.com/talks/%s.html',
		'auto_thumbnail' => true,
		'auto_title'     => true,
		'tests'          => [
			0 => [
				'url' => 'https://www.ted.com/talks/sam_harris_can_we_build_ai_without_losing_control_over_it',
				'id'  => 'sam_harris_can_we_build_ai_without_losing_control_over_it',
			],
		],
	],
	'twitch' => [
		'name'           => 'Twitch',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?twitch.tv/(?!directory)(?|[a-z0-9_]+/v/(?<id>[0-9]+)|(?<id>[a-z0-9_]+))#i',
		'embed_url'      => 'https://player.twitch.tv/?channel=%s',
		'auto_thumbnail' => true,
		'tests'          => [
			0 => [
				'url'              => 'https://www.twitch.tv/whiskeyexperts',
				'id'               => 'whiskeyexperts',
				'api_img_contains' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/whiskyexperts',
			],
			1 => [
				'url'     => 'https://www.twitch.tv/imaqtpie',
				'id'      => 'imaqtpie',
				'api_img' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/imaqtpie',
			],
			2 => [
				'url'     => 'https://www.twitch.tv/imaqtpie/v/95318019',
				'id'      => 95318019,
				'api_img' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/imaqtpie',
			],
		],
	],
	'ustream' => [
		'name'           => 'Ustream',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?ustream\\.tv/(embed/|channel/)?(?<id>[0-9]{8}|recorded/[0-9]{8}(/highlight/[0-9]+)?)#i',
		'embed_url'      => 'http://www.ustream.tv/embed/%s',
		'default_params' => 'html5ui',
		'auto_thumbnail' => false,
		'aspect_ratio'   => '480:270',
		'tests'          => [
			0 => [
				'url' => 'http://www.ustream.tv/recorded/59999872?utm_campaign=ustre.am&utm_source=ustre.am/:43KHS&utm_medium=social&utm_content=20170405204127',
				'id'  => 'recorded/59999872',
			],
			1 => [
				'url' => 'http://www.ustream.tv/embed/17074538?wmode=transparent&v=3&autoplay=false',
				'id'  => '17074538',
			],
		],
	],
	'viddler' => [
		'name'           => 'Viddler',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?viddler\\.com/(embed|v)/(?<id>[a-z0-9]{8})#i',
		'embed_url'      => 'https://www.viddler.com/embed/%s/',
		'rebuild_url'    => 'https://www.viddler.com/v/%s/',
		'default_params' => 'f=1&player=full&disablebackwardseek=false&disableseek=false&disableforwardseek=false&make_responsive=true&loop=false&nologo=true',
		'auto_thumbnail' => true,
		'auto_title'     => true,
		'aspect_ratio'   => '545:349',
		'tests'          => [
			0 => [
				'url' => 'https://www.viddler.com/v/a695c468',
				'id'  => 'a695c468',
			],
		],
	],
	'vimeo' => [
		'name'           => 'Vimeo',
		'oembed'         => true,
		'regex'          => '#https?://(player\\.)?vimeo\\.com/((video/)|(channels/[a-z]+/)|(groups/[a-z]+/videos/))?(?<id>[0-9]+)(?<vimeo_secret>/[0-9a-z]+)?#i',
		'embed_url'      => 'https://player.vimeo.com/video/%s',
		'rebuild_url'    => 'https://vimeo.com/%s',
		'default_params' => 'html5=1&title=1&byline=0&portrait=0',
		'auto_thumbnail' => true,
		'auto_title'     => true,
		'tests'          => [
			0 => [
				'url' => 'https://vimeo.com/124400795',
				'id'  => 124400795,
			],
			1 => [
				'url' => 'https://player.vimeo.com/video/265932452',
				'id'  => 265932452,
			],
		],
	],
	'vk' => [
		'name'           => 'VK',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?vk\\.com/video_ext\\.php\\?(?<id>[^ ]+)#i',
		'embed_url'      => 'https://vk.com/video_ext.php?%s',
		'rebuild_url'    => 'https://vk.com/video_ext.php?%s',
		'requires_src'   => true,
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'https://vk.com/video_ext.php?oid=162756656&id=171388096&hash=b82cc24232fe7f9f&hd=1',
				'id'  => 'oid=162756656&id=171388096&hash=b82cc24232fe7f9f&hd=1',
			],
		],
		'embed_codes'    => [
			0 => [
				'url'  => 'https://vk.com/just_vid?z=video-51189706_456247608%2Fe148d26229c2e82bd9%2Fpl_wall_-51189706',
				'code' => '<iframe src="https://vk.com/video_ext.php?oid=-51189706&id=456247608&hash=8256b948f3f020fd" width="640" height="360" frameborder="0" allowfullscreen></iframe>',
			],
		],
	],
	'wistia' => [
		'name'           => 'Wistia',
		'oembed'         => true,
		'regex'          => '#https?://([a-z0-9.-]+)wistia\\.(net|com)/(medias|embed/iframe)/(?<id>[a-z0-9]+)#i',
		'embed_url'      => 'https://fast.wistia.net/embed/iframe/%s',
		'default_params' => '',
		'tests'          => [
			0 => [
				'url' => 'https://fast.wistia.net/embed/iframe/g5pnf59ala',
				'id'  => 'g5pnf59ala',
			],
			1 => [
				'url' => 'https://how-2-drive.wistia.com/medias/fi1rqe3kiy',
				'id'  => 'fi1rqe3kiy',
			],
		],
	],
	'xtube' => [
		'name'           => 'XTube',
		'oembed'         => false,
		'regex'          => '#https?://(www\\.)?xtube\\.com/watch\\.php\\?v=(?<id>[a-z0-9_\\-]+)#i',
		'embed_url'      => 'http://www.xtube.com/embedded/user/play.php?v=%s',
		'auto_thumbnail' => false,
		'tests'          => [
			0 => [
				'url' => 'http://www.xtube.com/watch.php?v=1234',
				'id'  => 1234,
			],
		],
	],
	'yahoo' => [
		'name'           => 'Yahoo',
		'oembed'         => false,
		'regex'          => '#(?<id>https?://([a-z.]+)yahoo\\.com/[/-a-z0-9öäü]+\\.html)#i',
		'embed_url'      => '%s',
		'default_params' => 'format=embed',
		'auto_thumbnail' => true,
		'auto_title'     => true,
		'tests'          => [
			0 => [
				'url' => 'https://de.sports.yahoo.com/video/krasse-vorher-nachher-bilder-mann-094957265.html?format=embed&player_autoplay=false',
				'id'  => 'https://de.sports.yahoo.com/video/krasse-vorher-nachher-bilder-mann-094957265.html',
			],
			1 => [
				'url' => 'https://www.yahoo.com/movies/sully-trailer-4-211012511.html?format=embed',
				'id'  => 'https://www.yahoo.com/movies/sully-trailer-4-211012511.html',
			],
		],
	],
	'youku' => [
		'name'           => 'Youku',
		'oembed'         => false,
		'regex'          => '#https?://([a-z.]+)?\\.youku.com/(embed/|v_show/id_)(?<id>[a-z0-9]+)#i',
		'embed_url'      => 'https://player.youku.com/embed/%s',
		'auto_thumbnail' => false,
		'aspect_ratio'   => '450:292.5',
		'tests'          => [
			0 => [
				'url' => 'https://v.youku.com/v_show/id_XMTczMDAxMjIyNA==.html?f=27806190',
				'id'  => 'XMTczMDAxMjIyNA',
			],
			1 => [
				'url' => 'https://player.youku.com/embed/XMTUyODYwOTc4OA==',
				'id'  => 'XMTUyODYwOTc4OA',
			],
		],
	],
	'youtube' => [
		'name'           => 'YouTube',
		'oembed'         => true,
		'regex'          => '#https?://(www\\.)?(youtube\\.com\\/\\S*((\\/e(mbed))?\\/|watch\\?(\\S*?&?v\\=))|youtu\\.be\\/)(?<id>[a-zA-Z0-9_-]{6,11})#i',
		'embed_url'      => 'https://www.youtube.com/embed/%s',
		'rebuild_url'    => 'https://www.youtube.com/watch?v=%s',
		'default_params' => 'iv_load_policy=3&modestbranding=1&rel=0&autohide=1&playsinline=0',
		'auto_thumbnail' => true,
		'auto_title'     => true,
		'tests'          => [
			1 => [
				'url' => 'https://www.youtube.com/watch?v=-fEo3kgHFaw',
				'id'  => '-fEo3kgHFaw',
			],
			2 => [
				'url' => 'https://www.youtube.com/watch?time_continue=1&v=eqnnyDa7C7Q&feature=emb_logo',
				'id'  => 'eqnnyDa7C7Q',
			],
			4 => [
				'url' => 'https://youtu.be/hRonZ4wP8Ys',
				'id'  => 'hRonZ4wP8Ys',
			],
			5 => [
				'url' => 'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10',
				'id'  => 'GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA',
			],
			6 => [
				'url' => 'https://youtu.be/b8m9zhNAgKs?list=PLI_7Mg2Z_-4I-W_lI55D9lBUkC66ftHMg',
				'id'  => 'b8m9zhNAgKs?list=PLI_7Mg2Z_-4I-W_lI55D9lBUkC66ftHMg',
			],
		],
		'specific_tests' => [
			0  => null,
			1  => 'http://youtu.be/3Y8B93r2gKg',
			2  => null,
			3  => 'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10',
			4  => null,
			5  => '[youtube id="FKkejo2dMV4" parameters="playlist=FKkejo2dMV4&loop=1"]',
			6  => null,
			7  => '[youtube id="uCQXKYPiz6M" parameters="iv_load_policy=1"]',
			8  => null,
			9  => 'http://youtu.be/vrXgLhkv21Y?t=1h19m14s',
			10 => 'http://youtu.be/vrXgLhkv21Y?t=19m14s',
			11 => 'http://youtu.be/vrXgLhkv21Y?t=1h',
			12 => 'http://youtu.be/vrXgLhkv21Y?t=5m',
			13 => 'http://youtu.be/vrXgLhkv21Y?t=30s',
			14 => null,
			15 => '[youtube id="uCQXKYPiz6M" parameters="start=61"]',
		],
	],
	'youtubelist' => [
		'oembed'         => true,
		'regex'          => '#https?://(www\\.)?youtube\\.com/(embed/videoseries|playlist)\\?list=(?<id>[-_a-z0-9]+)#i',
		'name'           => 'YouTube Playlist',
		'embed_url'      => 'https://www.youtube.com/embed/videoseries?list=%s',
		'rebuild_url'    => 'https://www.youtube.com/watch?list=%s',
		'default_params' => 'iv_load_policy=3&modestbranding=1&rel=0&autohide=1&playsinline=0',
		'auto_thumbnail' => true,
		'tests'          => [
			0 => [
				'url' => 'https://www.youtube.com/playlist?list=PL3Esg-ZzbiUmeSKBAQ3ej1hQxDSsmnp-7',
				'id'  => 'PL3Esg-ZzbiUmeSKBAQ3ej1hQxDSsmnp-7',
			],
			1 => [
				'url' => 'https://www.youtube.com/watch?list=PLMUvgtCRyn-6obmhiDS4n5vYQN3bJRduk&v=cyoffsDl4Hw',
				'id'  => 'PLMUvgtCRyn-6obmhiDS4n5vYQN3bJRduk',
			],
		],
	],
];
