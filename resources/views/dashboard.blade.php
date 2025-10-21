<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='90'>📊</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">
                        <i class="fas fa-chart-line mr-2"></i>Dashboard
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">
                        <i class="fas fa-user-circle mr-2"></i>{{ Auth::user()->name ?? Auth::user()->email }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>Chiqish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Card 1 -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Foydalanuvchilar</p>
                        <h3 class="text-3xl font-bold text-gray-800">1,234</h3>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Buyurtmalar</p>
                        <h3 class="text-3xl font-bold text-gray-800">567</h3>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <i class="fas fa-shopping-cart text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Daromad</p>
                        <h3 class="text-3xl font-bold text-gray-800">$12,345</h3>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-4">
                        <i class="fas fa-dollar-sign text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Mahsulotlar</p>
                        <h3 class="text-3xl font-bold text-gray-800">89</h3>
                    </div>
                    <div class="bg-purple-100 rounded-full p-4">
                        <i class="fas fa-box text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Message -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Xush kelibsiz!</h2>
            <p class="text-gray-600 text-lg">Siz muvaffaqiyatli tizimga kirdingiz.</p>
        </div>
    </div>
</body>
</html>
