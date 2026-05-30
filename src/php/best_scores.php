<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: http://localhost/projects/Project/src/login_page.php");
    }

    $db = new SQLite3('mydb.sq3');
    $currentUserEmail = $_SESSION["email"]; 

    // 1. One query to rule them all using a CTE (WITH clause)
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

    // 2. Build the HTML Table (with fixed closing tags)
    $table = "
    <table>
        <tr>
            <th>Position</th>
            <th>User</th>
            <th>High Score</th>
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
        // Optional: Highlight the current user's row style-wise
        $isCurrent = ($row["email"] === $currentUserEmail) ? " style='background-color: #e0f7fa;'" : "";
        
        // If this is the extra row for the user outside the top 5, add a visual separator
        if (!$inTopFive && $row["email"] === $currentUserEmail) {
            $table .= "<tr><td colspan='3' style='text-align:center;'>...</td></tr>";
        }

        $table .= "<tr$isCurrent>
            <td>" . $row["rank"] . "</td>
            <td>" . $row["email"] . "</td>
            <td>" . $row["highScore"] . "</td>
        </tr>";
    }

    $table .= "</table>";

    echo($table);

    unset($db);
    return;
?>

