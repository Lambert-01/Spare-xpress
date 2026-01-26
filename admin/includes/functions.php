<?php
function countRows($table) {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function countRowsWhere($table, $condition) {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM $table WHERE $condition";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    return 0;
}
?>