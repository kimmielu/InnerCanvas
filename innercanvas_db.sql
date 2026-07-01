-- ============================================================
-- InnerCanvas: Web-Based Mental Wellness Support System
-- Database Schema
-- ============================================================

-- Create the database
CREATE DATABASE IF NOT EXISTS innercanvas;
USE innercanvas;

-- ============================================================
-- TABLE 1: YouthMember
-- Purpose: Store Youth Member accounts and profile information
-- ============================================================

CREATE TABLE YouthMember (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    profile_picture VARCHAR(255),
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- ============================================================
-- TABLE 2: Administrator
-- Purpose: Store Administrator accounts
-- ============================================================

CREATE TABLE Administrator (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username)
);

-- ============================================================
-- TABLE 3: MoodEntry
-- Purpose: Store daily mood records for each Youth Member
-- Relationship: YouthMember 1:M MoodEntry
-- ============================================================

CREATE TABLE MoodEntry (
    mood_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    mood_score INT NOT NULL,
    mood_note TEXT,
    entry_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES YouthMember(member_id) ON DELETE CASCADE,
    INDEX idx_member_id (member_id),
    INDEX idx_entry_date (entry_date)
);

-- ============================================================
-- TABLE 4: SideQuest
-- Purpose: Store available wellness activities/sidequests
-- ============================================================

CREATE TABLE SideQuest (
    quest_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50),
    points INT DEFAULT 10,
    difficulty VARCHAR(20),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_category (category)
);

-- ============================================================
-- TABLE 5: QuestProgress
-- Purpose: Track which quests each member has accepted and their status
-- Relationship: YouthMember 1:M QuestProgress
--               SideQuest 1:M QuestProgress
-- ============================================================

CREATE TABLE QuestProgress (
    progress_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    quest_id INT NOT NULL,
    status VARCHAR(20) DEFAULT 'Accepted',
    accepted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP NULL,
    points_earned INT DEFAULT 0,
    FOREIGN KEY (member_id) REFERENCES YouthMember(member_id) ON DELETE CASCADE,
    FOREIGN KEY (quest_id) REFERENCES SideQuest(quest_id) ON DELETE CASCADE,
    INDEX idx_member_id (member_id),
    INDEX idx_quest_id (quest_id),
    INDEX idx_status (status)
);

-- ============================================================
-- TABLE 6: QuestVerification
-- Purpose: Store completion evidence for sidequests
-- Relationship: QuestProgress 1:1 QuestVerification
-- ============================================================

CREATE TABLE QuestVerification (
    verification_id INT AUTO_INCREMENT PRIMARY KEY,
    progress_id INT UNIQUE NOT NULL,
    evidence_text TEXT NOT NULL,
    verification_status VARCHAR(20) DEFAULT 'Pending',
    submitted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_date TIMESTAMP NULL,
    FOREIGN KEY (progress_id) REFERENCES QuestProgress(progress_id) ON DELETE CASCADE,
    INDEX idx_verification_status (verification_status)
);

-- ============================================================
-- TABLE 7: Reflection
-- Purpose: Store reflections on completed activities
-- Relationship: QuestProgress 1:1 Reflection
-- ============================================================

CREATE TABLE Reflection (
    reflection_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    progress_id INT UNIQUE NOT NULL,
    reflection_text TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES YouthMember(member_id) ON DELETE CASCADE,
    FOREIGN KEY (progress_id) REFERENCES QuestProgress(progress_id) ON DELETE CASCADE,
    INDEX idx_member_id (member_id)
);

-- ============================================================
-- TABLE 8: ExpressionPost
-- Purpose: Store Expression Space posts (safe reflection space)
-- Relationship: YouthMember 1:M ExpressionPost
-- ============================================================

CREATE TABLE ExpressionPost (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    anonymous_status BOOLEAN DEFAULT FALSE,
    moderation_status VARCHAR(20) DEFAULT 'Pending',
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES YouthMember(member_id) ON DELETE CASCADE,
    INDEX idx_member_id (member_id),
    INDEX idx_moderation_status (moderation_status),
    INDEX idx_created_date (created_date)
);

-- ============================================================
-- TABLE 9: Report
-- Purpose: Store reports of inappropriate content
-- Relationship: ExpressionPost 1:M Report
-- ============================================================

CREATE TABLE Report (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    member_id INT NOT NULL,
    reason VARCHAR(150) NOT NULL,
    description TEXT,
    status VARCHAR(20) DEFAULT 'Pending',
    reported_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES ExpressionPost(post_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES YouthMember(member_id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id),
    INDEX idx_status (status)
);

-- ============================================================
-- TABLE 10: Resource
-- Purpose: Store wellness resources and articles
-- Managed by: Administrator
-- ============================================================

CREATE TABLE Resource (
    resource_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    content TEXT,
    link VARCHAR(255),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (admin_id) REFERENCES Administrator(admin_id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_is_active (is_active)
);

-- ============================================================
-- TABLE 11: Notification
-- Purpose: Store notifications for Youth Members
-- Relationship: YouthMember 1:M Notification
-- ============================================================

CREATE TABLE Notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(50),
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'Unread',
    FOREIGN KEY (member_id) REFERENCES YouthMember(member_id) ON DELETE CASCADE,
    INDEX idx_member_id (member_id),
    INDEX idx_status (status)
);

-- ============================================================
-- DATABASE CREATION COMPLETE
-- ============================================================
-- 
-- Summary of tables created:
-- 1. YouthMember - Member accounts
-- 2. Administrator - Admin accounts
-- 3. MoodEntry - Daily mood records
-- 4. SideQuest - Wellness activities
-- 5. QuestProgress - Quest tracking
-- 6. QuestVerification - Completion evidence
-- 7. Reflection - Activity reflections
-- 8. ExpressionPost - Community posts
-- 9. Report - Content reports
-- 10. Resource - Wellness resources
-- 11. Notification - Member notifications
--
-- All relationships and constraints implemented.
-- Ready for application development.
-- ============================================================