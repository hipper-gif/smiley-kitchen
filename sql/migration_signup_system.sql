-- ========================================
-- Smiley配食システム Phase 2
-- 企業自身による登録システム マイグレーション
-- ========================================

-- ========================================
-- companiesテーブル拡張
-- ========================================

ALTER TABLE companies
-- 登録状態管理
ADD COLUMN IF NOT EXISTS registration_status ENUM('pending','active','suspended') DEFAULT 'active'
    COMMENT '登録ステータス（pending=承認待ち, active=利用可能, suspended=停止中）' AFTER is_active,
ADD COLUMN IF NOT EXISTS registered_at TIMESTAMP NULL
    COMMENT '登録完了日時' AFTER registration_status,

-- 住所情報
ADD COLUMN IF NOT EXISTS postal_code VARCHAR(8)
    COMMENT '郵便番号' AFTER contact_email,
ADD COLUMN IF NOT EXISTS prefecture VARCHAR(20)
    COMMENT '都道府県' AFTER postal_code,
ADD COLUMN IF NOT EXISTS city VARCHAR(100)
    COMMENT '市区町村' AFTER prefecture,
ADD COLUMN IF NOT EXISTS address_line1 VARCHAR(200)
    COMMENT '住所・番地' AFTER city,
ADD COLUMN IF NOT EXISTS address_line2 VARCHAR(200)
    COMMENT '建物名・部屋番号' AFTER address_line1,
ADD COLUMN IF NOT EXISTS company_name_kana VARCHAR(200)
    COMMENT '企業名カナ' AFTER company_name,
ADD COLUMN IF NOT EXISTS delivery_location_name VARCHAR(100)
    COMMENT '配達先名称' AFTER company_address,
ADD COLUMN IF NOT EXISTS phone_extension VARCHAR(20)
    COMMENT '内線番号' AFTER phone,
ADD COLUMN IF NOT EXISTS delivery_notes TEXT
    COMMENT '配達時のご要望' AFTER payment_due_days,

-- システム管理
ADD COLUMN IF NOT EXISTS signup_ip VARCHAR(45)
    COMMENT '登録時IPアドレス' AFTER delivery_notes;

-- ========================================
-- usersテーブル拡張
-- ========================================

ALTER TABLE users
-- 基本情報
ADD COLUMN IF NOT EXISTS user_name_kana VARCHAR(200)
    COMMENT '氏名カナ' AFTER user_name,
ADD COLUMN IF NOT EXISTS email VARCHAR(255) UNIQUE
    COMMENT 'メールアドレス' AFTER user_name_kana,

-- ログイン情報
ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255)
    COMMENT 'パスワードハッシュ' AFTER email,
ADD COLUMN IF NOT EXISTS remember_token VARCHAR(100)
    COMMENT 'ログイン保持トークン' AFTER password_hash,
ADD COLUMN IF NOT EXISTS last_login_at TIMESTAMP NULL
    COMMENT '最終ログイン日時' AFTER remember_token,

-- 権限
ADD COLUMN IF NOT EXISTS is_company_admin TINYINT(1) DEFAULT 0
    COMMENT '企業管理者フラグ' AFTER role,
ADD COLUMN IF NOT EXISTS role ENUM('user','company_admin','smiley_staff','system_admin') DEFAULT 'user'
    COMMENT '権限' AFTER is_company_admin;

-- ========================================
-- インデックス追加
-- ========================================

-- usersテーブルのemail用インデックス（既存の場合はスキップ）
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- companiesテーブルのregistration_status用インデックス
CREATE INDEX IF NOT EXISTS idx_companies_registration_status ON companies(registration_status);

-- usersテーブルのremember_token用インデックス
CREATE INDEX IF NOT EXISTS idx_users_remember_token ON users(remember_token);

-- ========================================
-- 既存データの更新（デフォルト値設定）
-- ========================================

-- 既存企業をactiveステータスに
UPDATE companies
SET registration_status = 'active',
    registered_at = created_at
WHERE registration_status IS NULL;

-- 既存ユーザーのrole設定
UPDATE users
SET role = 'user'
WHERE role IS NULL;

-- ========================================
-- コメント
-- ========================================

-- Phase 2で追加された機能:
-- 1. 企業自身による新規登録システム
-- 2. メールアドレス + パスワードによるログイン
-- 3. Remember Me機能
-- 4. 企業管理者権限
-- 5. 登録状態管理（pending/active/suspended）
