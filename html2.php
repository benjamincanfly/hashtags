<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>LNJF Hashtags</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link type="text/css" rel="stylesheet" href="/normalize.css"/>
    <link type="text/css" rel="stylesheet" href="/style.css"/>
	<script type="text/javascript">
		var hashtag="<?php echo $config['hashtag'] ?>";
	</script>
  </head>
  <body id="<?php echo $pageID ?>">
	<div id="outer">
		<h1 id="title"><div class="controls"><a href="/index.php">Main Menu</a><a href="/settings.php">Settings</a><a href="/getFavorites.php">Get Favorites</a><a href="/rateTweets.php">Rate Tweets</a><a href="/ratedTweets.php">View Rated Tweets</a></div>LNJF Hashtags<span class="hashtag">#<?php echo $config['hashtag']?></span></h1>
		<div id="wrapper">
		    <?php echo $body; ?>
		</div>
	</div>
	<div id="audio"></div>
  </body>
</html>
