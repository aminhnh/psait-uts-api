CREATE DATABASE sait_db_uts;

USE sait_db_uts;

CREATE TABLE mahasiswa (
    nim VARCHAR(10) PRIMARY KEY,
    nama VARCHAR(20),
    alamat VARCHAR(40),
    tanggal_lahir DATE
);

CREATE TABLE matakuliah (
    kode_mk VARCHAR(10) PRIMARY KEY,
    nama_mk VARCHAR(100),
    sks INT(2)
);

CREATE TABLE perkuliahan (
    id_perkuliahan INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(10),
    kode_mk VARCHAR(10),
    nilai DOUBLE,
    FOREIGN KEY (nim) REFERENCES mahasiswa(nim),
    FOREIGN KEY (kode_mk) REFERENCES matakuliah(kode_mk)
);

INSERT INTO mahasiswa (nim, nama, alamat, tanggal_lahir) VALUES
('sv_001', 'Joko', 'Bantul', '1999-12-07'),
('sv_002', 'Paul', 'Sleman', '2000-10-07'),
('sv_003', 'Andy', 'Surabaya', '2000-02-09');

INSERT INTO matakuliah (kode_mk, nama_mk, sks) VALUES
('svpl_001', 'Database', 2),
('svpl_002', 'Kecerdasan Artifisial', 2),
('svpl_003', 'Interoperabilitas', 2);

INSERT INTO perkuliahan (nim, kode_mk, nilai) VALUES
('sv_001', 'svpl_001', 90),
('sv_001', 'svpl_002', 87),
('sv_001', 'svpl_003', 88),
('sv_002', 'svpl_001', 98),
('sv_002', 'svpl_002', 77);


-- 
-- SELECT 
--     mhs.nim,
--     mhs.nama,
--     mhs.alamat,
--     mhs.tanggal_lahir,
--     mk.kode_mk,
--     mk.nama_mk,
--     mk.sks,
--     p.nilai
-- FROM 
--     perkuliahan p
-- INNER JOIN 
--     mahasiswa mhs ON p.nim = mhs.nim
-- INNER JOIN 
--     matakuliah mk ON p.kode_mk = mk.kode_mk;
-- 

