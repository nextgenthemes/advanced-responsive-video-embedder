<div class="alert-warning">Please note that the documentation contains things only present in the upcoming versions 7.0 that brings a hell of a lot new cool functionality</div>

### Recommended use of Options and Attributes

Shortcode Attributes (Options selected in the 'Embed Video Dialog') always override settings on the ARVE settings page. It is recommended that you set the the options `Mode`, `Maximal Video Width` and `Autoplay` in a way that you want most (if not all) Videos appear on your site. You should only use attributes for settings that are also present on the settings page if you want some videos appear different then general ones rather then declaring this attributes for every single video.

For example if you have set Mode to Normal and Maximal Video Width to 500 the plugins options page, then URLs and Shortcodes will use that options to embed your videos. You can then decide to have a specific videos displayed differently by applying attributes to URLs or Shortcodes.

### Embedding via Shortcodes

Press the 'Embed Video' button in your post editor in WordPress and paste the URL or embed code into the field, optionally select options and press 'Update'. You can of course manually write Shortcodes. They have the advantage of being easier to read then URLs and you also can place them everywhere without the need for them to be on their own line, this becomes useful when aligning videos to text. You can also use them inside text widgets.

### Embedding Videos via URL

This is the WordPress way to embed videos. If you not want to be 'locked in' and only care about providers WordPress already supports (not responsively and without any customization options) then you can simply enable this plugin and see your videos magically become responsive. If you keep to providers WordPress also supports this means you are not getting 'locked in' by ARVE and may at any time delete the plugin and still have your Videos in place (basic unresponsive of course). Any special options and parameters will then be ignored.

To embed a video into a post or page, place its URL into the content area. Make sure the URL is on its own line and not hyper-linked (click-able when viewing the post).

For example: (Note the dash is only to prevent it from happening right here)

```
Check out this cool video:

-http://www.youtube.com/watch?v=dQw4w9WgXcQ

That was a cool video.
```

As a bonus you can add url parameters for ARVE to customize videos. See [supported-attributes](#supported-attributes)

#### Supported Attributes

Note the this table is generated from the plugins code for the shortcode UI dialog, so you have that exact same descriptions inside your WordPress admin

[arve_params]

# URL Query arguments
Some Providers offer to customize their iframe embed codes with URL-query arguments. They always start with `?` and then `&` is used as a seperator.

This is a embed code copy pasted from the YouTube embed options after deselected related videos, controls and title and player options.

```html
<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0&controls=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
```

For ARVE the shortcode for this would be:

`rel=0&controls=0&showinfo=0`

You can also extend the URL you post in a their own lines to embed videos with this querys.

`https://www.youtube.com?v=dQw4w9WgXcQ` and add `&rel=0&controls=0&showinfo=0` (note the query begins with &)  to

`https://www.youtube.com?v=dQw4w9WgXcQ&rel=0&controls=0&showinfo=0`

Please refer to the providers documentations on how to customize the embeds.
- [YouTube parameter documentation](https://developers.google.com/youtube/player_parameters#Parameters)
- [Vimeo parameter documentation](https://developer.vimeo.com/player/embedding#universal-parameters)
- [Dailymotion parameter documentation](https://developer.dailymotion.com/documentation#player-parameters)

Lets assume you have the parameters for YouTube on the ARVE settings page set to `iv_load_policy=3&rel=0&wmode=transparent` where `iv_load_policy=3` disables annotations but now you want to enable annotations for a single video, you would create this shortcode:

`[[youtube id="123456" paramaters="iv_load_policy=1&start=123"]]`

This shortcode would create the parameters

`?iv_load_policy=1&rel=0&wmode=transparent&start=123`, rather then just

`?iv_load_policy=1&start=123` because it merges the one from the options page, overriding existing ones and adding not set ones.

As for shortcode attributes it is also recommended to prefer options instead of adding the same parameters to every single video. While obviously parameters like `start` and `end` make not much sense on the options page.

Parameters for providers that are currently have on option field are ignored inside shortcodes. If you happed to find out one of them supports parameters please let me know.
<div class="alert alert-warning" markdown=1>
Do not use any autoplay parameters, use the auto-play attribute instead. This is because for lazyload autoplay is automatically disabled this way no matter if its set. Also the parameter differs between providers. ARVE provides consistency. You can use `autoplay=true`, `=1` or `=yes` as well all will work.

<del>`[[youtube id="123" parameters="autoplay=1"]]`</del>

`[[youtube id="123" autoplay="yes"]]`
</div>

### General Iframe Embedding

Generally speaking ARVEs embedding for unlisted providers should be pretty straight forward. Paste the iframe embed code in the 'Embed Video' dialog and be happy. But there are exceptions.

ARVE supports responsive embedding for any video provider gives out iframe embeds codes that handle resizing well. You can test this by taking the URL from the  `src=` attribute of the embed code and open it on your browser then resizing the browser window.

Example embed code a providers may give out: `<iframe src="http://example.com/embed.php?width=640&height=480" width="640" height="480">`

There are embed code providers that have height and width parameters in the URL as well, specifically speaking about the URL here not the iframe attributes. That is often a bad sign for fixed size embeds. But I also have seen this kids of embeds work responsively. If they do you should change the dimensions to something that represents the highest possible size you want you embeds to be on your site.

ARVE uses only only the `src` URL from iframe embed codes. If your embed code has a unusual aspect ratio you need to provide that as well. The above example has a 4:3 aspect ratio.

Manually creating a arve embed for the above example would be []

The plugin not changes anything to usual HTML `iframe` embed codes you have to use the shortcode creator dialog and paste iframe embed codes there or write them manually. They will become `[[arve url="http://..." ...]]`. The`url=` represents what is the `src=` in HTML embeds. ARVE assumes a URL is a iframe src if none of the supported providers is detected from that url.

Alternatively there is also the `[[iframe src="http://..." ...]]` shortcode that actually

### Feature Table

You may not need to add title or thumbnails depending on what provider you use.

[arve_supported]
