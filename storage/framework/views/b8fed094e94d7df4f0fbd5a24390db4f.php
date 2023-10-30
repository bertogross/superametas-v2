<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Image Submission</title>
    <style>
        #results{
            width: 700px;
            height: 700px;
        }
    </style>
</head>
<body>
<form enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <input type="file" id="imageInput">
    <br>
    <textarea id="textInput"></textarea>
    <br>
    <button id="submitButton">Submit</button>
</form>

<div id="results"></div>

<script>
const resultsDiv = document.getElementById('results');
const inputFile = document.getElementById('imageInput');
const inputText = document.getElementById('textInput');

if (inputFile) {
    inputFile.addEventListener("change", function() {
        const file = inputFile.files[0];
        const reader = new FileReader();

        reader.addEventListener("load", function() {

            const img = new Image();
            img.src = reader.result;
            //console.log("Image source:", img.src);

            img.onload = function() {
                //console.log("Image loaded with dimensions:", img.width, "x", img.height);

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                let targetWidth = img.width;
                let targetHeight = img.height;

                if (targetWidth > 1920 || targetHeight > 1920) {
                    const aspectRatio = targetWidth / targetHeight;
                    if (targetWidth > targetHeight) {
                        targetWidth = 1920;
                        targetHeight = targetWidth / aspectRatio;
                    } else {
                        targetHeight = 1920;
                        targetWidth = targetHeight * aspectRatio;
                    }
                }

                canvas.width = targetWidth;
                canvas.height = targetHeight;
                ctx.drawImage(img, 0, 0, targetWidth, targetHeight);


                canvas.toBlob(function(blob) {
                    const formData = new FormData();
                    formData.append('file', blob, file.name);
                    formData.append('text', inputText);

                    //console.log("Blob size:", blob.size);

                    fetch('<?php echo e(route('ClarifaiSubmit')); ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(response => {
                        console.log(response);

                        if (response.success) {
                            //toastAlert(response.message, 'success');
                        } else {
                            //toastAlert(response.message, 'danger');
                        }
                        //resultsDiv.innerHTML = JSON.stringify(response);
                        if (response.message) {
                            resultsDiv.innerHTML = '<p>' + response.message + '</p>';
                        }
                        if (response.results && response.results.outputs) {
                            const concepts = response.results.outputs[0].data.concepts;
                            const conceptList = concepts.map(concept => {
                                return '<li>' + concept.name + ': ' + (concept.value * 100).toFixed(2) + '%</li>';
                            }).join('');
                            resultsDiv.innerHTML += '<ul>' + conceptList + '</ul>';
                        }

                        if (response.interpretation) {
                            resultsDiv.innerHTML += '<p>' + response.interpretation + '</p>';
                        }
                    })
                    .catch(error => {
                        //toastAlert('Upload failed: ' + error, 'danger');
                        console.error('Error:', error);
                    });
                }, 'image/jpeg', 0.7);
            };
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    });

    function interpretConcepts(concepts) {
        // Process the concepts and generate an interpretation
        // This is just an example, you can customize this function according to your needs
        const mainConcept = concepts[0].name;
        const value = concepts[0].value;
        return `The main concept identified in the image is ${mainConcept} with a confidence of ${(value * 100).toFixed(2)}%.`;
    }

}
</script>
</body>
</html>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/clarifai/submit.blade.php ENDPATH**/ ?>