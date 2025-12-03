<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>body{font-family:Segoe UI,Arial,sans-serif;padding:2rem}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ddd;padding:8px}</style>
</head>
<body>
    <h2>Admin Dashboard - Inventory</h2>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <h3 style="margin-top:1rem">Flowers Inventory</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Stock Now</th>
                <th>Total Used</th>
                <th>Price</th>
                <th>Expired At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($flowers as $f)
                <tr>
                    <td>{{ $f->id }}</td>
                    <td>{{ $f->name }}</td>
                    <td>{{ $f->kategori }}</td>
                    <td>{{ $f->stock_now }}</td>
                    <td>{{ $f->total_used }}</td>
                    <td>{{ number_format($f->price_per_unit,0,',','.') }}</td>
                    <td>{{ $f->expired_at?->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
