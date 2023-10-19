const googleDriveAPI = require('./google-drive-api');

// Initialize with your credentials
googleDriveAPI.initializeDriveAPI(YOUR_CLIENT_ID, YOUR_CLIENT_SECRET, YOUR_REDIRECT_URL, YOUR_ACCESS_TOKEN);


googleDriveAPI.listFiles((err, files) => {
    if (err) {
        console.error(err);
        return;
    }
    console.log(files);
});

document.addEventListener('DOMContentLoaded', function() {
    // Your JS code here

    // Example: Add event listener to a button
    let createFolderBtn = document.querySelector('.create-folder-modal');
    if (createFolderBtn) {
        createFolderBtn.addEventListener('click', function() {
            // Logic to create a folder
        });
    }
});
