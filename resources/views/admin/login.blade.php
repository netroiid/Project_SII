<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/css/app.css">

    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            background: white;
            padding: 2rem 2.5rem;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn .6s ease-out;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        label {
            font-weight: 600;
            color: #555;
        }

        input {
            width: 100%;
            padding: .7rem;
            margin-top: .3rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: .2s;
        }

        input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 3px rgba(99, 102, 241, 0.4);
            outline: none;
        }

        button {
            width: 100%;
            padding: .8rem;
            background: #6366f1;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            font-size: 15px;
            transition: .2s;
        }

        button:hover {
            background: #4f46e5;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: .7rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="login-box">
        <h2>Admin Login</h2>

        @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>

</body>

</html>
