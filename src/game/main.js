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

const directionalLight2 = new THREE.DirectionalLight( 0xffffff, 1 );
directionalLight2.position.set(10, -8, 3);
scene.add(directionalLight2);


const max_x = 18;
let xs = [];
let rows = [];
let cube_number = 0;
let z_coords = 0;
let initial_loop = true;
let reached_final_z = false;

function getRndInteger(min, max) {
  return Math.floor(Math.random() * (max - min + 1) ) + min;
}


// Define a list of sizes x for each cube without letting them occupy more the what the screen can show
// Problem: objects from far will look like they have no wall 
function defineXs() {
    let sum = 0;
    while (sum <= max_x) {
        const size_x = getRndInteger(1, 5);
        if (sum + size_x <= max_x) {
            sum += size_x;
            xs.push(size_x);
        } else {
            break;
        }
    }
    return;
}


function defineCube(cube_number) {
    const size_x = xs[cube_number];
    const size_y = getRndInteger(2, 20);
    const size_z = getRndInteger(1, 4);
    
    const geometry = new THREE.BoxGeometry(size_x, size_y, size_z);
    const material = new THREE.MeshLambertMaterial({  emissive: 0x000000, });
    const cube = new THREE.Mesh(geometry, material);
    
    let next_x = 0;
    for (let i = 0; i < cube_number; i++) {
        next_x += xs[i];
    }
    cube.position.x = -16 + next_x*2 + xs[cube_number];
    cube.position.y = 8;
    cube.position.z = -50;

    cube.material.color = new THREE.Color().setRGB( Math.random(), Math.random(), Math.random());

    scene.add(cube);
    rows.push(cube);

    return;
}


function forwardZ(cube_number) {
    rows[cube_number].position.z += 0.5;
    if (rows[cube_number].position.z > camera.position.z) { // i == rows.length-1 && 
        reached_final_z = true;
    }
    return;
}


function generateCubes() {
    let cube_number = 0;
    while (cube_number < xs.length) {
        defineCube(cube_number);
        cube_number += 1;
    }
    return;
}


function forwardZs() {
    let cube_number = 0;
    while (cube_number < rows.length) {
        forwardZ(cube_number);
        cube_number += 1;
    }
    return;
}


function sendRowsBackAndRearrangeXs() {
    xs = [];
    defineXs();
    let cube_number = 0;
    while (cube_number < rows.length) {

        // Accessing the original dimensions
        const original_size_x = rows[cube_number].geometry.parameters.width;
        const original_size_y = rows[cube_number].geometry.parameters.height;
        const original_size_z = rows[cube_number].geometry.parameters.depth;

        const size_x = xs[cube_number];
        const size_y = getRndInteger(2, 20);
        const size_z = getRndInteger(1, 4);
        rows[cube_number].scale.set(size_x / original_size_x, size_y / original_size_y, size_z/ original_size_z);

        rows[cube_number].material.color = new THREE.Color().setRGB( Math.random(), Math.random(), Math.random());
        
        let next_x = 0;
        for (let i = 0; i < cube_number; i++) {
            next_x += xs[i];
        }
        rows[cube_number].position.x = -16 + next_x*2 + xs[cube_number];
        rows[cube_number].position.z = -50;

        cube_number += 1;
    } 
    return;
}


function animate(time) {

    if (initial_loop) {
        rows = [];
        xs = [];
        defineXs();
        generateCubes();
        initial_loop = false;
    } else {
        forwardZs();
        if (reached_final_z) {
            //initial_loop = true;
            sendRowsBackAndRearrangeXs();
            reached_final_z = false;
        }
    }
    renderer.render(scene, camera);
}
renderer.setAnimationLoop(animate);
