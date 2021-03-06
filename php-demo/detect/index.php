<?php
    $configs = include('../config.php');
    define('DEMO_ENV', (getenv('STAGE') ? getenv('STAGE') : 'dev'));
?>
<html>
<html lang="en">

<head>
    <title>Kairos Detect Demo</title>   
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="css/detect.css">
    <link rel="stylesheet" href="css/detect-mediaqueries.css">
</head>
<body>
    
    <div class="main-container container">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 main-image-container">
                <img id="previewImage" width="100%" src="/images/team/Brian_Brackeen.jpg">
                <div class="webcam-video-container">
                    <div class="face-overlay"></div>
                    <div class="webcam-counter"></div>
                </div>
                <div class="canvas-container"><canvas id="displayCanvas" /></div>
                <div class="display-image-container"></div>
                <div class="image-container-template"></div>
            </div>
            <a href="" class="show-hide-json hidden-sm">SHOW JSON</a>
            <!-- <a href="" class="show-hide-json hidden-sm">SHOW JSON/ETHNICITY</a> -->
            <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="json-title hidden-xs">JSON</div>
                <!-- <a href="" class="show-hide-ethnicity">ETHNICITY</a> -->
                <button class="copy-json-button btn btn-primary" data-clipboard-action="copy" data-clipboard-target=".json-response">COPY</button>
                <div class="json-response"><pre></pre></div>
                <div class="json-response-template"></div>
                <div class="ethnicity-container">
                    <div class="ethnicity-graph">
                        <div class="ethnicity-graph-inner"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 thumbnails container">
                <a href="image1" class="photo-thumbnail image1">
                    <img src="/images/team/Brian_Brackeen.jpg" crossorigin="Anonymous" />
                </a>
                <a href="image2" class="photo-thumbnail image2">
                    <img src="/images/team/Ben_Virdee-Chapman.jpg" crossorigin="Anonymous" />
                </a>
                <a href="image3" class="photo-thumbnail image3">
                    <img src="/images/team/Rajnesah_Belyeu.jpg" crossorigin="Anonymous" />
                </a>
                <a href="image4" class="photo-thumbnail image4">
                    <img src="/images/team/Neil_Pitts.jpg" crossorigin="Anonymous" />
                </a>
                <a href="image5" class="photo-thumbnail image5">
                    <img src="/images/team/ben_allison.jpg" crossorigin="Anonymous" />
                </a>
            </div>
        </div>
        <div class="row options-panel col-md-12">
            <div class="col-xs-12 col-sm-6 col-md-6">
                <h4>Options</h4>
                <div class="form-group">
                    <label class="control-label" for="optionConfidenceThreshold">Confidence threshold:</label><span class="prompt">Enter a value between .1 and 1 (this only affects the face box drawing, not the payload to the API)</span>
                    <input class="form-control" type="text" name="optionConfidenceThreshold" id="optionConfidenceThreshold" value=".98"><span class="option-error-confidence"></span>
                    <div class="confidencethreshold-slider"></div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="optionMinHeadScale">minHeadScale:</label><span class="prompt">Enter a value between .015 (1:64 scale) and .5 (1:2 scale)</span>
                    <input class="form-control" type="text" name="optionMinHeadScale" id="optionMinHeadScale" value=".015"><span class="option-error-minheadscale"></span>
                    <div class="minheadscale-slider"></div>
                </div>
                <label class="control-label" for="optionSelector">Selector:</label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary active">
                        <input type="radio" name="selector" class="selector" value="frontal" autocomplete="off" checked> Frontal
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" name="selector" class="selector" value="full" autocomplete="off"> Full
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" name="selector" class="selector" value="partial" autocomplete="off"> Partial
                    </label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="fancy-checkbox" id="fancy-checkbox" class="opt-selector" value="roll" autocomplete="off" />
                    <div class="btn-group">
                        <label for="fancy-checkbox" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            <span> </span>
                        </label>
                        <label for="fancy-checkbox" class="btn btn-default active">
                            Roll (optional)
                        </label>
                    </div>
                </div>
                <div class="payload-display">Payload: <span></span></div>
            </div>
        </div>
        <div class="row ui-buttons">
            <div class="webcam col-md-6">
                <button class="webcam-button btn btn-kairos">WEBCAM</button>
            </div>
            <div class="upload col-md-6">
                <form method="post" enctype="multipart/form-data" id="mediaUploadForm"> 
                    <div class="upload-button btn btn-kairos">UPLOAD<input type="file" id="upload" name="upload"></div>
                </form>
                <div id="uploadError"></div>
            </div>
            <div class="url col-xs-6 col-sm-8 col-md-8">
                <input type="text" class="url-from-web" value="URL from the web" />
                <div class="url-error"></div>
            </div>
            <div class="submit col-xs-6 col-sm-4 col-md-4">
                <button class="submit-button btn btn-kairos">SUBMIT</button>
            </div>
            <div class="ui-buttons-mask"></div>
        </div>
        
    </div>  

    <script id="image-container-template" type="text/x-handlebars-template">
        <div class="spinner-message-container">
            {{#if spinner}}
              <div class="processing-spinner"></div>
            {{/if}}i 
            {{#if sadFace}}
              <div class="sad-face"></div>
            {{/if}}
            <div class="message-container strong">{{message1}}</div>
            <div class="message-container">{{message2}}</div>
        </div>
    </script>
    <script id="json-response-template" type="text/x-handlebars-template">
        <div class="spinner-message-container">
            {{#if spinner}}
              <div class="processing-spinner"></div>
            {{/if}}
            {{#if sadFace}}
              <div class="sad-face"></div>
            {{/if}}
            <div class="message-container strong">{{message1}}</div>
            <div class="message-container">{{message2}}</div>
        </div>
    </script>
            

    <!-- hosted libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.12/clipboard.min.js"></script>

    <!-- custom libraries -->
    <script src="../js/utils.js"></script>
    <script src="js/detectDemoApp.js"></script>
    <script src="js/detectUi.js"></script>
    <script src="../js/exif.js"></script>
    <script src="../js/transparentImageData.js"></script>
    
    <!-- initialize custom libraries if API credentials are valid -->
    <?php
        if (
            (defined("APP_ID") && APP_ID != "") &&
            (defined("APP_KEY") && APP_KEY != "") &&
            (defined("API_URL") && API_URL != "")
        ) {
    ?>
        <script>
            detectDemoApp.init({
                "uploadFileSizeImage":<?php echo $configs["uploadFileSizeImage"] ?>,
                "uploadFileTypesImage":<?php echo $configs["uploadFileTypesImage"] ?>,
                "apiCredentials":true
            });
        </script>
    <?php
        }
        else {
    ?>
        <script>
            detectDemoApp.init({
                "apiCredentials":false
            });
        </script>
    <?php  
        }
    ?>

</body>

</html>

