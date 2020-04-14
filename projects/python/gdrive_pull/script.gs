function doPost(e) {
  return (function(id){
    var file = DriveApp.getFileById(id);
    return ContentService
          .createTextOutput(JSON.stringify({
            result: file.getBlob().getBytes(),
            name: file.getName(),
            mimeType: file.getBlob().getContentType()
          }))
          .setMimeType(ContentService.MimeType.JSON);
  })(e.parameters.id);
}