/**
 * Post-build script for Perfex CRM
 * Runs after Laravel Mix compilation to perform cleanup and optimization tasks
 */

import { readdir, stat, unlink } from 'fs/promises';
import { join } from 'path';

const BUILDS_DIR = './assets/builds';

/**
 * Remove old/duplicate build artifacts
 */
async function cleanupBuilds() {
    try {
        const files = await readdir(BUILDS_DIR);
        
        // Remove any .map files in production (optional - comment out if you need sourcemaps)
        // const mapFiles = files.filter(f => f.endsWith('.map'));
        // for (const file of mapFiles) {
        //     await unlink(join(BUILDS_DIR, file));
        //     console.log(`Removed sourcemap: ${file}`);
        // }
        
        console.log('✓ Build completed successfully');
        console.log(`  Assets directory: ${BUILDS_DIR}`);
        console.log(`  Total files: ${files.length}`);
        
    } catch (error) {
        // Directory might not exist yet on first build
        if (error.code !== 'ENOENT') {
            console.error('Build cleanup error:', error.message);
        }
    }
}

/**
 * Display build summary
 */
async function displaySummary() {
    try {
        const files = await readdir(BUILDS_DIR);
        const cssFiles = files.filter(f => f.endsWith('.css'));
        const jsFiles = files.filter(f => f.endsWith('.js'));
        
        console.log('\n📦 Build Summary:');
        console.log(`   CSS files: ${cssFiles.length}`);
        console.log(`   JS files: ${jsFiles.length}`);
        console.log('');
    } catch (error) {
        // Silently ignore if directory doesn't exist
    }
}

// Run post-build tasks
await cleanupBuilds();
await displaySummary();
