<?php
/*
 * login_with_instagram.php
 *
 * @(#) $Id: login_with_instagram.php,v 1.1 2013/01/10 05:29:04 mlemos Exp $
 *
 */

	require('http.php');
	require('oauth_client.php');

	$client = new oauth_client_class;
	$client->debug = 1;
	$client->server = 'Instagram';
	$client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
		dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_instagram.php';

	$client->client_id = ''; $application_line = __LINE__;
	$client->client_secret = '';

	if(strlen($client->client_id) == 0
	|| strlen($client->client_secret) == 0)
		die('Please go to Instagram Apps page http://instagram.com/developer/register/ , '.
			'create an application, and in the line '.$application_line.
			' set the client_id to client id key and client_secret with client secret');

	/* API permissions
	 */
	$client->scope = 'basic';
	if(($success = $client->Initialize()))
	{
		if(($success = $client->Process()))
		{
			if(strlen($client->access_token))
			{
				$success = $client->CallAPI(
					'https://api.instagram.com/v1/users/self/', 
					'GET', array(), array('FailOnAccessError'=>true), $user);
				if(!$success)
				{
					$client->ResetAccessToken();
				}
			}
		}
		$success = $client->Finalize($success);
	}
	if($client->exit)
		exit;
	if($success)
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Instagram OAuth client results</title>
</head>
<body>
<?php
		echo '<h1>', HtmlSpecialChars($user->data->full_name), 
			' you have logged in successfully with Instagram!</h1>';
		echo '<pre>', HtmlSpecialChars(print_r($user, 1)), '</pre>';
?>
</body>
</html>
<?php
	}
	else
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OAuth client error</title>
</head>
<body>
<h1>OAuth client error</h1>
<pre>Error: <?php echo HtmlSpecialChars($client->error); ?></pre>
</body>
</html>
<?php
	}

?>