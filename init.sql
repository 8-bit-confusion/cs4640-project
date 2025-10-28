-- don't forget to drop table!
DROP TABLE IF EXISTS project_user CASCADE;
DROP TABLE IF EXISTS project_resource CASCADE;


CREATE TABLE project_user {
    username TEXT PRIMARY KEY
    display_name TEXT NOT NULL,
    password_hash TEXT NOT NULL,
}

CREATE TABLE project_resource {
    id SERIAL PRIMARY KEY,
    owner TEXT NOT NULL REFERENCES project_user(username) ON DELETE CASCADE,
    title TEXT NOT NULL, -- 120 char
    body TEXT NOT NULL, -- 500 char
    tags TEXT[]


}