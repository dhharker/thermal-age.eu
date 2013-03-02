<?php
/**
 * Opauth basic configuration file to quickly get you started
 * ==========================================================
 * To use: rename to opauth.conf.php and tweak as you like
 * If you require advanced configuration options, refer to opauth.conf.php.advanced
 */

$config = array(
/**
 * Path where Opauth is accessed.
 *  - Begins and ends with /
 *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
 *  - if Opauth is reached via http://auth.example.org/, path is '/'
 */
	'path' => '/users/oauth/',

/**
 * Callback URL: redirected to after authentication, successful or otherwise
 */
	'callback_url' => '/users/oacb/google',
	
/**
 * A random string used for signing of $auth response.
 */
	'security_salt' => 'q3tn978ywoa83gfhaqo4btq834afBNY8LOBG7UILQ3CFRq234tbvfhk\zasybgftibiYUV768IOWE4VTB79OW3G',
		
/**
 * Strategy
 * Refer to individual strategy's documentation on configuration requirements.
 * 
 * eg.
 * 'Strategy' => array(
 * 
 *   'Facebook' => array(
 *      'app_id' => 'APP ID',
 *      'app_secret' => 'APP_SECRET'
 *    ),
 * 
 * )
 *
 */
	'Strategy' => array(
		// Define strategies and their respective configs here
		'Google' => array (
                    'client_id' => '1017893960545-kkd0pq7kp16is7m7513jcfqq7iei2ncu.apps.googleusercontent.com',
                    'client_secret' => 'yZ2dW2E6sDibLv8OP-IzpBgF',
                    'redirect_uri' => 'http://thermal-age.eu/users/oacb/',
                )
	),
);