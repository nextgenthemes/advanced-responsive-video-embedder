<!-- TOC depth:6 withLinks:1 updateOnSave:1 orderedList:0 -->

- [Embedding via Shortcodes](#embedding-via-shortcodes)
	- [Examples](#examples)
- [Embedding Videos via URL](#embedding-videos-via-url)
	- [Examples](#examples)
	- [Limitations](#limitations)
- [Recommended use of Options and Attributes](#recommended-use-of-options-and-attributes)
- [Supported Attributes](#supported-attributes)
- [URL Query arguments](#url-query-arguments)
- [General Iframe Embedding](#general-iframe-embedding)
- [Manual Shortcode Creation](#manual-shortcode-creation)
	- [Exceptions for the getting the embed code instead of the URL](#exceptions-for-the-getting-the-embed-code-instead-of-the-url)
<!-- /TOC -->

# Embedding via Shortcodes
Press the 'Embed Video' button in your post editor in WordPress and paste the URL or embed code into the field, optionally select options and press 'Insert Shortcode'. You can of course manually write Shortcodes. They have the advantage of being easier to read then URLs and you also can place them everywhere without the need for them to be on their own line, this becomes useful when aligning videos to text. You can also use them inside text widgets.

## Examples
`[[vimeo id=23316783]]` will be embedded with settings from the options page.

`[[youtube id="23316783" parameters="start=123&end=234" mode="normal" maxwidth="300" align="right"]]` will overwrite mode and maxwidth from the options and apply align and start+endtime in seconds.

As URL this would be:

`http://www.youtube.com/watch?v=dQw4w9WgXcQ&start=123&end=234&arve[mode]=normal&arve[maxwidth]=300&arve[align]=right`

For more examples visit the [Tests and Examples](https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/tests-and-examples/) page.

# Embedding Videos via URL
This is the WordPress way to embed objects. If you not want to be 'locked in' and only care about providers WordPress already supports (not responsively and without any customization options) then you can simply enable this plugin and see your videos magically become responsive. If you keep to providers WordPress also supports this means you are not getting 'locked in' by ARVE and may at any time delete the plugin and still have your Videos in place (basic unresponsive of course). Any special options and parameters will then simple be ignored.

To embed a video into a post or page, place its URL into the content area. Make sure the URL is on its own line and not hyper-linked (click-able when viewing the post).

For example: (Note the dash is only to prevent it from happening right here)

```
Check out this cool video:

-http://www.youtube.com/watch?v=dQw4w9WgXcQ

That was a cool video.
```

As a bonus you can add url parameters for ARVE to customize videos. If your URL already contains a query (everything behind a `?` like the Youtube URL above) then you need to start with `&`

`arve[xxxxx]=` controls the options the plugin provides, see [supported-attributes](#supported-attributes)

## Examples
This examples assume you post this URLs on their own line like

Simple Video embeds displayed the way you setup ARVE on the settings page.

`http://www.youtube.com/watch?v=dQw4w9WgXcQ`

`https://youtu.be/dQw4w9WgXcQ`

`http://vimeo.com/23316783`

Define a specific mode and align the video.

`http://vimeo.com/23316783?arve[mode]=lazyload-lightbox&arve[align]=right`

Define use the YouTube parameter to enable the light theme, start the video at the 30 second mark and use the arve[maxwidth] parameter to make ARVE limit the width of the embed.

`http://www.youtube.com/watch?v=dQw4w9WgXcQ&theme=light&start=30&arve[maxwidth]=400`

## Limitations
- Some providers are not supported via URL, see the main plugin page.
- For URLs with a `#` in it adding arguments with not work (Dailymotion playlists)

# Recommended use of Options and Attributes
Attributes always override options. It is recommended that you set the the options `Mode`, `Maximal Video Width` and `Autoplay` in a way that you want most (if not all) Videos appear on your site. You should only use attributes if you want some videos appear different then general ones rather then declaring this attributes for every single video.

For example if you have set Mode to Normal and Maximal Video Width to 500 the plugins options page, then URLs and Shortcodes will use that options to embed your videos. You can then decide to have a specific videos displayed differently by applying attrbutes to URLs or Shortcodes

# Supported Attributes

Attribute     | Used for
------------- | ----------------------------
id            | Is required for shortcodes and automatically generated within the shortcode button, obsolete for embeds via URL. For the `[[iframe]]` Shortcode this becomes the what would be the `src` for in a html iframe code
mode          | (Pro Addon only) normal/lazyload/lazyload-lightbox/lazyload-fullscreen/lazyload-fixed, optional option override
thumbnail     | (Pro Addon only) either a URL to a image or a ID to a media gallery image to be used as thumbnail. To get a item ID from the media gallery click a image in WordPress Admin -&gt; Media -&gt; Library and your URL will look like `.../upload.php?item=1234` where `1234` is the ID
autoplay      | optional option override
aspect\_ratio | for example `4:3`
maxwidth      | maximal width for videos in normal mode, optional option override
align         | left/right/center
start         | only for vimeo (1m2s format)
parameters    | support for custom URL parameters providers offer, currently only supported via shortcodes, will merge/overwrite with the values set on the options page

# URL Query arguments
Some Providers offer to customize their iframe embed codes with URL-query arguments. They always start with `?` and then `&` is used as a seperator.

This is a embed code copy pasted from the YouTube embed options after deselected related videos, controls and title and player options.

```html
<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0&controls=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
```

For ARVE the shortcode for this would be:

`[[youtube id="dQw4w9WgXcQ" parameters="rel=0&controls=0&showinfo=0"]]`

You can also extend the URL you post in a their own lines to embed videos with this querys.

`https://www.youtube.com?v=dQw4w9WgXcQ` and add `&rel=0&controls=0&showinfo=0` (note the query begins with &)  to

`https://www.youtube.com?v=dQw4w9WgXcQ&rel=0&controls=0&showinfo=0`

Please refer to the providers documentations on how to customize the embeds.
- [YouTube parameter documentation](https://developers.google.com/youtube/player_parameters#Parameters)
- [Vimeo parameter documentation](https://developer.vimeo.com/player/embedding#universal-parameters)
- [Dailymotion parameter documentation](https://developer.dailymotion.com/documentation#player-parameters)

Lets assume you have the parameters for YouTube on the ARVE settings page set to `iv_load_policy=3 rel=0 wmode=transparent` where `iv_load_policy=3` disables annotations but now you want to enable annotations for a single video, you would create this shortcode:

`[[youtube id="123456" paramaters="iv_load_policy=1&start=123"]]`

This shorcode would create the parameters

`?iv_load_policy=1&rel=0&wmode=transparent&;start=123`, rather then just

`?iv_load_policy=1&start=123` because it merges the one from the options page, overriding existing ones and adding not set ones.

As for shortcode attributes it is also recommended to prefer options instead of adding the same parameters to every single video. While obviously parameters like `start` and `end` make not much sense on the options page.

Parameters for providers that are currently have on option field are ignored inside shortcodes. If you happed to find out one of them supports parameters please let me know.
<div class="alert alert-warning" markdown=1>
Do not use any autoplay parameters, use the auto-play attribute instead. This is because for lazyload autoplay is automatically disabled this way no matter if its set. Also the parameter differs between providers. ARVE provides consistency. You can use `autoplay=true`, `=1` or `=yes` as well all will work.

<del>`[[youtube id="123" parameters="autoplay=1"]]`</del>

`[[youtube id="123" autoplay="yes"]]`
</div>

# General Iframe Embedding
This plugin not changes anything to usual HTML `iframe` embed codes you have to use the shortcode creator dialog and paste iframe embed codes there or write them manually. They will become `[[iframe id="http://..."]]`. The`id=` represents what is the `src=` in HTML embeds.

# Manual Shortcode Creation
This is not really needed anymore since the URL embeds and the Shortcode Creator should detect the IDs automatically, but in case something fails or you prefer to write shortcodes manually.

The id of a video is easy to find in the providers URL, for example:
- metacafe.com/watch/**237147**/9_11_alex_jones_and_charlie_sheen_interview
- youtube.com/watch?v=**QFbKhAfw4RI**&hd=1
- vimeo.com/**48237385**
- dailymotion.com/video/**abcdef_some_long_title**

I won't list all here, they are easy to guess, exceptions below:
For Ustream when you can often get the id from the URL ttp://www.ustream.tv/<strong>recorded/28355397/highlight/316911</strong> all this is the id! Sometimes its just like this ttp://www.ustream.tv/<strong>28355397</strong>/ if you are on a channel that has no number in the URL then go hover over the video and click share->url-icon (below the twitter icon), a URL like this ttp://www.ustream.tv/channel/<strong>12882755</strong> will be copied to your clipboard, that number is your video id.

## Exceptions for the getting the embed code instead of the URL
- Videojug: <object [...] /player?id=**e37b3839-21e4-de7d-f6ee-ff0008ca2ccd**"></param> [...]
- Gametrailers, Comedycentral and Spike: [...] <iframe src="[...]  http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:**8a0cde95-c528-44b9-ab44-5ff91955a38d**" [...]
