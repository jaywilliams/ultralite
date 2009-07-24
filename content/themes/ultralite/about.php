<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language->locale; ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title><?php echo $site->title; ?></title>
	
	<base href="<?php echo $site->url; ?>" />
	
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
		}

		body {
			width: 650px;
			margin: 0 auto;
			text-align: center;
			font: 14px "Lucida Grande", Lucida, Verdana, sans-serif;
		}

		#ultralite {
			text-align: left;
		}

		h1 {
			font-size: 2.0em;
		}

		h2 {
			font-size: 1.3em;
		}

		h3#title {
			font-size: 1.6em;
			float: left;
		}

		h2,h3 {
			margin-bottom: 12px;
		}
		
		#published {
			float: right;
			margin-top: 10px;
		}

		img#photo {
			border: 1px solid #000;
		}

		#description p {
			padding: 12px;
			text-align: justify;
		}
	</style>
</head>

<body>

<div id="ultralite">
	<h1><?php echo $site->title; ?></h1>
	
	<h2><?php echo $site->slogan; ?></h2>
	
	<h3>Are you tired of photoblog software the tries to do it all?</h3>
	
	<div id="description">
		<p>We were, so that's why we created Ultralite.  The photoblog app that is super fast and has the features you need, nothing more, nothing less. That's what makes Ultralite different.</p>
	</div>
</div>

</body>
</html>