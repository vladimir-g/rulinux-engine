DROP TABLE IF EXISTS themes CASCADE;
DROP TABLE IF EXISTS blocks CASCADE;
DROP TABLE IF EXISTS marks CASCADE;
DROP TABLE IF EXISTS groups CASCADE;
DROP TABLE IF EXISTS links CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS filters CASCADE;
DROP TABLE IF EXISTS faq CASCADE;
DROP TABLE IF EXISTS settings CASCADE;
DROP TABLE IF EXISTS sections CASCADE;
DROP TABLE IF EXISTS subsections CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS threads CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;

CREATE TABLE themes(id integer NOT NULL AUTO_INCREMENT, name varchar(64), description varchar(255), directory varchar(255), PRIMARY KEY(id), UNIQUE(name), UNIQUE(directory))ENGINE=InnoDB;
CREATE TABLE blocks(id integer NOT NULL AUTO_INCREMENT, name varchar(64), description varchar(255), directory varchar(255), PRIMARY KEY(id), UNIQUE(name), UNIQUE(directory))ENGINE=InnoDB;
CREATE TABLE marks(id integer NOT NULL AUTO_INCREMENT, name varchar(64), file varchar(128), description text, PRIMARY KEY(id), UNIQUE(name), UNIQUE(file))ENGINE=InnoDB;
CREATE TABLE links(id integer NOT NULL AUTO_INCREMENT, name varchar(128), link varchar(255), PRIMARY KEY(id), UNIQUE(name), UNIQUE(link))ENGINE=InnoDB;
CREATE TABLE groups(id integer NOT NULL AUTO_INCREMENT, name varchar(64), description VARCHAR(256), PRIMARY KEY(id), UNIQUE(name))ENGINE=InnoDB;
CREATE TABLE users(id integer NOT NULL AUTO_INCREMENT, gid integer REFERENCES groups(id) ON DELETE CASCADE, nick varchar(255), password varchar(32), name varchar(255), lastname varchar(255), birthday timestamp, gender boolean, email varchar(128), show_email boolean, im varchar(255), show_im boolean, country varchar(255), city varchar(255), photo varchar(512), register_date timestamp, last_visit timestamp, captcha integer, blocks varchar(255), additional text, raw_additional text, news_on_page integer, comments_on_page integer, threads_on_page integer, show_avatars boolean, show_ua boolean, show_resp boolean, theme integer REFERENCES themes(id) ON DELETE CASCADE, gmt varchar(3), filters varchar(512), mark integer REFERENCES marks(id) ON DELETE CASCADE, banned boolean, sort_to boolean, openid varchar(255), PRIMARY KEY(id), UNIQUE(nick))ENGINE=InnoDB;
CREATE TABLE filters(id integer NOT NULL AUTO_INCREMENT, name varchar(255), text varchar(512), directory varchar(128), class varchar(128), PRIMARY KEY(id), UNIQUE(name), UNIQUE(directory))ENGINE=InnoDB;
CREATE TABLE faq (id integer NOT NULL AUTO_INCREMENT, subject varchar(512), question text, raw_question text, answer text, raw_answer text, answered boolean, available boolean, PRIMARY KEY(id))ENGINE=InnoDB;
CREATE TABLE settings(id integer NOT NULL AUTO_INCREMENT, name varchar(255), value text, PRIMARY KEY(id), UNIQUE(name))ENGINE=InnoDB;
CREATE TABLE sections(id integer NOT NULL AUTO_INCREMENT, name varchar(64), rewrite varchar(64), description varchar(255), file varchar(128), PRIMARY KEY(id), UNIQUE(name))ENGINE=InnoDB;
CREATE TABLE subsections(id integer NOT NULL AUTO_INCREMENT, section integer REFERENCES sections(id) ON DELETE CASCADE, name varchar(255), description varchar(512), shortfaq text, rewrite varchar(255), sort integer, icon varchar(255), PRIMARY KEY(id))ENGINE=InnoDB;
CREATE TABLE comments (id integer NOT NULL AUTO_INCREMENT, tid integer, uid integer REFERENCES users(id) ON DELETE CASCADE, referer integer, timest timestamp, subject text, comment text, raw_comment text, useragent varchar(512), changing_timest timestamp, changed_by integer, changed_for varchar(512), filters varchar(512), show_ua boolean, md5 varchar(32), session_id varchar(128), PRIMARY KEY(id))ENGINE=InnoDB;
CREATE TABLE threads(id integer NOT NULL AUTO_INCREMENT, cid integer  REFERENCES comments(id) ON DELETE CASCADE, section integer REFERENCES sections(id) ON DELETE CASCADE, subsection integer REFERENCES forums(id) ON DELETE CASCADE, attached boolean, approved boolean, approved_by integer, approve_timest timestamp, file varchar(32), file_size integer, image_size varchar(9), extension varchar(4), md5 varchar(32), prooflink varchar(2047), timest timestamp, changing_timest timestamp, PRIMARY KEY(id), UNIQUE(md5))ENGINE=InnoDB;
CREATE TABLE sessions(id integer NOT NULL AUTO_INCREMENT, session_id varchar(128), uid integer REFERENCES users(id) ON DELETE CASCADE, tid integer REFERENCES threads(id) ON DELETE CASCADE, timest timestamp, PRIMARY KEY(id), UNIQUE(session_id))ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER `anonymous_upd_trigger` BEFORE UPDATE ON `users`
FOR EACH ROW 
BEGIN
IF NEW.id = 1 
THEN 
IF NEW.banned = 1 
THEN 
SET NEW.banned = 0;
ELSE 
SET NEW.banned = 0;
END IF;
END IF;
END;$$

CREATE TRIGGER `anonymous_del_trigger` BEFORE DELETE ON users
FOR EACH ROW BEGIN
IF OLD.id = 1
THEN
INSERT INTO logs SET costyl = 1;
END IF;
END; $$

DELIMITER ;
