(function () {
    const canvas = document.getElementById('three-canvas');
    if (!canvas || typeof THREE === 'undefined') return;

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xcbd5e1);
    scene.fog = new THREE.FogExp2(0xcbd5e1, 0.006);

    const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 140);
    camera.position.set(4.5, 5.2, 13.5);
    camera.lookAt(-1.5, 1.8, -1.5);

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, powerPreference: 'high-performance' });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    // Balanced Industrial Lighting (No Glare)
    const hemiLight = new THREE.HemisphereLight(0xffffff, 0x475569, 0.65);
    scene.add(hemiLight);

    const dirLight = new THREE.DirectionalLight(0xffffff, 0.72);
    dirLight.position.set(8, 18, 12);
    dirLight.castShadow = true;
    dirLight.shadow.mapSize.width = 2048;
    dirLight.shadow.mapSize.height = 2048;
    dirLight.shadow.camera.near = 1;
    dirLight.shadow.camera.far = 45;
    const d = 16;
    dirLight.shadow.camera.left = -d;
    dirLight.shadow.camera.right = d;
    dirLight.shadow.camera.top = d;
    dirLight.shadow.camera.bottom = -d;
    scene.add(dirLight);

    // Shared Materials (Matching Reference Photo)
    const floorGreen = new THREE.MeshStandardMaterial({ color: 0x5c9478, roughness: 0.6, metalness: 0.08 });
    const safetyYellow = new THREE.MeshStandardMaterial({ color: 0xfacc15, roughness: 0.4 });
    const steelBlue = new THREE.MeshStandardMaterial({ color: 0x1d4ed8, metalness: 0.45, roughness: 0.4 });
    const craneYellow = new THREE.MeshStandardMaterial({ color: 0xeab308, metalness: 0.35, roughness: 0.4 });
    const pressCream = new THREE.MeshStandardMaterial({ color: 0xf3e8d2, roughness: 0.45, metalness: 0.15 });
    const darkMetal = new THREE.MeshStandardMaterial({ color: 0x334155, metalness: 0.65, roughness: 0.35 });
    const robotOrange = new THREE.MeshStandardMaterial({ color: 0xf97316, metalness: 0.4, roughness: 0.35 });
    const carBodyMat = new THREE.MeshStandardMaterial({ color: 0xf1f5f9, metalness: 0.35, roughness: 0.25, emissive: 0x334155, emissiveIntensity: 0.15 });
    const carTrimMat = new THREE.MeshStandardMaterial({ color: 0xcbd5e1, metalness: 0.3, roughness: 0.3, emissive: 0x1e293b, emissiveIntensity: 0.1 });
    const safetyOrange = new THREE.MeshStandardMaterial({ color: 0xfb923c, roughness: 0.6 });
    const shirtBlue = new THREE.MeshStandardMaterial({ color: 0x2563eb, roughness: 0.7 });
    const whiteUniform = new THREE.MeshStandardMaterial({ color: 0xf8fafc, roughness: 0.7 });
    const pantsDark = new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.8 });
    const skinMat = new THREE.MeshStandardMaterial({ color: 0xf1c27d, roughness: 0.6 });
    const accentCyan = new THREE.MeshStandardMaterial({ color: 0x0ea5e9, emissive: 0x0284c7, emissiveIntensity: 0.6 });

    function enableShadows(obj) {
        obj.traverse((child) => {
            if (child.isMesh) {
                child.castShadow = true;
                child.receiveShadow = true;
            }
        });
    }

    // ==========================================
    // 1. GREEN EPOXY FLOOR & YELLOW WALKWAY LINES
    // ==========================================
    const floor = new THREE.Mesh(new THREE.PlaneGeometry(90, 90), floorGreen);
    floor.rotation.x = -Math.PI / 2;
    floor.receiveShadow = true;
    scene.add(floor);

    const grid = new THREE.GridHelper(60, 48, 0x43735b, 0x4d8067);
    grid.position.y = 0.01;
    scene.add(grid);

    // Double Yellow Safety Walkway Lines
    [-0.4, 2.2].forEach((zPos) => {
        const line = new THREE.Mesh(new THREE.PlaneGeometry(34, 0.14), safetyYellow);
        line.rotation.x = -Math.PI / 2;
        line.position.set(-2, 0.015, zPos);
        scene.add(line);
    });

    // ==========================================
    // 2. BLUE ROOF TRUSSES, COLUMNS, CRANE & LAMPS
    // ==========================================
    const structureGroup = new THREE.Group();

    // Blue Steel Columns & Yellow Overhead Crane Rails
    for (let x = -12; x <= 10; x += 5.5) {
        const col = new THREE.Mesh(new THREE.BoxGeometry(0.35, 7.8, 0.35), steelBlue);
        col.position.set(x, 3.9, -5.8);
        structureGroup.add(col);

        // Roof Truss Beam
        const truss = new THREE.Mesh(new THREE.BoxGeometry(0.3, 0.55, 16), steelBlue);
        truss.position.set(x, 7.6, -1.0);
        structureGroup.add(truss);

        // Hanging High-Bay Dome Lamp
        const cord = new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 1.3, 6), darkMetal);
        cord.position.set(x, 6.8, -0.5);
        const shade = new THREE.Mesh(new THREE.ConeGeometry(0.45, 0.35, 16, 1, true), carTrimMat);
        shade.position.set(x, 6.1, -0.5);
        const bulb = new THREE.Mesh(new THREE.SphereGeometry(0.14, 10, 10), new THREE.MeshBasicMaterial({ color: 0xffffff }));
        bulb.position.set(x, 6.02, -0.5);
        structureGroup.add(cord, shade, bulb);
    }

    // Yellow Overhead Crane Rail along the press line
    const craneRail = new THREE.Mesh(new THREE.BoxGeometry(28, 0.45, 0.45), craneYellow);
    craneRail.position.set(-1, 5.4, -4.6);
    structureGroup.add(craneRail);

    // Back Wall
    const backWall = new THREE.Mesh(
        new THREE.BoxGeometry(40, 8, 0.3),
        new THREE.MeshStandardMaterial({ color: 0xcbd5e1, roughness: 0.85 })
    );
    backWall.position.set(0, 4, -7.8);
    structureGroup.add(backWall);

    enableShadows(structureGroup);
    scene.add(structureGroup);

    // ==========================================
    // 3. ROW OF CREAM AIDA-STYLE STAMPING PRESSES
    // ==========================================
    // Hazard Stripe Texture (Yellow-Black Police/Safety Line)
    const hzCanvas = document.createElement('canvas');
    hzCanvas.width = 128;
    hzCanvas.height = 32;
    const hzCtx = hzCanvas.getContext('2d');
    hzCtx.fillStyle = '#facc15';
    hzCtx.fillRect(0, 0, 128, 32);
    hzCtx.fillStyle = '#0f172a';
    for (let i = -32; i < 160; i += 24) {
        hzCtx.beginPath();
        hzCtx.moveTo(i, 32);
        hzCtx.lineTo(i + 12, 32);
        hzCtx.lineTo(i + 28, 0);
        hzCtx.lineTo(i + 16, 0);
        hzCtx.closePath();
        hzCtx.fill();
    }
    const hazardTex = new THREE.CanvasTexture(hzCanvas);
    hazardTex.wrapS = THREE.RepeatWrapping;
    hazardTex.repeat.set(3, 1);
    const hazardMat = new THREE.MeshStandardMaterial({ map: hazardTex, roughness: 0.4 });

    const stampingPresses = [];
    function createStampingPress(x, z) {
        const press = new THREE.Group();
        press.position.set(x, 0, z);

        // Cream/Beige Main Frame
        const base = new THREE.Mesh(new THREE.BoxGeometry(2.3, 0.9, 1.8), pressCream);
        base.position.y = 0.45;
        const colL = new THREE.Mesh(new THREE.BoxGeometry(0.48, 3.2, 1.6), pressCream);
        colL.position.set(-0.88, 2.5, 0);
        const colR = new THREE.Mesh(new THREE.BoxGeometry(0.48, 3.2, 1.6), pressCream);
        colR.position.set(0.88, 2.5, 0);
        const crown = new THREE.Mesh(new THREE.BoxGeometry(2.4, 0.9, 1.85), pressCream);
        crown.position.y = 4.35;

        // Animated Ram + Yellow-Black Hazard Strip at Bottom
        const ram = new THREE.Group();
        ram.position.y = 2.4;
        const ramBody = new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.7, 1.1), darkMetal);
        const ramHazard = new THREE.Mesh(new THREE.BoxGeometry(1.22, 0.12, 1.12), hazardMat);
        ramHazard.position.y = -0.32;
        ram.add(ramBody, ramHazard);

        // Side Inspection / SOP Board (White & Red)
        const sopBoard = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.95, 0.65), whiteUniform);
        sopBoard.position.set(1.18, 2.4, 0.3);
        const sopHeader = new THREE.Mesh(new THREE.BoxGeometry(0.07, 0.14, 0.65), new THREE.MeshBasicMaterial({ color: 0xdc2626 }));
        sopHeader.position.set(1.18, 2.82, 0.3);

        press.add(base, colL, colR, crown, ram, sopBoard, sopHeader);
        enableShadows(press);
        scene.add(press);

        const data = { press, ram };
        stampingPresses.push(data);
        return data;
    }

    createStampingPress(-7.5, -3.2);
    createStampingPress(-4.5, -3.2);
    createStampingPress(-1.5, -3.2);
    createStampingPress(1.5, -3.2);

    // ==========================================
    // 4. COLORFUL PLASTIC CRATES & BLUE STILLAGE RACKS
    // ==========================================
    function createCrateStack(x, z, colorHex, count = 3) {
        const stack = new THREE.Group();
        stack.position.set(x, 0, z);
        const mat = new THREE.MeshStandardMaterial({ color: colorHex, roughness: 0.55 });
        for (let i = 0; i < count; i++) {
            const crate = new THREE.Mesh(new THREE.BoxGeometry(0.55, 0.32, 0.42), mat);
            crate.position.y = 0.16 + i * 0.33;
            stack.add(crate);
        }
        enableShadows(stack);
        scene.add(stack);
    }

    createCrateStack(-6.0, -1.6, 0xfacc15, 3); // Yellow
    createCrateStack(-5.3, -1.6, 0x2563eb, 2); // Blue
    createCrateStack(-3.0, -1.6, 0x16a34a, 3); // Green
    createCrateStack(-2.3, -1.6, 0xfacc15, 2); // Yellow

    // Helper: Stamped Car Body Part (Silver)
    function createCarBodyPart() {
        const part = new THREE.Group();
        const lowerPanel = new THREE.Mesh(new THREE.BoxGeometry(0.85, 0.32, 0.55), carBodyMat);
        lowerPanel.position.y = 0.16;
        const upperFrame = new THREE.Mesh(new THREE.BoxGeometry(0.55, 0.24, 0.5), carBodyMat);
        upperFrame.position.set(-0.04, 0.44, 0);
        const windowCut = new THREE.Mesh(new THREE.BoxGeometry(0.38, 0.16, 0.52), carTrimMat);
        windowCut.position.set(-0.04, 0.44, 0);
        part.add(lowerPanel, upperFrame, windowCut);
        return part;
    }

    // Blue Stillage Rack with Stacked Silver Car Parts (Foreground Right-Center)
    const rack = new THREE.Group();
    rack.position.set(0.2, 0, 3.6);
    const rackBase = new THREE.Mesh(new THREE.BoxGeometry(2.6, 0.15, 1.1), steelBlue);
    rackBase.position.y = 0.08;
    rack.add(rackBase);
    for (let i = 0; i < 4; i++) {
        const p = createCarBodyPart();
        p.rotation.y = Math.PI / 2;
        p.scale.setScalar(0.85);
        p.position.set(-0.9 + i * 0.6, 0.2, 0);
        rack.add(p);
    }
    enableShadows(rack);
    scene.add(rack);

    // ==========================================
    // 5. CONVEYOR & ROBOTIC ARM (SILVER CAR PARTS)
    // ==========================================
    const conveyorGroup = new THREE.Group();
    conveyorGroup.position.set(-3.5, 0.55, 0.9);
    scene.add(conveyorGroup);

    const beltBase = new THREE.Mesh(new THREE.BoxGeometry(9.5, 0.18, 1.1), darkMetal);
    conveyorGroup.add(beltBase);
    for (let x = -4; x <= 4; x += 2) {
        const leg = new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.55, 0.95), darkMetal);
        leg.position.set(x, -0.275, 0);
        conveyorGroup.add(leg);
    }

    const partsOnBelt = [];
    for (let i = 0; i < 4; i++) {
        const carPart = createCarBodyPart();
        carPart.scale.setScalar(0.8);
        carPart.position.set(-4 + i * 2.5, 0.1, 0);
        enableShadows(carPart);
        conveyorGroup.add(carPart);
        partsOnBelt.push(carPart);
    }

    // Robotic Arm
    function createRoboticArm(x, z) {
        const root = new THREE.Group();
        root.position.set(x, 0, z);
        const base = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.6, 0.35, 16), darkMetal);
        base.position.y = 0.175;
        root.add(base);

        const turret = new THREE.Group();
        turret.position.y = 0.35;
        const turretMesh = new THREE.Mesh(new THREE.CylinderGeometry(0.36, 0.42, 0.35, 16), robotOrange);
        turretMesh.position.y = 0.175;
        turret.add(turretMesh);
        root.add(turret);

        const lowerPivot = new THREE.Group();
        lowerPivot.position.y = 0.35;
        const lowerArm = new THREE.Mesh(new THREE.BoxGeometry(0.24, 1.55, 0.24), robotOrange);
        lowerArm.position.y = 0.775;
        lowerPivot.add(lowerArm);
        turret.add(lowerPivot);

        const upperPivot = new THREE.Group();
        upperPivot.position.y = 1.55;
        const upperArm = new THREE.Mesh(new THREE.BoxGeometry(0.2, 1.25, 0.2), robotOrange);
        upperArm.position.y = 0.625;
        const gripper = new THREE.Mesh(new THREE.ConeGeometry(0.13, 0.28, 8), darkMetal);
        gripper.position.y = 1.32;
        upperPivot.add(upperArm, gripper);
        lowerPivot.add(upperPivot);

        enableShadows(root);
        scene.add(root);
        return { root, turret, lowerPivot, upperPivot };
    }

    const arm1 = createRoboticArm(-3.5, -0.4);

    // ==========================================
    // 6. WORKERS IN WHITE UNIFORMS & GENBA BOARD
    // ==========================================
    function createWorker(x, z, rotY, hasTablet = false) {
        const worker = new THREE.Group();
        worker.position.set(x, 0, z);
        worker.rotation.y = rotY;

        const legGeo = new THREE.CylinderGeometry(0.1, 0.085, 0.8, 10);
        const legL = new THREE.Mesh(legGeo, pantsDark);
        legL.position.set(-0.12, 0.4, 0);
        const legR = new THREE.Mesh(legGeo, pantsDark);
        legR.position.set(0.12, 0.4, 0);
        worker.add(legL, legR);

        const torso = new THREE.Mesh(new THREE.BoxGeometry(0.42, 0.58, 0.24), shirtBlue);
        torso.position.y = 1.1;
        const vest = new THREE.Mesh(new THREE.BoxGeometry(0.44, 0.52, 0.26), safetyOrange);
        vest.position.y = 1.09;
        const stripe = new THREE.Mesh(new THREE.BoxGeometry(0.45, 0.055, 0.27), whiteUniform);
        stripe.position.y = 1.0;
        worker.add(torso, vest, stripe);

        const headGroup = new THREE.Group();
        headGroup.position.y = 1.54;
        const head = new THREE.Mesh(new THREE.SphereGeometry(0.16, 14, 14), skinMat);
        const cap = new THREE.Mesh(new THREE.SphereGeometry(0.18, 14, 10, 0, Math.PI * 2, 0, Math.PI * 0.55), safetyYellow);
        cap.position.y = 0.03;
        headGroup.add(head, cap);
        worker.add(headGroup);

        // Left & Right Arms (Pivoted at Shoulders)
        const armGeo = new THREE.CylinderGeometry(0.065, 0.055, 0.5, 10);
        const handGeo = new THREE.SphereGeometry(0.06, 10, 10);

        const armLPivot = new THREE.Group();
        armLPivot.position.set(-0.27, 1.32, 0);
        const armL = new THREE.Mesh(armGeo, shirtBlue);
        armL.position.y = -0.22;
        const handL = new THREE.Mesh(handGeo, skinMat);
        handL.position.y = -0.48;
        armLPivot.add(armL, handL);

        const armRPivot = new THREE.Group();
        armRPivot.position.set(0.27, 1.32, 0);
        const armR = new THREE.Mesh(armGeo, shirtBlue);
        armR.position.y = -0.22;
        const handR = new THREE.Mesh(handGeo, skinMat);
        handR.position.y = -0.48;
        armRPivot.add(armR, handR);

        worker.add(armLPivot, armRPivot);

        if (hasTablet) {
            const tablet = new THREE.Mesh(new THREE.BoxGeometry(0.28, 0.025, 0.2), darkMetal);
            tablet.position.set(0, 1.02, 0.34);
            tablet.rotation.x = 0.45;
            const tabScreen = new THREE.Mesh(new THREE.PlaneGeometry(0.24, 0.16), accentCyan);
            tabScreen.rotation.x = -Math.PI / 2;
            tabScreen.position.y = 0.014;
            tablet.add(tabScreen);
            worker.add(tablet);
        }

        enableShadows(worker);
        scene.add(worker);
        return { worker, headGroup, armLPivot, armRPivot };
    }

    const worker1 = createWorker(-6.2, -1.0, 0.3);
    const worker2 = createWorker(-1.2, -1.0, -0.3);
    const worker3 = createWorker(-6.8, 2.8, 0.5, true);

    // Digital TV Display (GENBA Checksheet Form)
    const tvGroup = new THREE.Group();
    tvGroup.position.set(-7.8, 0, 2.6);
    tvGroup.rotation.y = 0.35;

    // TV Floor Stand
    const tvBase = new THREE.Mesh(new THREE.BoxGeometry(1.1, 0.08, 0.65), darkMetal);
    tvBase.position.y = 0.04;
    const tvPole = new THREE.Mesh(new THREE.CylinderGeometry(0.06, 0.06, 0.8, 12), darkMetal);
    tvPole.position.set(0, 0.4, -0.02);
    tvGroup.add(tvBase, tvPole);

    // Sleek Black TV Bezel
    const tvBezel = new THREE.Mesh(new THREE.BoxGeometry(2.0, 1.35, 0.08), pantsDark);
    tvBezel.position.y = 1.45;
    tvGroup.add(tvBezel);

    // Canvas Texture for Digital Checksheet Form
    const tvCanvas = document.createElement('canvas');
    tvCanvas.width = 512;
    tvCanvas.height = 340;
    const ctx = tvCanvas.getContext('2d');

    // Screen background
    ctx.fillStyle = '#f8fafc';
    ctx.fillRect(0, 0, 512, 340);

    // Header bar
    ctx.fillStyle = '#1d4ed8';
    ctx.fillRect(0, 0, 512, 54);
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 24px sans-serif';
    ctx.fillText('GENBA CHECKSHEET', 24, 35);

    // Status pill
    ctx.fillStyle = '#22c55e';
    ctx.fillRect(390, 14, 98, 26);
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 14px sans-serif';
    ctx.fillText('LIVE', 422, 32);

    // Checksheet rows
    const items = ['1. Stamping Press', '2. Robotic Assy', '3. Safety Area', '4. Part Quality'];
    items.forEach((label, idx) => {
        const y = 76 + idx * 62;
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(20, y, 472, 48);
        ctx.strokeStyle = '#cbd5e1';
        ctx.lineWidth = 2;
        ctx.strokeRect(20, y, 472, 48);

        ctx.fillStyle = '#1e293b';
        ctx.font = 'bold 18px sans-serif';
        ctx.fillText(label, 36, y + 30);

        // Green OK badge
        ctx.fillStyle = '#dcfce7';
        ctx.fillRect(405, y + 10, 70, 28);
        ctx.fillStyle = '#16a34a';
        ctx.font = 'bold 16px sans-serif';
        ctx.fillText('OK ✓', 418, y + 30);
    });

    const tvTex = new THREE.CanvasTexture(tvCanvas);
    const tvScreen = new THREE.Mesh(
        new THREE.PlaneGeometry(1.86, 1.22),
        new THREE.MeshBasicMaterial({ map: tvTex })
    );
    tvScreen.position.set(0, 1.45, 0.045);
    tvGroup.add(tvScreen);

    enableShadows(tvGroup);
    scene.add(tvGroup);

    // ==========================================
    // 7. ANIMATED 3D FORKLIFTS (LEFT AREA)
    // ==========================================
    function createForklift(x, z, scale = 0.85) {
        const forklift = new THREE.Group();
        forklift.position.set(x, 0, z);
        forklift.scale.setScalar(scale);

        // Body & Counterweight
        const fkBody = new THREE.Mesh(new THREE.BoxGeometry(1.3, 0.65, 0.9), robotOrange);
        fkBody.position.y = 0.52;
        const fkHood = new THREE.Mesh(new THREE.BoxGeometry(0.55, 0.35, 0.85), darkMetal);
        fkHood.position.set(-0.35, 0.92, 0);

        // Overhead Guard / Roll Cage
        const cageTop = new THREE.Mesh(new THREE.BoxGeometry(0.95, 0.08, 0.85), darkMetal);
        cageTop.position.set(-0.1, 1.75, 0);
        [[-0.5, -0.38], [-0.5, 0.38], [0.32, -0.38], [0.32, 0.38]].forEach(([px, pz]) => {
            const post = new THREE.Mesh(new THREE.CylinderGeometry(0.035, 0.035, 0.95, 8), darkMetal);
            post.position.set(px, 1.28, pz);
            forklift.add(post);
        });

        // 4 Wheels
        const wheelGeo = new THREE.CylinderGeometry(0.24, 0.24, 0.18, 16);
        wheelGeo.rotateX(Math.PI / 2);
        [[-0.45, -0.48], [-0.45, 0.48], [0.45, -0.48], [0.45, 0.48]].forEach(([wx, wz]) => {
            const wheel = new THREE.Mesh(wheelGeo, pantsDark);
            wheel.position.set(wx, 0.24, wz);
            forklift.add(wheel);
        });

        // Front Mast & Forks
        const mastL = new THREE.Mesh(new THREE.BoxGeometry(0.1, 1.85, 0.1), darkMetal);
        mastL.position.set(0.7, 1.0, -0.28);
        const mastR = mastL.clone();
        mastR.position.z = 0.28;

        const forkL = new THREE.Mesh(new THREE.BoxGeometry(0.85, 0.06, 0.12), carTrimMat);
        forkL.position.set(1.12, 0.25, -0.22);
        const forkR = forkL.clone();
        forkR.position.z = 0.22;

        // Pallet + Silver Car Part on Forks
        const pallet = new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.1, 0.85), craneYellow);
        pallet.position.set(1.15, 0.33, 0);
        const fkPart = createCarBodyPart();
        fkPart.scale.setScalar(0.75);
        fkPart.position.set(1.15, 0.38, 0);

        forklift.add(fkBody, fkHood, cageTop, mastL, mastR, forkL, forkR, pallet, fkPart);
        enableShadows(forklift);
        scene.add(forklift);
        return forklift;
    }

    const forklift1 = createForklift(-16.5, -2.2, 0.85);
    const forklift2 = createForklift(-21.5, -4.0, 0.78);

    // Mouse Parallax & Resize
    let mouseX = 0;
    let mouseY = 0;
    window.addEventListener('mousemove', (e) => {
        mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
        mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
    });

    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });

    // Animation Loop
    const clock = new THREE.Clock();
    function animate() {
        requestAnimationFrame(animate);
        const t = clock.getElapsedTime();

        partsOnBelt.forEach((p) => {
            p.position.x += 0.02;
            if (p.position.x > 4.6) p.position.x = -4.6;
        });

        arm1.turret.rotation.y = Math.sin(t * 0.8) * 0.45;
        arm1.lowerPivot.rotation.x = 0.45 + Math.sin(t * 1.4) * 0.18;
        arm1.upperPivot.rotation.x = 1.75 + Math.cos(t * 1.4) * 0.28;

        stampingPresses.forEach((p, idx) => {
            p.ram.position.y = 2.05 + Math.sin(t * 2.4 + idx * 1.2) * 0.45;
        });

        [worker1, worker2, worker3].forEach((w, idx) => {
            w.headGroup.rotation.y = Math.sin(t * 0.8 + idx) * 0.22;
            w.armLPivot.rotation.x = -0.5 + Math.sin(t * 2.2 + idx) * 0.25;
            w.armRPivot.rotation.x = -0.5 + Math.cos(t * 2.2 + idx) * 0.25;
        });

        // 2 Forklifts patrolling in the left-back green floor area
        forklift1.position.x = -16.5 + Math.sin(t * 0.4) * 1.6;
        forklift1.rotation.y = Math.cos(t * 0.4) >= 0 ? 0 : Math.PI;

        forklift2.position.x = -21.5 + Math.cos(t * 0.35) * 1.6;
        forklift2.rotation.y = -Math.sin(t * 0.35) >= 0 ? 0 : Math.PI;

        camera.position.x += (4.5 + mouseX * 0.9 - camera.position.x) * 0.04;
        camera.position.y += (5.2 - mouseY * 0.6 - camera.position.y) * 0.04;
        camera.lookAt(-1.5, 1.8, -1.5);

        renderer.render(scene, camera);
    }
    animate();
})();
