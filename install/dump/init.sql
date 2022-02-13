CREATE SEQUENCE "topics_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "tags_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "articles_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "tag2article_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "demo_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "topics"
(
    "id"        integer DEFAULT nextval('topics_id_seq') NOT NULL,
    "title"     character varying(64)                    NOT NULL,
    "slug"      character varying(128)                   NOT NULL,
    "parent_id" integer,
    "is_active" boolean DEFAULT true                     NOT NULL,
    "cdate"     integer                                  NOT NULL,
    "mdate"     integer,
    "ddate"     integer,
    CONSTRAINT "topics_id" PRIMARY KEY ("id"),
    CONSTRAINT "topics_title" UNIQUE ("title"),
    CONSTRAINT "topics_slug" UNIQUE ("slug"),
    CONSTRAINT "topics_parent_id_fkey" FOREIGN KEY ("parent_id") REFERENCES "topics" ("id")
        ON UPDATE CASCADE
        ON DELETE SET NULL
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "tags"
(
    "id"        integer DEFAULT nextval('tags_id_seq') NOT NULL,
    "title"     character varying(64)                  NOT NULL,
    "slug"      character varying(128)                 NOT NULL,
    "is_active" boolean DEFAULT true                   NOT NULL,
    "cdate"     integer                                NOT NULL,
    "mdate"     integer,
    "ddate"     integer,
    CONSTRAINT "tags_id" PRIMARY KEY ("id"),
    CONSTRAINT "tags_title" UNIQUE ("title"),
    CONSTRAINT "tags_slug" UNIQUE ("slug")
) WITH (oids = false);

CREATE TABLE "articles"
(
    "id"               integer DEFAULT nextval('articles_id_seq') NOT NULL,
    "title"            character varying(64)                      NOT NULL,
    "slug"             character varying(128)                     NOT NULL,
    "image"            character varying(255),
    "summary"          text,
    "text"             text                                       NOT NULL,
    "html"             text,
    "topic_id"         integer,
    "meta_title"       character varying(255),
    "meta_description" character varying(512),
    "user_id"          integer,
    "is_active"        boolean DEFAULT true                       NOT NULL,
    "cdate"            integer                                    NOT NULL,
    "mdate"            integer,
    "ddate"            integer,
    CONSTRAINT "articles_id" PRIMARY KEY ("id"),
    CONSTRAINT "articles_title" UNIQUE ("title"),
    CONSTRAINT "articles_slug" UNIQUE ("slug"),
    CONSTRAINT "articles_topic_id_fkey" FOREIGN KEY ("topic_id") REFERENCES "topics" ("id")
        ON UPDATE CASCADE
        ON DELETE SET NULL
        NOT DEFERRABLE,
    CONSTRAINT "articles_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users" ("id")
        ON UPDATE CASCADE
        ON DELETE SET NULL
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "tag2article"
(
    "id"         integer DEFAULT nextval('tag2article_id_seq') NOT NULL,
    "tag_id"     integer                                       NOT NULL,
    "article_id" integer                                       NOT NULL,
    CONSTRAINT "tag2article_id" PRIMARY KEY ("id"),
    CONSTRAINT "tag2article_tag_id_article_id" UNIQUE ("tag_id", "article_id"),
    CONSTRAINT "tag2article_tag_id_fkey" FOREIGN KEY ("tag_id") REFERENCES "tags" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE,
    CONSTRAINT "tag2article_article_id_fkey" FOREIGN KEY ("article_id") REFERENCES "articles" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "demo"
(
    "id"        integer DEFAULT nextval('demo_id_seq') NOT NULL,
    "foo"       character varying(8)                   NOT NULL,
    "bar"       character varying(8)                   NOT NULL,
    "is_active" boolean DEFAULT true                   NOT NULL,
    "cdate"     integer                                NOT NULL,
    "mdate"     integer,
    "ddate"     integer,
    CONSTRAINT "demo_id" PRIMARY KEY ("id"),
    CONSTRAINT "demo_foo_bar" UNIQUE ("foo", "bar")
) WITH (oids = false);

CREATE INDEX "tags_is_active" ON "tags" USING btree ("is_active");
CREATE INDEX "tags_cdate" ON "tags" USING btree ("cdate");
CREATE INDEX "tags_mdate" ON "tags" USING btree ("mdate");
CREATE INDEX "tags_ddate" ON "tags" USING btree ("ddate");
CREATE INDEX "tags_id_title" ON "tags" USING btree ("id", "title");
CREATE INDEX "tags_id_slug" ON "tags" USING btree ("id", "slug");
CREATE INDEX "tags_is_active_ddate" ON "tags" USING btree ("is_active", "ddate");
CREATE INDEX "tags_id_is_active" ON "tags" USING btree ("id", "is_active");
CREATE INDEX "tags_id_is_active_ddate" ON "tags" USING btree ("id", "is_active", "ddate");
CREATE INDEX "tags_slug_is_active" ON "tags" USING btree ("slug", "is_active");
CREATE INDEX "tags_slug_is_active_ddate" ON "tags" USING btree ("slug", "is_active", "ddate");

CREATE INDEX "articles_topic_id" ON "articles" USING btree ("topic_id");
CREATE INDEX "articles_user_id" ON "articles" USING btree ("user_id");
CREATE INDEX "articles_is_active" ON "articles" USING btree ("is_active");
CREATE INDEX "articles_cdate" ON "articles" USING btree ("cdate");
CREATE INDEX "articles_mdate" ON "articles" USING btree ("mdate");
CREATE INDEX "articles_ddate" ON "articles" USING btree ("ddate");
CREATE INDEX "articles_id_title" ON "articles" USING btree ("id", "title");
CREATE INDEX "articles_id_meta_title" ON "articles" USING btree ("id", "meta_title");
CREATE INDEX "articles_id_slug" ON "articles" USING btree ("id", "slug");
CREATE INDEX "articles_is_active_ddate" ON "articles" USING btree ("is_active", "ddate");
CREATE INDEX "articles_id_is_active" ON "articles" USING btree ("id", "is_active");
CREATE INDEX "articles_id_is_active_ddate" ON "articles" USING btree ("id", "is_active", "ddate");
CREATE INDEX "articles_slug_is_active" ON "articles" USING btree ("slug", "is_active");
CREATE INDEX "articles_slug_is_active_ddate" ON "articles" USING btree ("slug", "is_active", "ddate");
CREATE INDEX "articles_topic_id_is_active" ON "articles" USING btree ("topic_id", "is_active");
CREATE INDEX "articles_topic_id_is_active_ddate" ON "articles" USING btree ("topic_id", "is_active", "ddate");
CREATE INDEX "articles_user_id_is_active" ON "articles" USING btree ("user_id", "is_active");
CREATE INDEX "articles_user_id_is_active_ddate" ON "articles" USING btree ("user_id", "is_active", "ddate");

CREATE INDEX "tag2article_tag_id" ON "tag2article" USING btree ("tag_id");
CREATE INDEX "tag2article_article_id" ON "tag2article" USING btree ("article_id");

CREATE INDEX "demo_foo" ON "demo" USING btree ("foo");
CREATE INDEX "demo_bar" ON "demo" USING btree ("bar");
CREATE INDEX "demo_is_active" ON "demo" USING btree ("is_active");
CREATE INDEX "demo_cdate" ON "demo" USING btree ("cdate");
CREATE INDEX "demo_mdate" ON "demo" USING btree ("mdate");
CREATE INDEX "demo_ddate" ON "demo" USING btree ("ddate");
CREATE INDEX "demo_id_foo_bar" ON "demo" USING btree ("id", "foo", "bar");
CREATE INDEX "demo_is_active_ddate" ON "demo" USING btree ("is_active", "ddate");
CREATE INDEX "demo_id_is_active" ON "demo" USING btree ("id", "is_active");
CREATE INDEX "demo_id_is_active_ddate" ON "demo" USING btree ("id", "is_active", "ddate");

INSERT INTO "role_actions" ("id", "name", "is_system", "is_active", "cdate", "mdate", "ddate")
VALUES (1, 'read-articles', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (2, 'read-comments', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (3, 'write-comments', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (4, 'vote-for-articles', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (5, 'write-private-messages', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (6, 'login-to-admin', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (7, 'write-articles', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (8, 'manage-comments', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (9, 'manage-articles', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (10, 'manage-taxonomy', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (11, 'ban-users', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (12, 'manage-private-messages', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (13, 'manage-users', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (14, 'login-as-user', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (15, 'manage-role-actions', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (16, 'manage-roles', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (17, 'manage-cache', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (18, 'manage-settings', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (19, 'manage-cron', 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL);

INSERT INTO "roles" ("id", "name", "parent_id", "is_system", "is_active", "cdate", "mdate", "ddate")
VALUES (1, 'guest', NULL, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (2, 'banned', 1, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (3, 'bot', 2, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (4, 'user', 1, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (5, 'author', 4, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (6, 'editor', 5, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (7, 'moderator', 6, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (8, 'webmaster', 4, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (9, 'admin', 7, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (10, 'root', NULL, 't', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL);

INSERT INTO "role2action" ("id", "role_id", "action_id", "is_allowed")
VALUES (1, 1, 1, 't'),
       (2, 1, 2, 't'),
       (3, 1, 3, 't'),
       (4, 2, 3, 'f'),
       (5, 3, 1, 'f'),
       (6, 3, 2, 'f'),
       (7, 4, 4, 't'),
       (8, 4, 5, 't'),
       (9, 5, 6, 't'),
       (10, 5, 7, 't'),
       (11, 6, 9, 't'),
       (12, 6, 10, 't'),
       (13, 7, 8, 't'),
       (14, 7, 11, 't'),
       (15, 7, 12, 't'),
       (16, 8, 6, 't'),
       (17, 8, 17, 't'),
       (18, 8, 18, 't'),
       (19, 8, 19, 't'),
       (20, 9, 13, 't'),
       (21, 9, 14, 't'),
       (22, 9, 15, 't'),
       (23, 9, 16, 't'),
       (24, 9, 17, 't'),
       (25, 9, 18, 't'),
       (26, 9, 19, 't'),
       (27, 10, 1, 't'),
       (28, 10, 2, 't'),
       (29, 10, 3, 't'),
       (30, 10, 4, 't'),
       (31, 10, 5, 't'),
       (32, 10, 6, 't'),
       (33, 10, 7, 't'),
       (34, 10, 8, 't'),
       (35, 10, 9, 't'),
       (36, 10, 10, 't'),
       (37, 10, 11, 't'),
       (38, 10, 12, 't'),
       (39, 10, 13, 't'),
       (40, 10, 14, 't'),
       (41, 10, 15, 't'),
       (42, 10, 16, 't'),
       (44, 10, 17, 't'),
       (45, 10, 18, 't'),
       (46, 10, 19, 't');

INSERT INTO "users" ("id", "login", "email", "role_id", "is_active", "cdate", "mdate", "ddate")
VALUES (1, 'admin', 'admin@admin.admin', 10, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL);

INSERT INTO "demo" ("id", "foo", "bar", "is_active", "cdate", "mdate", "ddate")
VALUES (1, 'test', 'a b c', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (2, 'qwerty', '123', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL),
       (3, 'one', 'two', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER), NULL, NULL);

ALTER SEQUENCE "role_actions_id_seq" RESTART WITH 20;

ALTER SEQUENCE "roles_id_seq" RESTART WITH 11;
ALTER SEQUENCE "role2action_id_seq" RESTART WITH 47;

ALTER SEQUENCE "users_id_seq" RESTART WITH 2;

ALTER SEQUENCE "demo_id_seq" RESTART WITH 4;
