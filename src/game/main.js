import * as THREE from 'three';
import {
  WebGLRenderer, PerspectiveCamera, Scene, BoxGeometry, MeshPhongMaterial, Mesh, DirectionalLight,
} from 'three';

////////////////////////////////////
// SCENE ///////////////////////////
const scene = new THREE.Scene();        // FOV, aspect ratio            clipping plane: near  far   
const camera = new THREE.PerspectiveCamera(75, window.screen.width / window.innerHeight, 0.1, 1000);
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
let webGrowFactor = 1;
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
    const size_y = getRndInteger(2, 20);  // 40, 70
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


function forwardZs() {
    let row = 0;
    while (row < rows_number) {
        let cube_number = 0;
        while (cube_number < rows.length) {
            forwardZ(row, cube_number);
            cube_number += 1;
        }
        row += 1;
    }
    return;
}


function forwardZ(row, cube_number) {
    rows[row][cube_number].position.z += 0.1;
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
    } else if (border == 1) { // down
        const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
        const material = new THREE.MeshBasicMaterial();
        const cube = new THREE.Mesh(geometry, material);
        cube.position.x = 0;
        cube.position.y = -10;
        cube.position.z = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 1);
        scene.add(cube);
    } else if (border == 2) { // left
        const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
        const material = new THREE.MeshLambertMaterial({  emissive: 0x000000, });
        const cube = new THREE.Mesh(geometry, material);
        cube.position.x = -18;
        cube.position.y = 0;
        cube.position.z = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 0);
        scene.add(cube);
    } else { // right
        const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
        const material = new THREE.MeshLambertMaterial({  emissive: 0x000000, });
        const cube = new THREE.Mesh(geometry, material);
        cube.position.x = 18;
        cube.position.y = 0;
        cube.position.z = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 0);
        scene.add(cube);
    }
    //cube.position.z = 0;

    //cube.material.color = new THREE.Color().setRGB(1, 0, 0);

    return;
}


function generateBorders() {
    let border = 0; // 0: up, 1: down, 2: left, 3: right 
    while (border < 4) {
        defineWorldBorder(border);
        border += 1;
    }
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
    web.rotation.x = -Math.PI / 4;    // tilt up/down

    scene.add(web);
}


function scaleWeb() {
    web.scale.y += webGrowFactor;
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
    const webBox = new THREE.Box3().setFromObject(web);
    for (let row of rows) {
        for (let cube of row) {
            const cubeBB = new THREE.Box3().setFromObject(cube);
            if (webBox.intersectsBox(cubeBB)) {
                window.alert("web collision");
                shootingWeb = false;
                attachedCube = cube;

                pendulumAngle = Math.atan2(
                    camera.position.x - attachedCube.position.x,
                    camera.position.y - attachedCube.position.y
                );
                ropeLength = camera.position.distanceTo(anchorPoint);
                angularVelocity = 0.03; // initial push, tune this

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
    camera.position.y -= 0.05;
    return;
}


function moveCamera() {
    const GRAVITY = 0.001; // tune for feel

    // Gravity pulls angle back toward vertical
    angularVelocity -= (GRAVITY / ropeLength) * Math.sin(pendulumAngle);

    pendulumAngle += angularVelocity;

    // Camera orbits around anchor
    camera.position.x = anchorPoint.x + ropeLength * Math.sin(pendulumAngle);
    camera.position.y = anchorPoint.y - ropeLength * Math.cos(pendulumAngle);
}

// todo: borders as well
function checkCameraCollision() {
    let row = 0;
    while (row < rows_number) {
        let cube_number = 0;
        while (cube_number < rows.length) {
            if (rows[row][cube_number].position.z == 9) { // if block in "same" z as camera z
                if ((rows[row][cube_number].position.x <= camera.position.x - 1 ||  
                     rows[row][cube_number].position.x >= camera.position.x + 1) &&
                    (8 - rows[row][cube_number].size.y >= camera.position.y - 1 ||  
                     8 - rows[row][cube_number].size.y <= camera.position.y + 1)) 
                {
                    cameraCollision = true;
                    break;
                }
            }
            cube_number += 1;
        }
        if (cameraCollision) {
            break;
        }
        row += 1;
    }
    return;
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
                moveCamera();
                forwardZs();
                checkCameraCollision();
                
                window.alert("here");
            }
        } 
    } else {
        destroyWeb();
        // fall();
        isWebInScene = false;
    }

    console.log(camera.position.y + " " + web.position.y);

    if (initial_loop) {
        rows = [[], [], [], [], [], [], [], [], [], []];
        xs = [[], [], [], [], [], [], [], [], [], []];
        defineRowsXs();
        generateRowsCubes();
        initial_loop = false;
    } else {
        forwardZs();
        if (reached_final_z !== -1) {
            sendRowBackAndRearrangeXs(reached_final_z);
            //reached_final_z = -1;
        }
    }
    renderer.render(scene, camera);
    if (cameraCollision) {
        window.alert("you lost :(");
    } 
}
renderer.setAnimationLoop(animate);
////////////////////////////////////
