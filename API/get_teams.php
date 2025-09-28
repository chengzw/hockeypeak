<?php
function _format_division($division) {
    $teams = array();
    foreach($division as $row) {
        $teams[] = _row_to_team($row);
    }
    return array("title" => $division[0]["division_title"], "teams" => $teams);
}
function get_teams() {
    $sql = "select teams.*, divisions.title as division_title, divisions.conference from teams, divisions where teams.division_id = divisions.id order by teams.division_id";
    $rows = get_rows_by_sql($sql);

    $teams = array();
    $divisions = _group_rows($rows, "division_id");
    foreach ($divisions as $division) {
        $conference_name = $division[0]["conference"];
        if (!array_key_exists($conference_name, $teams)) {
            $teams[$conference_name] = array("title" => $conference_name, "divisions" => array());
        }
        $teams[$conference_name]["divisions"][] = _format_division($division);
    }
    return array_values($teams);
}
?>
