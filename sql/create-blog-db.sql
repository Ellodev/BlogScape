CREATE TABLE users (
    user_id int PRIMARY KEY AUTO_INCREMENT,
    username varchar(50) UNIQUE,
    firstname varchar(50),
    lastname varchar(50),
    password varchar(255),
    email varchar(255) UNIQUE
);

CREATE TABLE posts (
    post_id int PRIMARY KEY AUTO_INCREMENT,
    user_id int,
    title varchar(255),
    content text,
    image varchar(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE comments (
    comment_id int PRIMARY KEY AUTO_INCREMENT,
    comment_text text,
    user_id int,
    post_id int,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
);

CREATE TABLE likes (
    like_id int PRIMARY KEY AUTO_INCREMENT,
    user_id int,
    post_id int,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
);

ALTER TABLE users ADD profile_picture varchar(255);
