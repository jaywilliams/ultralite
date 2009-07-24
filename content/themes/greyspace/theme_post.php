<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php eprint($config->locale); ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title><?php eprint($post->title); ?> / <?php eprint($config->name); ?></title>
	
	<base href="<?php eprint($config->url); ?>" />
	
	<link rel="alternate" type="application/rss+xml" title="<?php eprint($config->name); ?> RSS Feed" href="<?php url("view=rss",true); ?>" />
	<link rel="stylesheet" href="content/themes/greyspace/style_dark.css" type="text/css" charset="utf-8" title="Dark" />
	<link rel="alternate stylesheet" href="content/themes/greyspace/style_light.css" type="text/css" charset="utf-8" title="Light" />
	<style type="text/css">
		.section{
			width: <?php echo $post->width; ?>px;
		}
	</style>
	<?php $plugins->do_action('theme_head');  ?>
</head>

<body>

<div id="wrapper">

	<div class="top section">
		<span class="published"><?php eprint($post->published); ?></span>
		<div class="nav"><a href="./" class="active">Home</a> <a href="archive">Archive</a> <a href="about">About</a></div>
		<h1 class="title"><?php eprint($post->title); ?></h1>
		<br class="clear"/>
	</div>

	
	<div class="middle section">
		<a href="<?php url("id={$next_image->id}",true); ?>"><img src="content/images/<?php eprint($post->filename); ?>" alt="<?php eprint($post->title); ?>" <?php echo $post->dimensions; ?> id="photo" /></a>
		<div class="site section">
			<h2 class="name"><a href="./" title="View Latest Photo"><?php eprint($config->name); ?></a></h2>
			<em class="tagline"><?php eprint($config->description); ?></em>
		</div>
	</div>


	<div class="bottom section">
		<div id="description">
			<p><?php eprint($post->description); ?></p>
		</div>
	</div>

</div>

<?php $plugins->do_action('theme_body'); ?>
</body>
</html>