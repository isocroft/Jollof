<?php 

namespace Providers\Core;


class InputManager {

     protected $maxUploadSize;

     protected $binaryFilesAllowed = array(
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG     
     );

     protected $textFilesAllowed = array(
         "application/zip",
         "application/xhtml+xml",
         "application/xml", 
         "application/x-zip", 
         "application/x-zip-compressed",
         "application/xml",
         "text/html"
     );

     protected $allowedFileExtentions = array(
         'zip',
         'mp4',
         'mp3',
         'docx',
         'mpeg',
         'avi',
         'pdf',
         'png',
         'gif',
         'odt',
         'html',
         'doc',
         'jpeg',
         'jpg',
         'pptx', 
         'dotx', 
         'xlsx'
     );

     protected $uploadSettings;

     protected $uploadTempDir;

     protected $httpInput = array('fields'=>array(), 'files'=>NULL);

     public function __construct(array $httpInput = array(), array $uploadConfig = array()){
           
          $this->maxUploadSize = $uploadConfig['max_upload_size'];
          $this->uploadSettings = $uploadConfig['upload_settings'];
          $this->uploadTempDir = $uploadConfig['temp_upload_dir'];
          $this->canExtractZip = $uploadConfig['can_extract_zip'];

          $this->httpInput['files'] = $_FILES;
          
          foreach($httpInput as $name => $value){
              if(!is_string($value)){
                 $this->httpInput['files'][$name] = (array_key_exists($name, $_FILES) && $value !== $_FILES[$name]) ? $_FILES[$name] : $value;
              }else{
                 $this->httpInput['fields'][$name] = $value;
              }
          }

     }

