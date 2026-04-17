<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API デモ</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        h1 { border-bottom: 2px solid #333; padding-bottom: 0.5rem; }
        h2 { margin-top: 2rem; }
        input { padding: 0.4rem 0.6rem; margin-right: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 0.4rem 0.8rem; background: #3b82f6; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2563eb; }
        button.delete { background: #ef4444; }
        button.delete:hover { background: #dc2626; }
        button.edit { background: #f59e0b; }
        button.edit:hover { background: #d97706; }
        button.save { background: #10b981; }
        button.save:hover { background: #059669; }
        button.cancel { background: #6b7280; margin-left: 0.3rem; }
        button.cancel:hover { background: #4b5563; }
        td input { padding: 0.2rem 0.4rem; border: 1px solid #ccc; border-radius: 4px; width: 90%; }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: 0.5rem 1rem; text-align: left; }
        th { background: #f3f4f6; }
        #status { margin: 0.5rem 0; min-height: 1.5rem; color: #059669; font-size: 0.9rem; }
        #status.error { color: #dc2626; }
        .nav { margin-bottom: 1.5rem; font-size: 0.9rem; }
        .section { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; }
        .section h3 { margin: 0 0 0.75rem; font-size: 0.85rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        code { background: #e5e7eb; padding: 0.1rem 0.4rem; border-radius: 3px; font-size: 0.85rem; }
    </style>
</head>
<body>
    <h1>API デモ（fetch API）</h1>
    <div class="nav">
        <a href="/greeting">← Blade版（通常のWebページ）を見る</a>
    </div>

    <!-- API エンドポイント一覧 -->
    <div class="section">
        <h3>API エンドポイント</h3>
        <ul>
            <li><code>GET /api/greetings</code> — 一覧取得</li>
            <li><code>POST /api/greetings</code> — 新規作成（JSON ボディ）</li>
            <li><code>PUT /api/greetings/{id}</code> — 更新（JSON ボディ）</li>
            <li><code>DELETE /api/greetings/{id}</code> — 削除</li>
        </ul>
    </div>

    <!-- 新規作成フォーム -->
    <h2>新規追加（POST）</h2>
    <form id="create-form">
        <input type="text" id="person" placeholder="名前" required>
        <input type="text" id="message" placeholder="メッセージ" required>
        <button type="submit">追加</button>
    </form>
    <p id="status"></p>

    <!-- 一覧 -->
    <h2>一覧（GET）</h2>
    <button id="reload-btn">一覧を再取得</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>メッセージ</th>
                <th>作成日時</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="list">
            <tr><td colspan="5">読み込み中...</td></tr>
        </tbody>
    </table>

    <script>
        const API_BASE = '/api/greetings';

        // ステータス表示ヘルパー
        function setStatus(message, isError = false) {
            const el = document.getElementById('status');
            el.textContent = message;
            el.className = isError ? 'error' : '';
        }

        // ---- 一覧取得 (GET /api/greetings) ----
        async function loadGreetings() {
            const res = await fetch(API_BASE);          // GET リクエスト（デフォルト）
            const json = await res.json();              // レスポンス: { data: [...] }

            const tbody = document.getElementById('list');
            tbody.innerHTML = '';

            if (json.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">データがありません</td></tr>';
                return;
            }

            json.data.forEach(g => {
                const tr = document.createElement('tr');
                tr.id = `row-${g.id}`;
                tr.innerHTML = `
                    <td>${g.id}</td>
                    <td>${g.person}</td>
                    <td>${g.message}</td>
                    <td>${g.created_at}</td>
                    <td>
                        <button class="edit" onclick="startEdit(${g.id}, '${g.person}', '${g.message}')">編集</button>
                        <button class="delete" onclick="deleteGreeting(${g.id})">削除</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // ---- 新規作成 (POST /api/greetings) ----
        document.getElementById('create-form').addEventListener('submit', async (e) => {
            e.preventDefault();  // フォームのデフォルト送信（ページ遷移）を止める

            const body = {
                person:  document.getElementById('person').value,
                message: document.getElementById('message').value,
            };

            const res = await fetch(API_BASE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },  // JSON として送信
                body: JSON.stringify(body),
            });

            if (res.ok) {
                setStatus('追加しました！');
                e.target.reset();       // フォームをクリア
                await loadGreetings();  // 一覧を更新
            } else {
                const err = await res.json();
                // バリデーションエラー（422）の場合 err.errors にエラー詳細が入る
                setStatus('エラー: ' + JSON.stringify(err.errors), true);
            }
        });

        // ---- 編集モード開始 ----
        function startEdit(id, person, message) {
            const tr = document.getElementById(`row-${id}`);
            tr.innerHTML = `
                <td>${id}</td>
                <td><input type="text" id="edit-person-${id}" value="${person}"></td>
                <td><input type="text" id="edit-message-${id}" value="${message}"></td>
                <td colspan="2">
                    <button class="save" onclick="saveGreeting(${id})">保存</button>
                    <button class="cancel" onclick="loadGreetings()">キャンセル</button>
                </td>
            `;
        }

        // ---- 更新 (PUT /api/greetings/{id}) ----
        async function saveGreeting(id) {
            const body = {
                person:  document.getElementById(`edit-person-${id}`).value,
                message: document.getElementById(`edit-message-${id}`).value,
            };

            const res = await fetch(`${API_BASE}/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });

            if (res.ok) {
                setStatus(`ID:${id} を更新しました`);
                await loadGreetings();
            } else {
                const err = await res.json();
                setStatus('エラー: ' + JSON.stringify(err.errors), true);
            }
        }

        // ---- 削除 (DELETE /api/greetings/{id}) ----
        async function deleteGreeting(id) {
            const res = await fetch(`${API_BASE}/${id}`, {
                method: 'DELETE',
            });

            if (res.ok) {
                setStatus(`ID:${id} を削除しました`);
                await loadGreetings();
            } else {
                setStatus('削除に失敗しました', true);
            }
        }

        // ---- 再取得ボタン ----
        document.getElementById('reload-btn').addEventListener('click', loadGreetings);

        // ---- 初期ロード ----
        loadGreetings();
    </script>
</body>
</html>
