<?php
$db = new SQLite3('database/database.sqlite');
$res = $db->query('SELECT email, password FROM users LIMIT 5');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    echo "Email: " . $row['email'] . ", Password: " . $row['password'] . PHP_EOL;
}
?>