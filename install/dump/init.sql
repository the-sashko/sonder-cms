CREATE SEQUENCE "topics_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "tags_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "articles_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "tag2article_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "hits_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "hit_aggregations_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "comments_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "shortener_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "demo_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "topics"
(
    "id"          integer DEFAULT nextval('topics_id_seq') NOT NULL,
    "title"       character varying(64)                    NOT NULL,
    "slug"        character varying(128)                   NOT NULL,
    "parent_id"   integer,
    "views_count" integer,
    "is_active"   boolean DEFAULT true                     NOT NULL,
    "cdate"       integer                                  NOT NULL,
    "mdate"       integer,
    "ddate"       integer,
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
    "id"          integer DEFAULT nextval('tags_id_seq') NOT NULL,
    "title"       character varying(64)                  NOT NULL,
    "slug"        character varying(128)                 NOT NULL,
    "views_count" integer,
    "is_active"   boolean DEFAULT true                   NOT NULL,
    "cdate"       integer                                NOT NULL,
    "mdate"       integer,
    "ddate"       integer,
    CONSTRAINT "tags_id" PRIMARY KEY ("id"),
    CONSTRAINT "tags_title" UNIQUE ("title"),
    CONSTRAINT "tags_slug" UNIQUE ("slug")
) WITH (oids = false);

