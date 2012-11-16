

/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth/twitteroauth.php');
require_once('twitteroauth/config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./twitteroauth/clearsessions.php');
}

/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
$content = $connection->get('account/verify_credentials');

/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
//$connection->post('statuses/update', array('status' => date(DATE_RFC822)));
//$connection->post('statuses/destroy', array('id' => 5437877770));
//$connection->post('friendships/create', array('id' => 9436992));
//$connection->post('friendships/destroy', array('id' => 9436992));

//count=200&screen_name=namesfuckinhank

//$body.='<pre>'.print_r($connection, true).'</pre><br/>';

$thing=$connection->get('search/tweets', array('q' => "faketwilightspoilers"));

//, 'result_type'=>"recent", 'count'=>10, 'since'=>'268837894528065536'));

foreach($thing as $tweet){
	
	$tweet=object_to_array($tweet);

	$body.='<pre>'.print_r($tweet,true).'</pre><br/>';
}
