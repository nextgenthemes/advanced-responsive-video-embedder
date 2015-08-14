### Quick Introduction ###

URLs like `https://vimeo.com/124400795` on its own line will produce full responsive embeds. If you already use this with WordPress default, this is the perfect drop-in replacement to make everything responsive, no need to do anything.

https://vimeo.com/124400795

Extremly customizable with support for anything providers offer to customize embeds. Other plugins offer some of this features with huge bloated dialogs, ARVE is different, it just lets you do anything you want if you have a few seconds to look up what the parameters do.

Here we are starting the video at the 30 seconds mark, disable the fullscreen button, switch to YouTubes light theme and set the aspect_ratio to cinemascope.

`https://youtu.be/Q6goNzXrmFs?start=30&fs=0&theme=light&arve[aspect_ratio]=21:9`

the same thing can be done with a shortcode:

`[[youtube id="Q6goNzXrmFs" parameters="start=30 fs=0 theme=light" aspect_ratio="21:9"]]`

https://youtu.be/Q6goNzXrmFs?start=30&fs=0&theme=light&arve[aspect_ratio]=21:9