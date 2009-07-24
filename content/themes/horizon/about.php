<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?php eprint($site->title); ?></title>

<base href="<?php echo $site->url; ?>" />
<!-- META -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<!-- CSS -->
<link rel="stylesheet" type="text/css" href="themes/horizon/styles/horizon.css" />
<!-- SCRIPTS -->
</head>

<body>
<div id="header">
<div class="inside">
<h1><a href="./" title="Return to current image"><?php eprint($site->title); ?></a></h1>
<ul>
<li><a href="./" title="Return to Homepage">Home</a></li>
<li><a href="<?php url("view=archive",true); ?>" title="Archive">Archive</a></li>
<li><a href="<?php url("view=about",true); ?>" title="About">About</a></li>
</ul>
</div> <!-- /inside -->
</div> <!-- /header -->

<div id="infobox">
<div id="page">
<h2>So, what the heck is all this? </h2>
<p class="summary">This is my &quot;photoblog&quot; or &quot;photographic web log&quot;. This template is a  default of the Ultralite install, and I have yet to edit the  information posted here.</p>
<div class="clear"></div>
<div id="subbox">
<div id="col1">
<h2>Info</h2>
<p>I will eventually put some information about myself or my photography here. For now, please just enjoy the photos. </p>
</div>

<div id="col2">
<h2>Links</h2>
<p>Below is a list of links that you may find are useful in getting your blog up and running:</p>
<ul>
<li><a href="http://www.pixelpost.org">Pixelpost.org</a> - This is the application that runs this photoblog.</li>
<li><a href="http://www.photoblogs.org">Photoblogs.org</a> - Pretty much the oracle of photoblog community.</li>

</ul>
</div>

<div id="col3">
<h2>Additional Links </h2>
<ul>
<li><a href="http://www.photoblogs.org/">Photoblogs.org</a></li>
<li><a href="http://www.photoblogsmagazine.org">PhotoblogsMagazine.org</a></li>
<li><a href="http://www.photofriday.com">PhotoFriday.com</a></li>
<li><a href="http://photos.vfxy.com/">VFXY</a></li>
<li><a href="http://www.jpgmag.com">JPGmag.com</a></li>
<li><a href="http://www.coolphotoblogs.com/">CoolPhotoblogs.com</a></li>
</ul>
</div>
</div> <!--/subbox-->
</div> <!--/page -->
<div class="clear"></div>
</div> <!--/infobox -->

<div id="footer">
<ul>
<li>Powered by <a href="http://www.pixelpost.org" title="Powered by Ultralite">Ultralite</a></li>
<li>Designed by <a href="http://www.cancerbox.com" title="Designed by Scott Craig">SCraig</a></li>
<li>This website uses valid <a href="http://validator.w3.org/check/referer" title="This website uses Valid xHTML">xHTML</a> / <a href="http://jigsaw.w3.org/css-validator/check/referer" title="This website uses Valid CSS2">CSS</a></li>
</ul>
<ul>
<li>&copy; <?php echo date('Y'); ?> <?php eprint($site->title); ?></li>
<li><a href="<?php url("view=rss",true); ?>">RSS 2.0</a></li>
<li><a href="admin/">Login</a></li>
</ul>
</div>


</body>
</html>