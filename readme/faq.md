
## Frequently Asked Questions ##

### I have a problem ... ###

Please report it on [nextgenthemes.com/support/](https://nextgenthemes.com/support/) **and please do not on the wordpess.org forums, thanks.**

### I’m experiencing these errors on Google Search Console ###

Best option to resolve these errors is to get [ARVE Pro](https://nextgenthemes.com/plugins/arve-pro/) as it fills these in for you hassle-free, only activate the add-on - done. For more options, please see the [detailed answer](https://nextgenthemes.com/plugins/arve/faq/).

### How to get the pro version working? ###

1. Go though the purchase process on [nextgenthemes.com](https://nextgenthemes.com/plugins/arve-pro/)
1. Follow the 3 easy steps you get with the purchase receipt. It is basically downloading a arve-pro.zip and installing it through your WordPress Admin panel.
1. After that you may want to switch your default mode to Lazyload or Lightbox on the ARVE settings page.

### Why are my videos not filling their container? ###

You may need to adjust your 'Maximal Width' setting to your liking.

You are most likely use `align`, this plugin has a option for limiting video width with alignment. If you want your videos to fill their containers then you should not use the `align` shortcode attribute.

### Can you add a video provider? ###

Feel free to ask.

### How do I embed videos from a unlisted providers / iframes? ###

This plugin not changes anything to usual HTML `<iframe>` embed codes you have to use the shortcodes or the Gutenberg Block. They will become `[arve url="https://..."]`. The url represents what is the `src` in HTML embeds. It works as simple as this, if the `[arve]` shortcode does not detect a known URL structure then it will treat the URL as a `src` for the iframe. 

### Why does my YouTube video not repeat/loop? ###

This plugins embed is considered as 'custom player' by YouTube so you have to pass the video ID as playlist parameters to make the loop work.

`[arve url="https://www.youtube.com/watch?v=pvRqvX413Ik" parameters="loop=1&playlist=pvRqvX413Ik"]`
