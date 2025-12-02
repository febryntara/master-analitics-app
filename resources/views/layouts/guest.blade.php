<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Guest Layout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        #network {
            position: fixed;
            inset: 0;
            z-index: -10;
        }
    </style>
</head>

<body class="min-h-screen text-white bg-transparent">

    <!-- Background Particle Canvas -->
    <canvas id="network"></canvas>

    <!-- Header Navigation -->
    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden absolute top-4 right-4 z-20">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="bg-white inline-block px-5 py-1.5 text-black border border-transparent hover:border-black rounded-sm text-sm leading-normal">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="bg-white inline-block px-5 py-1.5 text-black border border-transparent hover:border-black rounded-sm text-sm leading-normal">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="bg-white inline-block px-5 py-1.5 text-black border border-transparent hover:border-black rounded-sm text-sm leading-normal">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- Page Content -->
    <main class="flex justify-center items-center min-h-[80vh] px-4">
        {{ $slot }}
    </main>

    <!-- Particle Script -->
    <script>
        const CONFIG = {
            nodeCount: 150,
            maxNodeRadius: 2.5,
            minNodeRadius: 1.2,
            maxSpeed: 0.6,
            linkDistance: 140,
            linkWidth: 1,
            nodeColor: '200,220,255',
            linkColor: '180,200,255',
            backgroundColor: '10,14,30',
            repelOnMouse: true,
            mouseRepelRadius: 120,
            showNode: true,
            retinaScale: true
        };
        const canvas = document.getElementById('network');
        const ctx = canvas.getContext('2d');

        function resize() {
            const dpr = CONFIG.retinaScale && window.devicePixelRatio ? window.devicePixelRatio : 1;
            canvas.width = window.innerWidth * dpr;
            canvas.height = window.innerHeight * dpr;
            canvas.style.width = window.innerWidth + 'px';
            canvas.style.height = window.innerHeight + 'px';
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }
        window.addEventListener('resize', resize);
        resize();
        class Node {
            constructor(x, y, r, vx, vy) {
                this.x = x;
                this.y = y;
                this.r = r;
                this.vx = vx;
                this.vy = vy;
            }
            update(dt) {
                this.x += this.vx * dt;
                this.y += this.vy * dt;
                if (this.x < 0 || this.x > innerWidth) this.vx *= -1;
                if (this.y < 0 || this.y > innerHeight) this.vy *= -1;
            }
        }
        const nodes = [];

        function initNodes() {
            nodes.length = 0;
            for (let i = 0; i < CONFIG.nodeCount; i++) {
                const x = Math.random() * innerWidth;
                const y = Math.random() * innerHeight;
                const r = Math.random() * (CONFIG.maxNodeRadius - CONFIG.minNodeRadius) + CONFIG.minNodeRadius;
                const angle = Math.random() * Math.PI * 2;
                const speed = Math.random() * CONFIG.maxSpeed;
                nodes.push(new Node(x, y, r, Math.cos(angle) * speed, Math.sin(angle) * speed));
            }
        }
        initNodes();
        let last = performance.now();

        function animate(t) {
            const dt = (t - last) / 16.67;
            last = t;
            ctx.clearRect(0, 0, innerWidth, innerHeight);
            for (const a of nodes) {
                a.update(dt);
                ctx.beginPath();
                ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${CONFIG.nodeColor},0.9)`;
                ctx.fill();
                for (const b of nodes) {
                    if (a === b) continue;
                    const dx = a.x - b.x;
                    const dy = a.y - b.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < CONFIG.linkDistance) {
                        ctx.beginPath();
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.strokeStyle = `rgba(${CONFIG.linkColor},${1-dist/CONFIG.linkDistance})`;
                        ctx.lineWidth = CONFIG.linkWidth;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    </script>

</body>

</html>
