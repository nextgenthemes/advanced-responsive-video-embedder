<!doctype html>
<html lang="">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Advanced Responsive Video Embed Player</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      body {
        overflow-y:hidden;
      }
      html,
      body,
      .arve-wrapper,
      .arve-embed-container,
      .arve-video {
        height: 100%;
        width: 100%;
        padding: 0 !important; /* to overwrite inline style on container */
        margin: 0;
        background-color: #000;
      }
    </style>
  </head>
  <body>
    <!--[if lt IE 9]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://getfirefox-/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <?php echo $embed_html; ?>
  </body>
</html>
