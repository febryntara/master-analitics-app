<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html,
        body {
            height: 100%;
            margin: 0
        }

        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: -1
        }
    </style>
</head>

<body class="h-full text-white bg-transparent">

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
    <canvas id="network" class="bg-canvas"></canvas>

    <div class="relative z-10 flex flex-col items-center justify-center min-h-screen px-6">
        <div class="p-10 text-black bg-white border shadow-xl backdrop-blur-md rounded-2xl">
            <h1 class="mb-4 text-4xl font-bold text-center">Master Analitics</h1>
            <p class="max-w-xl mb-8 text-center text-md">Discover what people really think.</p>
        </div>
    </div>

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
            const dpr = (CONFIG.retinaScale && window.devicePixelRatio) ? window.devicePixelRatio : 1;
            canvas.width = Math.floor(window.innerWidth * dpr);
            canvas.height = Math.floor(window.innerHeight * dpr);
            canvas.style.width = window.innerWidth + 'px';
            canvas.style.height = window.innerHeight + 'px';
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }
        window.addEventListener('resize', resize, {
            passive: true
        });
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
                if (this.x < 0 || this.x > innerWidth) {
                    this.vx *= -1;
                    this.x = Math.max(0, Math.min(innerWidth, this.x));
                }
                if (this.y < 0 || this.y > innerHeight) {
                    this.vy *= -1;
                    this.y = Math.max(0, Math.min(innerHeight, this.y));
                }
            }
        }
        const nodes = [];

        function random(min, max) {
            return Math.random() * (max - min) + min
        }

        function initNodes() {
            nodes.length = 0;
            for (let i = 0; i < CONFIG.nodeCount; i++) {
                const x = Math.random() * innerWidth;
                const y = Math.random() * innerHeight;
                const r = random(CONFIG.minNodeRadius, CONFIG.maxNodeRadius);
                const angle = Math.random() * Math.PI * 2;
                const speed = random(0.05, CONFIG.maxSpeed);
                const vx = Math.cos(angle) * speed;
                const vy = Math.sin(angle) * speed;
                nodes.push(new Node(x, y, r, vx, vy));
            }
        }
        initNodes();
        const mouse = {
            x: -9999,
            y: -9999,
            active: false
        };
        window.addEventListener('mousemove', e => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
            mouse.active = true;
        });
        window.addEventListener('mouseleave', () => {
            mouse.active = false;
            mouse.x = -9999;
            mouse.y = -9999
        });
        let last = performance.now();

        function animate(t) {
            const dt = Math.min(40, t - last) / 16.6667;
            last = t;
            ctx.clearRect(0, 0, innerWidth, innerHeight);
            const g = ctx.createLinearGradient(0, 0, innerWidth, innerHeight);
            // g.addColorStop(0, `rgba(${CONFIG.backgroundColor},0.06)`);
            // g.addColorStop(1, `rgba(${CONFIG.backgroundColor},0.02)`);
            ctx.fillStyle = g;
            ctx.fillRect(0, 0, innerWidth, innerHeight);
            for (const n of nodes) {
                if (CONFIG.repelOnMouse && mouse.active) {
                    const dx = n.x - mouse.x;
                    const dy = n.y - mouse.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < CONFIG.mouseRepelRadius && dist > 0.1) {
                        const force = (CONFIG.mouseRepelRadius - dist) / CONFIG.mouseRepelRadius;
                        const push = 0.8 * force;
                        n.vx += (dx / dist) * push * dt;
                        n.vy += (dy / dist) * push * dt;
                    }
                }
                n.vx += (Math.random() - 0.5) * 0.02 * dt;
                n.vy += (Math.random() - 0.5) * 0.02 * dt;
                const sp = Math.sqrt(n.vx * n.vx + n.vy * n.vy);
                if (sp > CONFIG.maxSpeed) {
                    n.vx = (n.vx / sp) * CONFIG.maxSpeed;
                    n.vy = (n.vy / sp) * CONFIG.maxSpeed;
                }
                n.update(dt);
            }
            ctx.lineWidth = CONFIG.linkWidth;
            for (let i = 0; i < nodes.length; i++) {
                const a = nodes[i];
                if (CONFIG.showNode) {
                    ctx.beginPath();
                    ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(${CONFIG.nodeColor},0.9)`;
                    ctx.fill();
                }
                for (let j = i + 1; j < nodes.length; j++) {
                    const b = nodes[j];
                    const dx = a.x - b.x;
                    const dy = a.y - b.y;
                    const dist2 = dx * dx + dy * dy;
                    const maxD = CONFIG.linkDistance;
                    if (dist2 <= maxD * maxD) {
                        const dist = Math.sqrt(dist2);
                        const alpha = 1 - (dist / maxD);
                        ctx.beginPath();
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.strokeStyle = `rgba(${CONFIG.linkColor},${Math.max(0,alpha*0.85)})`;
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
