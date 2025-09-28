#!/usr/bin/php
<?php
    include_once(__DIR__."/config.php");
    include_once(__DIR__."/conn.php");
    include_once("/home/services/php_utils/catcher.class.php");

    $url = "https://api-web.nhle.com/v1/schedule-calendar/2024-10-22";      // 以后可能要修改url
    $content = "";
    $debug = false;
    // JsonCatcher($url, $isUtf8, &$content, $content_pattern, $outputUtf8=false, $debug=false)
    $catcher = new JsonCatcher($url, true, $content, null, true, $debug);
    $properties = array("nhl_team_id" => "id",
                        "name" => "name|default",
                        "logo" => "logo",
                        "logo_dark" => "darkLogo",
                        "english_abbr" => "abbrev",
                        );
    $teams = $catcher->GetList(array("teams"), $properties);
    // print_r($teams);

    foreach ($teams as $team) {
        $sql = sprintf("UPDATE teams SET english='%s', english_abbr='%s', logo='%s', logo_dark='%s' WHERE nhl_team_id=%d",
                        $team["name"], $team["english_abbr"], $team["logo"], $team["logo_dark"], $team["nhl_team_id"]);
        echo "$sql\n";
        query_no_result($sql);
    }

    close_connection();
?>
