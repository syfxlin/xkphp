const path = require("path");
const fs = require("fs");

const except = ["index.php"];

function deleteFolder(filePath) {
  if (fs.existsSync(filePath)) {
    if (fs.statSync(filePath).isFile()) {
      fs.unlinkSync(filePath);
      return;
    }
    const files = fs.readdirSync(filePath);
    files.forEach(file => {
      const nextFilePath = `${filePath}/${file}`;
      const states = fs.statSync(nextFilePath);
      if (states.isDirectory()) {
        deleteFolder(nextFilePath);
      } else {
        fs.unlinkSync(nextFilePath);
      }
    });
    fs.rmdirSync(filePath);
  }
}

fs.readdirSync(path.resolve(__dirname, "public")).forEach(item => {
  if (except.indexOf(item) === -1) {
    deleteFolder(path.resolve(__dirname, `public/${item}`));
  }
});
