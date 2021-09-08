//Creates table with relation
CREATE TABLE login_tokens(
    id INT NOT NULL AUTO_INCREMENT,
    token CHAR(64) NOT NULL,
    user_id int(11) NOT NULL,
    PRIMARY KEY (id), 
    FOREIGN KEY (user_id) REFERENCES usertable(id) 
) ENGINE=INNODB;

USE swap;   
ALTER TABLE password_tokens   
ADD CONSTRAINT AK_id UNIQUE (token); 

ALTER TABLE usertable
MODIFY username VARCHAR(32) NOT NULL;

ALTER TABLE usertable
ALTER profile SET DEFAULT 'icons/profile.jpg';

SELECT posts.body, posts.likes, usertable.`username` FROM usertable, posts, followers
WHERE posts.user_id = followers.user_id
AND usertable.id = posts.user_id
AND follower_id = 22
ORDER BY posts.posted_at DESC;

SELECT usertable.username, posts.body FROM usertable, posts
WHERE usertable.username LIKE '%claire%'
OR posts.body LIKE '%hello';

SELECT comments.comment, usertable.`username` FROM comments, usertable
WHERE post_id = 25
AND comments.user_id = usertable.id
ORDER BY comments.posted_at DESC;

ALTER TABLE imgtable
ADD FOREIGN KEY comments_ibfk_2;

ALTER TABLE imgtable
ADD CONSTRAINT FOREIGN KEY (user_id) REFERENCES usertable (id);

select COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_COLUMN_NAME, REFERENCED_TABLE_NAME
from information_schema.KEY_COLUMN_USAGE
where TABLE_NAME = 'post_likes';

ALTER TABLE usertable
ADD profilepic text NULL;

ALTER TABLE profileimg RENAME TO imgtable;

SELECT * FROM imgtable 
WHERE user_id=38 ORDER BY id DESC;

SELECT * FROM imgtable ORDER BY id DESC;

SELECT post_id FROM img_likes WHERE post_id=37 AND user_id=38;

UPDATE imgtable SET likes=likes+6 WHERE id=38;

ALTER TABLE usertable
DROP COLUMN profile;