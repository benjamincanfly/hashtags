<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Late Night Hashtags</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link type="text/css" rel="stylesheet" href="/normalize.css"/>
    <link type="text/css" rel="stylesheet" href="/style.css"/>
	<script type="text/javascript" src="/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/script.js"></script>
	<script type="text/javascript" src="/joey.js"></script>
	<script type="text/javascript">
		var hashtag="<?php echo $config['hashtag'] ?>";
	</script>
	
	<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1; user-scalable=0;">
	
	<?php echo $head; ?>
	
	<link rel="stylesheet" href="handheld.css" media="handheld, only screen and (max-device-width:640px)"/>
	<link rel="stylesheet" href="handheld.css"/>
	
  </head>
  <body id="<?php echo $pageID ?>">
	<div id="header">
		<div class="wrap">
			<h1 id="title"><a href="/index.php">Late Night Hashtags</a><br/><span id="hashtag" class="hashtag"><em class='one'>#<?php echo $config['hashtag']?></em><em class='two'>Click to set a new hashtag</em></span></h1>
			<span class="controls"><a href="/tweets_1.php">1) Favorite Tweets</a><a href="/tweets_2.php">2) Cross-Favorite Tweets</a><a href="/tweets_3.php">3) Rate Tweets</a><a href="/tweets_4.php">4) View Rated Tweets</a><a id="joeyLink">Joey</a></span>
		</div>
	</div>
	<div id="content">
		<div class="wrap">
		    <?php echo $body; ?>
		</div>
	</div>
	<div id="audio"></div>
  </body>
</html>
