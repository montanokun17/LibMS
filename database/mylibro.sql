CREATE DATABASE mylibro;

USE mylibro;

CREATE TABLE users (
    id_no INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(60) NOT NULL,
    con_num VARCHAR(20) NOT NULL,
    firstname VARCHAR(60) NOT NULL,
    lastname VARCHAR(60) NOT NULL,
    password VARCHAR(60) NOT NULL,
    acctype ENUM("Admin", "Librarian", "Student", "Guest") NOT NULL,
    schlvl ENUM("Elementary", "Junior High School", "Senior High School", "College","Graduated","Guest") NOT NULL,
    brgy VARCHAR(100) NOT NULL,
    status ENUM("Active", "Disabled") NOT NULL,
    deleted INT NOT NULL,
    token_pin INT NOT NULL,
    pin_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users(id_no,username,email,con_num,firstname,lastname,password,acctype,schlvl,brgy, status,deleted)
VALUES ('100001','admin','mylibrolibrarymanagementsystem@gmail.com','09154985773','MyLibro','Administrator','admin01','Admin','','San Nicolas','Active','0'),
       ('100002','librarian','mylibrolibrarymanagementsystem@gmail.com','09154985773','Librarian','Librarian LibMS','librarian01','Librarian','','San Nicolas','Active','0');


CREATE TABLE qr_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT references users(id_no),
    username VARCHAR(255) references users(username),
    qr_code_data BLOB NOT NULL,
    qr_code_type VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_pics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT references users(id_no),
    username VARCHAR(255) references users(username),
    user_pic_data BLOB NOT NULL,
    user_pic_type VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    dewey varchar(100) NOT NULL,
    section VARCHAR(50) NOT NULL,
    book_title VARCHAR(100) NOT NULL,
    volume VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    stocks INT NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(100) NOT NULL,
    status ENUM("GOOD","DAMAGED","LOST","DILAPITATED") NOT NULL,
    deleted INT NOT NULL
);

/*CREATE TABLE qr_table (
    id_no INT references users(id_no),
    username VARCHAR(50) references users(username),
    qr_code LONGBLOB
);*/

INSERT INTO qr_table(id_no,username,qr_code)
VALUES ('100001','admin', 'admin.png', LOAD_FILE'/LibMS/qr_bin/admin.png'),
       ('100002','librarian','librarian.png', LOAD_FILE '/LibMS/qr_bin/librarian.png');
