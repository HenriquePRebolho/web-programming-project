import * as THREE from 'three';
import {
  WebGLRenderer, PerspectiveCamera, Scene, BoxGeometry, MeshPhongMaterial, Mesh, DirectionalLight,
} from 'three';

////////////////////////////////////
// SCENE ///////////////////////////
const scene = new THREE.Scene();        // FOV, aspect ratio            clipping plane: near  far   
const camera = new THREE.PerspectiveCamera(75, window.screen.width / window.innerHeight, 0.1, 1000);
const cameraBox = new THREE.Box3();
camera.position.z = 10;

const renderer = new THREE.WebGLRenderer();
renderer.setSize(window.innerWidth, window.innerHeight);
document.body.appendChild(renderer.domElement);

// White directional light at half intensity shining from the top.
const directionalLight = new THREE.DirectionalLight( 0xffffff, 10 );
directionalLight.position.set(-10, -8, 3);
scene.add(directionalLight);

// From top right
const directionalLight2 = new THREE.DirectionalLight( 0xffffff, 1 );
directionalLight2.position.set(10, -8, 3);
scene.add(directionalLight2);

// For bottom
const directionalLight3 = new THREE.DirectionalLight( 0xf1ffff, 10 );
directionalLight3.position.set(0, -10, -20);
scene.add(directionalLight3);
////////////////////////////////////


////////////////////////////////////
// VARIABLES ///////////////////////
const max_x = 18;
const rows_number = 10;
let xs = [[], [], [], [], [], [], [], [], [], []];
let rows = [[], [], [], [], [], [], [], [], [], []];
let cube_number = 0;
let z_coords = 0;
let initial_loop = true;
let reached_final_z = -1;
let isWebInScene = false;
let shootingWeb = false;
let attachedCube;
let cameraCollision = false;
let pendulumAngle;      // current angle from vertical
let angularVelocity;    // how fast the angle is changing
let ropeLength;
let mouseX = 0;
let mouseY = 0;
let mouseDown = false;
let web;
let webX = 0.1; 
let webY = 0.05; 
let webZ = 0.1; 
let webSpeed = 7;
let anchorPoint;
let borders = [];
let anchorBlockZ;
let points = 0;
////////////////////////////////////


////////////////////////////////////
// BLOCKS //////////////////////////
function defineRowsXs() {
    let row = 0;
    while (row < rows_number) {
        defineXs(row);
        row += 1;
    }
    return;
}


function defineXs(row) {
    let sum = 0;
    while (sum <= max_x) {
        const size_x = getRndInteger(1, 5);
        if (sum + size_x <= max_x) {
            sum += size_x;
            xs[row].push(size_x);
        } else {
            break;
        }
    }
    return;
}


function generateRowsCubes(){
    let row = 0;
    while (row < rows_number) {
        generateCubes(row);
        row += 1;
    }
    return;
}


function generateCubes(row) {
    let cube_number = 0;
    while (cube_number < xs.length) {
        defineCube(row, cube_number);
        cube_number += 1;
    }
    return;
}


function defineCube(row, cube_number) {
    const size_x = xs[row][cube_number];
    const size_y = getRndInteger(2, 20);
    const size_z = getRndInteger(1, 4);
    
    const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
    const material = new THREE.MeshLambertMaterial({  emissive: 0x000000, });
    const cube = new THREE.Mesh(geometry, material);
    
    let next_x = 0;
    for (let i = 0; i < cube_number; i++) {
        next_x += xs[row][i];
    }
    cube.position.x = -16 + next_x*2 + xs[row][cube_number];
    cube.position.y = 8;  // 50
    cube.position.z = row*5 - 50;

    cube.material.color = new THREE.Color().setRGB( Math.random(), Math.random(), Math.random());

    scene.add(cube);
    rows[row].push(cube);

    return;
}


function forwardZs(speed) {
    let row = 0;
    while (row < rows_number) {
        let cube_number = 0;
        while (cube_number < rows.length) {
            forwardZ(row, cube_number, speed);
            cube_number += 1;
        }
        row += 1;
    }
    return;
}


function forwardZ(row, cube_number, speed) {
    rows[row][cube_number].position.z += speed;
    if (rows[row][cube_number].position.z > camera.position.z) { // i == rows.length-1 && 
        reached_final_z = row;
    }
    return;
}


function sendRowBackAndRearrangeXs(row) {
    xs = [[], [], [], [], [], [], [], [], [], []];
    defineXs(row);
    let cube_number = 0;
    while (cube_number < rows[row].length) {

        // Accessing the original dimensions
        const original_size_x = rows[row][cube_number].geometry.parameters.width;
        const original_size_y = rows[row][cube_number].geometry.parameters.height;
        const original_size_z = rows[row][cube_number].geometry.parameters.depth;

        const size_x = xs[row][cube_number];
        const size_y = getRndInteger(2, 20);
        const size_z = getRndInteger(1, 4);
        rows[row][cube_number].scale.set(size_x / original_size_x, size_y / original_size_y, size_z/ original_size_z);

        rows[row][cube_number].material.color = new THREE.Color().setRGB( Math.random(), Math.random(), Math.random());

        let next_x = 0;
        for (let i = 0; i < cube_number; i++) {
            next_x += xs[row][i];
        }
        rows[row][cube_number].position.x = -16 + next_x*2 + xs[cube_number];
        rows[row][cube_number].position.z = -50;

        cube_number += 1;
    }
    reached_final_z = -1;
    return;
}
////////////////////////////////////


