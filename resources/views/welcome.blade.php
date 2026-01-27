<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Disaster Info</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)), 
                        url('https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
            font-family: 'Instrument Sans', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 440px;
            height: 720px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 4.5rem;
            box-shadow: 0 50px 100px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        /* パーツごとの位置固定 */
        .card-top { position: absolute; top: 70px; width: 100%; text-align: center; }
        .card-bottom { position: absolute; bottom: 60px; width: 100%; padding: 0 48px; }
        
        .btn-signin {
            display: block;
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 20px 0;
            border-radius: 20px;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-align: center;
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s;
        }
        .btn-signin:hover { transform: translateY(-2px); }

        /* タイトルの特別スタイル */
        .main-title {
            font-size: 3.8rem; /* サイズを大幅アップ */
            font-weight: 800;
            letter-spacing: -0.04em; /* 文字間を詰めて密度を上げる */
            line-height: 1.0;
            margin-bottom: 1rem;
            color: white;
        }
    </style>
</head>
<body>

    <header class="fixed top-0 right-0 p-10 flex gap-4">
        <a href="{{ route('login') }}" class="px-6 py-2 border border-white/20 text-white rounded-xl text-sm font-medium hover:bg-white/10 transition">Log in</a>
        <a href="{{ route('register') }}" class="px-6 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-500 transition">Register</a>
    </header>

    <main class="auth-card">
        
        <div class="card-top px-4">
            <div class="flex justify-center mb-6">
                <div class="bg-blue-600 p-6 rounded-[2.2rem] shadow-2xl">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="main-title">Disaster Info</h1>
            <p class="text-white/50 text-lg font-medium tracking-wide">Sign in to stay informed</p>
        </div>

        <div class="card-bottom">
            <a href="{{ url('/login') }}" class="btn-signin mb-12">
                Sign In
            </a>

            <div class="w-full h-[1px] bg-white/10 mb-10"></div>

            <div class="text-center mb-12">
                <a href="{{ route('register') }}" class="text-white text-lg font-bold hover:underline">
                    Create Account
                </a>
            </div>

            <div class="text-center opacity-20">
                <p class="text-[9px] tracking-[0.4em] text-white uppercase leading-relaxed">
                    &copy; 2026 DISASTER INFO<br>SHARING SYSTEM
                </p>
            </div>
        </div>

    </main>

</body>
</html>