<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Expense Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .tech-icon {
            transition: all 0.3s ease;
        }
        .tech-icon:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="<?= isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php' ?>" class="flex items-center space-x-2">
                <i class="bi bi-arrow-left text-xl"></i>
                <span>Back</span>
            </a>
            <h1 class="text-xl font-bold">About Us</h1>
            <div class="w-8"><!-- Spacer --></div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-blue-600 text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Expense Tracker</h1>
            <p class="text-xl mb-6">Your personal finance management solution</p>
            <i class="bi bi-cash-stack text-5xl"></i>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-12">
        <!-- Team Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Meet the Team</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Team Member 1 -->
                <div class="team-card bg-white rounded-lg shadow-md p-6 text-center transition-all">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                        <img src="../assets/kenneth.jpg" 
                             alt="Kenneth Gumalog" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold mb-1">Kenneth Gumalog</h3>
                    <p class="text-blue-600 mb-3">BSIT 2L</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-github text-xl"></i></a>
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-linkedin text-xl"></i></a>
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-envelope text-xl"></i></a>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="team-card bg-white rounded-lg shadow-md p-6 text-center transition-all">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                        <img src="" 
                             alt="Ronald Joel Meim" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold mb-1">Ronald Joel Meim</h3>
                    <p class="text-blue-600 mb-3">BSIT 2L</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-github text-xl"></i></a>
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-linkedin text-xl"></i></a>
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-envelope text-xl"></i></a>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="team-card bg-white rounded-lg shadow-md p-6 text-center transition-all">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                        <img src="" 
                             alt="Einstein Morales" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold mb-1">Einstein Morales</h3>
                    <p class="text-blue-600 mb-3">BSIT 2L</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-github text-xl"></i></a>
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-linkedin text-xl"></i></a>
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i class="bi bi-envelope text-xl"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tech Stack Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Our Technology Stack</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="tech-icon flex flex-col items-center p-4">
                    <i class="bi bi-filetype-php text-5xl text-blue-600 mb-2"></i>
                    <p class="font-medium">PHP 8.1</p>
                </div>
                <div class="tech-icon flex flex-col items-center p-4">
                    <i class="bi bi-filetype-js text-5xl text-yellow-500 mb-2"></i>
                    <p class="font-medium">JavaScript</p>
                </div>
                <div class="tech-icon flex flex-col items-center p-4">
                    <i class="bi bi-filetype-html text-5xl text-orange-500 mb-2"></i>
                    <p class="font-medium">HTML 5</p>
                </div>
                <div class="tech-icon flex flex-col items-center p-4">
                    <i class="bi bi-bootstrap text-5xl text-purple-600 mb-2"></i>
                    <p class="font-medium">Bootstrap 5</p>
                </div>
            </div>
        </section>

        <!-- Project Info Section -->
        <section class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-3xl font-bold mb-6">About the Project</h2>
            <div class="space-y-4">
                <p>The Expense Tracker was developed to help individuals take control of their personal finances through simple yet powerful tools.</p>
                <p>Key features include:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Real-time expense tracking and categorization</li>
                    <li>Customizable budget management</li>
                    <li>Interactive visualizations of spending patterns</li>
                    <li>Secure data export capabilities</li>
                </ul>
                <p class="pt-4">Version: 1.0.0</p>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="mb-4">&amp;copy; 2023 Expense Tracker. All rights reserved.</p>
            <div class="flex justify-center space-x-6">
                <a href="#" class="hover:text-blue-400">Terms of Service</a>
                <a href="#" class="hover:text-blue-400">Privacy Policy</a>
                <a href="#" class="hover:text-blue-400">Contact Us</a>
            </div>
        </div>
    </footer>
</body>
</html>