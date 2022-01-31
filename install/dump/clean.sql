TRUNCATE "topics" CASCADE;
TRUNCATE "tags" CASCADE;
TRUNCATE "users" CASCADE;
TRUNCATE "roles" CASCADE;
TRUNCATE "role_actions" CASCADE;
TRUNCATE "role2action" CASCADE;

DROP TABLE IF EXISTS "topics" CASCADE;
DROP TABLE IF EXISTS "tags" CASCADE;
DROP TABLE IF EXISTS "articles" CASCADE;
DROP TABLE IF EXISTS "tag2article" CASCADE;

ALTER SEQUENCE "users_id_seq" RESTART WITH 1;
ALTER SEQUENCE "role2action_id_seq" RESTART WITH 1;
ALTER SEQUENCE "role_actions_id_seq" RESTART WITH 1;
ALTER SEQUENCE "roles_id_seq" RESTART WITH 1;

DROP SEQUENCE IF EXISTS "topics_id_seq" CASCADE;
DROP SEQUENCE IF EXISTS "tags_id_seq" CASCADE;
DROP SEQUENCE IF EXISTS "articles_id_seq" CASCADE;
DROP SEQUENCE IF EXISTS "tag2article_id_seq" CASCADE;
