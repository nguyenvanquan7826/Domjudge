<?php
/**
 * View the submissionqueue
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

$viewtypes = array(0 => 'newest', 1 => 'unverified', 2 => 'unjudged', 3 => 'all');

$view = 0;

// Restore most recent view from cookie (overridden by explicit selection)
if ( isset($_COOKIE['domjudge_submissionview']) && isset($viewtypes[$_COOKIE['domjudge_submissionview']]) ) {
	$view = $_COOKIE['domjudge_submissionview'];
}

if ( isset($_REQUEST['view']) ) {
	// did someone press any of the four view buttons?
	foreach ($viewtypes as $i => $name) {
		if ( isset($_REQUEST['view'][$i]) ) $view = $i;
	}
}

require('init.php');
$refresh = '15;url=submissions.php?' .
	urlencode('view[' . $view . ']') . '=' . urlencode($viewtypes[$view]);
$title = 'Submissions';

// Set cookie of submission view type, expiry defaults to end of session.
dj_setcookie('domjudge_submissionview', $view);

$jury_member = $username;

require(LIBWWWDIR . '/header.php');
echo "<div class='container' style='margin-bottom: 100px;'>";

echo "<h1 class=\"title-scoreboard flow-text\">$title</h1>\n\n";

$restrictions = array();
if ( $viewtypes[$view] == 'unverified' ) $restrictions['verified'] = 0;
if ( $viewtypes[$view] == 'unjudged' ) $restrictions['judged'] = 0;

echo addForm($pagename, 'get') . "<p>Show submissions:\n";
for($i=0; $i<count($viewtypes); ++$i) {
	echo addSubmit($viewtypes[$i], 'view['.$i.']', null, ($view != $i));
}
echo "</p>\n" . addEndForm();

$contests = $cdatas;
if ( $cid !== null ) {
	$contests = array($cid => $cdata);
}

putSubmissions($contests, $restrictions, ($viewtypes[$view] == 'newest' ? 50 : 0));

echo '</div>';
require(LIBWWWDIR . '/footer.php');
