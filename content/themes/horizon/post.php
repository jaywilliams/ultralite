<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?php eprint($site->title); ?></title>

<base href="<?php echo $site->url; ?>" />

<!-- Link for RSS feed autodiscovery -->
<link rel="alternate" type="application/rss+xml" title="<?php eprint($site->title); ?> RSS Feed" href="<?php url("view=rss",true); ?>" />

<!-- META -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta http-equiv="content-type" name="keywords" content="PhotoBlog,<?php eprint($site->title); ?>,<?php eprint($image->title); ?>,Pixelpost,Ultralite" />
<meta http-equiv="content-type" name="description" content="<?php eprint($site->title); ?>: <?php eprint($image->title); ?>, <?php eprint(strip_tags($image->description)); ?>" />

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="themes/horizon/styles/horizon.css" />

<!-- SCRIPTS -->
<script src="themes/horizon/scripts/lib/prototype.js" type="text/javascript"></script>
<script src="themes/horizon/scripts/src/scriptaculous.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
<!--
	function clearBox(box) {
		 if(box.value==box.defaultValue) {
	 	 	 box.value = "";
	 	 }
	 }
//-->
</script>
</head>

<body>

<div id="image-info" style="display:none">

<div class="inside">
<h2><?php eprint($image->title); ?> <em><?php eprint($image->published); ?></em> <!--<em> <IMAGE_COMMENTS_NUMBER> <IMAGE_COMMENT_TEXT> </em>--> </h2>

<div id="image_notes">
<?php eprint($image->description); ?>
<p><a href="#" onclick="Effect.toggle('image-info','BLIND'); return false;">(close)</a></p>

<div id="addcomment">
<!-- <form method='post' action='index.php?x=save_comment' name='commentform' accept-charset='UTF-8'>
<textarea name='message' rows='2' cols='40' onfocus="clearBox(this);">Type your comment here.</textarea><br /><br />
<input type='text' name='name' class='input' value='<VINFO_NAME>' id="name"/>&nbsp;&nbsp;&nbsp; <label for="name">Name</label><br /><br />
<input type='text' name='url' class='input' value='<VINFO_URL>' id="url"/>&nbsp;&nbsp;&nbsp; <label for="url">Website URL, if any</label><br /><br />
<input class='input' type='text' name='email' value='<VINFO_EMAIL>' id="email"/>&nbsp;&nbsp;&nbsp; <label for="email">Email (not visible to others)</label><br /><br />

<input name='vcookie' type='checkbox' id="saveinfo" value='set' checked="checked" />
<label for="saveinfo">Save User Info</label><br /><br />
&nbsp;&nbsp;<input type='submit' value='Add' class="comment-button"/>
<input type='hidden' name='parent_id' value='<IMAGE_ID>' />
<input type='hidden' name='parent_name' value='<IMAGE_NAME>' />

</form> -->
</div>

</div> <!-- End image notes -->

<div id="image_comments">
<!-- <IMAGE_COMMENTS> -->
</div> <!--/image comments -->

</div>  <!-- /inside -->
<div class="clear"></div>
</div> <!--/d2 -->

<div id="header">
<div class="inside">
	<h1><a href="./" title="Return to current image"><?php eprint($site->title); ?></a></h1>
	<ul>
		<li><a href="./" title="Return to Homepage">Home</a></li>
		<li><a href="<?php url("view=archive",true); ?>" title="Archive">Archive</a></li>
		<li><a href="<?php url("view=about",true); ?>" title="About">About</a></li>
		<li>/</li>
		<li class="secondary"><a href="#" onclick="Effect.toggle('image-info','BLIND'); return false;">Info <!-- <IMAGE_COMMENTS_NUMBER> <IMAGE_COMMENT_TEXT> --></a></li>
	</ul>
</div> <!-- /inside -->
</div> <!-- /header -->

<div id="photobox">
	<a href="<?php url("id={$next_image->id}",true); ?>">
		<img src="images/<?php echo $image->filename; ?>" alt="<?php eprint($image->title); ?>" title="<?php eprint($image->title); ?>" <?php echo $image->dimensions; ?> id="photo" />
	</a>
</div> <!-- /photo -->

<div id="infobox">
<div id="image-navigate" class="inside">
	<ul>
		<li class="left"><a href="<?php url("id={$next_image->id}",true); ?>">Previous</a></li>
		<li class="left" style="text-align:center"><a href="#" onclick="Effect.toggle('image-info','BLIND'); return false;">Info <!-- &amp; 0 Comments --></a></li>
		<li class="right" style="text-align:right"><a href="<?php url("id={$previous_image->id}",true); ?>">Next</a></li>
	</ul>
</div>
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