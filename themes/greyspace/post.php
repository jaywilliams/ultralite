<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language->locale; ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title><?php echo $site->title; ?></title>
	
	<link rel="alternate" type="application/rss+xml" title="<?php echo $site->title; ?> RSS Feed" href="?view=rss" />
	<link rel="stylesheet" href="themes/greyspace/style_dark.css" type="text/css" charset="utf-8" title="Dark" />
	<link rel="alternate stylesheet" href="themes/greyspace/style_light.css" type="text/css" charset="utf-8" title="Light" />
	<style type="text/css">
		.section{
			width: <?php echo $image->width; ?>px;
		}
	</style>
</head>

<body>

<div id="wrapper">

	<div class="top section">
		<span class="published"><?php echo $image->published; ?></span>
		<h1 class="title"><?php echo $image->title; ?></h1>
	</div>

	
	<div class="middle section">
		<a href="?id=<?php echo $next_image->id; ?>"><img src="images/<?php echo $image->filename; ?>" alt="<?php echo $image->title; ?>" <?php echo $image->dimensions; ?> id="photo" /></a>
		<div class="site section">
			<h2 class="name"><a href="?" title="View Latest Photo"><?php echo $site->title; ?></a></h2>
			<em class="tagline"><?php echo $site->tagline; ?></em>
		</div>
	</div>


	<div class="bottom section">
		<div id="description">
			<p><?php echo $image->description; ?></p>
		</div>
	</div>

</div>

</body>
</html>