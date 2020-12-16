<?php
/*
Plugin Name: Amazon Affiliate
Plugin URI: https://github.com/floschliep/YOURLS-Amazon-Affiliate
Description: Add your Amazon Affiliate-Tag to all Amazon URLs before redirection
Version: 1.2
Author: Florian Schliep
Author URI: https://floschliep.com
*/

yourls_add_action('pre_redirect', 'flo_avonRep');

function flo_avonRep($args) {
	// insert your personal settings here
	$tagAR = 'YOUR_TAG_HERE';

	// get url from arguments; create dictionary with all regex patterns and their respective affiliate tag as key/value pairs
	$url = $args[0];
	$patternTagPairs = array(
		'/^http(s)?:\\/\\/(www\\.)?avon.uk.com+/ui' => $tagAR
	);

	// check if URL is a supported Amazon URL
	foreach ($patternTagPairs as $pattern => $tag) {
		if (preg_match($pattern, $url) == true) {
			// matched URL, now modify URL
			$url = cleanUpURL($url);
			$url = addTagToURL($url, $tag);

			// redirect
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $url");

			// now die so the normal flow of event is interrupted
			die();
		}
	}
}

function cleanUpURL($url) {
	// check if last char is an "/" (in case it is, remove it)
	if (substr($url, -1) == "/") {
		$url = substr($url, 0, -1);
	}

	// remove existing affiliate tag if needed
	$existingTag;
	if (preg_match('/tag=.+&?/ui', $url, $matches) == true) {
		$existingTag = $matches[0];
	}
	if ($existingTag) {
		$url = str_replace($existingTag, "", $url);
	}


	return $url;
}

function addTagToURL($url, $tag) {
	// add our tag to the URL
	if (strpos($url, '?') !== false) {
		// there's already a query string in our URL, so add our tag with "&"
		// add tag depending on if we need to add a "&" or not
		if (substr($url, -1) == "&") {
			$url = $url.'attach='.$tag;
		} else {
			$url = $url.'&attach='.$tag;
		}
	} else { // start a new query string
		$url = $url.'?attach='.$tag;
	}

	return $url;
}


?>