////////////////////////////////////
// BORDERS /////////////////////////
function generateBorders() {
    let border = 0; // 0: up, 1: down, 2: left, 3: right 
    while (border < 4) {
        defineWorldBorder(border);
        border += 1;
    }
    return;
}


function defineWorldBorder(border) {
    // 0: up, 1: down, 2: left, 3: right 
    const size_x = (border == 0 || border == 1) ? 40 : 2;
    const size_y = (border == 0 || border == 1) ? 2 : 40;
    const size_z = 650;
    
    
    if (border == 0) { // up
        const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
        const material = new THREE.MeshLambertMaterial({  emissive: 0x000000, });
        const cube = new THREE.Mesh(geometry, material);
        cube.position.x = 0;
        cube.position.y = 10;
        cube.position.z = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 0);
        scene.add(cube);
        borders.push(cube);
    } else if (border == 1) { // down
        const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
        const material = new THREE.MeshBasicMaterial();
        const cube = new THREE.Mesh(geometry, material);
        cube.position.x = 0;
        cube.position.y = -20;
        cube.position.z = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 1);
        scene.add(cube);
        borders.push(cube);
    } else if (border == 2) { // left
        const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
        const material = new THREE.MeshLambertMaterial({  emissive: 0x000000, });
        const cube = new THREE.Mesh(geometry, material);
        cube.position.x = -18;
        cube.position.y = 0;
        cube.position.z = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 0);
        scene.add(cube);
        borders.push(cube);
    } else { // right
        const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
        const material = new THREE.MeshLambertMaterial({  emissive: 0x000000, });
        const cube = new THREE.Mesh(geometry, material);
        cube.position.x = 18;
        cube.position.y = 0;
        cube.position.z = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 0);
        scene.add(cube);
        borders.push(cube);
    }
    //cube.position.z = 0;

    //cube.material.color = new THREE.Color().setRGB(1, 0, 0);

    return;
}
////////////////////////////////////


////////////////////////////////////
// WEB /////////////////////////////
function generateWeb() {
    const geometry = new THREE.BoxGeometry(webX, webY, webZ);
    const material = new THREE.MeshLambertMaterial({  emissive: 0x000000, });
    web = new THREE.Mesh(geometry, material);

    web.material.color = new THREE.Color().setRGB(1, 1, 1);
    return;
}


function addWebToScene() {
    web.position.x = camera.position.x;
    web.position.y = camera.position.y;
    web.position.z = 9;

    // rotate x and z based on mouse position
    const screenWidth = window.screen.width;
    const screenHeight = window.screen.height;
    
    const ndcX = (mouseX / window.innerWidth) * 2 - 1;   // -1 (left) to +1 (right)
    const ndcY = -(mouseY / window.innerHeight) * 2 + 1;  // -1 (bottom) to +1 (top)

    web.rotation.z = -Math.atan2(ndcX, ndcY); // tilt left/right
    web.rotation.x = -Math.PI / 3;    // tilt up/down

    scene.add(web);
}


function scaleWeb() {
    web.scale.y += webSpeed;
    const currentLength = webY * web.scale.y;

    // Get the web's local "up" direction in world space
    const direction = new THREE.Vector3(0, 1, 0);
    direction.applyEuler(web.rotation);

    // Pin base to camera, extend tip along the rotated direction
    web.position.x = camera.position.x + direction.x * (currentLength / 2);
    web.position.y = camera.position.y + direction.y * (currentLength / 2);
    //web.position.z = camera.position.z + direction.z * (currentLength / 2);
}


function checkWebCollision() {
    const direction = new THREE.Vector3(0, 1, 0);
    direction.applyEuler(web.rotation);
    const currentLength = 0.1 * web.scale.y;

    // Compute actual tip and base in world space
    const base = camera.position.clone();
    const tip = new THREE.Vector3(
        camera.position.x + direction.x * currentLength,
        camera.position.y + direction.y * currentLength,
        camera.position.z + direction.z * currentLength
    );

    // Build box from those two real points
    const webBox = new THREE.Box3().setFromPoints([base, tip]);
    webBox.expandByScalar(0.2); // small tolerance

    for (let row of rows) {
        for (let cube of row) {
            cube.updateMatrixWorld();
            const cubeBB = new THREE.Box3().setFromObject(cube);
            
            if (webBox.intersectsBox(cubeBB) ||
                webBox.intersectsBox(topBB) ||
                webBox.intersectsBox(bottomBB) ||
                webBox.intersectsBox(leftBB) ||
                webBox.intersectsBox(rightBB)
            ) {
                shootingWeb = false;
                attachedCube = cube;
                anchorPoint = tip.clone();
                // In checkWebCollision(), replace ropeLength and pendulumAngle with:
                const dx = camera.position.x - anchorPoint.x;
                const dy = camera.position.y - anchorPoint.y;
                ropeLength = Math.sqrt(dx * dx + dy * dy); // true distance from camera to anchor
                pendulumAngle = Math.atan2(dx, -dy);       // angle consistent with sin/cos formula
                // Inside checkWebCollision(), replace angularVelocity = 0.03 with:
                const prevAngle = Math.atan2(
                    camera.position.x - anchorPoint.x,
                    camera.position.y - anchorPoint.y  // wait this is wrong
                );
                // Use the camera's current horizontal movement direction as initial kick
                angularVelocity = -0.03 * Math.sign(camera.position.x - anchorPoint.x) || 0.03;

                anchorBlockZ = attachedCube.position.z;
                return;
            }
        }
    }
}


