## Frequently Asked Questions ##

### I have a problem ... ###

Please report it on [community.nextgenthemes.com](https://community.nextgenthemes.com) **and plaese not on the wordpess.org forums, thanks.**

### Why are my videos not filling their container? ###

You are most likely use `align`, this plugin has a option for limiting video with with alignment. If you want your videos to fill their containers then you should not use the `align=left/right/center` shortcode attribute or the `arve[align]=` URL parameter. This assumes that you left the 'Video Maximal Width' field on the options page empty (default + recommended)

### Why are videos opening full window (provider URL) when I click on them? ###

You most likely do not have the 'jQuery Colorbox' Plugin installed or JavaScript disabled both is needed for thumbnail mode. Another reason can be that do have JavascriptErrors or Browser addons like NoScript blocking JavaScript trom Executing.

### Can you add a video provider? ###

I have no plans on implementing providers that include videos via JavaScript such as www.nicovideo.jp. I also will not implement videos from mainstream media news organizations. For others, feel free to ask.

### How do I embed Iframes? ###

This plugin not changes anything to usual HTML `<iframe>` embed codes you have to use the shortcode creator dialog and paste iframe embed codes there or write them manually. They will become `[iframe id="https://..."]`. The id represents what is the `src` in HTML embeds.

### Why does my Youtube video not repeat/loop? ###

This plugins embed is considered as 'custom player' by YouTube so you have to pass the video ID as playlist parameters to make the loop work.

`[[youtube id="123456" parameters="loop=1 playlist=123456"]`