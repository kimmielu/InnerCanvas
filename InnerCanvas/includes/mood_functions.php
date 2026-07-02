<?php
// ============================================================
// Mood Tracking Functions
// File: includes/mood_functions.php
// Purpose: Handle mood recording, retrieval, and analysis
// ============================================================

require_once __DIR__ . '/../config/db_connection.php';

// ============================================================
// FUNCTION 1: Record a mood entry
// ============================================================

function recordMood($member_id, $mood_score, $mood_note = "") {
    global $conn;
    
    // Validate mood score (1-10)
    if ($mood_score < 1 || $mood_score > 10) {
        return ["success" => false, "message" => "Mood score must be between 1 and 10"];
    }
    
    // Insert mood entry
    $insert = $conn->prepare("INSERT INTO MoodEntry (member_id, mood_score, mood_note) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $member_id, $mood_score, $mood_note);
    
    if ($insert->execute()) {
        $insert->close();
        return ["success" => true, "message" => "Mood recorded successfully"];
    } else {
        $insert->close();
        return ["success" => false, "message" => "Failed to record mood"];
    }
}

// ============================================================
// FUNCTION 2: Get today's mood entry
// ============================================================

function getTodayMood($member_id) {
    global $conn;
    
    $query = $conn->prepare("
        SELECT mood_id, mood_score, mood_note, entry_date 
        FROM MoodEntry 
        WHERE member_id = ? 
        AND DATE(entry_date) = CURDATE()
        LIMIT 1
    ");
    $query->bind_param("i", $member_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $mood = $result->fetch_assoc();
        $query->close();
        return $mood;
    }
    
    $query->close();
    return null;
}

// ============================================================
// FUNCTION 3: Get mood history (last 30 days)
// ============================================================

function getMoodHistory($member_id, $days = 30) {
    global $conn;
    
    $query = $conn->prepare("
        SELECT mood_id, mood_score, mood_note, entry_date 
        FROM MoodEntry 
        WHERE member_id = ? 
        AND entry_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ORDER BY entry_date DESC
    ");
    $query->bind_param("ii", $member_id, $days);
    $query->execute();
    $result = $query->get_result();
    
    $moods = [];
    while ($row = $result->fetch_assoc()) {
        $moods[] = $row;
    }
    
    $query->close();
    return $moods;
}

// ============================================================
// FUNCTION 4: Get average mood (last 7 days)
// ============================================================

function getAverageMood($member_id) {
    global $conn;
    
    $query = $conn->prepare("
        SELECT ROUND(AVG(mood_score), 1) as average_mood 
        FROM MoodEntry 
        WHERE member_id = ? 
        AND entry_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $query->bind_param("i", $member_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $query->close();
        return $row['average_mood'] ?? 0;
    }
    
    $query->close();
    return 0;
}

// ============================================================
// FUNCTION 5: Get mood streak (consecutive days tracking)
// ============================================================

function getMoodStreak($member_id) {
    global $conn;
    
    $query = $conn->prepare("
        SELECT COUNT(DISTINCT DATE(entry_date)) as streak_days
        FROM MoodEntry 
        WHERE member_id = ? 
        AND entry_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $query->bind_param("i", $member_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $query->close();
        return $row['streak_days'] ?? 0;
    }
    
    $query->close();
    return 0;
}

// ============================================================
// FUNCTION 6: Get mood label based on score
// ============================================================

function getMoodLabel($score) {
    if ($score <= 2) return "Very Low 😢";
    if ($score <= 4) return "Low 😟";
    if ($score <= 6) return "Okay 😐";
    if ($score <= 8) return "Good 😊";
    return "Excellent 😄";
}

// ============================================================
// FUNCTION 7: Get mood color for visualization
// ============================================================

function getMoodColor($score) {
    if ($score <= 2) return "#FF6B6B"; // Red
    if ($score <= 4) return "#FFA500"; // Orange
    if ($score <= 6) return "#FFD93D"; // Yellow
    if ($score <= 8) return "#6BCB77"; // Green
    return "#4D96FF"; // Blue
}

?>