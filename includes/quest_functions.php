<?php
// ============================================================
// Sidequest Functions
// File: includes/quest_functions.php
// Purpose: Handle sidequest management and tracking
// ============================================================

require_once __DIR__ . '/../config/db_connection.php';

// ============================================================
// FUNCTION 1: Get all active sidequests
// ============================================================

function getAllSidequests() {
    global $conn;
    
    $query = $conn->prepare("
        SELECT quest_id, title, description, category, points, difficulty
        FROM SideQuest
        WHERE is_active = TRUE
        ORDER BY difficulty ASC, points DESC
    ");
    $query->execute();
    $result = $query->get_result();
    
    $quests = [];
    while ($row = $result->fetch_assoc()) {
        $quests[] = $row;
    }
    
    $query->close();
    return $quests;
}

// ============================================================
// FUNCTION 2: Get a specific sidequest
// ============================================================

function getSidequestById($quest_id) {
    global $conn;
    
    $query = $conn->prepare("
        SELECT quest_id, title, description, category, points, difficulty
        FROM SideQuest
        WHERE quest_id = ? AND is_active = TRUE
    ");
    $query->bind_param("i", $quest_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $quest = $result->fetch_assoc();
        $query->close();
        return $quest;
    }
    
    $query->close();
    return null;
}

// ============================================================
// FUNCTION 3: Accept a sidequest
// ============================================================

function acceptSidequest($member_id, $quest_id) {
    global $conn;
    
    // Check if quest exists
    $check = $conn->prepare("SELECT quest_id FROM SideQuest WHERE quest_id = ? AND is_active = TRUE");
    $check->bind_param("i", $quest_id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows == 0) {
        $check->close();
        return ["success" => false, "message" => "Quest does not exist"];
    }
    $check->close();
    
    // Check if member already accepted this quest
    $check_accepted = $conn->prepare("
        SELECT progress_id FROM QuestProgress 
        WHERE member_id = ? AND quest_id = ? AND status IN ('Accepted', 'In Progress', 'Completed')
    ");
    $check_accepted->bind_param("ii", $member_id, $quest_id);
    $check_accepted->execute();
    $check_accepted->store_result();
    
    if ($check_accepted->num_rows > 0) {
        $check_accepted->close();
        return ["success" => false, "message" => "You have already accepted this quest"];
    }
    $check_accepted->close();
    
    // Insert quest progress
    $insert = $conn->prepare("
        INSERT INTO QuestProgress (member_id, quest_id, status) 
        VALUES (?, ?, 'Accepted')
    ");
    $insert->bind_param("ii", $member_id, $quest_id);
    
    if ($insert->execute()) {
        $progress_id = $conn->insert_id;
        $insert->close();
        return ["success" => true, "message" => "Quest accepted!", "progress_id" => $progress_id];
    } else {
        $insert->close();
        return ["success" => false, "message" => "Failed to accept quest"];
    }
}

// ============================================================
// FUNCTION 4: Get member's active quests
// ============================================================

function getMemberActiveQuests($member_id) {
    global $conn;
    
    $query = $conn->prepare("
        SELECT 
            qp.progress_id,
            qp.quest_id,
            qp.status,
            qp.accepted_date,
            sq.title,
            sq.description,
            sq.category,
            sq.points,
            sq.difficulty
        FROM QuestProgress qp
        JOIN SideQuest sq ON qp.quest_id = sq.quest_id
        WHERE qp.member_id = ? AND qp.status IN ('Accepted', 'In Progress')
        ORDER BY qp.accepted_date DESC
    ");
    $query->bind_param("i", $member_id);
    $query->execute();
    $result = $query->get_result();
    
    $quests = [];
    while ($row = $result->fetch_assoc()) {
        $quests[] = $row;
    }
    
    $query->close();
    return $quests;
}

// ============================================================
// FUNCTION 5: Get member's completed quests
// ============================================================

function getMemberCompletedQuests($member_id) {
    global $conn;
    
    $query = $conn->prepare("
        SELECT 
            qp.progress_id,
            qp.quest_id,
            qp.status,
            qp.completion_date,
            qp.points_earned,
            sq.title,
            sq.category,
            sq.points
        FROM QuestProgress qp
        JOIN SideQuest sq ON qp.quest_id = sq.quest_id
        WHERE qp.member_id = ? AND qp.status = 'Completed'
        ORDER BY qp.completion_date DESC
    ");
    $query->bind_param("i", $member_id);
    $query->execute();
    $result = $query->get_result();
    
    $quests = [];
    while ($row = $result->fetch_assoc()) {
        $quests[] = $row;
    }
    
    $query->close();
    return $quests;
}

// ============================================================
// FUNCTION 6: Get difficulty badge color
// ============================================================

function getDifficultyColor($difficulty) {
    switch(strtolower($difficulty)) {
        case 'easy':
            return "#4CAF50"; // Green
        case 'medium':
            return "#FFC107"; // Orange
        case 'hard':
            return "#F44336"; // Red
        default:
            return "#9C27B0"; // Purple
    }
}

// ============================================================
// FUNCTION 7: Get difficulty emoji
// ============================================================

function getDifficultyEmoji($difficulty) {
    switch(strtolower($difficulty)) {
        case 'easy':
            return "⭐";
        case 'medium':
            return "⭐⭐";
        case 'hard':
            return "⭐⭐⭐";
        default:
            return "❓";
    }
}

// ============================================================
// FUNCTION 8: Get total points earned by member
// ============================================================

function getMemberTotalPoints($member_id) {
    global $conn;
    
    $query = $conn->prepare("
        SELECT COALESCE(SUM(points_earned), 0) as total_points
        FROM QuestProgress
        WHERE member_id = ? AND status = 'Completed'
    ");
    $query->bind_param("i", $member_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $query->close();
        return $row['total_points'] ?? 0;
    }
    
    $query->close();
    return 0;
}

?>