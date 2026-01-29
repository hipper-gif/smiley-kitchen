<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録 - Smiley配食システム</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: #F5F5F5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .brand-logo {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
        }

        .page-title {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 32px;
        }

        .registration-flow {
            background: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 40px;
        }

        .flow-steps {
            display: flex;
            justify-content: space-around;
            margin-top: 32px;
        }

        .step {
            text-align: center;
            flex: 1;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: #4CAF50;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            margin: 0 auto 12px;
        }

        .form-section {
            background: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .form-section h3 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 24px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 12px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .badge-required {
            background: #F44336;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 8px;
        }

        .form-control, .form-select {
            height: 48px;
            font-size: 16px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        textarea.form-control {
            height: auto;
        }

        .terms-box {
            max-height: 300px;
            overflow-y: auto;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            background: #F9F9F9;
        }

        .btn-submit {
            background: #4CAF50;
            color: white;
            border: none;
            height: 60px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            display: block;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        }

        .notice-box {
            background: #FFF3CD;
            border-left: 4px solid #FFC107;
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 4px;
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #E0E0E0;
        }
    </style>
</head>
<body>
    <!-- ヘッダー -->
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <span class="brand-logo">🍱 Smiley Kitchen</span>
                <a href="login.php" class="btn btn-outline-primary">既に登録済みの方はこちら</a>
            </div>
        </div>
    </div>

    <div class="container" style="max-width: 800px;">
        <!-- ページタイトル -->
        <h1 class="page-title">新規登録</h1>

        <!-- 登録の流れ -->
        <div class="registration-flow">
            <h2 class="text-center mb-4">ご登録の流れ</h2>
            <div class="flow-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <p>フォーム入力</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <p>登録完了</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <p>注文開始！</p>
                </div>
            </div>
        </div>

        <!-- 注意事項 -->
        <div class="notice-box">
            <ul class="mb-0">
                <li>※ご登録後、すぐにご利用いただけます</li>
                <li>※「@smiley-kitchen.com」からのメールを受信できるよう設定してください</li>
                <li>※Google Chromeを推奨しています</li>
            </ul>
        </div>

        <!-- 登録フォーム -->
        <form id="signupForm">
            <!-- 企業・お届け先情報 -->
            <div class="form-section">
                <h3>企業・お届け先情報</h3>

                <div class="mb-3">
                    <label class="form-label">
                        郵便番号<span class="badge-required">必須</span>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="postal_code" id="postalCode"
                               placeholder="1234567" maxlength="8" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="searchAddress()">
                            郵便番号から住所を入力する
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        都道府県<span class="badge-required">必須</span>
                    </label>
                    <select class="form-select" name="prefecture" id="prefecture" required>
                        <option value="">選択してください</option>
                        <option value="北海道">北海道</option>
                        <option value="青森県">青森県</option>
                        <option value="岩手県">岩手県</option>
                        <option value="宮城県">宮城県</option>
                        <option value="秋田県">秋田県</option>
                        <option value="山形県">山形県</option>
                        <option value="福島県">福島県</option>
                        <option value="茨城県">茨城県</option>
                        <option value="栃木県">栃木県</option>
                        <option value="群馬県">群馬県</option>
                        <option value="埼玉県">埼玉県</option>
                        <option value="千葉県">千葉県</option>
                        <option value="東京都">東京都</option>
                        <option value="神奈川県">神奈川県</option>
                        <option value="新潟県">新潟県</option>
                        <option value="富山県">富山県</option>
                        <option value="石川県">石川県</option>
                        <option value="福井県">福井県</option>
                        <option value="山梨県">山梨県</option>
                        <option value="長野県">長野県</option>
                        <option value="岐阜県">岐阜県</option>
                        <option value="静岡県">静岡県</option>
                        <option value="愛知県">愛知県</option>
                        <option value="三重県">三重県</option>
                        <option value="滋賀県">滋賀県</option>
                        <option value="京都府">京都府</option>
                        <option value="大阪府">大阪府</option>
                        <option value="兵庫県">兵庫県</option>
                        <option value="奈良県">奈良県</option>
                        <option value="和歌山県">和歌山県</option>
                        <option value="鳥取県">鳥取県</option>
                        <option value="島根県">島根県</option>
                        <option value="岡山県">岡山県</option>
                        <option value="広島県">広島県</option>
                        <option value="山口県">山口県</option>
                        <option value="徳島県">徳島県</option>
                        <option value="香川県">香川県</option>
                        <option value="愛媛県">愛媛県</option>
                        <option value="高知県">高知県</option>
                        <option value="福岡県">福岡県</option>
                        <option value="佐賀県">佐賀県</option>
                        <option value="長崎県">長崎県</option>
                        <option value="熊本県">熊本県</option>
                        <option value="大分県">大分県</option>
                        <option value="宮崎県">宮崎県</option>
                        <option value="鹿児島県">鹿児島県</option>
                        <option value="沖縄県">沖縄県</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        市区町村<span class="badge-required">必須</span>
                    </label>
                    <input type="text" class="form-control" name="city" id="city" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        住所・番地<span class="badge-required">必須</span>
                    </label>
                    <input type="text" class="form-control" name="address_line1" id="addressLine1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">建物名・部屋番号</label>
                    <input type="text" class="form-control" name="address_line2">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        企業名<span class="badge-required">必須</span>
                    </label>
                    <input type="text" class="form-control" name="company_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        企業名カナ<span class="badge-required">必須</span>
                    </label>
                    <input type="text" class="form-control" name="company_name_kana"
                           placeholder="カブシキガイシャスマイリー" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        配達先名称<span class="badge-required">必須</span>
                    </label>
                    <input type="text" class="form-control" name="delivery_location_name"
                           placeholder="例: 総務部、1階受付" required>
                    <small class="form-text text-muted">部署名や配達場所を入力してください</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        企業電話番号<span class="badge-required">必須</span>
                    </label>
                    <input type="tel" class="form-control" name="company_phone"
                           placeholder="0312345678" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">内線番号</label>
                    <input type="text" class="form-control" name="phone_extension">
                </div>

                <div class="mb-3">
                    <label class="form-label">配達時のご要望など</label>
                    <textarea class="form-control" name="delivery_notes" rows="3"
                              placeholder="例: 受付に預けてください"></textarea>
                </div>
            </div>

            <!-- 担当者情報 -->
            <div class="form-section">
                <h3>ご登録者情報</h3>

                <div class="mb-3">
                    <label class="form-label">
                        氏名<span class="badge-required">必須</span>
                    </label>
                    <input type="text" class="form-control" name="user_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        氏名カナ<span class="badge-required">必須</span>
                    </label>
                    <input type="text" class="form-control" name="user_name_kana"
                           placeholder="ヤマダタロウ" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        メールアドレス<span class="badge-required">必須</span>
                    </label>
                    <input type="email" class="form-control" name="email" id="email" required>
                    <small class="form-text text-muted">
                        ※キャリアメールをご利用の場合は、「@smiley-kitchen.com」からのメールを受信できるよう設定してください
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        メールアドレス確認<span class="badge-required">必須</span>
                    </label>
                    <input type="email" class="form-control" name="email_confirm" id="emailConfirm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        パスワード<span class="badge-required">必須</span>
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" id="password"
                               minlength="8" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                            表示
                        </button>
                    </div>
                    <small class="form-text text-muted">8文字以上で入力してください</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        パスワード確認<span class="badge-required">必須</span>
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password_confirm" id="passwordConfirm"
                               minlength="8" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordConfirm')">
                            表示
                        </button>
                    </div>
                </div>
            </div>

            <!-- 利用規約 -->
            <div class="form-section">
                <h3>利用規約</h3>
                <div class="terms-box">
                    <h5>利用規約</h5>
                    <p>本サービスをご利用いただくには、以下の利用規約に同意いただく必要があります。</p>
                    <h6>第1条（適用）</h6>
                    <p>本規約は、本サービスの利用に関し、当社と利用者との間の権利義務関係を定めることを目的とし、利用者と当社との間の本サービスの利用に関わる一切の関係に適用されます。</p>
                    <h6>第2条（利用登録）</h6>
                    <p>登録希望者は、本規約に同意の上、当社の定める方法によって利用登録を申請し、当社がこれを承認することによって、利用登録が完了するものとします。</p>
                    <h6>第3条（禁止事項）</h6>
                    <p>利用者は、本サービスの利用にあたり、以下の行為をしてはなりません：</p>
                    <ul>
                        <li>法令または公序良俗に違反する行為</li>
                        <li>犯罪行為に関連する行為</li>
                        <li>本サービスの内容等、本サービスに含まれる著作権、商標権ほか知的財産権を侵害する行為</li>
                        <li>当社、ほかの利用者、またはその他第三者のサーバーまたはネットワークの機能を破壊したり、妨害したりする行為</li>
                    </ul>
                    <h6>第4条（サービスの変更・停止）</h6>
                    <p>当社は、利用者に通知することなく、本サービスの内容を変更し、または本サービスの提供を停止することができるものとします。</p>
                    <h6>第5条（免責事項）</h6>
                    <p>当社は、本サービスに関して、利用者と他の利用者または第三者との間において生じた取引、連絡または紛争等について一切責任を負いません。</p>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn-submit">
                        利用規約に同意して登録する
                    </button>
                </div>

                <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;"></div>
            </div>
        </form>

        <!-- ログインリンク -->
        <div class="login-link">
            <p><a href="login.php">既に登録済みの方はこちら</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 郵便番号から住所検索
        async function searchAddress() {
            const postalCode = document.getElementById('postalCode').value.replace(/-/g, '');

            if (postalCode.length !== 7) {
                alert('正しい郵便番号を入力してください（ハイフンなし7桁）');
                return;
            }

            try {
                const response = await fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${postalCode}`);
                const data = await response.json();

                if (data.results) {
                    const address = data.results[0];
                    document.getElementById('prefecture').value = address.address1;
                    document.getElementById('city').value = address.address2;
                    document.getElementById('addressLine1').value = address.address3;
                } else {
                    alert('住所が見つかりませんでした');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('住所検索中にエラーが発生しました');
            }
        }

        // パスワード表示/非表示切替
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;

            if (field.type === 'password') {
                field.type = 'text';
                button.textContent = '非表示';
            } else {
                field.type = 'password';
                button.textContent = '表示';
            }
        }

        // フォーム送信処理
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('errorMessage');
            errorDiv.style.display = 'none';

            // メールアドレス一致チェック
            const email = document.getElementById('email').value;
            const emailConfirm = document.getElementById('emailConfirm').value;

            if (email !== emailConfirm) {
                errorDiv.textContent = 'メールアドレスが一致しません';
                errorDiv.style.display = 'block';
                return;
            }

            // パスワード一致チェック
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('passwordConfirm').value;

            if (password !== passwordConfirm) {
                errorDiv.textContent = 'パスワードが一致しません';
                errorDiv.style.display = 'block';
                return;
            }

            // フォーム送信
            const formData = new FormData(this);
            const submitBtn = this.querySelector('.btn-submit');
            submitBtn.disabled = true;
            submitBtn.textContent = '登録中...';

            try {
                const response = await fetch('../api/signup_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('登録が完了しました！\n利用者コード: ' + result.data.user_code);
                    window.location.href = 'login.php';
                } else {
                    errorDiv.textContent = result.error || '登録に失敗しました';
                    errorDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = '利用規約に同意して登録する';
                }
            } catch (error) {
                console.error('Error:', error);
                errorDiv.textContent = '通信エラーが発生しました。もう一度お試しください。';
                errorDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = '利用規約に同意して登録する';
            }
        });
    </script>
</body>
</html>
