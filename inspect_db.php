<?php
include 'includes/config.php';

echo "=== DATABASE INSPECTION ===\n\n";

// Show all tables
echo "ALL TABLES:\n";
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    echo "- " . $row[0] . "\n";
}

echo "\n=== NOTIFICATION-RELATED TABLES ===\n";

// Check for existing notification tables
$notification_tables = ['notifications', 'conversations', 'messages', 'client_messages'];
foreach ($notification_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "\n$table TABLE EXISTS - DESCRIBING:\n";
        $describe = $conn->query("DESCRIBE $table");
        while ($col = $describe->fetch_assoc()) {
            echo "  " . $col['Field'] . " - " . $col['Type'] . " - " . ($col['Null'] == 'NO' ? 'NOT NULL' : 'NULL') . "\n";
        }
    } else {
        echo "$table table does not exist\n";
    }
}

echo "\n=== CUSTOMERS TABLE ===\n";
$result = $conn->query("SHOW TABLES LIKE 'customers_enhanced'");
if ($result->num_rows > 0) {
    $describe = $conn->query("DESCRIBE customers_enhanced");
    while ($col = $describe->fetch_assoc()) {
        echo "  " . $col['Field'] . " - " . $col['Type'] . " - " . ($col['Null'] == 'NO' ? 'NOT NULL' : 'NULL') . "\n";
    }
} else {
    echo "customers_enhanced table does not exist\n";
}

$conn->close();
?>