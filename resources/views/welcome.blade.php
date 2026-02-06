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
            overflow: hidden;
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
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-signin:hover { 
            transform: translateY(-3px);
            background: #1d4ed8;
        }

        .main-title {
            font-size: 3.8rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.0;
            margin-bottom: 0.5rem;
            color: white;
        }

        .logo-bounce {
            animation: mini-bounce 3s infinite ease-in-out;
        }
        @keyframes mini-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>

    <header class="fixed top-0 right-0 p-10 flex gap-4 z-50">
        <a href="{{ route('login') }}" class="px-6 py-2 border border-white/20 text-white rounded-xl text-sm font-medium hover:bg-white/10 transition">Log in</a>
        <a href="{{ route('register') }}" class="px-6 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-500 transition">Register</a>
    </header>

    <main class="auth-card">
        
        <div class="card-top px-4">
            <div class="flex justify-center mb-6 logo-bounce">
                <svg width="120" height="120" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="20" cy="20" r="20" fill="#2563EB" />
                    
                    <g transform="translate(4, 8)">
                        <path d="M7 16.5C7 17.3284 7.67157 18 8.5 18H10.5V15H8.5C7.67157 15 7 15.6716 7 16.5Z" fill="white"/>
                        <path d="M3 13.5V10.5C3 9.39543 3.89543 8.5 5 8.5H7L15 5V19L7 15.5H5C3.89543 15.5 3 14.6046 3 13.5Z" fill="#2563EB" stroke="white" stroke-width="1" stroke-linejoin="round"/>
                        <path d="M15 5C17.2091 5 19 8.13401 19 12C19 15.866 17.2091 19 15 19" fill="white" stroke="white" stroke-width="1"/>
                        
                        <path d="M20 9C20.8 10 21.2 11 21.2 12C21.2 13 20.8 14 20 15" stroke="white" stroke-width="1.2" stroke-linecap="round">
                            <animate attributeName="opacity" values="0.4;1;0.4" dur="2s" repeatCount="indefinite" />
                        </path>
                        <path d="M22.5 7.5C23.7 9 24.3 10.5 24.3 12C24.3 13.5 23.7 15 22.5 16.5" stroke="white" stroke-width="1.2" stroke-linecap="round">
                            <animate attributeName="opacity" values="0.3;1;0.3" dur="2s" begin="0.3s" repeatCount="indefinite" />
                        </path>
                        <path d="M25 6C26.5 8 27 10 27 12C27 14 26.5 16 25 18" stroke="white" stroke-width="1.2" stroke-linecap="round">
                            <animate attributeName="opacity" values="0.2;1;0.2" dur="2s" begin="0.6s" repeatCount="indefinite" />
                        </path>
                    </g>
                </svg>
            </div>

            <h1 class="main-title">Disaster Info</h1>
            <p class="text-white/60 text-lg font-medium tracking-wide">Sign in to stay informed</p>
        </div>

        <div class="card-bottom">
            <a href="{{ route('login') }}" class="btn-signin mb-12">
                Sign In
            </a>

            <div class="w-full h-[1px] bg-white/10 mb-10"></div>

            <div class="text-center mb-12">
                <a href="{{ route('register') }}" class="text-white text-lg font-bold hover:underline underline-offset-8">
                    Create Account
                </a>
            </div>

            <div class="text-center opacity-30">
                <p class="text-[9px] tracking-[0.4em] text-white uppercase leading-relaxed font-bold">
                    &copy; 2026 DISASTER INFO<br>SHARING SYSTEM
                </p>
            </div>
        </div>

    </main>

</body>
</html>