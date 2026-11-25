(function () {
    'use strict';
    const canvas = document.getElementById('three-canvas');
    if (!canvas || typeof THREE === 'undefined') return;

    // ==========================================
    // KOMPATIBILITAS VERSI & PERANGKAT
    // ==========================================
    const REV = parseInt(THREE.REVISION, 10);
    const PHYSICAL = REV >= 155; // r155+ memakai satuan cahaya fisik
    const LI = (v) => (PHYSICAL ? v * Math.PI : v);
    const reduceMotion = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    const isMobile = window.innerWidth < 768;
    const speedScale = reduceMotion ? 0.25 : 1;

    // ==========================================
    // HELPER
    // ==========================================
    function makeCanvas(w, h) {
        const c = document.createElement('canvas');
        c.width = w; c.height = h;
        return [c, c.getContext('2d')];
    }
    function srgb(tex) {
        if ('colorSpace' in tex) tex.colorSpace = THREE.SRGBColorSpace;
        else tex.encoding = THREE.sRGBEncoding;
        return tex;
    }
    function rrect(g, x, y, w, h, r) {
        g.beginPath();
        g.moveTo(x + r, y);
        g.arcTo(x + w, y, x + w, y + h, r);
        g.arcTo(x + w, y + h, x, y + h, r);
        g.arcTo(x, y + h, x, y, r);
        g.arcTo(x, y, x + w, y, r);
        g.closePath();
    }
    const smooth = (x) => { x = Math.min(1, Math.max(0, x)); return x * x * (3 - 2 * x); };

    // ==========================================
    // SCENE, CAMERA, RENDERER
    // ==========================================
    const FOG_COLOR = 0xb0bfcf;
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(FOG_COLOR);
    scene.fog = new THREE.Fog(FOG_COLOR, 22, 58);

    const camera = new THREE.PerspectiveCamera(42, window.innerWidth / window.innerHeight, 0.1, 140);
    const camBase = { x: 5.0, y: 5.8, z: 14.0 };
    const lookBase = new THREE.Vector3(-1.5, 1.5, -1.5);

    const renderer = new THREE.WebGLRenderer({
        canvas, antialias: !isMobile, powerPreference: 'high-performance'
    });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, isMobile ? 1.5 : 2));
    if ('outputColorSpace' in renderer) renderer.outputColorSpace = THREE.SRGBColorSpace;
    else renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 0.72;
    renderer.shadowMap.enabled = !isMobile;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    function applyAspect() {
        const a = window.innerWidth / window.innerHeight;
        camera.aspect = a;
        if (a < 0.8) {
            // Khusus Mobile Portrait: Mundur dan naik sedikit agar seluruh area manufaktur terlihat jelas & terbingkai indah
            camera.fov = 56;
            camBase.x = 0.5;
            camBase.y = 5.2;
            camBase.z = 21.0;
            lookBase.set(-2.0, 1.8, -1.0);
        } else if (a < 1.2) {
            // Tablet
            camera.fov = 48;
            camBase.x = 3.0;
            camBase.y = 5.5;
            camBase.z = 16.0;
            lookBase.set(-1.5, 1.5, -1.5);
        } else {
            // Desktop (TETAP SAMA, TIDAK DIUBAH)
            camera.fov = 42;
            camBase.x = 5.0;
            camBase.y = 5.8;
            camBase.z = 14.0;
            lookBase.set(-1.5, 1.5, -1.5);
        }
        camera.updateProjectionMatrix();
    }
    applyAspect();

    // ==========================================
    // ENVIRONMENT MAP (pantulan pada logam & lantai)
    // ==========================================
    const pmrem = new THREE.PMREMGenerator(renderer);
    const envScene = new THREE.Scene();
    envScene.background = new THREE.Color(0xbdd0e5);
    envScene.add(new THREE.Mesh(new THREE.SphereGeometry(40, 16, 16),
        new THREE.MeshBasicMaterial({ color: 0xcfdbe8, side: THREE.BackSide })));
    const envFloor = new THREE.Mesh(new THREE.PlaneGeometry(80, 80), new THREE.MeshBasicMaterial({ color: 0x5f9a7e }));
    envFloor.rotation.x = -Math.PI / 2;
    envScene.add(envFloor);
    // Strip lampu panjang supaya logam punya highlight yang jelas
    for (let i = -3; i <= 3; i++) {
        const strip = new THREE.Mesh(new THREE.PlaneGeometry(2.2, 26), new THREE.MeshBasicMaterial({ color: 0xffffff }));
        strip.rotation.x = Math.PI / 2;
        strip.position.set(i * 8, 22, 0);
        envScene.add(strip);
    }
    const winPanel = new THREE.Mesh(new THREE.PlaneGeometry(40, 10), new THREE.MeshBasicMaterial({ color: 0xffffff }));
    winPanel.position.set(0, 12, -30);
    envScene.add(winPanel);
    const envTex = pmrem.fromScene(envScene, 0.04).texture;
    pmrem.dispose();

    // ==========================================
    // PENCAHAYAAN (lembut, tidak menyilaukan mata)
    // ==========================================
    scene.add(new THREE.HemisphereLight(0xc8d6e5, 0x3d6b52, LI(0.32)));
    scene.add(new THREE.AmbientLight(0xc5d0dc, LI(0.12)));

    const sun = new THREE.DirectionalLight(0xfff5ea, LI(0.68));
    sun.position.set(-18, 22, 14);
    sun.target.position.set(-8, 0, -1);
    scene.add(sun, sun.target);
    if (!isMobile) {
        sun.castShadow = true;
        sun.shadow.mapSize.set(2048, 2048);
        const sc = sun.shadow.camera;
        sc.left = -24; sc.right = 24; sc.top = 20; sc.bottom = -20;
        sc.near = 1; sc.far = 80;
        sun.shadow.bias = -0.0004;
        sun.shadow.normalBias = 0.03;
        if ('radius' in sun.shadow) sun.shadow.radius = 3;
    }

    // ==========================================
    // TEKSTUR PROSEDURAL
    // ==========================================
    const glowTex = (() => {
        const [c, g] = makeCanvas(128, 128);
        const gr = g.createRadialGradient(64, 64, 0, 64, 64, 64);
        gr.addColorStop(0, 'rgba(255,255,255,1)');
        gr.addColorStop(0.25, 'rgba(255,255,255,0.55)');
        gr.addColorStop(1, 'rgba(255,255,255,0)');
        g.fillStyle = gr; g.fillRect(0, 0, 128, 128);
        return srgb(new THREE.CanvasTexture(c));
    })();

    // Gradien vertikal untuk berkas cahaya (atas terang, bawah hilang)
    const beamTex = (() => {
        const [c, g] = makeCanvas(64, 256);
        const v = g.createLinearGradient(0, 0, 0, 256);
        v.addColorStop(0, 'rgba(255,255,255,0.9)');
        v.addColorStop(0.5, 'rgba(255,255,255,0.25)');
        v.addColorStop(1, 'rgba(255,255,255,0)');
        g.fillStyle = v; g.fillRect(0, 0, 64, 256);
        g.globalCompositeOperation = 'destination-in';
        const h = g.createLinearGradient(0, 0, 64, 0);
        h.addColorStop(0, 'rgba(0,0,0,0)');
        h.addColorStop(0.5, 'rgba(0,0,0,1)');
        h.addColorStop(1, 'rgba(0,0,0,0)');
        g.fillStyle = h; g.fillRect(0, 0, 64, 256);
        return new THREE.CanvasTexture(c);
    })();

    // Lantai epoxy: bintik halus + noda lembut
    const floorTex = (() => {
        const S = 512;
        const [c, g] = makeCanvas(S, S);
        g.fillStyle = '#4d8b6a'; g.fillRect(0, 0, S, S);
        for (let i = 0; i < 40; i++) {
            const x = 60 + Math.random() * (S - 120), y = 60 + Math.random() * (S - 120), r = 30 + Math.random() * 50;
            const gr = g.createRadialGradient(x, y, 0, x, y, r);
            const light = Math.random() > 0.5;
            gr.addColorStop(0, light ? 'rgba(255,255,255,0.05)' : 'rgba(0,20,10,0.06)');
            gr.addColorStop(1, 'rgba(0,0,0,0)');
            g.fillStyle = gr; g.fillRect(x - r, y - r, r * 2, r * 2);
        }
        const img = g.getImageData(0, 0, S, S);
        for (let i = 0; i < img.data.length; i += 4) {
            const n = (Math.random() - 0.5) * 16;
            img.data[i] += n; img.data[i + 1] += n; img.data[i + 2] += n;
        }
        g.putImageData(img, 0, 0);
        const t = srgb(new THREE.CanvasTexture(c));
        t.wrapS = t.wrapT = THREE.RepeatWrapping;
        t.repeat.set(22, 22);
        t.anisotropy = renderer.capabilities.getMaxAnisotropy();
        return t;
    })();

    // Dinding metal bergelombang
    function corrugatedTex(repeatX) {
        const [c, g] = makeCanvas(64, 64);
        for (let i = 0; i < 4; i++) {
            const gr = g.createLinearGradient(i * 16, 0, i * 16 + 16, 0);
            gr.addColorStop(0, '#c4cfda'); gr.addColorStop(0.5, '#b2bfcc'); gr.addColorStop(1, '#9fadbb');
            g.fillStyle = gr; g.fillRect(i * 16, 0, 16, 64);
        }
        const t = srgb(new THREE.CanvasTexture(c));
        t.wrapS = t.wrapT = THREE.RepeatWrapping;
        t.repeat.set(repeatX, 1);
        return t;
    }

    // Sabuk conveyor
    const beltTex = (() => {
        const [c, g] = makeCanvas(64, 64);
        g.fillStyle = '#2c2c2f'; g.fillRect(0, 0, 64, 64);
        g.fillStyle = '#3f3f43';
        g.fillRect(0, 0, 6, 64);
        g.fillStyle = '#1f1f22';
        g.fillRect(6, 0, 3, 64);
        const t = srgb(new THREE.CanvasTexture(c));
        t.wrapS = t.wrapT = THREE.RepeatWrapping;
        t.repeat.set(19.2, 1);
        return t;
    })();

    // Garis bahaya kuning-hitam
    const hazardTex = (() => {
        const [c, g] = makeCanvas(128, 32);
        g.fillStyle = '#f5c518'; g.fillRect(0, 0, 128, 32);
        g.fillStyle = '#1a1a1a';
        for (let i = -32; i < 160; i += 22) {
            g.beginPath(); g.moveTo(i, 32); g.lineTo(i + 11, 32);
            g.lineTo(i + 25, 0); g.lineTo(i + 14, 0); g.closePath(); g.fill();
        }
        const t = srgb(new THREE.CanvasTexture(c));
        t.wrapS = THREE.RepeatWrapping; t.repeat.set(3, 1);
        return t;
    })();

    function signTexture(text, sub, bg) {
        const [c, g] = makeCanvas(512, 176);
        g.fillStyle = '#ffffff'; rrect(g, 0, 0, 512, 176, 22); g.fill();
        g.fillStyle = bg; rrect(g, 10, 10, 492, 156, 16); g.fill();
        g.fillStyle = '#ffffff';
        g.font = 'bold 62px sans-serif'; g.textAlign = 'center';
        g.fillText(text, 256, 92);
        g.font = '600 26px sans-serif';
        g.fillText(sub, 256, 136);
        return srgb(new THREE.CanvasTexture(c));
    }

    // ==========================================
    // MATERIAL (semua memakai envMap yang sama)
    // ==========================================
    const M = (color, o) => new THREE.MeshStandardMaterial(Object.assign(
        { color, roughness: 0.5, metalness: 0.0, envMap: envTex, envMapIntensity: 0.5 }, o || {}));

    const safetyYellow = M(0xf5c518, { roughness: 0.3 });
    const steelBlue = M(0x1a56db, { metalness: 0.6, roughness: 0.32, envMapIntensity: 0.9 });
    const craneYellow = M(0xd4a30b, { metalness: 0.45, roughness: 0.35, envMapIntensity: 0.8 });
    const pressCream = M(0xeae0cc, { roughness: 0.45, metalness: 0.1 });
    const darkMetal = M(0x2d3a4a, { metalness: 0.75, roughness: 0.28, envMapIntensity: 1.0 });
    const robotOrange = M(0xea6c20, { metalness: 0.45, roughness: 0.28, envMapIntensity: 0.9 });
    const carBodyMat = M(0x9ca3af, { metalness: 0.88, roughness: 0.22, envMapIntensity: 1.2 });
    const carTrimMat = M(0x6b7280, { metalness: 0.82, roughness: 0.28, envMapIntensity: 1.0 });
    const safetyOrange = M(0xf08030, { roughness: 0.55 });
    const shirtBlue = M(0x2060c0, { roughness: 0.7, envMapIntensity: 0.15 });
    const whiteUniform = M(0xf0f0f0, { roughness: 0.65, envMapIntensity: 0.15 });
    const pantsDark = M(0x1c2838, { roughness: 0.8, envMapIntensity: 0.1 });
    const skinMat = M(0xe8b87a, { roughness: 0.55, envMapIntensity: 0.15 });
    const wallLowerMat = M(0x5a6a7a, { roughness: 0.7, envMapIntensity: 0.25 });
    const concreteMat = M(0x8a9aaa, { roughness: 0.9, envMapIntensity: 0.1 });
    const ledGreen = new THREE.MeshBasicMaterial({ color: 0x22c55e, toneMapped: false });

    const floorMat = new THREE.MeshPhysicalMaterial({
        map: floorTex, roughness: 0.34, metalness: 0.0,
        clearcoat: 0.7, clearcoatRoughness: 0.25,
        envMap: envTex, envMapIntensity: 0.9
    });

    function enableShadows(obj) {
        obj.traverse((child) => {
            if (child.isMesh) { child.castShadow = true; child.receiveShadow = true; }
        });
    }

    function glowSprite(x, y, z, size, color, opacity) {
        const s = new THREE.Sprite(new THREE.SpriteMaterial({
            map: glowTex, color, transparent: true, opacity,
            blending: THREE.AdditiveBlending, depthWrite: false, fog: false, toneMapped: false
        }));
        s.position.set(x, y, z);
        s.scale.set(size, size, 1);
        return s;
    }

    // ==========================================
    // 1. LANTAI & MARKA KESELAMATAN
    // ==========================================
    const floor = new THREE.Mesh(new THREE.PlaneGeometry(90, 90), floorMat);
    floor.rotation.x = -Math.PI / 2;
    floor.receiveShadow = true;
    scene.add(floor);

    const grid = new THREE.GridHelper(60, 56, 0x3e7258, 0x447860);
    grid.position.y = 0.01;
    grid.material.opacity = 0.28;
    grid.material.transparent = true;
    scene.add(grid);

    const yellowLineMat = new THREE.MeshBasicMaterial({ color: 0xf5c518 });
    [-0.4, 2.3, 4.6].forEach((zPos) => {
        const line = new THREE.Mesh(new THREE.PlaneGeometry(38, 0.12), yellowLineMat);
        line.rotation.x = -Math.PI / 2;
        line.position.set(-3, 0.012, zPos);
        scene.add(line);
    });

    const hazardMat = new THREE.MeshStandardMaterial({ map: hazardTex, roughness: 0.4 });
    const hazardPlaneMat = new THREE.MeshBasicMaterial({ map: hazardTex });

    // ==========================================
    // 2. STRUKTUR PABRIK
    // ==========================================
    const structureGroup = new THREE.Group();
    const noShadowMeshes = [];

    const bwLower = new THREE.Mesh(new THREE.BoxGeometry(44, 2.6, 0.35), wallLowerMat);
    bwLower.position.set(0, 1.3, -7.6);
    const bwUpper = new THREE.Mesh(new THREE.BoxGeometry(44, 5.8, 0.3), M(0xffffff, { map: corrugatedTex(110), roughness: 0.75, envMapIntensity: 0.15 }));
    bwUpper.position.set(0, 5.5, -7.6);
    structureGroup.add(bwLower, bwUpper);

    const swLeft = new THREE.Mesh(new THREE.BoxGeometry(0.3, 8.5, 20), M(0xffffff, { map: corrugatedTex(50), roughness: 0.75, envMapIntensity: 0.15 }));
    swLeft.position.set(-18, 4.25, -1.0);
    structureGroup.add(swLeft);

    for (let x = -15; x <= 10; x += 5) {
        const col = new THREE.Mesh(new THREE.BoxGeometry(0.32, 7.6, 0.32), steelBlue);
        col.position.set(x, 3.8, -5.5);
        const flange1 = new THREE.Mesh(new THREE.BoxGeometry(0.52, 7.6, 0.06), steelBlue);
        flange1.position.set(x, 3.8, -5.35);
        const flange2 = flange1.clone(); flange2.position.z = -5.65;
        // Pelat dasar kolom
        const plate = new THREE.Mesh(new THREE.BoxGeometry(0.7, 0.08, 0.7), darkMetal);
        plate.position.set(x, 0.04, -5.5);
        structureGroup.add(col, flange1, flange2, plate);

        const truss = new THREE.Mesh(new THREE.BoxGeometry(0.28, 0.5, 18), steelBlue);
        truss.position.set(x, 7.5, -0.5);
        structureGroup.add(truss);
        noShadowMeshes.push(truss);

        const diagGeo = new THREE.CylinderGeometry(0.04, 0.04, 2.8, 6);
        const diag1 = new THREE.Mesh(diagGeo, steelBlue);
        diag1.position.set(x, 6.5, -3.5); diag1.rotation.z = 0.65;
        const diag2 = new THREE.Mesh(diagGeo, steelBlue);
        diag2.position.set(x, 6.5, 2.0); diag2.rotation.z = -0.65;
        structureGroup.add(diag1, diag2);
        noShadowMeshes.push(diag1, diag2);

        // Lampu gantung pabrik (cahaya lembut, tanpa kerucut sorot yang menyilaukan)
        [-2.5, 1.5].forEach((lz) => {
            const cord = new THREE.Mesh(new THREE.CylinderGeometry(0.015, 0.015, 1.1, 6), darkMetal);
            cord.position.set(x, 6.75, lz);
            const shade = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.42, 0.3, 16, 1, true),
                M(0xd0d8e0, { metalness: 0.5, roughness: 0.3, side: THREE.DoubleSide }));
            shade.position.set(x, 6.1, lz);
            const bulb = new THREE.Mesh(new THREE.SphereGeometry(0.11, 10, 10),
                new THREE.MeshStandardMaterial({ color: 0xffeed0, roughness: 0.4 }));
            bulb.position.set(x, 6.04, lz);
            structureGroup.add(cord, shade, bulb);
            noShadowMeshes.push(cord, shade, bulb);
        });
    }

    // Rel crane
    const craneRailL = new THREE.Mesh(new THREE.BoxGeometry(32, 0.4, 0.3), craneYellow);
    craneRailL.position.set(-2, 5.6, -4.2);
    const craneRailR = craneRailL.clone(); craneRailR.position.z = -5.0;
    structureGroup.add(craneRailL, craneRailR);

    const craneTrolley = new THREE.Group();
    const trolleyBody = new THREE.Mesh(new THREE.BoxGeometry(1.6, 0.35, 1.3), craneYellow);
    const hookCord = new THREE.Mesh(new THREE.CylinderGeometry(0.03, 0.03, 2.5, 6), darkMetal);
    hookCord.position.y = -1.4;
    const hook = new THREE.Mesh(new THREE.TorusGeometry(0.15, 0.04, 8, 12, Math.PI), darkMetal);
    hook.position.y = -2.7;
    craneTrolley.add(trolleyBody, hookCord, hook);
    craneTrolley.position.set(-2, 5.35, -4.6);
    structureGroup.add(craneTrolley);

    // Jendela pabrik (tenang & tidak berkedip)
    for (let x = -13; x <= 8; x += 5) {
        const wx = x + 2.5;
        const winFrame = new THREE.Mesh(new THREE.BoxGeometry(3.4, 1.6, 0.12), darkMetal);
        winFrame.position.set(wx, 6.5, -7.38);
        const winGlass = new THREE.Mesh(new THREE.PlaneGeometry(3.1, 1.3),
            new THREE.MeshBasicMaterial({ color: 0xd9e5f3, depthWrite: true, toneMapped: false }));
        winGlass.position.set(wx, 6.5, -7.30);
        structureGroup.add(winFrame, winGlass);
        noShadowMeshes.push(winGlass);
    }

    // Spanduk / Banner Kain Logo Summit Adyawinsa (diikat tali ke tiang/dinding)
    const logoLoader = new THREE.TextureLoader();
    const logoUrl = window.SAI_LOGO_URL || '/image/sai_logo.png';
    logoLoader.load(logoUrl, (tex) => {
        if ('colorSpace' in tex) tex.colorSpace = THREE.SRGBColorSpace;
        else tex.encoding = THREE.sRGBEncoding;

        const bannerGroup = new THREE.Group();
        bannerGroup.rotation.y = Math.PI / 2;
        bannerGroup.position.set(-17.76, 5.65, -2.5);

        const bw = 3.6, bh = 1.35;

        // Kain Spanduk / Banner (putih bersih, tidak teredup bayangan/pencahayaan)
        const clothGeo = new THREE.PlaneGeometry(bw, bh);
        const clothMat = new THREE.MeshBasicMaterial({
            color: 0xffffff,
            side: THREE.DoubleSide,
            toneMapped: false
        });
        const cloth = new THREE.Mesh(clothGeo, clothMat);
        bannerGroup.add(cloth);

        // Logo tercetak pada kain banner
        const printMat = new THREE.MeshBasicMaterial({
            map: tex,
            transparent: true,
            opacity: 0.96,
            depthWrite: false,
            toneMapped: false
        });
        const printPlane = new THREE.Mesh(new THREE.PlaneGeometry(bw * 0.82, bh * 0.82), printMat);
        printPlane.position.z = 0.008;
        bannerGroup.add(printPlane);

        // Ring Mata Ayam / Grommet Besi di 4 sudut dan tengah
        const grommetMat = M(0x94a3b8, { metalness: 0.85, roughness: 0.25 });
        const grommetGeo = new THREE.TorusGeometry(0.045, 0.018, 8, 16);
        const ropeMat = M(0xd97706, { roughness: 0.85 }); // Tali tambang pengikat oranye/cokelat
        const cornerCoords = [
            [-bw / 2 + 0.08, bh / 2 - 0.08, -0.35, 0.35],
            [bw / 2 - 0.08, bh / 2 - 0.08, 0.35, 0.35],
            [-bw / 2 + 0.08, -bh / 2 + 0.08, -0.35, -0.35],
            [bw / 2 - 0.08, -bh / 2 + 0.08, 0.35, -0.35]
        ];

        cornerCoords.forEach(([gx, gy, rx, ry]) => {
            const gr = new THREE.Mesh(grommetGeo, grommetMat);
            gr.position.set(gx, gy, 0.009);
            bannerGroup.add(gr);

            // Tali pengikat ditarik serong ke dinding
            const ropeGeo = new THREE.CylinderGeometry(0.012, 0.012, 0.52, 6);
            const rope = new THREE.Mesh(ropeGeo, ropeMat);
            rope.position.set(gx + rx * 0.5, gy + ry * 0.5, 0.005);
            rope.rotation.z = Math.atan2(ry, rx) - Math.PI / 2;
            bannerGroup.add(rope);

            // Baut / Paku pengait tali di dinding
            const bolt = new THREE.Mesh(new THREE.SphereGeometry(0.035, 8, 8), darkMetal);
            bolt.position.set(gx + rx, gy + ry, -0.01);
            bannerGroup.add(bolt);
        });

        structureGroup.add(bannerGroup);
    });

    // Pipa di dinding belakang
    const pipeA = new THREE.Mesh(new THREE.CylinderGeometry(0.09, 0.09, 40, 12), M(0x0ea5e9, { metalness: 0.6, roughness: 0.3, envMapIntensity: 0.9 }));
    pipeA.rotation.z = Math.PI / 2; pipeA.position.set(0, 3.45, -7.3);
    const pipeB = new THREE.Mesh(new THREE.CylinderGeometry(0.06, 0.06, 40, 10), craneYellow);
    pipeB.rotation.z = Math.PI / 2; pipeB.position.set(0, 3.15, -7.3);
    structureGroup.add(pipeA, pipeB);
    for (let x = -18; x <= 18; x += 3) {
        const br = new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.5, 0.12), darkMetal);
        br.position.set(x, 3.3, -7.38);
        structureGroup.add(br);
    }

    // Kabinet kontrol
    [-15, -13.9, 4.6, 5.7].forEach((cx, i) => {
        const cab = new THREE.Mesh(new THREE.BoxGeometry(1.0, 2.1, 0.55), M(0xcfd6de, { roughness: 0.5, metalness: 0.3 }));
        cab.position.set(cx, 1.05, -7.1);
        const panel = new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.9, 0.02), darkMetal);
        panel.position.set(cx, 1.3, -6.81);
        const led = new THREE.Mesh(new THREE.SphereGeometry(0.035, 8, 8),
            new THREE.MeshBasicMaterial({ color: i % 2 ? 0x22c55e : 0xf5c518, toneMapped: false }));
        led.position.set(cx + 0.3, 1.65, -6.8);
        structureGroup.add(cab, panel, led);
    });

    // Tanda keselamatan ditempel di dinding seng bergelombang sebelah kiri (swLeft) + APAR
    const signA = new THREE.Mesh(new THREE.PlaneGeometry(3.6, 1.25),
        new THREE.MeshBasicMaterial({ map: signTexture('SAFETY FIRST', 'Gunakan APD di area produksi', '#16a34a'), toneMapped: false }));
    signA.rotation.y = Math.PI / 2;
    signA.position.set(-17.82, 4.2, -2.5);
    structureGroup.add(signA);

    // ==========================================
    // RAK / LEMARI ALAT TERBUKA (TANPA PINTU)
    // ==========================================
    const toolRack = new THREE.Group();
    toolRack.position.set(-17.2, 0, -2.5);
    toolRack.rotation.y = Math.PI / 2;

    const rackFrameMat = M(0x1d4ed8, { metalness: 0.5, roughness: 0.35 }); // Rangka besi biru industrial
    const shelfMat = M(0x475569, { metalness: 0.6, roughness: 0.4 });     // Plat ambalan rak

    // 4 Tiang sudut rak
    const postGeo = new THREE.BoxGeometry(0.08, 2.5, 0.08);
    [[-1.8, -0.4], [-1.8, 0.4], [1.8, -0.4], [1.8, 0.4], [0, -0.4], [0, 0.4]].forEach(([px, pz]) => {
        const post = new THREE.Mesh(postGeo, rackFrameMat);
        post.position.set(px, 1.25, pz);
        toolRack.add(post);
    });

    // Ambalan rak bertingkat (4 tingkat)
    [0.15, 0.9, 1.65, 2.45].forEach((sy) => {
        const shelf = new THREE.Mesh(new THREE.BoxGeometry(3.7, 0.05, 0.88), shelfMat);
        shelf.position.y = sy;
        toolRack.add(shelf);
    });

    // Dinding belakang rak (wire mesh / plat penyangga)
    const toolRackBack = new THREE.Mesh(new THREE.BoxGeometry(3.65, 2.3, 0.02), M(0x334155, { metalness: 0.4, roughness: 0.6 }));
    toolRackBack.position.set(0, 1.3, -0.42);
    toolRack.add(toolRackBack);

    // Isi Rak: Toolbox, Kotak part/baut, dan Alat kerja
    const tbMat1 = M(0xdc2626, { roughness: 0.4 }); // Toolbox merah
    const tbMat2 = M(0x2563eb, { roughness: 0.4 }); // Toolbox biru
    const binYellow = M(0xfacc15, { roughness: 0.5 });
    const binGreen = M(0x16a34a, { roughness: 0.5 });
    const toolSilver = M(0x94a3b8, { metalness: 0.8, roughness: 0.25 });

    // Tingkat 1 (Bawah): Box alat berat & parts besar
    const heavyBox1 = new THREE.Mesh(new THREE.BoxGeometry(0.85, 0.55, 0.6), tbMat1);
    heavyBox1.position.set(-1.1, 0.45, 0);
    const heavyBox2 = new THREE.Mesh(new THREE.BoxGeometry(0.75, 0.5, 0.55), tbMat2);
    heavyBox2.position.set(0.0, 0.42, 0);
    const heavyBox3 = new THREE.Mesh(new THREE.BoxGeometry(0.85, 0.55, 0.6), M(0x475569, { roughness: 0.6 }));
    heavyBox3.position.set(1.1, 0.45, 0);
    toolRack.add(heavyBox1, heavyBox2, heavyBox3);

    // Tingkat 2 (Tengah bawah): Deretan wadah bin part kecil (kuning & hijau)
    for (let bi = -1.4; bi <= 1.4; bi += 0.56) {
        const bin = new THREE.Mesh(new THREE.BoxGeometry(0.44, 0.35, 0.55), (Math.abs(bi) < 0.6) ? binYellow : binGreen);
        bin.position.set(bi, 1.1, 0.05);
        toolRack.add(bin);
    }

    // Tingkat 3 (Tengah atas): Kotak perkakas portabel & mesin bor/gerinda silinder
    const toolCase = new THREE.Mesh(new THREE.BoxGeometry(0.7, 0.32, 0.48), tbMat1);
    toolCase.position.set(-1.0, 1.83, 0);
    const powerTool = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, 0.6, 12), toolSilver);
    powerTool.rotation.z = Math.PI / 2;
    powerTool.position.set(0.2, 1.8, 0);
    const sprayCan1 = new THREE.Mesh(new THREE.CylinderGeometry(0.065, 0.065, 0.35, 10), M(0x0284c7, { roughness: 0.3 }));
    sprayCan1.position.set(1.1, 1.85, -0.1);
    const sprayCan2 = new THREE.Mesh(new THREE.CylinderGeometry(0.065, 0.065, 0.35, 10), binYellow);
    sprayCan2.position.set(1.3, 1.85, 0.1);
    toolRack.add(toolCase, powerTool, sprayCan1, sprayCan2);

    structureGroup.add(toolRack);

    const apar = new THREE.Group();
    const aparBody = new THREE.Mesh(new THREE.CylinderGeometry(0.11, 0.11, 0.5, 14), M(0xd42020, { roughness: 0.35, metalness: 0.3 }));
    aparBody.position.y = 0.5;
    const aparHead = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.08, 0.1, 10), darkMetal);
    aparHead.position.y = 0.8;
    apar.add(aparBody, aparHead);
    apar.position.set(-9.6, 0.2, -7.3);
    const aparSign = new THREE.Mesh(new THREE.PlaneGeometry(0.4, 0.4), new THREE.MeshBasicMaterial({ color: 0xd42020 }));
    aparSign.position.set(-9.6, 1.9, -7.43);
    structureGroup.add(apar, aparSign);

    enableShadows(structureGroup);
    noShadowMeshes.forEach((m) => { m.castShadow = false; });
    scene.add(structureGroup);

    // ==========================================
    // 3. MESIN STAMPING (4 unit) + PERCIKAN
    // ==========================================
    const stampingPresses = [];
    const PRESS_HIT = 0.25; // fase saat ram menyentuh die
    const RAM_BOTTOM = 1.47, RAM_TOP = 2.35;

    function createStampingPress(x, z, idx) {
        const press = new THREE.Group();
        press.position.set(x, 0, z);

        const pad = new THREE.Mesh(new THREE.BoxGeometry(2.6, 0.12, 2.0), concreteMat);
        pad.position.y = 0.06;
        const base = new THREE.Mesh(new THREE.BoxGeometry(2.3, 0.85, 1.75), pressCream);
        base.position.y = 0.425;
        const colL = new THREE.Mesh(new THREE.BoxGeometry(0.45, 3.1, 1.55), pressCream);
        colL.position.set(-0.85, 2.4, 0);
        const colR = colL.clone(); colR.position.x = 0.85;
        const crown = new THREE.Mesh(new THREE.BoxGeometry(2.35, 0.85, 1.8), pressCream);
        crown.position.y = 4.3;

        // Garis aksen biru di crown
        const accent = new THREE.Mesh(new THREE.BoxGeometry(2.37, 0.08, 1.82), steelBlue);
        accent.position.y = 4.1;

        const flywheel = new THREE.Mesh(new THREE.CylinderGeometry(0.65, 0.65, 0.22, 24), darkMetal);
        flywheel.rotation.z = Math.PI / 2;
        flywheel.position.set(1.25, 3.7, 0);
        const spokeMat = M(0xf5c518, { roughness: 0.4 });
        const flyMark = new THREE.Mesh(new THREE.BoxGeometry(0.24, 0.12, 0.5), spokeMat);
        flyMark.position.set(0, 0, 0.3);
        flywheel.add(flyMark);

        const ram = new THREE.Group();
        const ramBody = new THREE.Mesh(new THREE.BoxGeometry(1.15, 0.65, 1.05), darkMetal);
        const ramHazard = new THREE.Mesh(new THREE.BoxGeometry(1.17, 0.11, 1.07), hazardMat);
        ramHazard.position.y = -0.3;
        ram.add(ramBody, ramHazard);
        ram.position.y = 2.3;

        // Batang piston dari crown ke ram
        const rodGeo = new THREE.CylinderGeometry(0.05, 0.05, 1, 10);
        const rodMat = M(0xcfd6de, { metalness: 0.9, roughness: 0.15, envMapIntensity: 1.4 });
        const rods = [-0.3, 0.3].map((rx) => {
            const r = new THREE.Mesh(rodGeo, rodMat);
            r.position.x = rx;
            press.add(r);
            return r;
        });

        // Die bawah (bisa menyala saat benturan)
        const dieMat = M(0x2d3a4a, { metalness: 0.75, roughness: 0.28, envMapIntensity: 1.0, emissive: 0xff6a00, emissiveIntensity: 0 });
        const lowerDie = new THREE.Mesh(new THREE.BoxGeometry(1.3, 0.25, 1.1), dieMat);
        lowerDie.position.y = 0.98;

        const sopBoard = new THREE.Mesh(new THREE.BoxGeometry(0.05, 0.85, 0.55), whiteUniform);
        sopBoard.position.set(1.15, 2.3, 0.3);
        const sopHeader = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.12, 0.55), new THREE.MeshBasicMaterial({ color: 0xd42020 }));
        sopHeader.position.set(1.15, 2.68, 0.3);

        const panelLight = new THREE.Mesh(new THREE.SphereGeometry(0.06, 8, 8), ledGreen);
        panelLight.position.set(-1.16, 2.0, 0.6);

        // Andon tower (lampu status)
        const andon = new THREE.Group();
        andon.position.set(-0.8, 4.725, 0.5);
        const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.025, 0.025, 0.4, 8), darkMetal);
        pole.position.y = 0.2;
        const gMat = M(0x22c55e, { emissive: 0x22c55e, emissiveIntensity: 1.2, roughness: 0.3 });
        const yMat = M(0xf5c518, { emissive: 0xf5c518, emissiveIntensity: 0, roughness: 0.3 });
        const rMat = M(0xd42020, { emissive: 0xd42020, emissiveIntensity: 0, roughness: 0.3 });
        [[gMat, 0.45], [yMat, 0.57], [rMat, 0.69]].forEach(([mat, y]) => {
            const l = new THREE.Mesh(new THREE.CylinderGeometry(0.07, 0.07, 0.12, 14), mat);
            l.position.y = y;
            andon.add(l);
        });
        andon.add(pole);

        // Zona bahaya di depan mesin
        const hz = new THREE.Mesh(new THREE.PlaneGeometry(2.7, 0.2), hazardPlaneMat);
        hz.rotation.x = -Math.PI / 2;
        hz.position.set(0, 0.02, 1.15);

        press.add(pad, base, colL, colR, crown, accent, flywheel, ram, lowerDie, sopBoard, sopHeader, panelLight, andon, hz);
        enableShadows(press);
        hz.castShadow = false;
        scene.add(press);

        stampingPresses.push({
            press, ram, flywheel, rods, dieMat, yMat, x, z, idx,
            prevP: 0, flash: 0, speed: 0.42 + idx * 0.015, offset: idx * 0.27
        });
    }

    createStampingPress(-7.5, -3.0, 0);
    createStampingPress(-4.5, -3.0, 1);
    createStampingPress(-1.5, -3.0, 2);
    createStampingPress(1.5, -3.0, 3);

    // Sistem percikan api
    const SPARKS = 180;
    const sPos = new Float32Array(SPARKS * 3);
    const sCol = new Float32Array(SPARKS * 3);
    const sVel = new Float32Array(SPARKS * 3);
    const sLife = new Float32Array(SPARKS);
    for (let i = 0; i < SPARKS; i++) sPos[i * 3 + 1] = -100;
    const sGeo = new THREE.BufferGeometry();
    sGeo.setAttribute('position', new THREE.BufferAttribute(sPos, 3));
    sGeo.setAttribute('color', new THREE.BufferAttribute(sCol, 3));
    const sparks = new THREE.Points(sGeo, new THREE.PointsMaterial({
        size: 0.11, map: glowTex, vertexColors: true, transparent: true,
        depthWrite: false, blending: THREE.AdditiveBlending, toneMapped: false, fog: false
    }));
    sparks.frustumCulled = false;
    scene.add(sparks);
    let sparkCursor = 0;

    function emitSparks(px, pz) {
        for (let n = 0; n < 14; n++) {
            const i = sparkCursor; sparkCursor = (sparkCursor + 1) % SPARKS;
            const a = Math.random() * Math.PI * 2;
            const sp = 0.8 + Math.random() * 2.2;
            sPos[i * 3] = px + Math.cos(a) * 0.5;
            sPos[i * 3 + 1] = 1.15;
            sPos[i * 3 + 2] = pz + 0.5 + Math.sin(a) * 0.4;
            sVel[i * 3] = Math.cos(a) * sp;
            sVel[i * 3 + 1] = 1.2 + Math.random() * 2.6;
            sVel[i * 3 + 2] = Math.sin(a) * sp * 0.8 + 0.6;
            sLife[i] = 0.6 + Math.random() * 0.5;
        }
    }
    function updateSparks(dt) {
        for (let i = 0; i < SPARKS; i++) {
            if (sLife[i] <= 0) continue;
            sLife[i] -= dt;
            const i3 = i * 3;
            if (sLife[i] <= 0) { sPos[i3 + 1] = -100; sCol[i3] = sCol[i3 + 1] = sCol[i3 + 2] = 0; continue; }
            sVel[i3 + 1] -= 9 * dt;
            sPos[i3] += sVel[i3] * dt;
            sPos[i3 + 1] += sVel[i3 + 1] * dt;
            sPos[i3 + 2] += sVel[i3 + 2] * dt;
            if (sPos[i3 + 1] < 0.04) {
                sPos[i3 + 1] = 0.04;
                sVel[i3 + 1] *= -0.3; sVel[i3] *= 0.6; sVel[i3 + 2] *= 0.6;
            }
            const k = Math.min(1, sLife[i] * 1.8);
            sCol[i3] = 1.0 * k;
            sCol[i3 + 1] = (0.35 + 0.5 * sLife[i]) * k;
            sCol[i3 + 2] = 0.08 * k;
        }
        sGeo.attributes.position.needsUpdate = true;
        sGeo.attributes.color.needsUpdate = true;
    }

    // ==========================================
    // 4. CRATE & RAK STILLAGE
    // ==========================================
    const crateHandleMat = M(0x000000, { roughness: 0.9, envMapIntensity: 0 });
    function createCrateStack(x, z, colorHex, count) {
        const stack = new THREE.Group();
        stack.position.set(x, 0, z);
        const mat = M(colorHex, { roughness: 0.42 });
        for (let i = 0; i < count; i++) {
            const crate = new THREE.Mesh(new THREE.BoxGeometry(0.52, 0.28, 0.4), mat);
            crate.position.y = 0.14 + i * 0.29;
            const handle = new THREE.Mesh(new THREE.BoxGeometry(0.2, 0.04, 0.012), crateHandleMat);
            handle.position.set(0, 0.2 + i * 0.29, 0.203);
            stack.add(crate, handle);
        }
        enableShadows(stack);
        scene.add(stack);
    }
    createCrateStack(-6.0, -1.4, 0xf0c020, 3);
    createCrateStack(-5.3, -1.4, 0x2060d0, 2);
    createCrateStack(-3.0, -1.4, 0x1aa050, 3);
    createCrateStack(-2.3, -1.4, 0xf0c020, 2);
    createCrateStack(0.3, -1.4, 0x1aa050, 2);
    createCrateStack(0.9, -1.4, 0x2060d0, 3);

    function createCarBodyPart() {
        const part = new THREE.Group();
        const lowerPanel = new THREE.Mesh(new THREE.BoxGeometry(0.85, 0.3, 0.52), carBodyMat);
        lowerPanel.position.y = 0.15;
        const upperFrame = new THREE.Mesh(new THREE.BoxGeometry(0.55, 0.22, 0.48), carBodyMat);
        upperFrame.position.set(-0.04, 0.42, 0);
        const windowCut = new THREE.Mesh(new THREE.BoxGeometry(0.36, 0.14, 0.5), carTrimMat);
        windowCut.position.set(-0.04, 0.42, 0);
        const fender = new THREE.Mesh(new THREE.CylinderGeometry(0.14, 0.14, 0.54, 10, 1, false, 0, Math.PI), carTrimMat);
        fender.rotation.x = Math.PI / 2;
        fender.position.set(0.28, 0.06, 0);
        part.add(lowerPanel, upperFrame, windowCut, fender);
        return part;
    }

    const rack = new THREE.Group();
    rack.position.set(0.2, 0, 3.6);
    const rackBase = new THREE.Mesh(new THREE.BoxGeometry(2.6, 0.12, 1.0), steelBlue);
    rackBase.position.y = 0.06;
    const rackBack = new THREE.Mesh(new THREE.BoxGeometry(2.6, 1.2, 0.08), steelBlue);
    rackBack.position.set(0, 0.65, -0.46);
    rack.add(rackBase, rackBack);
    [[-1.25, -0.45], [1.25, -0.45], [-1.25, 0.45], [1.25, 0.45]].forEach(([px, pz]) => {
        const post = new THREE.Mesh(new THREE.BoxGeometry(0.06, 1.25, 0.06), steelBlue);
        post.position.set(px, 0.7, pz);
        rack.add(post);
    });
    const topRail = new THREE.Mesh(new THREE.BoxGeometry(2.56, 0.05, 0.05), steelBlue);
    topRail.position.set(0, 1.32, 0.45);
    rack.add(topRail);
    for (let i = 0; i < 4; i++) {
        const p = createCarBodyPart();
        p.rotation.y = Math.PI / 2;
        p.scale.setScalar(0.8);
        p.position.set(-0.9 + i * 0.6, 0.15, 0);
        rack.add(p);
    }
    enableShadows(rack);
    scene.add(rack);

    const rack2 = rack.clone();
    rack2.position.set(3.5, 0, 2.5);
    rack2.rotation.y = -0.4;
    scene.add(rack2);

    // ==========================================
    // 5. CONVEYOR & LENGAN ROBOT
    // ==========================================
    const conveyorGroup = new THREE.Group();
    conveyorGroup.position.set(-3.5, 0.55, 0.9);
    scene.add(conveyorGroup);

    const beltBase = new THREE.Mesh(new THREE.BoxGeometry(10, 0.16, 1.05), darkMetal);
    conveyorGroup.add(beltBase);
    const beltSurface = new THREE.Mesh(new THREE.BoxGeometry(9.6, 0.04, 0.9),
        new THREE.MeshStandardMaterial({ map: beltTex, roughness: 0.9, metalness: 0 }));
    beltSurface.position.y = 0.1;
    conveyorGroup.add(beltSurface);

    const railMat = M(0x94a3b8, { metalness: 0.7, roughness: 0.25, envMapIntensity: 1.0 });
    const railL = new THREE.Mesh(new THREE.BoxGeometry(10, 0.12, 0.06), railMat);
    railL.position.set(0, 0.14, 0.5);
    const railR = railL.clone(); railR.position.z = -0.5;
    conveyorGroup.add(railL, railR);

    const ledStripMat = new THREE.MeshBasicMaterial({ color: 0x38bdf8, toneMapped: false });
    const ledStrip = new THREE.Mesh(new THREE.BoxGeometry(9.8, 0.025, 0.01), ledStripMat);
    ledStrip.position.set(0, 0.0, 0.532);
    conveyorGroup.add(ledStrip);

    for (let x = -4; x <= 4; x += 2) {
        const leg = new THREE.Mesh(new THREE.BoxGeometry(0.1, 0.55, 0.9), darkMetal);
        leg.position.set(x, -0.275, 0);
        conveyorGroup.add(leg);
    }

    const partsOnBelt = [];
    for (let i = 0; i < 4; i++) {
        const carPart = createCarBodyPart();
        carPart.scale.setScalar(0.75);
        carPart.position.set(-4 + i * 2.5, 0.12, 0);
        enableShadows(carPart);
        conveyorGroup.add(carPart);
        partsOnBelt.push(carPart);
    }
    const BELT_SPEED = 1.08; // unit per detik

    function createRoboticArm(x, z) {
        const root = new THREE.Group();
        root.position.set(x, 0, z);

        const basePlate = new THREE.Mesh(new THREE.CylinderGeometry(0.65, 0.68, 0.1, 24), darkMetal);
        basePlate.position.y = 0.05;
        const base = new THREE.Mesh(new THREE.CylinderGeometry(0.45, 0.55, 0.3, 20), darkMetal);
        base.position.y = 0.2;
        const ringMat = new THREE.MeshBasicMaterial({ color: 0x38bdf8, toneMapped: false });
        const ring = new THREE.Mesh(new THREE.TorusGeometry(0.5, 0.018, 8, 32), ringMat);
        ring.rotation.x = Math.PI / 2; ring.position.y = 0.3;
        root.add(basePlate, base, ring);

        const turret = new THREE.Group();
        turret.position.y = 0.35;
        const turretMesh = new THREE.Mesh(new THREE.CylinderGeometry(0.34, 0.4, 0.32, 18), robotOrange);
        turretMesh.position.y = 0.16;
        turret.add(turretMesh);
        root.add(turret);

        const lowerPivot = new THREE.Group();
        lowerPivot.position.y = 0.32;
        const joint1 = new THREE.Mesh(new THREE.CylinderGeometry(0.18, 0.18, 0.3, 16), darkMetal);
        joint1.rotation.x = Math.PI / 2;
        const lowerArm = new THREE.Mesh(new THREE.BoxGeometry(0.22, 1.5, 0.22), robotOrange);
        lowerArm.position.y = 0.75;
        const hLine = new THREE.Mesh(new THREE.CylinderGeometry(0.025, 0.025, 1.3, 6), darkMetal);
        hLine.position.set(0.14, 0.7, 0);
        lowerPivot.add(joint1, lowerArm, hLine);
        turret.add(lowerPivot);

        const upperPivot = new THREE.Group();
        upperPivot.position.y = 1.5;
        const joint2 = new THREE.Mesh(new THREE.CylinderGeometry(0.14, 0.14, 0.26, 14), darkMetal);
        joint2.rotation.x = Math.PI / 2;
        const upperArm = new THREE.Mesh(new THREE.BoxGeometry(0.18, 1.2, 0.18), robotOrange);
        upperArm.position.y = 0.6;
        const gripper = new THREE.Mesh(new THREE.ConeGeometry(0.12, 0.26, 10), darkMetal);
        gripper.position.y = 1.28;
        upperPivot.add(joint2, upperArm, gripper);
        lowerPivot.add(upperPivot);

        enableShadows(root);
        scene.add(root);
        return { root, turret, lowerPivot, upperPivot, ringMat };
    }
    const arm1 = createRoboticArm(-3.5, -0.3);

    // ==========================================
    // 6. PEKERJA (APD lengkap, bisa berjalan)
    // ==========================================
    const shoeMat = M(0x1a1a1a, { roughness: 0.8, envMapIntensity: 0.1 });
    const legGeo = new THREE.CylinderGeometry(0.09, 0.08, 0.76, 10);
    const shoeGeo = new THREE.BoxGeometry(0.12, 0.06, 0.2);
    const armGeo = new THREE.CylinderGeometry(0.06, 0.05, 0.48, 10);
    const handGeo = new THREE.SphereGeometry(0.055, 10, 10);
    const eyeMat = new THREE.MeshBasicMaterial({ color: 0x1a1a1a });

    function createWorker(x, z, rotY, opts) {
        const o = opts || {};
        const hasTablet = !!o.tablet;
        const walk = o.walk || null;

        const worker = new THREE.Group();
        worker.position.set(x, 0, z);
        worker.rotation.y = rotY;

        const legs = [-0.1, 0.1].map((px) => {
            const piv = new THREE.Group();
            piv.position.set(px, 0.8, 0);
            const leg = new THREE.Mesh(legGeo, pantsDark);
            leg.position.y = -0.38;
            const shoe = new THREE.Mesh(shoeGeo, shoeMat);
            shoe.position.set(0, -0.77, 0.03);
            piv.add(leg, shoe);
            worker.add(piv);
            return piv;
        });

        const torso = new THREE.Mesh(new THREE.BoxGeometry(0.4, 0.55, 0.22), shirtBlue);
        torso.position.y = 1.08;
        const vest = new THREE.Mesh(new THREE.BoxGeometry(0.42, 0.5, 0.24), safetyOrange);
        vest.position.y = 1.07;
        const stripeMat = new THREE.MeshBasicMaterial({ color: 0xe8eef5 });
        const stripe1 = new THREE.Mesh(new THREE.BoxGeometry(0.43, 0.04, 0.25), stripeMat);
        stripe1.position.y = 1.0;
        const stripe2 = stripe1.clone(); stripe2.position.y = 1.18;
        worker.add(torso, vest, stripe1, stripe2);

        const headGroup = new THREE.Group();
        headGroup.position.y = 1.5;
        const head = new THREE.Mesh(new THREE.SphereGeometry(0.15, 18, 18), skinMat);
        const eyeL = new THREE.Mesh(new THREE.SphereGeometry(0.025, 6, 6), eyeMat);
        eyeL.position.set(-0.05, 0.03, 0.14);
        const eyeR = eyeL.clone(); eyeR.position.x = 0.05;
        const hMat = o.blueHelmet ? M(0x1d4ed8, { roughness: 0.35, metalness: 0.3 }) : safetyYellow;
        const helmet = new THREE.Mesh(new THREE.SphereGeometry(0.17, 18, 12, 0, Math.PI * 2, 0, Math.PI * 0.55), hMat);
        helmet.position.y = 0.03;
        const brim = new THREE.Mesh(new THREE.CylinderGeometry(0.19, 0.19, 0.025, 16, 1, false, -Math.PI * 0.4, Math.PI * 0.8), hMat);
        brim.position.set(0, 0.025, 0.04);
        const ridge = new THREE.Mesh(new THREE.BoxGeometry(0.03, 0.02, 0.3), hMat);
        ridge.position.set(0, 0.2, 0);
        headGroup.add(head, eyeL, eyeR, helmet, brim, ridge);
        worker.add(headGroup);

        function makeArm(px) {
            const piv = new THREE.Group();
            piv.position.set(px, 1.28, 0);
            const a = new THREE.Mesh(armGeo, shirtBlue);
            a.position.y = -0.2;
            const h = new THREE.Mesh(handGeo, skinMat);
            h.position.y = -0.45;
            piv.add(a, h);
            worker.add(piv);
            return piv;
        }
        const armLPivot = makeArm(-0.26);
        const armRPivot = makeArm(0.26);

        if (hasTablet) {
            armLPivot.rotation.x = -1.1; armLPivot.rotation.z = 0.3;
            armRPivot.rotation.x = -1.1; armRPivot.rotation.z = -0.3;
            const tablet = new THREE.Mesh(new THREE.BoxGeometry(0.26, 0.015, 0.18), darkMetal);
            tablet.position.set(0, 0.95, 0.32);
            tablet.rotation.x = 0.5;
            const tabScreen = new THREE.Mesh(new THREE.PlaneGeometry(0.22, 0.14),
                new THREE.MeshBasicMaterial({ color: 0x38bdf8, toneMapped: false }));
            tabScreen.rotation.x = -Math.PI / 2;
            tabScreen.position.y = 0.009;
            tablet.add(tabScreen);
            worker.add(tablet);
        }

        enableShadows(worker);
        scene.add(worker);
        return {
            worker, headGroup, armLPivot, armRPivot, legs, hasTablet,
            walk: walk ? { minX: walk.minX, maxX: walk.maxX, speed: walk.speed, dir: 1, phase: 0 } : null
        };
    }

    const workers = [
        createWorker(-6.2, -0.8, 0.3, { blueHelmet: true }),
        createWorker(-1.2, -0.8, -0.3, { blueHelmet: true }),
        createWorker(-6.8, 2.8, 0.5, { tablet: true }),
        createWorker(-10.5, 1.85, Math.PI / 2, { walk: { minX: -11, maxX: 1.2, speed: 0.75 } })
    ];

    // ==========================================
    // 7. TV GENBA CHECKSHEET (hidup)
    // ==========================================
    const tvGroup = new THREE.Group();
    tvGroup.position.set(-7.8, 0, 2.6);
    tvGroup.rotation.y = 0.35;

    const tvBase = new THREE.Mesh(new THREE.BoxGeometry(0.9, 0.06, 0.5), darkMetal);
    tvBase.position.y = 0.03;
    const tvPole = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.05, 0.75, 10), darkMetal);
    tvPole.position.set(0, 0.38, -0.01);
    const tvBezel = new THREE.Mesh(new THREE.BoxGeometry(1.9, 1.3, 0.07), pantsDark);
    tvBezel.position.y = 1.42;
    tvGroup.add(tvBase, tvPole, tvBezel);

    const [tvCanvas, tctx] = makeCanvas(512, 340);
    const tvLabels = ['1. Stamping Press', '2. Robotic Assy', '3. Safety Area', '4. Part Quality'];
    function drawTV(tick) {
        const g = tctx;
        g.fillStyle = '#f8fafc'; g.fillRect(0, 0, 512, 340);
        g.fillStyle = '#1a56db'; g.fillRect(0, 0, 512, 52);
        g.fillStyle = '#ffffff'; g.font = 'bold 22px sans-serif'; g.textAlign = 'left';
        g.fillText('GENBA CHECKSHEET', 22, 34);
        g.fillStyle = '#22c55e'; rrect(g, 380, 12, 112, 28, 14); g.fill();
        g.fillStyle = '#ffffff'; g.font = 'bold 13px sans-serif';
        g.fillText((tick % 2 === 0 ? '\u25CF' : '\u25CB') + ' LIVE', 406, 30);
        tvLabels.forEach((label, idx) => {
            const y = 66 + idx * 58;
            const active = (tick % 4) === idx;
            g.fillStyle = active ? '#f0fdf4' : '#ffffff'; g.fillRect(16, y, 480, 46);
            g.strokeStyle = active ? '#22c55e' : '#e2e8f0'; g.lineWidth = active ? 2 : 1;
            g.strokeRect(16, y, 480, 46);
            g.fillStyle = '#1e293b'; g.font = 'bold 17px sans-serif';
            g.fillText(label, 32, y + 28);
            g.fillStyle = '#dcfce7'; rrect(g, 410, y + 9, 68, 28, 6); g.fill();
            g.fillStyle = '#16a34a'; g.font = 'bold 15px sans-serif';
            g.fillText('OK \u2713', 424, y + 28);
        });
        g.fillStyle = '#64748b'; g.font = '13px sans-serif';
        let clock = '';
        try { clock = new Date().toLocaleTimeString('id-ID'); } catch (e) { clock = ''; }
        g.fillText('Update terakhir ' + clock, 20, 328);
    }
    drawTV(0);
    const tvTex = srgb(new THREE.CanvasTexture(tvCanvas));
    const tvScreen = new THREE.Mesh(new THREE.PlaneGeometry(1.78, 1.18),
        new THREE.MeshBasicMaterial({ map: tvTex, toneMapped: false }));
    tvScreen.position.set(0, 1.42, 0.04);
    tvGroup.add(tvScreen);
    const tvGlow = new THREE.Mesh(new THREE.PlaneGeometry(3.6, 2.8),
        new THREE.MeshBasicMaterial({
            map: glowTex, color: 0x7dd3fc, transparent: true, opacity: 0.18,
            blending: THREE.AdditiveBlending, depthWrite: false, fog: false, toneMapped: false
        }));
    tvGlow.position.set(0, 1.42, 0.2);
    tvGroup.add(tvGlow);
    enableShadows(tvGroup);
    tvGlow.castShadow = false; tvScreen.castShadow = false;
    scene.add(tvGroup);
    let tvTick = 0, tvTimer = 0;

    // ==========================================
    // 8. FORKLIFT
    // ==========================================
    const forklifts = [];
    function createForklift(x, z, scale) {
        const fk = new THREE.Group();
        fk.position.set(x, 0, z);
        fk.scale.setScalar(scale);

        const fkBody = new THREE.Mesh(new THREE.BoxGeometry(1.25, 0.6, 0.85), robotOrange);
        fkBody.position.y = 0.5;
        const fkHood = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.3, 0.8), darkMetal);
        fkHood.position.set(-0.35, 0.88, 0);
        const seat = new THREE.Mesh(new THREE.BoxGeometry(0.35, 0.3, 0.5), pantsDark);
        seat.position.set(0.05, 0.95, 0);
        const cageTop = new THREE.Mesh(new THREE.BoxGeometry(0.9, 0.06, 0.8), darkMetal);
        cageTop.position.set(-0.1, 1.7, 0);
        [[-0.48, -0.36], [-0.48, 0.36], [0.3, -0.36], [0.3, 0.36]].forEach(([px, pz]) => {
            const post = new THREE.Mesh(new THREE.CylinderGeometry(0.03, 0.03, 0.9, 8), darkMetal);
            post.position.set(px, 1.25, pz);
            fk.add(post);
        });

        const wheelMat = M(0x1a1a1a, { roughness: 0.9, envMapIntensity: 0.1 });
        const wheelGeo = new THREE.CylinderGeometry(0.22, 0.22, 0.16, 18);
        wheelGeo.rotateX(Math.PI / 2);
        const hubGeo = new THREE.CylinderGeometry(0.1, 0.1, 0.17, 12);
        hubGeo.rotateX(Math.PI / 2);
        [[-0.42, -0.46], [-0.42, 0.46], [0.42, -0.46], [0.42, 0.46]].forEach(([wx, wz]) => {
            const wheel = new THREE.Mesh(wheelGeo, wheelMat);
            wheel.position.set(wx, 0.22, wz);
            const hub = new THREE.Mesh(hubGeo, carTrimMat);
            hub.position.set(wx, 0.22, wz);
            fk.add(wheel, hub);
        });

        const mastL = new THREE.Mesh(new THREE.BoxGeometry(0.08, 1.75, 0.08), darkMetal);
        mastL.position.set(0.68, 0.95, -0.26);
        const mastR = mastL.clone(); mastR.position.z = 0.26;
        const forkL = new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.05, 0.1), carTrimMat);
        forkL.position.set(1.08, 0.23, -0.2);
        const forkR = forkL.clone(); forkR.position.z = 0.2;
        const pallet = new THREE.Mesh(new THREE.BoxGeometry(0.75, 0.08, 0.8), craneYellow);
        pallet.position.set(1.1, 0.3, 0);
        const fkPart = createCarBodyPart();
        fkPart.scale.setScalar(0.7);
        fkPart.position.set(1.1, 0.35, 0);

        const beaconMat = M(0xf08030, { emissive: 0xff6a00, emissiveIntensity: 1.0, roughness: 0.4 });
        const beacon = new THREE.Mesh(new THREE.CylinderGeometry(0.06, 0.06, 0.1, 10), beaconMat);
        beacon.position.set(-0.35, 1.78, 0);
        const beaconGlow = glowSprite(-0.35, 1.8, 0, 0.8, 0xff8a30, 0.8);

        fk.add(fkBody, fkHood, seat, cageTop, mastL, mastR, forkL, forkR, pallet, fkPart, beacon, beaconGlow);
        enableShadows(fk);
        scene.add(fk);
        const obj = { fk, beaconMat, beaconGlow, rot: 0 };
        forklifts.push(obj);
        return obj;
    }
    const forklift1 = createForklift(-4.0, 5.8, 0.85);
    const forklift2 = createForklift(2.5, 5.8, 0.82);

    // ==========================================
    // 9. PARTIKEL DEBU DI UDARA
    // ==========================================
    const DUST = isMobile ? 120 : 260;
    const dPos = new Float32Array(DUST * 3);
    const dSpd = new Float32Array(DUST);
    for (let i = 0; i < DUST; i++) {
        dPos[i * 3] = -20 + Math.random() * 32;
        dPos[i * 3 + 1] = 0.4 + Math.random() * 6.6;
        dPos[i * 3 + 2] = -6 + Math.random() * 14;
        dSpd[i] = 0.02 + Math.random() * 0.05;
    }
    const dGeo = new THREE.BufferGeometry();
    dGeo.setAttribute('position', new THREE.BufferAttribute(dPos, 3));
    const dust = new THREE.Points(dGeo, new THREE.PointsMaterial({
        size: 0.07, map: glowTex, color: 0xffffff, transparent: true, opacity: 0.55,
        depthWrite: false, fog: false, toneMapped: false
    }));
    dust.frustumCulled = false;
    scene.add(dust);

    // ==========================================
    // INPUT & RESIZE
    // ==========================================
    let targetMouseX = 0, targetMouseY = 0, parX = 0, parY = 0;
    window.addEventListener('pointermove', (e) => {
        targetMouseX = (e.clientX / window.innerWidth - 0.5) * 2;
        targetMouseY = (e.clientY / window.innerHeight - 0.5) * 2;
    }, { passive: true });

    window.addEventListener('resize', () => {
        applyAspect();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });

    // ==========================================
    // LOOP ANIMASI
    // ==========================================
    let last = performance.now();
    let t = 0;
    let intro = reduceMotion ? 1 : 0;

    function animate(now) {
        requestAnimationFrame(animate);
        const rawDt = Math.min(0.05, (now - last) / 1000);
        last = now;
        const dt = rawDt * speedScale;
        t += dt;

        // Sabuk & part conveyor (berbasis waktu, bukan frame)
        beltTex.offset.x -= BELT_SPEED * dt / 0.5;
        partsOnBelt.forEach((p) => {
            p.position.x += BELT_SPEED * dt;
            if (p.position.x > 4.8) p.position.x = -4.8;
        });
        ledStripMat.color.setHex(Math.sin(t * 3) > 0 ? 0x38bdf8 : 0x0ea5e9);

        // Lengan robot
        arm1.turret.rotation.y = Math.sin(t * 0.65) * 0.4;
        arm1.lowerPivot.rotation.x = 0.4 + Math.sin(t * 1.2) * 0.16;
        arm1.upperPivot.rotation.x = 1.6 + Math.cos(t * 1.2) * 0.24;
        arm1.ringMat.color.setHex(Math.sin(t * 2.2) > -0.3 ? 0x38bdf8 : 0x0b7fb0);

        // Mesin press: turun cepat, naik pelan, percikan saat benturan
        stampingPresses.forEach((p) => {
            const ph = (t * p.speed + p.offset) % 1;
            let s;
            if (ph < PRESS_HIT) {
                const k = ph / PRESS_HIT;
                s = 1 - k * k;
            } else {
                s = smooth((ph - PRESS_HIT) / (1 - PRESS_HIT));
            }
            const ramY = RAM_BOTTOM + (RAM_TOP - RAM_BOTTOM) * s;
            p.ram.position.y = ramY;
            const rodTop = 3.875, rodBottom = ramY + 0.325;
            p.rods.forEach((r) => {
                r.scale.y = rodTop - rodBottom;
                r.position.y = (rodTop + rodBottom) / 2;
            });
            p.flywheel.rotation.x = t * 1.5;

            if (p.prevP < PRESS_HIT && ph >= PRESS_HIT) {
                emitSparks(p.x, p.z);
                p.flash = 1;
            }
            p.prevP = ph;
            p.flash = Math.max(0, p.flash - rawDt * 2.2);
            p.dieMat.emissiveIntensity = p.flash * 1.4;
            p.yMat.emissiveIntensity = p.idx === 2 ? (Math.sin(t * 4) > 0 ? 1.2 : 0.05) : 0;
        });
        updateSparks(dt);

        // Pekerja
        workers.forEach((w, idx) => {
            w.headGroup.rotation.y = Math.sin(t * 0.6 + idx * 1.2) * 0.18;
            if (w.walk) {
                const s = w.walk;
                w.worker.position.x += s.dir * s.speed * dt;
                if (w.worker.position.x > s.maxX) { w.worker.position.x = s.maxX; s.dir = -1; }
                if (w.worker.position.x < s.minX) { w.worker.position.x = s.minX; s.dir = 1; }
                const targetRot = s.dir > 0 ? Math.PI / 2 : -Math.PI / 2;
                w.worker.rotation.y += (targetRot - w.worker.rotation.y) * Math.min(1, rawDt * 5);
                s.phase += dt * s.speed * 5.5;
                const sw = Math.sin(s.phase);
                w.legs[0].rotation.x = sw * 0.55;
                w.legs[1].rotation.x = -sw * 0.55;
                w.armLPivot.rotation.x = -sw * 0.45;
                w.armRPivot.rotation.x = sw * 0.45;
                w.worker.position.y = Math.abs(sw) * 0.025;
            } else if (!w.hasTablet) {
                w.armLPivot.rotation.x = -0.4 + Math.sin(t * 1.8 + idx * 0.8) * 0.2;
                w.armRPivot.rotation.x = -0.4 + Math.cos(t * 1.8 + idx * 0.8) * 0.2;
            }
        });

        craneTrolley.position.x = -2 + Math.sin(t * 0.25) * 6;

        // Forklift berpatroli di jalur lorong depan (di depan garis kuning z = 4.6)
        const f1 = forklift1, f2 = forklift2;
        f1.fk.position.x = -4.0 + Math.sin(t * 0.35) * 2.8;
        f2.fk.position.x = 2.5 + Math.cos(t * 0.3) * 2.5;
        const tr1 = Math.cos(t * 0.35) >= 0 ? 0 : Math.PI;
        const tr2 = -Math.sin(t * 0.3) >= 0 ? 0 : Math.PI;
        f1.rot += (tr1 - f1.rot) * Math.min(1, rawDt * 4);
        f2.rot += (tr2 - f2.rot) * Math.min(1, rawDt * 4);
        f1.fk.rotation.y = f1.rot;
        f2.fk.rotation.y = f2.rot;
        forklifts.forEach((f, i) => {
            const on = Math.sin(t * 6 + i) > 0;
            f.beaconMat.emissiveIntensity = on ? 1.6 : 0.15;
            f.beaconGlow.material.opacity = on ? 0.85 : 0.1;
        });

        // TV
        tvTimer += rawDt;
        if (tvTimer > 1) {
            tvTimer = 0; tvTick++;
            drawTV(tvTick);
            tvTex.needsUpdate = true;
        }

        // Debu
        for (let i = 0; i < DUST; i++) {
            const i3 = i * 3;
            dPos[i3] += Math.sin(t * 0.3 + i) * 0.12 * dt;
            dPos[i3 + 1] += dSpd[i] * dt;
            dPos[i3 + 2] += Math.cos(t * 0.25 + i * 0.7) * 0.1 * dt;
            if (dPos[i3 + 1] > 7.2) dPos[i3 + 1] = 0.3;
        }
        dGeo.attributes.position.needsUpdate = true;

        // Kamera: intro dolly + parallax mouse + melayang pelan
        intro = Math.min(1, intro + rawDt / 3.2);
        const e = 1 - Math.pow(1 - intro, 3);
        if (!reduceMotion) {
            const k = 1 - Math.exp(-rawDt * 2.5);
            parX += (targetMouseX * 0.8 - parX) * k;
            parY += (-targetMouseY * 0.5 - parY) * k;
        }
        camera.position.set(
            camBase.x + parX + Math.sin(t * 0.12) * 0.6 + (1 - e) * 4.0,
            camBase.y + parY + Math.sin(t * 0.09) * 0.15 + (1 - e) * 2.5,
            camBase.z + (1 - e) * 5.0
        );
        camera.lookAt(lookBase.x + Math.sin(t * 0.1) * 0.5, lookBase.y, lookBase.z);

        renderer.render(scene, camera);
    }
    requestAnimationFrame(animate);
})();