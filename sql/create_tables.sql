-- Create database
CREATE DATABASE IF NOT EXISTS educational_site;
USE educational_site;

-- Create Users table
CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin', 'teacher') DEFAULT 'student',
    points INT DEFAULT 0,
    level INT DEFAULT 1,
    badges VARCHAR(255) DEFAULT NULL,
    progress JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create Chapters table
CREATE TABLE IF NOT EXISTS Chapters (
    chapter_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_number INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create SubChapters table
CREATE TABLE IF NOT EXISTS SubChapters (
    subchapter_id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_number INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (chapter_id) REFERENCES Chapters(chapter_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create Materials table with chapter reference
CREATE TABLE IF NOT EXISTS Materials (
    material_id INT AUTO_INCREMENT PRIMARY KEY,
    subchapter_id INT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('text', 'video', 'game') NOT NULL,
    phase TINYINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subchapter_id) REFERENCES SubChapters(subchapter_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Create Comments table
CREATE TABLE IF NOT EXISTS Comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    material_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (material_id) REFERENCES Materials(material_id)
) ENGINE=InnoDB;

-- Create QuizQuestions table
CREATE TABLE IF NOT EXISTS QuizQuestions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT DEFAULT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('drag_drop', 'matching', 'canvas_simulation') NOT NULL,
    options TEXT NOT NULL,
    correct_answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES Materials(material_id)
) ENGINE=InnoDB;
