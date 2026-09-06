const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
  fs.readdirSync(dir).forEach(f => {
    let dirPath = path.join(dir, f);
    let isDirectory = fs.statSync(dirPath).isDirectory();
    if (isDirectory) {
      walkDir(dirPath, callback);
    } else {
      callback(path.join(dir, f));
    }
  });
}

function fixImports(filePath) {
  if (!filePath.endsWith('.ts') && !filePath.endsWith('.tsx')) return;
  if (filePath.includes('node_modules') || filePath.includes('.next')) return;
  
  let content = fs.readFileSync(filePath, 'utf8');
  let updated = content
    .replace(/from\s+['"]\.\.\/.*?lib\/utils['"]/g, 'from "@/lib/utils"')
    .replace(/from\s+['"]\.\.\/.*?store\/jibikaStore['"]/g, 'from "@/store/jibikaStore"')
    .replace(/from\s+['"]\.\.\/.*?lib\/i18n['"]/g, 'from "@/lib/i18n"')
    .replace(/from\s+['"]\.\.\/.*?components\/(.*?)['"]/g, 'from "@/components/$1"')
    .replace(/from\s+['"]\.\.\/.*?data\/demoData['"]/g, 'from "@/data/demoData"')
    .replace(/from\s+['"]\.\.\/.*?types['"]/g, 'from "@/types"');

  if (content !== updated) {
    fs.writeFileSync(filePath, updated);
    console.log(`Updated ${filePath}`);
  }
}

walkDir('.', fixImports);
