<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php eprint($language->locale); ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title><?php eprint($image->title); ?> / <?php eprint($site->title); ?></title>
	
	<base href="<?php eprint($site->url); ?>" />
	
	<link rel="alternate" type="application/rss+xml" title="<?php eprint($site->title); ?> RSS Feed" href="<?php url("view=rss",true); ?>" />
	<link rel="stylesheet" href="themes/greyspace/style_dark.css" type="text/css" charset="utf-8" title="Dark" />
	<link rel="alternate stylesheet" href="themes/greyspace/style_light.css" type="text/css" charset="utf-8" title="Light" />
	<style type="text/css">
		.section{
			width: <?php echo $image->width; ?>px;
		}
	</style>
	<?php $plugins->do_action('head');  ?>
</head>

<body>

<div id="wrapper">

	<div class="top section">
		<span class="published"><?php eprint($image->published); ?></span>
		<div class="nav"><a href="./" class="active">Home</a> <a href="archive">Archive</a> <a href="about">About</a></div>
		<h1 class="title"><?php eprint($image->title); ?></h1>
		<br class="clear"/>
	</div>

	
	<div class="middle section">
		<a href="<?php url("id={$next_image->id}",true); ?>"><img src="images/<?php eprint($image->filename); ?>" alt="<?php eprint($image->title); ?>" <?php echo $image->dimensions; ?> id="photo" /></a>
		<div class="site section">
			<h2 class="name"><a href="./" title="View Latest Photo"><?php eprint($site->title); ?></a></h2>
			<em class="tagline"><?php eprint($site->tagline); ?></em>
		</div>
	</div>


	<div class="bottom section">
		<div id="description">
			<p><?php eprint($image->description); ?></p>
		</div>
	</div>

</div>
<?php $myMode='post'; $plugins->do_action('body',$myMode);  ?>


<?php echo $myMode; ?>
</body>
</html>