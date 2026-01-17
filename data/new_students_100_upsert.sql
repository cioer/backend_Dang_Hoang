USE school_management;

CREATE TABLE IF NOT EXISTS student_profiles (
  user_id INT PRIMARY KEY,
  dob DATE NOT NULL,
  gender ENUM('Nam','Nữ') NOT NULL,
  address VARCHAR(255) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO classes(name) SELECT '6A1' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='6A1');
INSERT INTO classes(name) SELECT '6A2' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='6A2');
INSERT INTO classes(name) SELECT '6A3' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='6A3');
INSERT INTO classes(name) SELECT '7A1' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='7A1');
INSERT INTO classes(name) SELECT '7A2' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='7A2');
INSERT INTO classes(name) SELECT '7A3' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='7A3');
INSERT INTO classes(name) SELECT '8A1' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='8A1');
INSERT INTO classes(name) SELECT '8A2' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='8A2');
INSERT INTO classes(name) SELECT '8A3' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='8A3');
INSERT INTO classes(name) SELECT '9A1' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='9A1');
INSERT INTO classes(name) SELECT '9A2' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='9A2');
INSERT INTO classes(name) SELECT '9A3' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='9A3');
INSERT INTO classes(name) SELECT '10A1' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='10A1');
INSERT INTO classes(name) SELECT '10A2' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='10A2');
INSERT INTO classes(name) SELECT '10A3' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='10A3');
INSERT INTO classes(name) SELECT '11A1' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='11A1');
INSERT INTO classes(name) SELECT '11A2' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='11A2');
INSERT INTO classes(name) SELECT '11A3' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='11A3');
INSERT INTO classes(name) SELECT '12A1' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='12A1');
INSERT INTO classes(name) SELECT '12A2' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='12A2');
INSERT INTO classes(name) SELECT '12A3' WHERE NOT EXISTS (SELECT 1 FROM classes WHERE name='12A3');

