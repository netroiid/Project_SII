<!DOCTYPE html>
<html lang="id">

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <title>@yield('title', 'Blossom Avenue')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: #fafbfc;
        }

        .sidebar {
            background: #fff;
            min-height: 100vh;
        }

        .sidebar .active {
            background: #ffe8f3;
        }

        .badge-custom {
            background: #f04ea0;
            color: #fff;
            border-radius: 12px;
        }

        .badge-custom2 {
            background: #a768f7;
            color: #fff;
            border-radius: 12px;
        }

        .badge-red {
            background: #ea3c4f;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="pt-3 col-2 sidebar">
                <div class="mb-4 ps-3">
                    <h5 class="mb-1" style="color: #f04ea0;">Blossom Avenue</h5>
                    <span style="font-size:0.9em;color:#969;display:block;">Fresh Flower Management</span>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link @if(request()->is('/')) active @endif"
                            href="{{ route('dashboard') }}"><i class="ri-bar-chart-2-line"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->is('inventory')) active @endif"
                            href="{{ route('inventory.index') }}"><i class="ri-stack-line"></i> Inventory</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->is('productions*')) active @endif"
                            href="{{ route('productions.index') }}"><i class="ri-collage-line"></i> Produksi</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->is('pesanan*')) active @endif"
                            href="{{ route('pesanan.index') }}"><i class="ri-shopping-cart-2-line"></i> Pesanan</a></li>
                        <li class="nav-item"><a class="nav-link @if(request()->is('laporan*')) active @endif"
                            href="{{ route('laporan.index') }}"><i class="ri-file-chart-line"></i> Laporan</a>
                        </li>
                </ul>
            </nav>
            <main class="px-5 py-4 col-10">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
