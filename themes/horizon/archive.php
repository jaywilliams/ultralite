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
<h2>Browse Archive:</h2>
<div id="thumbnails">
<!-- <BROWSE_CATEGORIES> -->
<br /><br />
<!-- <THUMBNAILS> -->
	
<?php tt('thumbnails'); ?>
	
	
</div> <!--/thumbnails -->

<ul>	
	<?php if (($site->page) > 1): ?>
		<li><a href="<?php url("view=archive&page=".($site->page-1),true) ?>" class="previous">Previous Page</a></li>
	<?php else: ?>
		<li><a class="previous disabled">Previous Page</a></li>
	<?php endif ?>
	
	<?php if ($site->page < $site->total_pages): ?>
		<li><a href="<?php url("view=archive&page=".($site->page+1),true) ?>" class="next">Next Page</a></li>
	<?php else: ?>
		<li><a class="next disabled">Next Page</a></li>
	<?php endif ?>
</ul>


</div> <!--/page-->
</div> <!--/infobox-->

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