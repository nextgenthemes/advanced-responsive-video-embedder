<?php
namespace Nextgenthemes\ARVE;

function get_host_properties() {

	$properties = [
		'alugha' => [
			'name'           => 'Alugha',
			'regex'          => '#https?://(www\.)?alugha\.com/(1/)?videos/(?<id>[a-z0-9_\-]+)#i',
			'oembed'         => false,
			'embed_url'      => 'https://alugha.com/embed/web-player/?v=%s',
			'default_params' => 'nologo=1',
			'auto_thumbnail' => true,
			'tests'          => [
				[
					'url' => 'https://alugha.com/videos/e269333a-579b-11e5-aa10-8587cc892389/share',
					'id'  => 'e269333a-579b-11e5-aa10-8587cc892389',
				],
				[
					'url' => 'https://alugha.com/1/videos/youtube-54m1YfEuYU8',
					'id'  => 'youtube-54m1YfEuYU8',
				],
				[
					'url' => 'https://alugha.com/videos/7cab9cd7-f64a-11e5-939b-c39074d29b86',
					'id'  => '7cab9cd7-f64a-11e5-939b-c39074d29b86',
				],
			],
		],
		'archiveorg' => [
			'name'           => 'Archive.org',
			'oembed'         => false,
			'regex'          => '#https?://(www\.)?archive\.org/(details|embed)/(?<id>[0-9a-z\-]+)#i',
			'embed_url'      => 'https://www.archive.org/embed/%s/',
			'default_params' => '',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://archive.org/details/arashyekt4_gmail_Cat',
					'id'  => 'arashyekt4',
				],
			],
		],
		'bitchute' => [
			'name'           => 'Bitchute',
			'oembed'         => false,
			'regex'          => '#https?://www\.bitchute\.com/(video|embed)/(?<id>[0-9a-z\-]+)#i',
			'embed_url'      => 'https://www.bitchute.com/embed/%s/',
			'rebuild_url'    => 'https://www.bitchute.com/video/%s/',
			'default_params' => '',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://www.bitchute.com/video/eCctkmPpK8tq/',
					'id'  => 'eCctkmPpK8tq',
				],
			],
		],
		'break' => [
			'name'           => 'Break',
			'regex'          => '#https?://(www\.|view\.)break\.com/(video/|embed/)?[-a-z0-9]*?(?<id>[0-9]+)#i',
			'oembed'         => false,
			'embed_url'      => 'http://break.com/embed/%s',
			'default_params' => 'embed=1',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://www.break.com/video/first-person-pov-of-tornado-strike-2542591-test',
					'id'  => 2542591,
				],
				[
					'url' => 'http://view.break.com/2542591-test',
					'id'  => 2542591,
				],
				[
					'url' => 'http://www.break.com/embed/2542591?embed=1',
					'id'  => 2542591,
				],
			],
		],
		'brightcove' => [
			'name'         => 'Brightcove',
			'regex'        => '#https?://(players|link)\.brightcove\.net/(?<account_id>[0-9]+)/(?<brightcove_player>[a-z0-9]+)_(?<brightcove_embed>[a-z0-9]+)/index\.html\?videoId=(?<id>[0-9]+)#i',
			'oembed'       => false,
			# 1) account_id
			# 2) player id
			# 3) embed_id
			# 4) id
			'embed_url'    => 'https://players.brightcove.net/%s/%s_%s/index.html?videoId=%s',
			'requires_src' => true,
			'tests'        => [
				[
					'url'               => 'https://players.brightcove.net/624246174001/BJXA5Px6f_default/index.html?videoId=5809251338001',
					'account_id'        => 624246174001,
					'brightcove_player' => 'BJXA5Px6f',
					'brightcove_embed'  => 'default',
					'id'                => 5809251338001,
				],
				[
					'url'               => 'http://players.brightcove.net/1160438696001/default_default/index.html?videoId=4587535845001',
					'account_id'        => 1160438696001,
					'brightcove_player' => 'default',
					'brightcove_embed'  => 'default',
					'id'                => 4587535845001,
				],
				[
					'url'               => 'http://players.brightcove.net/5107476400001/B1xUkhW8i_default/index.html?videoId=5371391223001',
					'account_id'        => 5107476400001,
					'brightcove_player' => 'B1xUkhW8i',
					'brightcove_embed'  => 'default',
					'id'                => 5371391223001,
				],
			],
		],
		'comedycentral' => [
			'name'           => 'Comedy Central',
			'regex'          => '#https?://media\.mtvnservices\.com/embed/mgid:arc:video:comedycentral\.com:(?<id>[-a-z0-9]{36})#i',
			'embed_url'      => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:%s',
			'requires_src'   => true,
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:c80adf02-3e24-437a-8087-d6b77060571c',
					'id'  => 'c80adf02-3e24-437a-8087-d6b77060571c',
				],
				[
					'url' => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:c3c1da76-96c2-48b4-b38d-8bb16fbf7a58',
					'id'  => 'c3c1da76-96c2-48b4-b38d-8bb16fbf7a58',
				],
			],
		],
		'dailymotion' => [
			'name'           => 'Dailymotion',
			'oembed'         => true,
			'regex'          => '#https?://(www\.)?(dai\.ly|dailymotion\.com/video)/(?<id>[a-z0-9]+)#i',
			'embed_url'      => 'https://www.dailymotion.com/embed/video/%s',
			'rebuild_url'    => 'https://www.dailymotion.com/video/%s',
			'default_params' => 'logo=0&hideInfos=1&related=0',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => [
				[
					'url' => 'http://www.dailymotion.com/video/x41ia79_mass-effect-andromeda-gameplay-alpha_videogames',
					'id'  => 'x41ia79',
				],
				[
					'url' => 'http://dai.ly/x3cwlqz',
					'id'  => 'x3cwlqz',
				],
			],
		],
		'dailymotion_playlist' => [
			'name'           => 'Dailymotion Playlist',
			'regex'          => '#https?://(www\.)?dailymotion\.com/playlist/(?<id>[a-z0-9]+)#i',
			'embed_url'      => 'https://www.dailymotion.com/embed/playlist/%s',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'http://www.dailymotion.com/playlist/x3yk8p_PHIL-MDS_nature-et-environnement-2011/1#video=xm3x45',
					'id'  => 'x3yk8p',
				],
			],
		],
		'dtube' => [
			'name'      => 'DTube',
			'oembed'    => true,
			'regex'     => '%https?://d\.tube(/#!)?/v/(?<id>[^"]+)%i',
			'embed_url' => 'https://emb.d.tube/#!/%s',
			'tests'     => [
				[
					'url' => 'https://d.tube/#!/v/exyle/bgc244pb',
					'id'  => 'exyle/bgc244pb',
				],
			],
		],
		'facebook' => [
			'name'           => 'Facebook',
			'oembed'         => true,
			'regex'          => '#(?<id>https?://([a-z]+\.)?facebook\.com/[-.a-z0-9]+/videos/[^\s]+)#i',
			'url_encode_id'  => true,
			'embed_url'      => 'https://www.facebook.com/plugins/video.php?href=%s',
			'auto_thumbnail' => true,
			'tests'          => [
				[
					'url' => 'https://www.facebook.com/TheKillingsOfTonyBlair/videos/vb.551089058285349/562955837098671/?type=2&theater',
					'id'  => 'https://www.facebook.com/TheKillingsOfTonyBlair/videos/vb.551089058285349/562955837098671/',
				],
				[
					'url' => 'https://web.facebook.com/XTvideo/videos/10153906059711871/',
					'id'  => 'https://web.facebook.com/XTvideo/videos/10153906059711871/',
				],
			],
		],
		'funnyordie' => [
			'oembed'         => true,
			'name'           => 'Funny or Die',
			'regex'          => '#https?://(www\.)?funnyordie\.com/videos/(?<id>[a-z0-9_]+)#i',
			'embed_url'      => 'https://www.funnyordie.com/embed/%s',
			'rebuild_url'    => 'https://www.funnyordie.com/videos/%s',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'aspect_ratio'   => '640:400',
			'tests'          => [
				[
					'url'          => 'http://www.funnyordie.com/videos/76585438d8/sarah-silverman-s-we-are-miracles-hbo-special',
					'id'           => '76585438d8',
					'oembed_title' => "Sarah Silverman's - We Are Miracles HBO Special",
				],
			],
		],
		'ign' => [
			'name'           => 'IGN',
			'regex'          => '#(?<id>https?://(www\.)?ign\.com/videos/[0-9]{4}/[0-9]{2}/[0-9]{2}/[0-9a-z\-]+)#i',
			'embed_url'      => 'https://widgets.ign.com/video/embed/content.html?url=%s',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://www.ign.com/videos/2012/03/06/mass-effect-3-video-review',
					'id'  => 'https://www.ign.com/videos/2012/03/06/mass-effect-3-video-review',
				],
			],
		],
		'kickstarter' => [
			'name'           => 'Kickstarter',
			'oembed'         => true,
			'regex'          => '#https?://(www\.)?kickstarter\.com/projects/(?<id>[0-9a-z\-]+/[-0-9a-z\-]+)#i',
			'embed_url'      => 'https://www.kickstarter.com/projects/%s/widget/video.html',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://www.kickstarter.com/projects/obsidian/project-eternity?ref=discovery',
					'id'  => 'obsidian/project-eternity',
				],
				[
					'url' => 'https://www.kickstarter.com/projects/trinandtonic/friendship-postcards?ref=category_featured',
					'id'  => 'trinandtonic/friendship-postcards',
				],
			],
		],
		'liveleak' => [
			'name'           => 'LiveLeak',
			'regex'          => '#https?://(www\.)?liveleak\.com/(view|ll_embed)\?(?<id>(f|i|t)=[0-9a-z\_]+)#i',
			'embed_url'      => 'https://www.liveleak.com/ll_embed?%s',
			'default_params' => '',
			'auto_thumbnail' => true,
			'tests'          => [
				[ # Page/item 'i=' URL
					'url' => 'http://www.liveleak.com/view?i=703_1385224413',
					'id'  => 'i=703_1385224413',
				],
				[ #File f= URL
					'url' => 'http://www.liveleak.com/view?f=c85bdf5e45b2',
					'id'  => 'f=c85bdf5e45b2',
				],
				[ #No clue new t= url
					'url' => 'https://www.liveleak.com/view?t=uGBX9_1579730411',
					'id'  => 't=uGBX9_1579730411',
				],
			],
			'test_ids'       => [
				'f=c85bdf5e45b2',
				'c85bdf5e45b2',
			],
		],
		'livestream' => [
			'name'           => 'Livestream.com',
			'regex'          => '#https?://(www\.)?livestream\.com/accounts/(?<id>[0-9]+/events/[0-9]+(/videos/[0-9]+)?)#i',
			'embed_url'      => 'https://livestream.com/accounts/%s/player',
			'default_params' => 'width=1280&height=720&enableInfoAndActivity=true&defaultDrawer=&mute=false',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://livestream.com/accounts/23470201/events/7021166',
					'id'  => '23470201/events/7021166',
				],
				[
					'url' => 'https://livestream.com/accounts/467901/events/2015991/videos/17500857/player?width=640&height=360&enableInfo=true&defaultDrawer=&autoPlay=true&mute=false',
					'id'  => '467901/events/2015991/videos/17500857',
				],
			],
		],
		'klatv' => [
			'name'           => 'Klagemauer TV',
			'regex'          => '#https?://(www\.)?kla(gemauer)?.tv/(?<id>[0-9]+)#i',
			'embed_url'      => 'https://www.kla.tv/index.php?a=showembed&vidid=%s',
			'name'           => 'kla.tv',
			'url'            => true,
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'http://www.klagemauer.tv/9106',
					'id'  => 9106,
				],
				[
					'url' => 'http://www.kla.tv/9122',
					'id'  => 9122,
				],
			],
		],
		'metacafe' => [
			'name'           => 'Metacafe',
			'regex'          => '#https?://(www\.)?metacafe\.com/(watch|fplayer)/(?<id>[0-9]+)#i',
			'embed_url'      => 'http://www.metacafe.com/embed/%s/',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'http://www.metacafe.com/watch/11433151/magical-handheld-fireballs/',
					'id'  => 11433151,
				],
				[
					'url' => 'http://www.metacafe.com/watch/11322264/everything_wrong_with_robocop_in_7_minutes/',
					'id'  => 11322264,
				],
			],
		],
		'myspace' => [
			'name'           => 'myspace',
			'regex'          => '#https?://(www\.)?myspace\.com/.+/(?<id>[0-9]+)#i',
			'embed_url'      => 'https://media.myspace.com/play/video/%s',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://myspace.com/myspace/video/dark-rooms-the-shadow-that-looms-o-er-my-heart-live-/109471212',
					'id'  => 109471212,
				],
			],
		],
		'snotr' => [
			'name'           => 'Snotr',
			'regex'          => '#https?://(www\.)?snotr\.com/(video|embed)/(?<id>[0-9]+)#i',
			'embed_url'      => 'https://www.snotr.com/embed/%s',
			'rebuild_url'    => 'https://www.snotr.com/video/%s',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://www.snotr.com/video/12314/How_big_a_truck_blind_spot_really_is',
					'id'  => 12314,
				],
			],
		],
		'spike' => [
			'name'           => 'Spike',
			'regex'          => '#https?://media.mtvnservices.com/embed/mgid:arc:video:spike\.com:(?<id>[a-z0-9\-]{36})#i',
			'embed_url'      => 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:%s',
			'requires_src'   => true,
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:6a219882-c412-46ce-a8c9-32e043396621',
					'id'  => '6a219882-c412-46ce-a8c9-32e043396621',
				],
			],
		],
		'ted' => [
			'name'           => 'TED Talks',
			'oembed'         => true,
			'regex'          => '#https?://(www\.)?ted\.com/talks/(?<id>[a-z0-9_]+)#i',
			'embed_url'      => 'https://embed.ted.com/talks/%s',
			'rebuild_url'    => 'https://www.ted.com/talks/%s.html',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => [
				[
					'url' => 'https://www.ted.com/talks/sam_harris_can_we_build_ai_without_losing_control_over_it',
					'id'  => 'sam_harris_can_we_build_ai_without_losing_control_over_it',
				],
			],
		],
		'twitch' => [
			'name'           => 'Twitch',
			'oembed'         => true,
			'regex'          => '#https?://(www\.)?twitch.tv/(?!directory)(?|[a-z0-9_]+/v/(?<id>[0-9]+)|(?<id>[a-z0-9_]+))#i',
			'embed_url'      => 'https://player.twitch.tv/?channel=%s', # if numeric id https://player.twitch.tv/?video=v%s
			'auto_thumbnail' => true,
			'tests'          => [
				[
					'url'              => 'https://www.twitch.tv/whiskeyexperts',
					'id'               => 'whiskeyexperts',
					'api_img_contains' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/whiskyexperts',
				],
				[
					'url'     => 'https://www.twitch.tv/imaqtpie',
					'id'      => 'imaqtpie',
					'api_img' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/imaqtpie',
				],
				[
					'url'     => 'https://www.twitch.tv/imaqtpie/v/95318019',
					'id'      => 95318019,
					'api_img' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/imaqtpie',
				],
			],
		],
		'ustream' => [
			'name'           => 'Ustream',
			'regex'          => '#https?://(www\.)?ustream\.tv/(embed/|channel/)?(?<id>[0-9]{8}|recorded/[0-9]{8}(/highlight/[0-9]+)?)#i',
			'embed_url'      => 'http://www.ustream.tv/embed/%s',
			'default_params' => 'html5ui',
			'auto_thumbnail' => false,
			'aspect_ratio'   => '480:270',
			'tests'          => [
				[
					'url' => 'http://www.ustream.tv/recorded/59999872?utm_campaign=ustre.am&utm_source=ustre.am/:43KHS&utm_medium=social&utm_content=20170405204127',
					'id'  => 'recorded/59999872',
				],
				[
					'url' => 'http://www.ustream.tv/embed/17074538?wmode=transparent&v=3&autoplay=false',
					'id'  => '17074538',
				],
			],
		],
		'rutube' => [
			'name'        => 'RuTube.ru',
			'regex'       => '#https?://(www\.)?rutube\.ru/play/embed/(?<id>[0-9]+)#i',
			'embed_url'   => 'https://rutube.ru/play/embed/%s',
			'tests'       => [
				[
					'url' => 'https://rutube.ru/play/embed/9822149',
					'id'  => '9822149',
				],
			],
			'embed_codes' => [
				[
					'url'  => 'https://rutube.ru/video/0c24c646267beb3091a52c43a46214b5/',
					'code' => '<iframe width="720" height="405" src="//rutube.ru/play/embed/9822149" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>',
				],
			],
		],
		'viddler' => [
			'name'           => 'Viddler',
			'regex'          => '#https?://(www\.)?viddler\.com/(embed|v)/(?<id>[a-z0-9]{8})#i',
			'embed_url'      => 'https://www.viddler.com/embed/%s/',
			'rebuild_url'    => 'https://www.viddler.com/v/%s/',
			'default_params' => 'f=1&player=full&disablebackwardseek=false&disableseek=false&disableforwardseek=false&make_responsive=true&loop=false&nologo=true',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'aspect_ratio'   => '545:349',
			'tests'          => [
				[
					'url' => 'https://www.viddler.com/v/a695c468',
					'id'  => 'a695c468',
				],
			],
		],
		'vidspot' => [
			'name'      => 'vidspot.net',
			'regex'     => '#https?://(www\.)?vidspot\.net/(embed-)?(?<id>[a-z0-9]+)#i',
			'embed_url' => 'http://vidspot.net/embed-%s.html',
			'tests'     => [
				[
					'url' => 'http://vidspot.net/285wf9uk3rry',
					'id'  => '285wf9uk3rry',
				],
				[
					'url' => 'http://vidspot.net/embed-285wf9uk3rry.html',
					'id'  => '285wf9uk3rry',
				],
			],
		],
		'vimeo' => [
			'name'           => 'Vimeo',
			'oembed'         => true,
			'regex'          => '#https?://(player\.)?vimeo\.com/((video/)|(channels/[a-z]+/)|(groups/[a-z]+/videos/))?(?<id>[0-9]+)(?<vimeo_secret>/[0-9a-z]+)?#i',
			'embed_url'      => 'https://player.vimeo.com/video/%s',
			'rebuild_url'    => 'https://vimeo.com/%s',
			'default_params' => 'html5=1&title=1&byline=0&portrait=0',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => [
				[
					'url' => 'https://vimeo.com/124400795',
					'id'  => 124400795,
				],
				[
					'url' => 'https://player.vimeo.com/124400795',
					'id'  => 124400795,
				],
			],
		],
		'vk' => [
			'name'           => 'VK',
			'regex'          => '#https?://(www\.)?vk\.com/video_ext\.php\?(?<id>[^ ]+)#i',
			'embed_url'      => 'https://vk.com/video_ext.php?%s',
			'rebuild_url'    => 'https://vk.com/video_ext.php?%s',
			'requires_src'   => true,
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://vk.com/video_ext.php?oid=162756656&id=171388096&hash=b82cc24232fe7f9f&hd=1',
					'id'  => 'oid=162756656&id=171388096&hash=b82cc24232fe7f9f&hd=1',
				],
			],
			'embed_codes'    => [
				[
					'url'  => 'https://vk.com/just_vid?z=video-51189706_456247608%2Fe148d26229c2e82bd9%2Fpl_wall_-51189706',
					'code' => '<iframe src="https://vk.com/video_ext.php?oid=-51189706&id=456247608&hash=8256b948f3f020fd" width="640" height="360" frameborder="0" allowfullscreen></iframe>',
				],
			],
		],
		'vzaar' => [
			'name'      => 'vzaar',
			'regex'     => '#https?://(www\.)?vzaar.(com|tv)/(videos/)?(?<id>[0-9]+)#i',
			'embed_url' => 'https://view.vzaar.com/%s/player',
			'tests'     => [
				[
					'url' => 'https://vzaar.com/videos/993324',
					'id'  => 993324,
				],
				[
					'url' => 'https://vzaar.com/videos/1515906',
					'id'  => 1515906,
				],
			],
		],
		'wistia' => [
			'name'           => 'Wistia',
			'oembed'         => true,
			'regex'          => '#https?://([a-z0-9.-]+)wistia\.(net|com)/(medias|embed/iframe)/(?<id>[a-z0-9]+)#i',
			'embed_url'      => 'https://fast.wistia.net/embed/iframe/%s',
			'default_params' => '',
			'tests'          => [
				[
					'url' => 'https://fast.wistia.net/embed/iframe/g5pnf59ala',
					'id'  => 'g5pnf59ala',
				],
				[
					'url' => 'https://how-2-drive.wistia.com/medias/fi1rqe3kiy',
					'id'  => 'fi1rqe3kiy',
				],
			],
		],
		'xtube' => [
			'name'           => 'XTube',
			'oembed'         => false,
			'regex'          => '#https?://(www\.)?xtube\.com/watch\.php\?v=(?<id>[a-z0-9_\-]+)#i',
			'embed_url'      => 'http://www.xtube.com/embedded/user/play.php?v=%s',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'http://www.xtube.com/watch.php?v=1234',
					'id'  => 1234,
				],
			],
		],
		'yahoo' => [
			'name'           => 'Yahoo',
			'regex'          => '#(?<id>https?://([a-z.]+)yahoo\.com/[/-a-z0-9öäü]+\.html)#i',
			'embed_url'      => '%s',
			'default_params' => 'format=embed',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => [
				[
					'url' => 'https://de.sports.yahoo.com/video/krasse-vorher-nachher-bilder-mann-094957265.html?format=embed&player_autoplay=false',
					'id'  => 'https://de.sports.yahoo.com/video/krasse-vorher-nachher-bilder-mann-094957265.html',
				],
				[
					'url' => 'https://www.yahoo.com/movies/sully-trailer-4-211012511.html?format=embed',
					'id'  => 'https://www.yahoo.com/movies/sully-trailer-4-211012511.html',
				],
			],
		],
		'youku' => [
			'name'           => 'Youku',
			'regex'          => '#https?://([a-z.]+)?\.youku.com/(embed/|v_show/id_)(?<id>[a-z0-9]+)#i',
			'oembed'         => false,
			'embed_url'      => 'https://player.youku.com/embed/%s',
			'auto_thumbnail' => false,
			'aspect_ratio'   => '450:292.5',
			'tests'          => [
				[
					'url' => 'http://v.youku.com/v_show/id_XMTczMDAxMjIyNA==.html?f=27806190',
					'id'  => 'XMTczMDAxMjIyNA',
				],
				[
					'url' => 'http://player.youku.com/embed/XMTUyODYwOTc4OA==',
					'id'  => 'XMTUyODYwOTc4OA',
				],
			],
		],
		'youtube' => [
			'oembed'         => true,
			'name'           => 'YouTube',
			'regex'          => '#https?://(www\.)?(youtube\.com\/\S*((\/e(mbed))?\/|watch\?(\S*?&?v\=))|youtu\.be\/)(?<id>[a-zA-Z0-9_-]{6,11})#i',
			'embed_url'      => 'https://www.youtube.com/embed/%s',
			'rebuild_url'    => 'https://www.youtube.com/watch?v=%s',
			'default_params' => 'iv_load_policy=3&modestbranding=1&rel=0&autohide=1&playsinline=0',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => [
				[
					'url' => 'https://youtu.be/dqLyB5srdGI',
					'id'  => 'dqLyB5srdGI',
				],
				[
					'url' => 'https://www.youtube.com/watch?v=-fEo3kgHFaw',
					'id'  => '-fEo3kgHFaw',
				],
				[
					'url'          => 'http://www.youtube.com/watch?v=vrXgLhkv21Y',
					'id'           => 'vrXgLhkv21Y',
					'oembed_title' => 'TerrorStorm Full length version',
				],
				[
					'url'          => 'https://youtu.be/hRonZ4wP8Ys',
					'id'           => 'hRonZ4wP8Ys',
					'oembed_title' => 'One Bright Dot',
				],
				[
					'url' => 'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10', # The index part will be ignored
					'id'  => 'GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA',
				],
				[
					'url' => 'https://youtu.be/b8m9zhNAgKs?list=PLI_7Mg2Z_-4I-W_lI55D9lBUkC66ftHMg',
					'id'  => 'b8m9zhNAgKs?list=PLI_7Mg2Z_-4I-W_lI55D9lBUkC66ftHMg',
				],
			],
			'specific_tests' => [
				__( 'URL from youtu.be shortener', 'advanced-responsive-video-embedder' ),
				'http://youtu.be/3Y8B93r2gKg',
				__( 'Youtube playlist URL inlusive the video to start at. The index part will be ignored and is not needed', 'advanced-responsive-video-embedder' ),
				'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10',
				__( 'Loop a YouTube video', 'advanced-responsive-video-embedder' ),
				'[youtube id="FKkejo2dMV4" parameters="playlist=FKkejo2dMV4&loop=1"]',
				__( 'Enable annotations and related video at the end (disable by default with this plugin)', 'advanced-responsive-video-embedder' ),
				'[youtube id="uCQXKYPiz6M" parameters="iv_load_policy=1"]',
				__( 'Testing Youtube Starttimes', 'advanced-responsive-video-embedder' ),
				'http://youtu.be/vrXgLhkv21Y?t=1h19m14s',
				'http://youtu.be/vrXgLhkv21Y?t=19m14s',
				'http://youtu.be/vrXgLhkv21Y?t=1h',
				'http://youtu.be/vrXgLhkv21Y?t=5m',
				'http://youtu.be/vrXgLhkv21Y?t=30s',
				__( 'The Parameter start only takes values in seconds, this will start the video at 1 minute and 1 second', 'advanced-responsive-video-embedder' ),
				'[youtube id="uCQXKYPiz6M" parameters="start=61"]',
			],
		],
		'youtubelist' => [
			'oembed'         => true,
			'regex'          => '#https?://(www\.)?youtube\.com/(embed/videoseries|playlist)\?list=(?<id>[-_a-z0-9]+)#i',
			'name'           => 'YouTube Playlist',
			'embed_url'      => 'https://www.youtube.com/embed/videoseries?list=%s',
			'rebuild_url'    => 'https://www.youtube.com/watch?list=%s',
			'default_params' => 'iv_load_policy=3&modestbranding=1&rel=0&autohide=1&playsinline=0',
			'auto_thumbnail' => true,
			'tests'          => [
				[
					'url' => 'https://www.youtube.com/playlist?list=PL3Esg-ZzbiUmeSKBAQ3ej1hQxDSsmnp-7',
					'id'  => 'PL3Esg-ZzbiUmeSKBAQ3ej1hQxDSsmnp-7',
				],
				[
					'url' => 'https://www.youtube.com/embed/videoseries?list=PLMUvgtCRyn-6obmhiDS4n5vYQN3bJRduk',
					'id'  => 'PLMUvgtCRyn-6obmhiDS4n5vYQN3bJRduk',
				],
			],
		],
		'html5' => [
			'name'         => 'mp4 or webm video files',
			'aspect_ratio' => false,
		],
		'iframe' => [
			'name'           => 'ARVE general iframe embed',
			'embed_url'      => '%s',
			'default_params' => '',
			'auto_thumbnail' => false,
			'tests'          => [
				[
					'url' => 'https://example.com/',
					'id'  => 'https://example.com/',
				],
			],
		],
		'googledrive' => [
			'name'      => 'Google Drive',
			'regex'     => '#https?://drive\.google\.com/file/d/(?<id>[^\s/]+)#i',
			'embed_url' => 'https://drive.google.com/file/d/%s/preview',
			'tests'     => [
				[
					'url' => 'https://drive.google.com/file/d/0BymXD1aD6QzJWkh4Q0hPRWlPYkk/edit',
					'id'  => '0BymXD1aD6QzJWkh4Q0hPRWlPYkk',
				],
			],
		],
		'ooyala' => [
			'name'         => 'ooyala',
			'requires_src' => true,
		],
		'imdb' => [
			'name'         => 'IMDB',
			'requires_src' => true,
		],
	];

	return $properties;
}

