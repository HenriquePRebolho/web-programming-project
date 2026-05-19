import * as THREE from 'three';
import {
  WebGLRenderer, PerspectiveCamera, Scene, BoxGeometry, MeshPhongMaterial, Mesh, DirectionalLight,
} from 'three';

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


const max_x = 18;
const rows_number = 10;
let xs = [[], [], [], [], [], [], [], [], [], []];
let rows = [[], [], [], [], [], [], [], [], [], []];
let cube_number = 0;
let z_coords = 0;
let initial_loop = true;
let reached_final_z = -1;


function getRndInteger(min, max) {
  return Math.floor(Math.random() * (max - min + 1) ) + min;
}

// Define a list of sizes x for each cube without letting them occupy more the what the screen can show
// Problem: objects from far will look like they have no wall 
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


function defineWorldBorder(border) {

    // 0: up, 1: down, 2: left, 3: right 
    const size_x = (border == 0 || border == 1) ? 40 : 2;
    const size_y = (border == 0 || border == 1) ? 2 : 40;
    const size_z = 650;
    
    const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
    const material = new THREE.MeshBasicMaterial();
    const cube = new THREE.Mesh(geometry, material);
    
    if (border == 0) { // up
        cube.position.x = 0;
        cube.position.y = 10;
        cube.material.color = new THREE.Color().setRGB(1, 0, 0);
    } else if (border == 1) { // down
        cube.position.x = 0;
        cube.position.y = -10;
        cube.material.color = new THREE.Color().setRGB(1, 1, 1);
    } else if (border == 2) { // left
        cube.position.x = -16;
        cube.position.y = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 0);
    } else { // right
        cube.position.x = 16;
        cube.position.y = 0;
        cube.material.color = new THREE.Color().setRGB(1, 0, 0);
    }
    cube.position.z = 0;

    //cube.material.color = new THREE.Color().setRGB(1, 0, 0);
    
    console.log(cube);
    scene.add(cube);
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

function generateCubes(row) {
    let cube_number = 0;
    while (cube_number < xs.length) {
        defineCube(row, cube_number);
        cube_number += 1;
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

function defineRowsXs() {
    let row = 0;
    while (row < rows_number) {
        defineXs(row);
        row += 1;
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




function animate(time) {

    if (initial_loop) {
        rows = [[], [], [], [], [], [], [], [], [], []];
        xs = [[], [], [], [], [], [], [], [], [], []];
        defineRowsXs();
        generateBorders();
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
}
renderer.setAnimationLoop(animate);
