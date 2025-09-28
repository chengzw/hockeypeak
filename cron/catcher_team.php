<?php
	include_once('/home/services/php_utils/catcher.class.php');

	function catch_team($url) {
		$debug = true;
		$content = "";
		// BasePattern($patt, $result, $patt2="", $result2="")
		$content_pattern = new BasePattern('<head`a</body>', '`a');
		// WebCatcher($url, $isUtf8, &$content, $content_pattern, $outputUtf8=false, $debug=false,$referer="") {
		$catcher = new WebCatcher($url, true, $content, $content_pattern, true, $debug);
		// ListPattern($content_pattern, $delimitor, $item_patterns, $outputUtf8, $debug=false)
		$delimitor = 'keywords';
		$item_patterns = array('name' => new BasePattern('<div class="top-nav__club-logo">`a<a`btitle="`c">`d<img`e</a>`f</div>', '`c'),
            'english' => new BasePattern('<div class="top-nav__club-logo">`a<a`btitle="`c">`d<img`e</a>`f</div>', '`c'),
            'english_abbr' => new BasePattern('dfpAdUnitHierarchy`acontent="`b_nhl_web"`csite_code', '`b'),
            'logo' => new BasePattern('<div class="top-nav__club-logo">`a<img`bsrc="`c"`d</a>`f</div>', '`c'),
            );
		$list = $catcher->GetList(null, $delimitor, $item_patterns);
		return $list;
	}
	
?>