function destroyWeb() {
    scene.remove(web);
    web.scale.set(1, 1, 1);
    web.rotation.set(0, 0, 0);
}
////////////////////////////////////


////////////////////////////////////
// CAMERA ///////////////////////
function fall() {
    camera.position.y -= 0.03;
    return;
}


function moveCamera() {
    const GRAVITY = 0.005;

    angularVelocity -= (GRAVITY / ropeLength) * Math.sin(pendulumAngle);
    angularVelocity *= 0.99;
    pendulumAngle += angularVelocity;
    pendulumAngle = Math.max(-Math.PI * 0.75, Math.min(Math.PI * 0.75, pendulumAngle));

    camera.position.x = anchorPoint.x + ropeLength * Math.sin(pendulumAngle);
    camera.position.y = anchorPoint.y - ropeLength * Math.cos(pendulumAngle);

    // Boost camera up when pendulum loses energy
    if (Math.abs(angularVelocity) < 0.005) {
        camera.position.y += 1; // tune this
        ropeLength = camera.position.distanceTo(anchorPoint); // keep rope consistent
    }
}

// todo: borders as well
function checkCameraCollision() {
    cameraBox.setFromCenterAndSize(
        camera.position,
        new THREE.Vector3(1, 1, 1) // tune this to feel right
    );

    for (let row of rows) {
        for (let cube of row) {
            const cubeBox = new THREE.Box3().setFromObject(cube);
            if (cameraBox.intersectsBox(cubeBox) || 
                cameraBox.intersectsBox(topBB) ||
                cameraBox.intersectsBox(bottomBB) ||
                cameraBox.intersectsBox(leftBB) ||
                cameraBox.intersectsBox(rightBB)
            ) {
                cameraCollision = true;
                return;
            }
        }
    }
}
////////////////////////////////////


////////////////////////////////////
// LISTENERS ///////////////////////
document.addEventListener('mousedown', (e) => {
    if (e.button === 0) {
        mouseDown = true;
    }
})
document.addEventListener('mouseup', (e) => {
    if (e.button === 0) {
        mouseDown = false;
    }
})
document.addEventListener('mousemove', function(event) {
    mouseX = event.clientX;
    mouseY = event.clientY;
});
////////////////////////////////////


////////////////////////////////////
// AUX /////////////////////////////
function getRndInteger(min, max) {
  return Math.floor(Math.random() * (max - min + 1) ) + min;
}
////////////////////////////////////


////////////////////////////////////
// METHOD CALLS ////////////////////
generateBorders();
generateWeb();
const topBB = new THREE.Box3().setFromObject(borders[0]);
const bottomBB = new THREE.Box3().setFromObject(borders[1]);
const leftBB = new THREE.Box3().setFromObject(borders[2]);
const rightBB = new THREE.Box3().setFromObject(borders[3]);
////////////////////////////////////


////////////////////////////////////
// TESTS ///////////////////////////

////////////////////////////////////


////////////////////////////////////
// ANIMATION ///////////////////////
function animate(time) {
    if (mouseDown) {
        if (!isWebInScene) {
            addWebToScene();
            isWebInScene = true;
            shootingWeb = true;
        }
        else {
            if (shootingWeb) {
                fall();
                scaleWeb();
                checkWebCollision();
            } else {
                if (!shootingWeb && attachedCube) {
                    if (Math.abs(attachedCube.position.z - anchorBlockZ) > 5) {
                        // block was recycled to the back
                        destroyWeb();
                        isWebInScene = false;
                        shootingWeb = false;
                        attachedCube = null;
                    }
                }
                moveCamera();
                //forwardZs(0.01);
            }
        } 
    } else {
        destroyWeb();
        fall();
        isWebInScene = false;
    }

    if (initial_loop) {
        rows = [[], [], [], [], [], [], [], [], [], []];
        xs = [[], [], [], [], [], [], [], [], [], []];
        defineRowsXs();
        generateRowsCubes();
        initial_loop = false;
    } else {
        forwardZs(0.1);
        if (reached_final_z !== -1) {
            sendRowBackAndRearrangeXs(reached_final_z);
        }
    }
    renderer.render(scene, camera);

    points += 1;
    
    checkCameraCollision();
    if (cameraCollision) {
        points = Math.floor(points/60)
        window.alert("you lost: " + points + " points");
        return points;
    }
}
renderer.setAnimationLoop(animate);
////////////////////////////////////

