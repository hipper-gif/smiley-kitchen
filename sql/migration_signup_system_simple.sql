-- ========================================
-- Smiley配食システム Phase 2
-- 企業自身による登録システム マイグレーション
-- 簡易版（古いMySQLバージョン用）
-- ========================================

-- companiesテーブル拡張
ALTER TABLE companies
ADD COLUMN registration_status ENUM('pending','active','suspended') DEFAULT 'active' COMMENT '登録ステータス' AFTER is_active;

ALTER TABLE companies
ADD COLUMN registered_at TIMESTAMP NULL COMMENT '登録完了日時' AFTER registration_status;

ALTER TABLE companies
ADD COLUMN postal_code VARCHAR(8) COMMENT '郵便番号' AFTER contact_email;

ALTER TABLE companies
ADD COLUMN prefecture VARCHAR(20) COMMENT '都道府県' AFTER postal_code;

ALTER TABLE companies
ADD COLUMN city VARCHAR(100) COMMENT '市区町村' AFTER prefecture;

ALTER TABLE companies
ADD COLUMN address_line1 VARCHAR(200) COMMENT '住所・番地' AFTER city;

ALTER TABLE companies
ADD COLUMN address_line2 VARCHAR(200) COMMENT '建物名・部屋番号' AFTER address_line1;

ALTER TABLE companies
ADD COLUMN company_name_kana VARCHAR(200) COMMENT '企業名カナ' AFTER company_name;

ALTER TABLE companies
ADD COLUMN delivery_location_name VARCHAR(100) COMMENT '配達先名称' AFTER company_address;

ALTER TABLE companies
ADD COLUMN phone_extension VARCHAR(20) COMMENT '内線番号' AFTER phone;

ALTER TABLE companies
ADD COLUMN delivery_notes TEXT COMMENT '配達時のご要望' AFTER payment_due_days;

ALTER TABLE companies
ADD COLUMN signup_ip VARCHAR(45) COMMENT '登録時IPアドレス' AFTER delivery_notes;

-- usersテーブル拡張
ALTER TABLE users
ADD COLUMN user_name_kana VARCHAR(200) COMMENT '氏名カナ' AFTER user_name;

ALTER TABLE users
ADD COLUMN email VARCHAR(255) UNIQUE COMMENT 'メールアドレス' AFTER user_name_kana;

ALTER TABLE users
ADD COLUMN password_hash VARCHAR(255) COMMENT 'パスワードハッシュ' AFTER email;

ALTER TABLE users
ADD COLUMN remember_token VARCHAR(100) COMMENT 'ログイン保持トークン' AFTER password_hash;

ALTER TABLE users
ADD COLUMN remember_expires TIMESTAMP NULL COMMENT 'ログイン保持期限' AFTER remember_token;

ALTER TABLE users
ADD COLUMN last_login_at TIMESTAMP NULL COMMENT '最終ログイン日時' AFTER remember_expires;

ALTER TABLE users
ADD COLUMN role ENUM('user','company_admin','smiley_staff','system_admin') DEFAULT 'user' COMMENT '権限' AFTER last_login_at;

ALTER TABLE users
ADD COLUMN is_company_admin TINYINT(1) DEFAULT 0 COMMENT '企業管理者フラグ' AFTER role;

-- インデックス追加
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_companies_registration_status ON companies(registration_status);
CREATE INDEX idx_users_remember_token ON users(remember_token);

-- 既存データの更新
UPDATE companies
SET registration_status = 'active',
    registered_at = created_at
WHERE registration_status IS NULL;

UPDATE users
SET role = 'user'
WHERE role IS NULL;
