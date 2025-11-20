DROP TABLE IF EXISTS project_user CASCADE;
DROP TABLE IF EXISTS project_resource CASCADE;
DROP TABLE IF EXISTS project_comment CASCADE;
DROP TABLE IF EXISTS project_file CASCADE;

CREATE TABLE project_user (
    username TEXT UNIQUE PRIMARY KEY,
    email TEXT UNIQUE NOT NULL,
    display_name TEXT NOT NULL,
    password_hash TEXT NOT NULL,
    bio TEXT NOT NULL DEFAULT ''
);

CREATE TABLE project_resource (
    id SERIAL PRIMARY KEY,
    author TEXT NOT NULL REFERENCES project_user(username) ON UPDATE CASCADE ON DELETE CASCADE,
    title TEXT NOT NULL, -- 120 char
    body TEXT NOT NULL, -- 500 char
    tags JSONB NOT NULL DEFAULT '[]'::jsonb, -- no spaces, lowercase
    download_count INT NOT NULL,
    files JSONB NOT NULL DEFAULT '[]'::jsonb
);

CREATE TABLE project_comment (
    id SERIAL PRIMARY KEY,
    resource_id INT NOT NULL REFERENCES project_resource(id) ON DELETE CASCADE,
    author TEXT NOT NULL REFERENCES project_user(username) ON UPDATE CASCADE ON DELETE CASCADE,
    parent_id INT REFERENCES project_comment(id) ON DELETE CASCADE, -- reply to another parent comment
    body TEXT NOT NULL -- 300 char
);

CREATE TABLE project_file (
    id SERIAL PRIMARY KEY,
    aws_key TEXT NOT NULL,
    url TEXT NOT NULL,
    name TEXT NOT NULL
);

CREATE OR REPLACE FUNCTION project_find_resource_by_tag(search_tag TEXT)
RETURNS TABLE(id INT, title TEXT, author TEXT, download_count INT)
AS $$
BEGIN
    RETURN QUERY --returns the result
    SELECT r.id, r.title, r.author, r.download_count FROM project_resource AS r
    WHERE r.tags ?| regexp_split_to_array(lower(search_tag), '[ ,]+');
END;
$$ LANGUAGE plpgsql;

-- CREATE OR REPLACE FUNCTION project_get_file_ids(resource_id INT)
-- RETURNS JSON AS $$
-- BEGIN
--     RETURN(
--         SELECT to_json(coalesce(array_agg(f.id ORDER BY f.id), ARRAY[]::INT[]))
--         FROM project_file f
--         WHERE f.resource_id = project_get_file_ids.resource_id
--     );
-- END;
-- $$ LANGUAGE plpgsql;

INSERT INTO project_user(username, email, display_name, password_hash) VALUES('a', 'a@a.com', 'a', 'a');
INSERT INTO project_resource(author, title, body, tags, download_count, files) VALUES('a', 'test title', 'test body', '["tag_a", "tag_b"]'::jsonb, 0, '[]'::jsonb);