<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>挨拶一覧</title>
</head>
<body>
    <h1>挨拶一覧</h1>

    <form action="{{ route('greeting.store') }}" method="POST">
        @csrf
        <input type="text" name="person" placeholder="名前" required>
        <input type="text" name="message" placeholder="メッセージ" required>
        <button type="submit">追加</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>メッセージ</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($greetings as $greeting)
            <tr>
                <td>{{ $greeting->id }}</td>
                <td>{{ $greeting->person }}</td>
                <td>{{ $greeting->message }}</td>
                <td>
                    <form action="{{ route('greeting.destroy', $greeting) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">削除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
