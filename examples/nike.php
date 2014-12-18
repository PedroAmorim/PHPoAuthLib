<?php

/**
 * Example of retrieving an authentication token of the Nike service
 *
 * PHP version 5.4
 *
 * @author     Pedro Amorim <dev.pamorim@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Nike;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['nike']['key'],
    $servicesCredentials['nike']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Nike service using the credentials, http client and storage mechanism for the token
/** @var $nikeService Nike */
$nikeService = $serviceFactory->createService('nike', $credentials, $storage);

if (!empty($_GET['code'])) {
    // retrieve the CSRF state parameter
    $state = isset($_GET['state']) ? $_GET['state'] : null;

    // This was a callback request from Nike, get the token
    $token = $nikeService->requestAccessToken($_GET['code'],$state);

    // Get User information
    $extraParams = $token->getExtraParams();

    // Show some of the resultant data
    echo 'Your unique user id is: '.$extraParams['user_id'].'<br><img src="'.$extraParams['profile_img_url'].'"></img>';

    // Send a request with it
    $result = json_decode($nikeService->request('v1/me/sport/activities'), true);
    echo 'Your activities:';
    foreach ($result['data'] as $key => $value) {
        echo print_r($value).'<br>'.$value['links']['href'];
    }

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $nikeService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Nike+!</a>";
}
