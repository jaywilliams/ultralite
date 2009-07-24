<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php eprint($config->locale); ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title>Archive / <?php eprint($config->name); ?> <?php if($config->page > 1) echo "/ Page $config->page"; ?></title>
	
	<base href="<?php eprint($config->url); ?>" />
	
	<link rel="alternate" type="application/rss+xml" title="<?php eprint($config->title); ?> RSS Feed" href="<?php url("view=rss",true); ?>" />
	<link rel="stylesheet" href="<?php eprint($config->url); ?>/content/themes/greyspace/style_dark.css" type="text/css" charset="utf-8" title="Dark" />
	<link rel="alternate stylesheet" href="<?php eprint($config->url); ?>/content/themes/greyspace/style_light.css" type="text/css" charset="utf-8" title="Light" />
	<!-- <style type="text/css">
		.section{
			width: 650px;
		}
	</style> -->
</head>

<body>

<div id="wrapper">

	<div class="top section">
		<span class="published">Archive</span>
		<div class="nav"><a href="./">Home</a> <a href="archive" class="active">Archive</a> <a href="about">About</a></div>
		<h1 class="title"><?php eprint($archive->title); ?></h1>
		<br class="clear"/>
	</div>

	
	<div class="middle section">
		
		
		<div class="thumbnails section">
			<?php tt('thumbnails'); ?>
		</div>
		
		<div class="site section">
			<h2 class="name"><a href="./" title="View Latest Photo"><?php eprint($config->name); ?></a></h2>
			<em class="tagline"><?php eprint($config->description); ?></em>
		</div>
	</div>

	<div class="bottom section">
		<?php if ($config->pagination > 0): ?>
		<div class="pagination">	
			<?php if (($config->page) > 1): ?>
				<a href="<?php url("view=archive&page=".($config->page-1),true) ?>" class="previous">&#x2190; Previous Page</a>
			<?php else: ?>
				<a class="previous disabled">&#x2190; Previous Page</a>
			<?php endif ?>

			<?php if ($config->page < $config->total_pages): ?>
				<a href="<?php url("view=archive&page=".($config->page+1),true) ?>" class="next">Next Page &#x2192;</a>
			<?php else: ?>
				<a class="next disabled">Next Page &#x2192;</a>
			<?php endif ?>
			
			<span class="page"><?php echo "Page $config->page of $config->total_pages"; ?></span>
			
			<br class="clear"/>
		</div>
		<?php endif ?>
		
		<!-- <div class="tags">
			<a href="#">tag1</a>
		</div> -->
		
	</div>

</div>

</body>
</html>