CREATE TABLE "articles"
(
    "id"               integer DEFAULT nextval('articles_id_seq') NOT NULL,
    "title"            character varying(64)                      NOT NULL,
    "slug"             character varying(128)                     NOT NULL,
    "image_dir"        character varying(255),
    "summary"          text                                       NOT NULL,
    "text"             text                                       NOT NULL,
    "html"             text                                       NOT NULL,
    "topic_id"         integer                                    NOT NULL,
    "meta_title"       character varying(255),
    "meta_description" character varying(512),
    "user_id"          integer                                    NOT NULL,
    "views_count"      integer,
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

CREATE TABLE "hits"
(
    "id"         integer DEFAULT nextval('hits_id_seq') NOT NULL,
    "article_id" integer,
    "topic_id"   integer,
    "tag_id"     integer,
    "ip"         character varying(39)                  NOT NULL,
    "is_active"  boolean DEFAULT true                   NOT NULL,
    "cdate"      integer                                NOT NULL,
    "mdate"      integer,
    "ddate"      integer,
    CONSTRAINT "hits_id" PRIMARY KEY ("id"),
    CONSTRAINT "hits_article_id_fkey" FOREIGN KEY ("article_id") REFERENCES "articles" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE,
    CONSTRAINT "hits_topic_id_fkey" FOREIGN KEY ("topic_id") REFERENCES "topics" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE,
    CONSTRAINT "hits_tag_id_fkey" FOREIGN KEY ("tag_id") REFERENCES "tags" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "hit_aggregations"
(
    "id"         integer DEFAULT nextval('hit_aggregations_id_seq') NOT NULL,
    "article_id" integer,
    "topic_id"   integer,
    "tag_id"     integer,
    "count"      integer,
    "is_active"  boolean DEFAULT true                               NOT NULL,
    "cdate"      integer                                            NOT NULL,
    "mdate"      integer,
    "ddate"      integer,
    CONSTRAINT "hit_aggregations_id" PRIMARY KEY ("id"),
    CONSTRAINT "hit_aggregations_article_id_fkey" FOREIGN KEY ("article_id") REFERENCES "articles" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE,
    CONSTRAINT "hit_aggregations_topic_id_fkey" FOREIGN KEY ("topic_id") REFERENCES "topics" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE,
    CONSTRAINT "hit_aggregations_tag_id_fkey" FOREIGN KEY ("tag_id") REFERENCES "tags" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "comments"
(
    "id"         integer DEFAULT nextval('comments_id_seq') NOT NULL,
    "parent_id"  integer,
    "article_id" integer                                    NOT NULL,
    "user_id"    integer,
    "user_name"  character varying(128),
    "user_email" character varying(255),
    "user_ip"    character varying(39),
    "text"       text                                       NOT NULL,
    "html"       text                                       NOT NULL,
    "is_active"  boolean DEFAULT true                       NOT NULL,
    "cdate"      integer                                    NOT NULL,
    "mdate"      integer,
    "ddate"      integer,
    CONSTRAINT "comments_id" PRIMARY KEY ("id"),
    CONSTRAINT "comments_parent_id_fkey" FOREIGN KEY ("parent_id") REFERENCES "comments" ("id")
        ON UPDATE CASCADE
        ON DELETE SET NULL
        NOT DEFERRABLE,
    CONSTRAINT "comments_article_id_fkey" FOREIGN KEY ("article_id") REFERENCES "articles" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE,
    CONSTRAINT "articles_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users" ("id")
        ON UPDATE CASCADE
        ON DELETE SET NULL
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "shortener"
(
    "id"        integer DEFAULT nextval('shortener_id_seq') NOT NULL,
    "code"      character varying(8),
    "url"       character varying(512)                      NOT NULL,
    "is_active" boolean DEFAULT true                        NOT NULL,
    "cdate"     integer                                     NOT NULL,
    "mdate"     integer,
    "ddate"     integer,
    CONSTRAINT "shortener_id" PRIMARY KEY ("id")
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

CREATE INDEX "hits_article_id" ON "hits" USING btree ("article_id");
CREATE INDEX "hits_topic_id" ON "hits" USING btree ("topic_id");
CREATE INDEX "hits_tag_id" ON "hits" USING btree ("tag_id");
CREATE INDEX "hits_is_active" ON "hits" USING btree ("is_active");
CREATE INDEX "hits_cdate" ON "hits" USING btree ("cdate");
CREATE INDEX "hits_mdate" ON "hits" USING btree ("mdate");
CREATE INDEX "hits_ddate" ON "hits" USING btree ("ddate");
CREATE INDEX "hits_is_active_ddate" ON "hits" USING btree ("is_active", "ddate");
CREATE INDEX "hits_article_id_is_active_ddate" ON "hits" USING btree ("article_id", "is_active", "ddate");
CREATE INDEX "hits_topic_id_is_active_ddate" ON "hits" USING btree ("topic_id", "is_active", "ddate");
CREATE INDEX "hits_tag_id_is_active_ddate" ON "hits" USING btree ("tag_id", "is_active", "ddate");

CREATE INDEX "hit_aggregations_article_id" ON "hit_aggregations" USING btree ("article_id");
CREATE INDEX "hit_aggregations_topic_id" ON "hit_aggregations" USING btree ("topic_id");
CREATE INDEX "hit_aggregations_tag_id" ON "hit_aggregations" USING btree ("tag_id");
CREATE INDEX "hit_aggregations_count" ON "hit_aggregations" USING btree ("count");
CREATE INDEX "hit_aggregations_is_active" ON "hit_aggregations" USING btree ("is_active");
CREATE INDEX "hit_aggregations_cdate" ON "hit_aggregations" USING btree ("cdate");
CREATE INDEX "hit_aggregations_mdate" ON "hit_aggregations" USING btree ("mdate");
CREATE INDEX "hit_aggregations_ddate" ON "hit_aggregations" USING btree ("ddate");
CREATE INDEX "hit_aggregations_is_active_ddate" ON "hit_aggregations" USING btree ("is_active", "ddate");
CREATE INDEX "hit_aggregations_article_id_is_active_ddate"
    ON "hit_aggregations"
    USING btree ("article_id", "is_active", "ddate");
CREATE INDEX "hit_aggregations_topic_id_is_active" ON "hit_aggregations" USING btree ("topic_id", "is_active", "ddate");
CREATE INDEX "hit_aggregations_tag_id_is_active_ddate"
    ON "hit_aggregations"
    USING btree ("tag_id", "is_active", "ddate");
CREATE INDEX "hit_aggregations_count_is_active_ddate" ON "hit_aggregations" USING btree ("count", "is_active", "ddate");

CREATE INDEX "comments_parent_id" ON "comments" USING btree ("parent_id");
CREATE INDEX "comments_article_id" ON "comments" USING btree ("article_id");
CREATE INDEX "comments_user_id" ON "comments" USING btree ("user_id");
CREATE INDEX "comments_user_ip" ON "comments" USING btree ("user_ip");
CREATE INDEX "comments_is_active" ON "comments" USING btree ("is_active");
CREATE INDEX "comments_cdate" ON "comments" USING btree ("cdate");
CREATE INDEX "comments_mdate" ON "comments" USING btree ("mdate");
CREATE INDEX "comments_ddate" ON "comments" USING btree ("ddate");
CREATE INDEX "comments_is_active_ddate" ON "comments" USING btree ("is_active", "ddate");
CREATE INDEX "comments_parent_id_is_active_ddate" ON "comments" USING btree ("parent_id", "is_active", "ddate");
CREATE INDEX "comments_article_id_is_active_ddate" ON "comments" USING btree ("article_id", "is_active", "ddate");
CREATE INDEX "comments_user_id_is_active_ddate" ON "comments" USING btree ("user_id", "is_active", "ddate");
CREATE INDEX "comments_user_ip_is_active_ddate" ON "comments" USING btree ("user_ip", "is_active", "ddate");

CREATE INDEX "shortener_code" ON "shortener" USING btree ("code");
CREATE INDEX "shortener_is_active" ON "shortener" USING btree ("is_active");
CREATE INDEX "shortener_cdate" ON "shortener" USING btree ("cdate");
CREATE INDEX "shortener_mdate" ON "shortener" USING btree ("mdate");
CREATE INDEX "shortener_ddate" ON "shortener" USING btree ("ddate");
CREATE INDEX "shortener_is_active_ddate" ON "shortener" USING btree ("is_active", "ddate");
CREATE INDEX "shortener_code_is_active_ddate" ON "shortener" USING btree ("code", "is_active", "ddate");

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

INSERT INTO "role_actions" ("id", "name", "is_system", "cdate")
VALUES (1, 'read-articles', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (2, 'read-comments', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (3, 'write-comments', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (4, 'vote-for-articles', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (5, 'write-private-messages', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (6, 'login-to-admin', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (7, 'write-articles', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (8, 'manage-comments', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (9, 'manage-articles', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (10, 'manage-taxonomy', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (11, 'ban-users', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (12, 'manage-private-messages', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (13, 'manage-users', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (14, 'login-as-user', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (15, 'manage-role-actions', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (16, 'manage-roles', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (17, 'manage-cache', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (18, 'manage-settings', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (19, 'manage-cron', 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER));

INSERT INTO "roles" ("id", "name", "parent_id", "is_system", "cdate")
VALUES (1, 'guest', NULL, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (2, 'banned', 1, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (3, 'bot', 2, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (4, 'user', 1, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (5, 'author', 4, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (6, 'editor', 5, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (7, 'moderator', 6, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (8, 'webmaster', 4, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (9, 'admin', 7, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (10, 'root', NULL, 't', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER));

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

INSERT INTO "users" ("id", "login", "email", "role_id", "cdate")
VALUES (1, 'admin', 'admin@admin.admin', 10, CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER));

INSERT INTO "cron_jobs" ("id", "alias", "controller", "controller_method", "interval", "cdate")
VALUES (3, 'RSS', 'cron_rss', 'generate', 1800/* 30 min */, CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (4, 'Hits', 'cron_hit', 'aggregate', 86400/* 1 day */, CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (5, 'Share', 'cron_share', 'send', 900/* 15 min */, CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (6, 'Sitemap', 'cron_sitemap', 'generate', 1800/* 30 min */,
        CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER));

INSERT INTO "demo" ("id", "foo", "bar", "cdate")
VALUES (1, 'test', 'a b c', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (2, 'qwerty', '123', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (3, 'one', 'two', CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER));

ALTER SEQUENCE "role_actions_id_seq" RESTART WITH 20;

ALTER SEQUENCE "roles_id_seq" RESTART WITH 11;
ALTER SEQUENCE "role2action_id_seq" RESTART WITH 47;

ALTER SEQUENCE "users_id_seq" RESTART WITH 2;

ALTER SEQUENCE "demo_id_seq" RESTART WITH 4;
