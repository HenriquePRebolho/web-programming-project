<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
    }

    $db = new SQLite3('mydb.sq3');
    $currentUserEmail = $_SESSION["email"]; 

    // CTE for defining table to get top 5 and user position
    $query = "
        WITH RankedUsers AS (
            SELECT 
                ROW_NUMBER() OVER (ORDER BY highScore DESC) as rank, 
                email, 
                highScore
            FROM users
        )
        SELECT * FROM RankedUsers WHERE rank <= 5
        UNION ALL
        SELECT * FROM RankedUsers WHERE email = :email AND rank > 5;
    ";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':email', $currentUserEmail, SQLITE3_TEXT);
    $result = $stmt->execute();

    // Table
    $table = "
    <table style='user-select: none;'>
        <tr style='user-select: none;'>
            <th style='user-select: none;'>Position</th>
            <th style='user-select: none;'>User</th>
            <th style='user-select: none;'>High Score</th>
        </tr>";

    $inTopFive = false;
    $rows = [];

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $rows[] = $row;
        if ($row['email'] === $currentUserEmail && $row['rank'] <= 5) {
            $inTopFive = true;
        }
    }

    foreach ($rows as $row) {
        // Highlight the current user's row style-wise
        $isCurrent = ($row["email"] === $currentUserEmail) ? " style='background-color: #e0f7fa; user-select: none;'" : "";
        
        // If this is the extra row for the user outside the top 5, add a visual separator
        if (!$inTopFive && $row["email"] === $currentUserEmail) {
            $table .= "<trstyle='user-select: none;' ><td colspan='3' style='text-align:center; user-select: none;'>...</td></tr>";
        }

        $table .= "<tr$isCurrent>
            <td style='user-select: none;'>" . $row["rank"] . "</td>
            <td style='user-select: none;'>" . $row["email"] . "</td>
            <td style='user-select: none;'>" . $row["highScore"] . "</td>
        </tr>";
    }

    $table .= "</table>";

    echo($table);

    unset($db);
    return;
?>

