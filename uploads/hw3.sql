-- initialization script to build all tables + sequences

DROP TABLE IF EXISTS hw3_users CASCADE;
DROP TABLE IF EXISTS hw3_words CASCADE;
DROP TABLE IF EXISTS hw3_guesses CASCADE;

CREATE TABLE hw3_users (
    user_id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL 
);

-- target words only (not guesses)
CREATE TABLE hw3_words (
    word_id SERIAL PRIMARY KEY,
    word TEXT NOT NULL UNIQUE,
    CHECK (char_length(word) = 7)    -- make sure the word is 7 letters
);

CREATE TABLE hw3_games (
    game_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES hw3_users(user_id) ON DELETE CASCADE,
    word_id INT NOT NULL REFERENCES hw3_words(word_id) ON DELETE CASCADE,
    score INT DEFAULT 0 CHECK (score >= 0),
    won BOOLEAN DEFAULT FALSE,
    quit_early BOOLEAN DEFAULT FALSE,
    CONSTRAINT uq_user_word UNIQUE (user_id, word_id)
);

-- CREATE TABLE hw3_guesses(
--     guess_id SERIAL PRIMARY KEY,
--     game_id INT NOT NULL REFERENCES hw3_games(game_id) ON DELETE CASCADE,
    -- user_id INT NOT NULL REFERENCES hw3_users(user_id) ON DELETE CASCADE,
--     guess TEXT NOT NULL,
--     is_valid BOOLEAN DEFAULT FALSE,
--     is_seven BOOLEAN DEFAULT FALSE,
--     points_awarded  INT NOT NULL DEFAULT 0
-- )

CREATE INDEX idx_hw3_games_user ON hw3_games(user_id_won);
CREATE INDEX idx_hw3_games_word ON hw3_games(word_id);