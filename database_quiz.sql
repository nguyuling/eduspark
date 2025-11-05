-- TABLE 1: Quizzes
CREATE TABLE quizzes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL COMMENT 'T4 Bab 1: Pengaturcaraan',
    syllabus_code VARCHAR(10) NOT NULL COMMENT 'T4-B1, T5-B3',
    quiz_type ENUM('Default', 'Custom') NOT NULL DEFAULT 'Custom' COMMENT 'Indicates if it is a core syllabus quiz or a teacher-created one.',
    passing_score_percentage TINYINT UNSIGNED NOT NULL DEFAULT 60 COMMENT 'Minimum percentage required to pass (0-100).',
    prerequisite_quiz_id INT UNSIGNED NULL, 
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (prerequisite_quiz_id) REFERENCES quizzes(id) ON DELETE SET NULL
);


-- TABLE 2: Questions
CREATE TABLE questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT UNSIGNED NOT NULL,
    question_type ENUM('MCQ', 'Fill_in_Blank', 'Subjective', 'Code_Output') NOT NULL,
    question_text TEXT NOT NULL,
    code_snippet TEXT NULL COMMENT 'For questions requiring a Java, SQL, or HTML code block.',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE 
    -- FK link the question back to its quiz
    -- ON DELETE CASCADE ensures questions are deleted if the parent quiz is deleted
);


-- TABLE 3: Options
CREATE TABLE options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id INT UNSIGNED NOT NULL,
    option_text TEXT NOT NULL COMMENT 'The text of the answer choice or the required correct answer.',
    is_correct BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'TRUE for the correct answer(s).',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
    -- FK links the option back to its question
);


-- TABLE 4: Quiz Results
CREATE TABLE quiz_results (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL COMMENT 'The student who took the quiz (Assumes a users table exists).',
    quiz_id INT UNSIGNED NOT NULL,
    score_percentage DECIMAL(5, 2) NOT NULL COMMENT 'Student score (e.g., 85.50).',
    is_passed BOOLEAN NOT NULL COMMENT 'Calculated based on passing_score_percentage in quizzes table.',
    attempt_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
    -- Foreign Key: Links the result to the quiz
);
