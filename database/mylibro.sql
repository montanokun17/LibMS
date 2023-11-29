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
    edition VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    author VARCHAR(100) NOT NULL,
    publisher VARCHAR(100) NOT NULL,
    isbn VARCHAR(100) NOT NULL,
    status ENUM("GOOD","DAMAGED","LOST","DILAPITATED") NOT NULL,
    book_borrow_status VARCHAR(50) NOT NULL,
    deleted INT NOT NULL
);

CREATE TABLE borrow_requests (
    borrow_id INT PRIMARY KEY AUTO_INCREMENT,
    borrower_user_id INT references users(id_no),
    borrower_username VARCHAR(255) references users(username),
    book_id INT references books(book_id),
    book_title VARCHAR(255) references books(book_title),
    borrow_days INT NOT NULL,
    borrow_status ENUM('Pending','Approved','Rejected') NOT NULL,
    request_date DATE NOT NULL,
    request_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
    notif_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_user_id INT references users(id_no),
    receiver_user_id INT references users(id_no),
    notification_message VARCHAR(255) NOT NULL,
    read_status ENUM('UNREAD','READ') NOT NULL, 
    notif_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE approved_borrow_requests (
    borrow_id INT PRIMARY KEY AUTO_INCREMENT,
    borrower_user_id INT,
    borrower_username VARCHAR(255),
    book_id INT,
    book_title VARCHAR(255),
    borrow_days INT,
    borrow_status VARCHAR(255),
    request_approval_date DATE,
    due_date DATE,
    pickup_date DATE,
    approved_by VARCHAR(255),
    FOREIGN KEY (borrower_user_id) REFERENCES users(id_no),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);


CREATE TABLE book_log_history (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    borrow_id INT,
    borrower_user_id INT,
    borrower_username VARCHAR(255),
    book_id INT,
    book_title VARCHAR(255),
    borrow_days INT,
    borrow_status VARCHAR(100) NOT NULL,
    request_date DATE NOT NULL,
    request_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    action_performed VARCHAR(50) NOT NULL,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    action_performed_by VARCHAR(255),
    FOREIGN KEY (borrow_id) REFERENCES borrow_requests(borrow_id),
    FOREIGN KEY (borrower_user_id) REFERENCES users(id_no),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);

CREATE TABLE returned_books (
    return_id INT PRIMARY KEY AUTO_INCREMENT,
    borrow_id INT,
    borrower_user_id INT,
    borrower_username VARCHAR(255),
    book_id INT,
    book_title VARCHAR(255),
    borrow_days INT,
    book_status VARCHAR(100) NOT NULL,
    return_date DATE NOT NULL,
    verified_by VARCHAR(100) NOT NULL,
    FOREIGN KEY (borrow_id) REFERENCES borrow_requests(borrow_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (borrower_user_id) REFERENCES users(id_no)
);

CREATE TABLE renew_requests (
    renew_id INT PRIMARY KEY AUTO_INCREMENT,
    borrow_id INT,
    borrower_user_id INT,
    borrower_username VARCHAR(255),
    book_id INT,
    book_title VARCHAR(255),
    borrow_days INT,
    renew_request_date DATE NOT NULL,
    renew_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrow_id) REFERENCES borrow_requests(borrow_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (borrower_user_id) REFERENCES users(id_no)
);

CREATE TABLE renewed_books (
    renew_id INT,
    borrow_id INT,
    borrower_user_id INT,
    borrower_username VARCHAR(255),
    book_id INT,
    book_title VARCHAR(255),
    borrow_days INT,
    renew_date DATE NOT NULL,
    renewed_by VARCHAR(100) NOT NULL,
    renew_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (renew_id) REFERENCES renew_requests(renew_id),
    FOREIGN KEY (borrow_id) REFERENCES borrow_requests(borrow_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (borrower_user_id) REFERENCES users(id_no)
);

/*CREATE TABLE approved_borrow_requests (
    borrow_id INT PRIMARY KEY AUTO_INCREMENT,
    borrower_user_id INT,
    borrower_username VARCHAR(255),
    book_id INT,
    book_title VARCHAR(255),
    borrow_days INT,
    borrow_status ENUM('Approved') DEFAULT 'Approved', -- Set to 'Approved' since it's moved to the borrowed table
    request_approval_date DATE,
    due_date DATE,
    approved_by VARCHAR(255),
    FOREIGN KEY (borrower_user_id) REFERENCES users(id_no),
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    CHECK (borrow_status = 'Approved' )
);*/



/*CREATE TABLE qr_table (
    id_no INT references users(id_no),
    username VARCHAR(50) references users(username),
    qr_code LONGBLOB
);*/

INSERT INTO qr_table(id_no,username,qr_code)
VALUES ('100001','admin', 'admin.png', LOAD_FILE'/LibMS/qr_bin/admin.png'),
       ('100002','librarian','librarian.png', LOAD_FILE '/LibMS/qr_bin/librarian.png');
