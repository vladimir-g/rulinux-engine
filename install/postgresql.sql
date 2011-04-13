CREATE TABLE themes(id SERIAL, name varchar(64), description varchar(255), directory varchar(255), PRIMARY KEY(id), UNIQUE(name), UNIQUE(directory));
CREATE TABLE blocks(id SERIAL, name varchar(64), description varchar(255), directory varchar(255), PRIMARY KEY(id), UNIQUE(name), UNIQUE(directory));
CREATE TABLE marks(id SERIAL, name varchar(64), file varchar(128), description text, PRIMARY KEY(id), UNIQUE(name), UNIQUE(file));
CREATE TABLE groups(id SERIAL, name varchar(64), description varchar(255), PRIMARY KEY(id), UNIQUE(name));
CREATE TABLE links(id SERIAL, name varchar(128), link varchar(255), PRIMARY KEY(id), UNIQUE(name), UNIQUE(link));
CREATE TABLE users(id SERIAL, gid integer REFERENCES groups(id), nick varchar(255), password varchar(32), name varchar(255), lastname varchar(255), birthday timestamp without time zone, gender boolean, email varchar(128), show_email boolean, im varchar(255), show_im boolean, country varchar(255), city varchar(255), photo varchar(512), register_date timestamp without time zone, last_visit timestamp without time zone, captcha integer, blocks varchar(255), additional text, news_on_page integer, comments_on_page integer, threads_on_page integer, show_avatars boolean, show_ua boolean, show_resp boolean, theme integer REFERENCES themes(id), gmt varchar(3), filters varchar(512), mark integer REFERENCES marks(id), banned boolean, sort_to boolean, PRIMARY KEY(id), UNIQUE(nick));
CREATE TABLE filters(id SERIAL, name varchar(255), text varchar(512), PRIMARY KEY(id), UNIQUE(name));
CREATE TABLE faq (id SERIAL, subject varchar(512), question text, raw_question text, answer text, raw_answer text, answered boolean, available boolean, PRIMARY KEY(id));
CREATE TABLE settings(id SERIAL, name varchar(255), value text, PRIMARY KEY(id), UNIQUE(name));
CREATE TABLE sections(id SERIAL, name varchar(64), rewrite varchar(64), description varchar(255), file varchar(128), PRIMARY KEY(id), UNIQUE(name));
CREATE TABLE subsections(id SERIAL, section integer REFERENCES sections(id), name varchar(255), description varchar(512), shortfaq text, rewrite varchar(255), sort integer, icon varchar(255), PRIMARY KEY(id));
CREATE TABLE regexps(id SERIAL, reg_exp text, category integer REFERENCES filters(id), PRIMARY KEY(id));
CREATE TABLE comments (id SERIAL, tid integer, uid integer REFERENCES users(id), referer integer, timest timestamp without time zone, subject text, comment text, raw_comment text, useragent varchar(512), changing_timest timestamp without time zone, changed_by integer DEFAULT 0, changed_for varchar(512), filters varchar(512), show_ua boolean, md5 varchar(32), PRIMARY KEY(id));
CREATE TABLE threads(id SERIAL, cid integer REFERENCES comments(id), section integer REFERENCES sections(id), subsection integer REFERENCES subsections(id), attached boolean, approved boolean, approved_by integer, approve_timest timestamp without time zone, file varchar(32), file_size integer, image_size varchar(9), extension varchar(4), md5 varchar(32), PRIMARY KEY(id), UNIQUE(md5));
CREATE TABLE sessions(id SERIAL, session_id varchar(32), uid integer REFERENCES users(id), tid integer REFERENCES threads(id), timest timestamp without time zone, PRIMARY KEY(id), UNIQUE(uid), UNIQUE(session_id));

CREATE LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION "ANONYMOUS_UPD"()
RETURNS trigger AS
$BODY$
BEGIN
IF NEW.id = 1
THEN
IF NEW.banned = true
THEN
NEW.banned := false;
RETURN NEW;
ELSE
RETURN NEW;
END IF;
ELSE
RETURN NEW;
END IF;
END
$BODY$
LANGUAGE 'plpgsql' VOLATILE;

CREATE TRIGGER "ANONYMOUS_UPD_TRIGGER" 
BEFORE UPDATE 
ON users 
FOR EACH ROW 
EXECUTE PROCEDURE "ANONYMOUS_UPD"();

CREATE OR REPLACE FUNCTION "ANONYMOUS_DEL"()
RETURNS trigger AS
$BODY$
DECLARE
nick varchar(255) = OLD.nick;
BEGIN
IF OLD.id = 1
THEN
RAISE EXCEPTION 'User % can`t be deleted',nick;
END IF;
RETURN OLD;
END
$BODY$
LANGUAGE 'plpgsql' VOLATILE;

CREATE TRIGGER "ANONYMOUS_DEL_TRIGGER" 
BEFORE DELETE 
ON users 
FOR EACH ROW 
EXECUTE PROCEDURE "ANONYMOUS_DEL"();