     public function uploadFiles(array $upload_path_map, array &$errors){

           $upload_base_dir = realpath($GLOBALS['env']['app.path.upload']);
           $root = $GLOBALS['env']['app.root'];
           $results = array();

           foreach ($upload_path_map as $name => $upload_path){
                if (array_key_exists($name, $this->httpInput['files'])){
                    $file = $this->httpInput['files'][$name];
                    $isRemoteUpload = false;
                    $errors[$name] = array();
                    
                    if($file['error'] !== UPLOAD_ERR_OK){
                       $errors[$name]['upload_error'] = 'file seems to have a problem';
                       $results[$name] = NULL;
                       continue;
                    }

                    if($file['error'] == UPLOAD_ERR_INI_SIZE 
                      || $file['error'] == UPLOAD_ERR_FORM_SIZE){
                        $errors[$name]['upload_error'] = 'file too big to upload';
                        $results[$name] = NULL;
                        continue;
                    }

                    if($file['error'] == UPLOAD_ERR_NO_FILE){
                        $errors[$name]['upload_error'] = 'no file to upload';
                        $results[$name] = NULL;
                        continue;
                    }

                    // $finfo = new finfo(FILEINFO_MIME_TYPE);

                    // validate  file size
                    $size = $file['size'];
                    if($size >= $this->maxUploadSize){
                       $errors[$name]['size_error'] = 'file is too large';
                       $results[$name] = NULL;
                       continue;
                    }

                    // sanitizing file name and create unique file name
                    $fname = preg_replace("/[^A-Z0-9._]/i", "-", $file['name']);
                    $file_parts = pathinfo($fname);
                    $ext = $file_parts['extension'];
                    $zip = NULL;

                    if(!in_array($ext, $this->allowedFileExtentions)){ 
                       $errors[$name]['type_error'] = 'the system cannot process this file';
                       $results[$name] = NULL;
                       continue;
                    }

                    // avoid accidentally overwriting a file (collisions can and do occur)
                    do{
                      if(!file_exists($upload_path)){ 
                          // if the directory doesn't exist, create it!
                          make_folder($upload_path);
                      }    
                      $_name = get_random_from_string($file_parts['filename']);
                      $target_path = $upload_base_dir . $upload_path . $_name . '.' . $ext;
                    }while(file_exists($target_path));

                    if(!is_binary_file($file['tmp_name'], $ext)){
                        // validate (text files)
                        $finfo = new finfo(FILEINFO_MIME); // FILEINFO_MIME_TYPE 
                        $ftype = $finfo->file($file['tmp_name']);
                        if(!in_array($ftype, $this->textFilesAllowed)){
                           $errors[$name]['type_error'] = 'the system cannot process this file';
                           $results[$name] = NULL;
                           continue;
                        }
                    }else{
                        //validate (binary files)
                        if($this->isAllowedBinary($file['tmp_name'], $ext)){
                            $errors[$name]['security_error'] = is_image_file($file['tmp_name']) ? 'image file not trusted' : 'binary file not trusted';
                            $results[$name] = NULL;
                            continue;
                        }
                    }

                    $driver = $this->uploadSettings['upload_driver'];
                    $key = $this->uploadSettings['key'];
                    $secret = $this->uploadSettings['secret'];
                    
                    switch($driver){
                      case "#aws-s3":
                          if(class_exists('Aws\S3\S3Client')){
                              $_region = $this->uploadSettings['region'];
                              $_bucket = $this->uploadSettings['bucket'];

                              $s3 = new \Aws\S3\S3Client(array('version'=>'latest', 'region'=>$_region)); # e.g. 'us-west-2'
                              try{
                                  $upload = $s3->putObject(array(
                                        'Bucket' => $_bucket,
                                        'Key' => $key,
                                        'Body' => fopen($file['tmp_name'], 'r'),
                                        'ACL' => 'public-read'
                                  ));
                                  $results[$name] = (method_exists($s3, 'getObjectUrl'))? $s3->getObjectUrl($_bucket, $key) : $upload->get('ObjectUrl');
                              }catch(Aws\Exception\S3Exception $e){
                                  $errors[$name]['write_error'] = 'file could not be uploaded -context: ' . $e->getMessage();
                                  $results[$name] = NULL;
                              }
                          }else{
                              $results[$name] = NULL;
                              $errors[$name]['step_error'] = 'Upload Driver Not Found';
                          }
                          $isRemoteUpload = true;
                      break;
                      case "#cloudinary":
                          if(class_exists('Cloudinary\Uploader')){
                              $_id = $this->uploadSettings['driver_id'];
                              $cloudinary_path = str_replace($upload_base_dir . $upload_path, '', $target_path);
                              $cloudinary_path = str_replace(('.'.$ext), '', $cloudinary_path);
                              \Cloudinary::config(array(
                                  'cloud_name' => $_id,
                                  'api_key' => $key,
                                  'api_secret' => $secret,
                              ));
                              \Cloudinary\Uploader::upload($file['tmp_name'], array('public_id'=> $cloudinary_path));
                              $results[$name] = cloudinary_url($cloudinary_path.'.'.$ext);
                          }else{
                              $results[$name] = NULL;
                              $errors[$name]['step_error'] = 'Upload Driver Not Found';
                          }
                          $isRemoteUpload = true;
                      break;  
                    }

                    if($isRemoteUpload){
                        continue;
                    }  
                     
                    if(!is_uploaded_file($file['tmp_name'])){
                         if($ext == "zip"){
                             $zip = new \ZipArcive();
                            //$target_path = str_replace('/'.$name, '/'.$file['name'], $target_path);
                         }
                         $result = move_uploaded_file($file['tmp_name'], $target_path);
                         if(!$result){
                             $errors[$name]['write_error'] = 'file could not be uploaded';
                             $results[$name] = NULL;
                             continue;
                         }
                         if($ext == "zip" && $result){
                            if($this->canExtractZip === TRUE){ 
                                 $x = $zip->open($target_path);
                                 if($x === TRUE){
                                      $target_path_temp = str_replace(('.'.$ext), '/', $target_path);
                                      $zip->extractTo($target_path_temp);
                                      $zip->close();
                                      unlink($target_path);
                                      $target_path = substr($target_path_temp, 0, (count($target_path_temp)-1));
                                      sleep(5);
                                 }
                            }   
                         }
                    }else{
                         $errors[$name]['write_error'] = 'file has already been created';
                         $results[$name] = NULL;
                         continue;
                    }   
 
                    // set proper permissions on new file
                    if(is_file($target_path)){
                       chmod($target_path, 0600); // 0644
                    }

                    if(count($errors[$name]) == 0){
                        unset($errors[$name]);
                    }

                    // cache unique name for return 
                    # $_temp = explode('/', $target_path);
                    $host = $GLOBALS['app']->getHost();
                    if($host === "localhost"){
                        $host .= '/' . $root;
                    }
                    $doc_root = Request::header('DOCUMENT_ROOT');
                    $results[$name] = str_replace($doc_root, $host, $target_path); 
                    # implode('/', (array_slice($_temp, (count($_temp)-2))));
                    
        
                
                }
           }

           return $results;
     }

     private function isAllowedBinary($file_tmp_name){

           $file_type = exif_imagetype($file_tmp_name);

           return in_array($file_type, $this->binaryFilesAllowed);
     }

     public function getFields(array $field_keys = array()){

         return $this->filterInput($field_keys, $this->httpInput['fields']);
     }

     public function getFiles(array $file_keys = array()){

        return $this->filterInput($file_keys, $this->httpInput['files']);
     }

     private function filterInput(array $vars = array(), $parameters){
            $filtered = NULL;
            if(count($vars) > 0){ 
                $filtered = array();
                foreach($vars as $field){
                    if(array_key_exists($field, $parameters)){
                        $filtered[$field] = $parameters[$field];
                    }
                }
            }else{
                $filtered = $parameters;
            }
            
            return (count($filtered) > 0)? $filtered : NULL;
     }

}


?>