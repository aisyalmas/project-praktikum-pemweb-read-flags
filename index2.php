<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>readflags - a home for every red flag</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #1a1614;
            --text-cream: #f9f6f0;
            --accent-pink: #e68eb0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-cream);
            /* Subtle background image overlay for the "moody" feel */
            background-image: linear-gradient(to bottom, rgba(26, 22, 20, 0.9), rgba(26, 22, 20, 0.95)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2580&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        /* Marquee Animation */
        .marquee-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            background-color: var(--text-cream);
            color: var(--bg-dark);
            padding: 0.75rem 0;
            font-family: 'Courier New', Courier, monospace; /* Typewriter feel */
            font-size: 0.9rem;
            font-weight: 600;
        }

        .marquee-content {
            display: inline-block;
            animation: marquee 25s linear infinite;
        }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); } /* Scrolls half the duplicated width */
        }
    </style>
</head>
<body class="antialiased flex flex-col justify-between">

    <!-- Navigation -->
    <header class="w-full p-4 md:px-8 md:py-6 flex justify-between items-center z-10">
        
        <!-- Logo -->
        <a href="#" class="bg-[#f9f6f0] text-[#1a1614] rounded-full px-5 py-2.5 flex items-center gap-3 font-bold text-xl hover:scale-105 transition-transform duration-200 shadow-lg">
            <!-- Custom pink icon with white flag cutout -->
            <svg width="24" height="24" viewBox="0 0 24 24" class="fill-[#e68eb0]">
                <rect width="24" height="24" rx="6" />
                <path d="M7 6h11v12H7l4-6-4-6z" fill="#ffffff" />
            </svg>
            readflags
        </a>

        <!-- Desktop Nav Links -->
        <nav class="hidden md:flex items-center gap-3">
            <a href="#" class="bg-[#f9f6f0] text-[#1a1614] px-6 py-2.5 rounded-full font-medium hover:bg-gray-200 transition-colors">shelves</a>
            <a href="#" class="bg-[#f9f6f0] text-[#1a1614] px-6 py-2.5 rounded-full font-medium hover:bg-gray-200 transition-colors">features</a>
            <a href="#" class="bg-[#f9f6f0] text-[#1a1614] px-6 py-2.5 rounded-full font-medium hover:bg-gray-200 transition-colors">pricing</a>
            
            <div class="w-px h-6 bg-white/20 mx-2"></div> <!-- Divider -->

            <a href="#" class="border border-white/40 text-[#f9f6f0] px-6 py-2.5 rounded-full font-medium hover:border-white transition-colors">log in</a>
            <a href="#" class="bg-[#e68eb0] text-[#1a1614] px-6 py-2.5 rounded-full font-medium hover:bg-[#d67da0] transition-colors">create account</a>
        </nav>

        <!-- Mobile Menu Button -->
        <button class="md:hidden text-[#f9f6f0] p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
    </header>

    <!-- Main Hero Section -->
    <main class="flex-grow flex flex-col justify-center px-6 md:px-12 lg:px-20 py-12 z-10 w-full max-w-[1600px] mx-auto">
        
        <!-- Top Badge -->
        <div class="inline-flex items-center gap-3 bg-[#1a1614]/80 backdrop-blur-md border border-white/10 rounded-full p-1.5 pr-5 mb-8 md:mb-12 w-fit cursor-pointer hover:bg-white/5 transition-colors shadow-xl">
            <span class="bg-[#e68eb0] text-[#1a1614] text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">New</span>
            <span class="text-sm font-medium text-[#f9f6f0]/90">a whole new look — take a peek &rarr;</span>
        </div>

        <!-- Giant Title & Icon -->
        <div class="flex items-center flex-wrap gap-4 md:gap-8 mb-16 md:mb-24">
            <h1 class="font-serif text-[5rem] sm:text-[8rem] md:text-[12rem] lg:text-[15rem] leading-[0.8] tracking-tighter text-[#f9f6f0]">
                readflags
            </h1>
            <!-- Large Pink Flag Icon -->
            <svg class="w-24 h-24 sm:w-32 sm:h-32 md:w-48 md:h-48 fill-[#e68eb0] flex-shrink-0 animate-pulse" viewBox="0 0 24 24">
                <path d="M4 2v20h2v-8h11.2c.5 0 .9-.3 1-.8.1-.4-.1-.9-.5-1.1l-3.3-2.6 3.3-2.6c.4-.2.6-.7.5-1.1-.1-.5-.5-.8-1-.8H6V2H4z"/>
            </svg>
        </div>

        <!-- Subtitle & Call to Action -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-10 w-full mt-auto">
            <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-sans font-medium max-w-2xl leading-[1.1] tracking-tight text-[#f9f6f0]">
                a home for every red flag<br>you spot, own and wish for
            </h2>
            
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <button class="bg-[#e68eb0] text-[#1a1614] px-8 py-4 rounded-full font-semibold text-lg hover:bg-[#d67da0] transition-transform hover:-translate-y-1 shadow-lg text-center">
                    start 7 days free
                </button>
                <button class="border border-[#f9f6f0]/60 text-[#f9f6f0] px-8 py-4 rounded-full font-semibold text-lg hover:border-[#f9f6f0] transition-colors hover:bg-white/5 text-center">
                    try the live demo
                </button>
            </div>
        </div>

    </main>

    <!-- Footer Marquee -->
    <footer class="mt-12 md:mt-24 z-10 w-full">
        <div class="marquee-container border-t border-b border-[#f9f6f0]">
            <div class="marquee-content">
                <!-- Text is duplicated twice to create a seamless infinite scrolling effect -->
                <span>• readflags (n.) warning sign, time for yourself • no pressure, dating should never feel like a chore • readflags (n.) warning sign, time for yourself • no pressure, dating should never feel like a chore </span>
                <span>• readflags (n.) warning sign, time for yourself • no pressure, dating should never feel like a chore • readflags (n.) warning sign, time for yourself • no pressure, dating should never feel like a chore </span>
            </div>
        </div>
    </footer>

</body>
</html>