function query_args() {

	$hosts = [
		'dailymotion' => [
			'query_args' => [
				'api'                => [ 0, 1 ],
				'autoplay'           => [ 0, 1 ],
				'chromeless'         => [ 0, 1 ],
				'highlight'          => [ 0, 1 ],
				'html'               => [ 0, 1 ],
				'id'                 => 'int',
				'info'               => [ 0, 1 ],
				'logo'               => [ 0, 1 ],
				'network'            => [ 'dsl', 'cellular' ],
				'origin'             => [ 0, 1 ],
				'quality'            => [ 240, 380, 480, 720, 1080, 1440, 2160 ],
				'related'            => [ 0, 1 ],
				'start'              => 'int',
				'startscreen'        => [ 0, 1 ],
				'syndication'        => 'int',
				'webkit-playsinline' => [ 0, 1 ],
				'wmode'              => [ 'direct', 'opaque' ],
			],
		],
		'vimeo' => [
			'query_args' => [
				'autoplay'  => [ 'bool', __( 'Autoplay', 'advanced-responsive-video-embedder' ) ],
				'badge'     => [ 'bool', __( 'Badge', 'advanced-responsive-video-embedder' ) ],
				'byline'    => [ 'bool', __( 'Byline', 'advanced-responsive-video-embedder' ) ],
				'color'     => 'string',
				'loop'      => [ 0, 1 ],
				'player_id' => 'int',
				'portrait'  => [ 0, 1 ],
				'title'     => [ 0, 1 ],
			],
		],
		'youtube' => [
			'query_args' => [
				[
					'attr' => 'autohide',
					'type' => 'bool',
					'name' => __( 'Autohide', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'autoplay',
					'type' => 'bool',
					'name' => __( 'Autoplay', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'cc_load_policy',
					'type' => 'bool',
					'name' => __( 'cc_load_policy', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'color',
					'type' => [
						''      => __( 'Default', 'advanced-responsive-video-embedder' ),
						'red'   => __( 'Red', 'advanced-responsive-video-embedder' ),
						'white' => __( 'White', 'advanced-responsive-video-embedder' ),
					],
					'name' => __( 'Color', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'controls',
					'type' => [
						'' => __( 'Default', 'advanced-responsive-video-embedder' ),
						0  => __( 'None', 'advanced-responsive-video-embedder' ),
						1  => __( 'Yes', 'advanced-responsive-video-embedder' ),
						2  => __( 'Yes load after click', 'advanced-responsive-video-embedder' ),
					],
					'name' => __( 'Controls', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'disablekb',
					'type' => 'bool',
					'name' => __( 'disablekb', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'enablejsapi',
					'type' => 'bool',
					'name' => __( 'JavaScript API', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'end',
					'type' => 'number',
					'name' => __( 'End', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'fs',
					'type' => 'bool',
					'name' => __( 'Fullscreen', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'hl',
					'type' => 'text',
					'name' => __( 'Language???', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'iv_load_policy',
					'type' => [
						'' => __( 'Default', 'advanced-responsive-video-embedder' ),
						1  => __( 'Show annotations', 'advanced-responsive-video-embedder' ),
						3  => __( 'Do not show annotations', 'advanced-responsive-video-embedder' ),
					],
					'name' => __( 'iv_load_policy', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'list',
					'type' => 'medium-text',
					'name' => __( 'Language???', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'listType',
					'type' => [
						''             => __( 'Default', 'advanced-responsive-video-embedder' ),
						'playlist'     => __( 'Playlist', 'advanced-responsive-video-embedder' ),
						'search'       => __( 'Search', 'advanced-responsive-video-embedder' ),
						'user_uploads' => __( 'User Uploads', 'advanced-responsive-video-embedder' ),
					],
					'name' => __( 'List Type', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'loop',
					'type' => 'bool',
					'name' => __( 'Loop', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'modestbranding',
					'type' => 'bool',
					'name' => __( 'Modestbranding', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'origin',
					'type' => 'bool',
					'name' => __( 'Origin', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'playerapiid',
					'type' => 'bool',
					'name' => __( 'playerapiid', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'playlist',
					'type' => 'bool',
					'name' => __( 'Playlist', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'playsinline',
					'type' => 'bool',
					'name' => __( 'playsinline', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'rel',
					'type' => 'bool',
					'name' => __( 'Related Videos at End', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'showinfo',
					'type' => 'bool',
					'name' => __( 'Show Info', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'start',
					'type' => 'number',
					'name' => __( 'Start', 'advanced-responsive-video-embedder' ),
				],
				[
					'attr' => 'theme',
					'type' => [
						''      => __( 'Default', 'advanced-responsive-video-embedder' ),
						'dark'  => __( 'Dark', 'advanced-responsive-video-embedder' ),
						'light' => __( 'Light', 'advanced-responsive-video-embedder' ),
					],
					'name' => __( 'Theme', 'advanced-responsive-video-embedder' ),
				],
			],
		],
	];

	return $hosts;
}