INSERT INTO users(username,password,full_name,role,email,phone,phone_verified,password_must_change)
VALUES
('HS-2001','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Khôi','student','hs2001@school.local',NULL,0,1),
('HS-2002','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Huy','student','hs2002@school.local',NULL,0,1),
('HS-2003','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Linh','student','hs2003@school.local',NULL,0,1),
('HS-2004','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Dũng','student','hs2004@school.local',NULL,0,1),
('HS-2005','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Trang','student','hs2005@school.local',NULL,0,1),
('HS-2006','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Hiếu','student','hs2006@school.local',NULL,0,1),
('HS-2007','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Khoa','student','hs2007@school.local',NULL,0,1),
('HS-2008','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo My','student','hs2008@school.local',NULL,0,1),
('HS-2009','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Vy','student','hs2009@school.local',NULL,0,1),
('HS-2010','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Nam','student','hs2010@school.local',NULL,0,1),
('HS-2011','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Hiếu','student','hs2011@school.local',NULL,0,1),
('HS-2012','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Khang','student','hs2012@school.local',NULL,0,1),
('HS-2013','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Mai','student','hs2013@school.local',NULL,0,1),
('HS-2014','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Phúc','student','hs2014@school.local',NULL,0,1),
('HS-2015','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Hạnh','student','hs2015@school.local',NULL,0,1),
('HS-2016','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Tâm','student','hs2016@school.local',NULL,0,1),
('HS-2017','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Thảo','student','hs2017@school.local',NULL,0,1),
('HS-2018','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Khang','student','hs2018@school.local',NULL,0,1),
('HS-2019','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Nhi','student','hs2019@school.local',NULL,0,1),
('HS-2020','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Phát','student','hs2020@school.local',NULL,0,1),
('HS-2021','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Tài','student','hs2021@school.local',NULL,0,1),
('HS-2022','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Như','student','hs2022@school.local',NULL,0,1),
('HS-2023','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Ngân','student','hs2023@school.local',NULL,0,1),
('HS-2024','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Long','student','hs2024@school.local',NULL,0,1),
('HS-2025','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Yến','student','hs2025@school.local',NULL,0,1),
('HS-2026','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Khánh','student','hs2026@school.local',NULL,0,1),
('HS-2027','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Huyền','student','hs2027@school.local',NULL,0,1),
('HS-2028','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Trí','student','hs2028@school.local',NULL,0,1),
('HS-2029','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Thư','student','hs2029@school.local',NULL,0,1),
('HS-2030','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Kỳ','student','hs2030@school.local',NULL,0,1),
('HS-2031','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Quân','student','hs2031@school.local',NULL,0,1),
('HS-2032','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Bảo','student','hs2032@school.local',NULL,0,1),
('HS-2033','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Phương','student','hs2033@school.local',NULL,0,1),
('HS-2034','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Hiếu','student','hs2034@school.local',NULL,0,1),
('HS-2035','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Diệu','student','hs2035@school.local',NULL,0,1),
('HS-2036','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Lộc','student','hs2036@school.local',NULL,0,1),
('HS-2037','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Nhung','student','hs2037@school.local',NULL,0,1),
('HS-2038','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Thiên','student','hs2038@school.local',NULL,0,1),
('HS-2039','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Hà','student','hs2039@school.local',NULL,0,1),
('HS-2040','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Tường','student','hs2040@school.local',NULL,0,1),
('HS-2041','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Thiện','student','hs2041@school.local',NULL,0,1),
('HS-2042','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Nguyên','student','hs2042@school.local',NULL,0,1),
('HS-2043','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Ánh','student','hs2043@school.local',NULL,0,1),
('HS-2044','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Khang','student','hs2044@school.local',NULL,0,1),
('HS-2045','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Mỹ','student','hs2045@school.local',NULL,0,1),
('HS-2046','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Phúc','student','hs2046@school.local',NULL,0,1),
('HS-2047','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Uyên','student','hs2047@school.local',NULL,0,1),
('HS-2048','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Hào','student','hs2048@school.local',NULL,0,1),
('HS-2049','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Tiên','student','hs2049@school.local',NULL,0,1),
('HS-2050','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Duy','student','hs2050@school.local',NULL,0,1),
('HS-2051','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Hưng','student','hs2051@school.local',NULL,0,1),
('HS-2052','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Phát','student','hs2052@school.local',NULL,0,1),
('HS-2053','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Thùy','student','hs2053@school.local',NULL,0,1),
('HS-2054','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Tường','student','hs2054@school.local',NULL,0,1),
('HS-2055','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Ngọc','student','hs2055@school.local',NULL,0,1),
('HS-2056','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Khoa','student','hs2056@school.local',NULL,0,1),
('HS-2057','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Nhã','student','hs2057@school.local',NULL,0,1),
('HS-2058','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Tín','student','hs2058@school.local',NULL,0,1),
('HS-2059','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Tuyết','student','hs2059@school.local',NULL,0,1),
('HS-2060','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Việt','student','hs2060@school.local',NULL,0,1),
('HS-2061','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Duy','student','hs2061@school.local',NULL,0,1),
('HS-2062','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Uyên','student','hs2062@school.local',NULL,0,1),
('HS-2063','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Hân','student','hs2063@school.local',NULL,0,1),
('HS-2064','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Hào','student','hs2064@school.local',NULL,0,1),
('HS-2065','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Nhung','student','hs2065@school.local',NULL,0,1),
('HS-2066','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Thiên','student','hs2066@school.local',NULL,0,1),
('HS-2067','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Hà','student','hs2067@school.local',NULL,0,1),
('HS-2068','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Tường','student','hs2068@school.local',NULL,0,1),
('HS-2069','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Kỳ','student','hs2069@school.local',NULL,0,1),
('HS-2070','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Thiện','student','hs2070@school.local',NULL,0,1),
('HS-2071','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Uyên','student','hs2071@school.local',NULL,0,1),
('HS-2072','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Hân','student','hs2072@school.local',NULL,0,1),
('HS-2073','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Huyền','student','hs2073@school.local',NULL,0,1),
('HS-2074','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Trí','student','hs2074@school.local',NULL,0,1),
('HS-2075','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Tiên','student','hs2075@school.local',NULL,0,1),
('HS-2076','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Duy','student','hs2076@school.local',NULL,0,1),
('HS-2077','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Phát','student','hs2077@school.local',NULL,0,1),
('HS-2078','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Hưng','student','hs2078@school.local',NULL,0,1),
('HS-2079','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Như','student','hs2079@school.local',NULL,0,1),
('HS-2080','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Nguyên','student','hs2080@school.local',NULL,0,1),
('HS-2081','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Ánh','student','hs2081@school.local',NULL,0,1),
('HS-2082','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Khoa','student','hs2082@school.local',NULL,0,1),
('HS-2083','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Tuyết','student','hs2083@school.local',NULL,0,1),
('HS-2084','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Việt','student','hs2084@school.local',NULL,0,1),
('HS-2085','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Uyên','student','hs2085@school.local',NULL,0,1),
('HS-2086','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Hân','student','hs2086@school.local',NULL,0,1),
('HS-2087','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Hào','student','hs2087@school.local',NULL,0,1),
('HS-2088','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Tiên','student','hs2088@school.local',NULL,0,1),
('HS-2089','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Duy','student','hs2089@school.local',NULL,0,1),
('HS-2090','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Phúc','student','hs2090@school.local',NULL,0,1),
('HS-2091','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nguyễn Minh Nhung','student','hs2091@school.local',NULL,0,1),
('HS-2092','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Trần Gia Thiên','student','hs2092@school.local',NULL,0,1),
('HS-2093','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lê Ngọc Hà','student','hs2093@school.local',NULL,0,1),
('HS-2094','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phạm Quang Tường','student','hs2094@school.local',NULL,0,1),
('HS-2095','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hoàng Thị Huyền','student','hs2095@school.local',NULL,0,1),
('HS-2096','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Huỳnh Đức Trí','student','hs2096@school.local',NULL,0,1),
('HS-2097','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Phan Hữu Tiên','student','hs2097@school.local',NULL,0,1),
('HS-2098','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vũ Bảo Duy','student','hs2098@school.local',NULL,0,1),
('HS-2099','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Võ Anh Phát','student','hs2099@school.local',NULL,0,1),
('HS-2100','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Đặng Tuấn Hưng','student','hs2100@school.local',NULL,0,1)
ON DUPLICATE KEY UPDATE
full_name=VALUES(full_name), role='student', email=VALUES(email);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A1' WHERE u.username='HS-2001'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-02-12', 'Nam', 'TP. Hồ Chí Minh' FROM users u WHERE u.username='HS-2001'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A2' WHERE u.username='HS-2002'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-03-15', 'Nam', 'Hà Nội' FROM users u WHERE u.username='HS-2002'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A3' WHERE u.username='HS-2003'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-04-18', 'Nữ', 'Đà Nẵng' FROM users u WHERE u.username='HS-2003'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A1' WHERE u.username='HS-2004'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-05-21', 'Nam', 'Cần Thơ' FROM users u WHERE u.username='HS-2004'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A2' WHERE u.username='HS-2005'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-06-24', 'Nữ', 'Bình Dương' FROM users u WHERE u.username='HS-2005'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A3' WHERE u.username='HS-2006'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-07-27', 'Nam', 'Đồng Nai' FROM users u WHERE u.username='HS-2006'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A1' WHERE u.username='HS-2007'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-08-05', 'Nam', 'Hải Phòng' FROM users u WHERE u.username='HS-2007'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A2' WHERE u.username='HS-2008'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-09-08', 'Nữ', 'Quảng Ninh' FROM users u WHERE u.username='HS-2008'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A3' WHERE u.username='HS-2009'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-10-11', 'Nữ', 'Khánh Hòa' FROM users u WHERE u.username='HS-2009'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A1' WHERE u.username='HS-2010'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-11-14', 'Nam', 'Nghệ An' FROM users u WHERE u.username='HS-2010'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A2' WHERE u.username='HS-2011'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-12-17', 'Nam', 'Thái Bình' FROM users u WHERE u.username='HS-2011'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A3' WHERE u.username='HS-2012'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-01-19', 'Nam', 'Thanh Hóa' FROM users u WHERE u.username='HS-2012'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A1' WHERE u.username='HS-2013'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-02-22', 'Nữ', 'Ninh Bình' FROM users u WHERE u.username='HS-2013'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A2' WHERE u.username='HS-2014'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-03-25', 'Nam', 'Nam Định' FROM users u WHERE u.username='HS-2014'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A3' WHERE u.username='HS-2015'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-04-28', 'Nữ', 'Hải Dương' FROM users u WHERE u.username='HS-2015'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A1' WHERE u.username='HS-2016'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-05-07', 'Nam', 'Bắc Ninh' FROM users u WHERE u.username='HS-2016'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A2' WHERE u.username='HS-2017'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-06-10', 'Nữ', 'Bắc Giang' FROM users u WHERE u.username='HS-2017'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A3' WHERE u.username='HS-2018'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-07-13', 'Nam', 'Phú Thọ' FROM users u WHERE u.username='HS-2018'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A1' WHERE u.username='HS-2019'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-08-16', 'Nữ', 'Vĩnh Phúc' FROM users u WHERE u.username='HS-2019'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A2' WHERE u.username='HS-2020'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-09-19', 'Nam', 'Quảng Nam' FROM users u WHERE u.username='HS-2020'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A3' WHERE u.username='HS-2021'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-10-22', 'Nam', 'Quảng Ngãi' FROM users u WHERE u.username='HS-2021'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A1' WHERE u.username='HS-2022'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-11-25', 'Nữ', 'Bình Thuận' FROM users u WHERE u.username='HS-2022'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A2' WHERE u.username='HS-2023'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-12-05', 'Nữ', 'Lâm Đồng' FROM users u WHERE u.username='HS-2023'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A3' WHERE u.username='HS-2024'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-01-08', 'Nam', 'Gia Lai' FROM users u WHERE u.username='HS-2024'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A1' WHERE u.username='HS-2025'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-02-11', 'Nữ', 'Kon Tum' FROM users u WHERE u.username='HS-2025'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A2' WHERE u.username='HS-2026'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-03-14', 'Nam', 'Đắk Lắk' FROM users u WHERE u.username='HS-2026'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A3' WHERE u.username='HS-2027'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-04-17', 'Nữ', 'Đắk Nông' FROM users u WHERE u.username='HS-2027'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A1' WHERE u.username='HS-2028'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-05-20', 'Nam', 'Hậu Giang' FROM users u WHERE u.username='HS-2028'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A2' WHERE u.username='HS-2029'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-06-23', 'Nữ', 'Bạc Liêu' FROM users u WHERE u.username='HS-2029'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A3' WHERE u.username='HS-2030'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-07-26', 'Nam', 'Cà Mau' FROM users u WHERE u.username='HS-2030'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A1' WHERE u.username='HS-2031'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-08-05', 'Nam', 'Long An' FROM users u WHERE u.username='HS-2031'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A2' WHERE u.username='HS-2032'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-09-08', 'Nam', 'Tiền Giang' FROM users u WHERE u.username='HS-2032'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A3' WHERE u.username='HS-2033'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-10-11', 'Nữ', 'Bến Tre' FROM users u WHERE u.username='HS-2033'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A1' WHERE u.username='HS-2034'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-11-14', 'Nam', 'Trà Vinh' FROM users u WHERE u.username='HS-2034'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A2' WHERE u.username='HS-2035'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-12-17', 'Nữ', 'Vĩnh Long' FROM users u WHERE u.username='HS-2035'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A3' WHERE u.username='HS-2036'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-01-19', 'Nam', 'An Giang' FROM users u WHERE u.username='HS-2036'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A1' WHERE u.username='HS-2037'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-02-22', 'Nữ', 'Kiên Giang' FROM users u WHERE u.username='HS-2037'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A2' WHERE u.username='HS-2038'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-03-25', 'Nam', 'Sóc Trăng' FROM users u WHERE u.username='HS-2038'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A3' WHERE u.username='HS-2039'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-04-28', 'Nữ', 'Hậu Giang' FROM users u WHERE u.username='HS-2039'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A1' WHERE u.username='HS-2040'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-05-07', 'Nam', 'Bình Phước' FROM users u WHERE u.username='HS-2040'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A2' WHERE u.username='HS-2041'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-06-10', 'Nam', 'Tây Ninh' FROM users u WHERE u.username='HS-2041'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A3' WHERE u.username='HS-2042'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-07-13', 'Nam', 'Bình Thuận' FROM users u WHERE u.username='HS-2042'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A1' WHERE u.username='HS-2043'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-08-16', 'Nữ', 'Lâm Đồng' FROM users u WHERE u.username='HS-2043'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A2' WHERE u.username='HS-2044'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-09-19', 'Nam', 'Khánh Hòa' FROM users u WHERE u.username='HS-2044'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A3' WHERE u.username='HS-2045'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-10-22', 'Nữ', 'Quảng Nam' FROM users u WHERE u.username='HS-2045'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A1' WHERE u.username='HS-2046'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-11-25', 'Nam', 'Quảng Ngãi' FROM users u WHERE u.username='HS-2046'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A2' WHERE u.username='HS-2047'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-12-05', 'Nữ', 'Phú Yên' FROM users u WHERE u.username='HS-2047'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A3' WHERE u.username='HS-2048'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-01-08', 'Nam', 'Bình Định' FROM users u WHERE u.username='HS-2048'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A1' WHERE u.username='HS-2049'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-02-11', 'Nữ', 'Quảng Bình' FROM users u WHERE u.username='HS-2049'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A2' WHERE u.username='HS-2050'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-03-14', 'Nam', 'Quảng Trị' FROM users u WHERE u.username='HS-2050'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A3' WHERE u.username='HS-2051'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-04-17', 'Nam', 'Thừa Thiên Huế' FROM users u WHERE u.username='HS-2051'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A1' WHERE u.username='HS-2052'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-05-20', 'Nam', 'Hà Tĩnh' FROM users u WHERE u.username='HS-2052'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A2' WHERE u.username='HS-2053'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-06-23', 'Nữ', 'Quảng Nam' FROM users u WHERE u.username='HS-2053'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A3' WHERE u.username='HS-2054'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-07-26', 'Nam', 'Quảng Ngãi' FROM users u WHERE u.username='HS-2054'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A1' WHERE u.username='HS-2055'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-08-05', 'Nữ', 'Khánh Hòa' FROM users u WHERE u.username='HS-2055'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A2' WHERE u.username='HS-2056'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-09-08', 'Nam', 'Ninh Thuận' FROM users u WHERE u.username='HS-2056'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A3' WHERE u.username='HS-2057'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-10-11', 'Nữ', 'Bình Thuận' FROM users u WHERE u.username='HS-2057'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A1' WHERE u.username='HS-2058'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-11-14', 'Nam', 'Phú Yên' FROM users u WHERE u.username='HS-2058'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A2' WHERE u.username='HS-2059'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-12-17', 'Nữ', 'Bình Định' FROM users u WHERE u.username='HS-2059'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A3' WHERE u.username='HS-2060'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-01-19', 'Nam', 'Quảng Bình' FROM users u WHERE u.username='HS-2060'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A1' WHERE u.username='HS-2061'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-02-22', 'Nam', 'Quảng Trị' FROM users u WHERE u.username='HS-2061'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A2' WHERE u.username='HS-2062'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-03-25', 'Nữ', 'Thừa Thiên Huế' FROM users u WHERE u.username='HS-2062'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A3' WHERE u.username='HS-2063'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-04-28', 'Nữ', 'Đà Nẵng' FROM users u WHERE u.username='HS-2063'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A1' WHERE u.username='HS-2064'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-05-07', 'Nam', 'Quảng Nam' FROM users u WHERE u.username='HS-2064'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A2' WHERE u.username='HS-2065'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-06-10', 'Nữ', 'Quảng Ngãi' FROM users u WHERE u.username='HS-2065'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A3' WHERE u.username='HS-2066'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-07-13', 'Nam', 'Phú Yên' FROM users u WHERE u.username='HS-2066'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A1' WHERE u.username='HS-2067'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-08-16', 'Nữ', 'Bình Định' FROM users u WHERE u.username='HS-2067'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A2' WHERE u.username='HS-2068'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-09-19', 'Nam', 'Quảng Bình' FROM users u WHERE u.username='HS-2068'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A3' WHERE u.username='HS-2069'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-10-22', 'Nam', 'Quảng Trị' FROM users u WHERE u.username='HS-2069'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A1' WHERE u.username='HS-2070'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-11-25', 'Nam', 'Thừa Thiên Huế' FROM users u WHERE u.username='HS-2070'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A2' WHERE u.username='HS-2071'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-12-05', 'Nữ', 'Đà Nẵng' FROM users u WHERE u.username='HS-2071'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A3' WHERE u.username='HS-2072'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-01-08', 'Nữ', 'Quảng Nam' FROM users u WHERE u.username='HS-2072'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A1' WHERE u.username='HS-2073'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-02-11', 'Nữ', 'Quảng Ngãi' FROM users u WHERE u.username='HS-2073'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A2' WHERE u.username='HS-2074'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-03-14', 'Nam', 'Phú Yên' FROM users u WHERE u.username='HS-2074'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A3' WHERE u.username='HS-2075'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-04-17', 'Nữ', 'Bình Định' FROM users u WHERE u.username='HS-2075'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A1' WHERE u.username='HS-2076'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-05-20', 'Nam', 'Quảng Bình' FROM users u WHERE u.username='HS-2076'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A2' WHERE u.username='HS-2077'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-06-23', 'Nam', 'Quảng Trị' FROM users u WHERE u.username='HS-2077'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A3' WHERE u.username='HS-2078'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-07-26', 'Nam', 'Thừa Thiên Huế' FROM users u WHERE u.username='HS-2078'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A1' WHERE u.username='HS-2079'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-08-05', 'Nữ', 'Đà Nẵng' FROM users u WHERE u.username='HS-2079'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A2' WHERE u.username='HS-2080'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-09-08', 'Nam', 'Quảng Nam' FROM users u WHERE u.username='HS-2080'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A3' WHERE u.username='HS-2081'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-10-11', 'Nữ', 'Quảng Ngãi' FROM users u WHERE u.username='HS-2081'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A1' WHERE u.username='HS-2082'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-11-14', 'Nam', 'Phú Yên' FROM users u WHERE u.username='HS-2082'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A2' WHERE u.username='HS-2083'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-12-17', 'Nữ', 'Bình Định' FROM users u WHERE u.username='HS-2083'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='9A3' WHERE u.username='HS-2084'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-01-19', 'Nam', 'Quảng Bình' FROM users u WHERE u.username='HS-2084'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A1' WHERE u.username='HS-2085'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-02-22', 'Nữ', 'Quảng Trị' FROM users u WHERE u.username='HS-2085'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A2' WHERE u.username='HS-2086'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-03-25', 'Nữ', 'Thừa Thiên Huế' FROM users u WHERE u.username='HS-2086'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='10A3' WHERE u.username='HS-2087'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-04-28', 'Nam', 'Đà Nẵng' FROM users u WHERE u.username='HS-2087'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A1' WHERE u.username='HS-2088'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-05-07', 'Nữ', 'Quảng Nam' FROM users u WHERE u.username='HS-2088'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A2' WHERE u.username='HS-2089'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-06-10', 'Nam', 'Quảng Ngãi' FROM users u WHERE u.username='HS-2089'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='11A3' WHERE u.username='HS-2090'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2006-07-13', 'Nam', 'Phú Yên' FROM users u WHERE u.username='HS-2090'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A1' WHERE u.username='HS-2091'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2007-08-16', 'Nữ', 'Bình Định' FROM users u WHERE u.username='HS-2091'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A2' WHERE u.username='HS-2092'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2008-09-19', 'Nam', 'Quảng Bình' FROM users u WHERE u.username='HS-2092'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='12A3' WHERE u.username='HS-2093'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2009-10-22', 'Nữ', 'Quảng Trị' FROM users u WHERE u.username='HS-2093'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A1' WHERE u.username='HS-2094'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2010-11-25', 'Nam', 'Thừa Thiên Huế' FROM users u WHERE u.username='HS-2094'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A2' WHERE u.username='HS-2095'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2011-12-05', 'Nữ', 'Đà Nẵng' FROM users u WHERE u.username='HS-2095'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='6A3' WHERE u.username='HS-2096'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2012-01-08', 'Nam', 'Quảng Nam' FROM users u WHERE u.username='HS-2096'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A1' WHERE u.username='HS-2097'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2013-02-11', 'Nữ', 'Quảng Ngãi' FROM users u WHERE u.username='HS-2097'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A2' WHERE u.username='HS-2098'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2014-03-14', 'Nam', 'Phú Yên' FROM users u WHERE u.username='HS-2098'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='7A3' WHERE u.username='HS-2099'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2015-04-17', 'Nam', 'Bình Định' FROM users u WHERE u.username='HS-2099'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);

INSERT INTO student_details(user_id,class_id)
SELECT u.id, c.id FROM users u JOIN classes c ON c.name='8A1' WHERE u.username='HS-2100'
ON DUPLICATE KEY UPDATE class_id=VALUES(class_id);
INSERT INTO student_profiles(user_id,dob,gender,address)
SELECT u.id, '2005-05-20', 'Nam', 'Quảng Bình' FROM users u WHERE u.username='HS-2100'
ON DUPLICATE KEY UPDATE dob=VALUES(dob), gender=VALUES(gender), address=VALUES(address);
