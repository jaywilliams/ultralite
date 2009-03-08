<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language->locale; ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title><?php eprint($site->title); ?> // Archive <?php if($site->page>1) echo "// Page $site->page of $site->total_pages"; ?></title>
	
	<base href="<?php eprint($site->url); ?>" />
	<link rel="stylesheet" href="themes/greyspace/style_dark.css" type="text/css" charset="utf-8" title="Dark" />
	<link rel="alternate stylesheet" href="themes/greyspace/style_light.css" type="text/css" charset="utf-8" title="Light" />
	<!-- <style type="text/css">
		img {
			border: 1px solid #aaa;
			padding: 10px;
			margin: 5px;
		}
	</style> -->
</head>

<body>

<div>

<!-- Include Thumbnails, as a template tag -->
<!-- <?php tt('thumbnails','mode=reverse'); ?> -->

<ul id="thumbnails">
<?php foreach ($image->thumbnails as $thumbnail): ?>
<?php

// List the thumbnails as a list
	echo
	"<li><a href=\"".url("view=post&id={$thumbnail->id}")."\" class=\"thumbnail\">".
		"<img src=\"thumbnails/thumb_{$thumbnail->filename}\" alt=\"".escape($thumbnail->title)."\" width=\"{$thumbnail->width}\" height=\"{$thumbnail->height}\" />".
	"</a></li>\n";

// List the thumbnails as images & links
	// echo
	// "<a href=\"".url("view=post&id={$thumbnail->id}")."\" class=\"thumbnail\">".
	// 	"<img src=\"thumbnails/thumb_{$thumbnail->filename}\" alt=\"".escape($thumbnail->title)."\" width=\"{$thumbnail->width}\" height=\"{$thumbnail->height}\" />".
	// "</a>";
	
?>
<?php endforeach ?>
</ul>

<br class="clear"/>

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

</div>

</body>
</html>