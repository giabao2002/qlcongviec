function createFileFromData(fileObject, index) {
  var fileData = atob(fileObject.data); // decode base64 data
  var array = [];
  for (var i = 0; i < fileData.length; i++) {
    array.push(fileData.charCodeAt(i));
  }
  var fileBlob = new Blob([new Uint8Array(array)], { type: fileObject.type });

  // Tạo một đối tượng File từ Blob
  var file = new File([fileBlob], fileObject.name, {
    type: fileObject.type,
    lastModified: Date.now(),
  });

  // Thay thế thông tin file trong selectedFiles bằng đối tượng File
  selectedFiles[index] = file;
}

function checkFileSize(newFiles, selectedFiles, maxSizeMB) {
  var maxSizeBytes = maxSizeMB * 1024 * 1024;
  var totalSize = 0;
  newFiles.forEach((file) => {
    totalSize += file.size;
  });
  totalSize += selectedFiles.reduce((total, file) => total + file.size, 0);
  if (totalSize > maxSizeBytes) {
    alert_toast(
      "Tổng kích thước tệp không được vượt quá " + maxSizeMB + "MB.",
      "error"
    );
    return false;
  } else {
    return true;
  }
}

function renderPDF(selectedFiles) {
  let fileNames = selectedFiles.map((file, index) => {
    return `<div id="file-${index}">
                    <span>${file.name}</span>
                    <button style="border: none; background-color: transparent;" type="button" onclick="removeFile(${index})">x</button>
                </div>`;
  });
  $("#file-names").html(fileNames.join(""));
}

function removeFileFromList(index, selectedFiles) {
  return selectedFiles.filter((file, i) => i !== index);
}
