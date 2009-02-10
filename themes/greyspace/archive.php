<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language->locale; ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title><?php echo $site->title; ?> // Archive</title>
	
	<base href="<?php echo $site->url; ?>" />
	
	<style type="text/css">
		img {
			border: 1px solid #aaa;
			padding: 10px;
			margin: 5px;
		}
	</style>
</head>

<body>

<div>

<!-- FOREACH $image->thumbnails -->

<?php foreach ($image->thumbnails as $thumbnail): ?>
<?php echo "<a href=\"".url("view=post&id={$thumbnail->id}")."\"> <img src=\"images/{$thumbnail->filename}\" alt=\"{$thumbnail->title}\" width=\"{$thumbnail->width}\" height=\"{$thumbnail->height}\" /> </a>\n"; ?>
<?php endforeach ?>

</div>

<div>

<!-- ECHO $thumbnails -->

<?php echo $thumbnails; ?>

</div>

</body>
</html>