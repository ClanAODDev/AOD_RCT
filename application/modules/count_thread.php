<?php

include "../lib.php";


$curl = curl_init('http://www.clanaod.net/forums/showthread.php?t=27574&goto=newpost');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
$page = curl_exec($curl);

function stripNonAlphaNumericSpaces( $string ) {
    return preg_replace( "/[^a-z0-9 ]/i", "", $string );
}


if(curl_errno($curl)) // check for execution errors
{
	echo 'Scraper error: ' . curl_error($curl);
	exit;
}

curl_close($curl);

$DOM = new DOMDocument;

libxml_use_internal_errors(true);

if (!$DOM->loadHTML($page))//*[@id="yui-gen52"]/strong/span
{
	$errors="";

	foreach (libxml_get_errors() as $error)  {
		$errors.=$error->message."<br/>"; 
	}

	libxml_clear_errors();
	print "libxml errors:<br>$errors";
	return;
}



$xpath = new DOMXPath($DOM);

$post_count = $xpath->query("(//div[@class='content'])[last()]")->item(0);
$curNum = preg_replace("/[^0-9,.]/", "", stripNonAlphaNumericSpaces($post_count->textContent));
$newNum = $curNum + 1;

echo "The current count is {$curNum}. The new count will be {$newNum}<br /><a href='?post'>Post a new number?</a>";


$forum = "http://www.clanaod.net/forums/";
$thread = "27574";

$vbff = new vBForumFunctions($forum);


if(!$vbff->login(FORUM_USER, FORUM_PASS)) {
	die("Unable to login!");
} else {
	if (isset($_GET['post'])) {
		$vbff->posts->postReply($thread, "[SIZE=7]" . number_format($newNum) . "![/SIZE]");
		echo "<br />Posted a new number!";
		echo '<script>setTimeout(function(){window.location = "http://guybrush.duckdns.org/aod_rct/application/modules/count_thread.php"; }, 2000);</script>';
	}

}


?>