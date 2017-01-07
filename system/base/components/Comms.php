<?php

use \Providers\Core\HttpClient as Client;

final class Comms {

    /*
     * @var Comms
     */

     private static $instance = NULL;

     protected $mailOptions;

     protected $connectionOptions;

     protected $messagingOptions;

     protected $mailer;

     protected $connector;

     protected $httpclient;

     protected $messenger;

     protected $isMailDriverLoadable = FALSE;

     protected $isConnectorDriverLoadable = FALSE;

     protected $isMessengerDriverLoadable = FALSE;

     protected $mailgunParameters;

     protected $messageNumber;

     protected $callNumber;

     private function __construct(array $mailOptions, array $connectionOptions, array $messagingOptions){

          $mailer_driver = $mailOptions['mail_driver'];
          $mail_api_key = $mailOptions['key'];

          $connector_driver = $connectionOptions['connector_driver'];
          $connector_api_key = $connectionOptions['key'];
          $connector_api_secret = $connectionOptions['secret'];
          $connector_app_id = $connectionOptions['driver_id'];

          $messenger_driver = $messagingOptions['messenger_driver'];
          $messenger_sid = $messagingOptions['driver_id'];
          $messenger_token = $messagingOptions['token'];
          $this->messageNumber = $messagingOptions['number'];
          $this->callNumber = $messagingOptions['number'];
         
          switch($mailer_driver){
          	  case '#php-mailer':
                 $this->isMailDriverLoadable = class_exists('PHPMailer');
                 $this->mailer = ($this->isMailDriverLoadable? new \PHPMailer() : NULL);
          	  break;
          	  case '#mailgun':
                 $this->isMailDriverLoadable = class_exists('Mailgun\Mailgun');
                 $this->mailer = ($this->isMailDriverLoadable? new \Mailgun\Mailgun($mail_api_key) : NULL);
          	  break;
          	  case '#native':
                 $this->mailer = new stdClass();
                 $this->mailer->type = "native";
          	  break;
          }

          switch($connector_driver){
              case "#pusher":
                 $this->isConnectorDriverLoadable = class_exists('Pusher');
                 $this->connector = ($this->isConnectorDriverLoadable? new \Pusher($connector_api_key, $connector_api_secret, $connector_app_id, array('encrypted' => false, 'curl_options' => array(), 'timeout' => 300000, 'scheme' => 'http')) : NULL);
              break;
              case "#native":
                 $this->connector = NULL;
              break;
          }

          switch($messenger_driver){
              case "#twilio":
                  $this->isMessengerDriverLoadable = class_exists('Twilio\Rest\Client');
                  $this->messenger = ($this->isMessengerDriverLoadable? new \Twilio\Rest\Client($messenger_sid, $messenger_token) : NULL);
              break;
          }

          unset($mailOptions['mail_driver']);

          $this->mailOptions = $mailOptions;

          $this->mailgunParameters = new \stdClass();

          $this->setMailCredentials();
     }

     public static function createInstance(array $mailOptions, array $connectionOptions, array $messagingOptions){
          if(static::$instance == NULL){
               static::$instance = new Comms($mailOptions, $connectionOptions, $messagingOptions);
               return static::$instance;
          } 
     }

     private function getMailer(){
        
          return $this->mailer;
     }

     private function getConnector(){

          return $this->connector;
     }

     private function getMessenger(){

          return $this->messenger;
     }

     private function getMailgunParameters(){

         return $this->mailgunParameters;
     }

     private function setMailgunParameters($param_name, $param_value){

          $this->mailgunParameters->{$param_name} = $param_value;
     }

     public static function privateAuth($channelName, $socketId){

          return static::$instance->getConnector()->socket_auth($channelName, $socketId);
     }

     public static function getChannels(){

          return static::$instance->getConnector()->get_channels();
     }

     public static function getChannelInfo($channelName){

          return static::$instance->getConnector()->get_channel_info($channelName);
     }

     public static function presenceAuth($channelName, $socketId, $user){

          $columns = array_keys($user);
          $presence = array();
          $id = '';

          if(in_array('full_name', $columns) || in_array('first_name', $columns)){
              $presence['name'] = (array_key_exists('full_name', $user)? $user['full_name'] : $user['first_name']);
          }

          if(in_array('id', $columns)){
              $id = $user['id'];
          }

          return static::$instance->getConnector()->presence_auth($channelName, $socketId, $id, $presence);
     }

     public static function sendBroadcast($channelName, $eventName, $payload){

         static::$instance->getConnector()->trigger($channelName, $eventName, $payload);
     }

     public static function sendMessage($mobileNumber, $msgBody){

          static::$instance->getMessenger()->messeges->create(
              static::$instance->messageNumber,
              array(
                  'from' => $mobileNumber,
                  'body' => $msgBody
              )
          );
     }

     public static function sendCall($mobileNumber, $callerXMLUrl){

          static::$instance->getMessenger()->calls->create(
              $mobileNumber, // Make the call to this number
              static::$instance->callNumber, // Make the call from this valid Twilio Number
              array(
                  'url' => $callerXMLUrl
              )
          );
     }

     public function curl($host='http://127.0.0.1', array $config = array(), $port = 80){

          $this->httpclient = NULL;

          $this->httpclient = new Client($host, $port, $config['method']);
          $this->httpclient->setHeaders($config['headers']);
          $this->httpclient->setRequest($config['path'], $config['client_id'], $config['params']);

          if(starts_with($host, 'https:')){
              $this->httpclient->setAsStale();
          }

          return $this->httpclient->getResponse();
     }

     public static function sendHTTPRequest($host='http://127.0.0.1', array $config = array(), $port = 80){

        return static::$instance->curl($host, $config, $port);

     }

     public static function sendMail($domain = 'example.com'){
        $mailer = static::$instance->getMailer();
        $params = static::$instance->getMailgunParameters();
        
        switch(get_class($mailer)){
            case  "Mailgun\Mailgun": // Mailgun
                $mailer->sendMessage($domain, array(
                      'from' => $params->From,
                      'to' => $params->To,
                      'subject' => $params->Subject,
                      'text' => $params->Body
                ));
            break;
            case "PHPMailer": // PHPMailer
               if(!$mailer->send()){
                    \Logger::error($mailer->ErrorInfo);
               }
            break;
            case "stdClass":
                $headers = "From: ". $mailer->From ."\n"; // noreply@learnsty.com
                //$headers .= "Reply-To: "; 
                try{
                    if(!mail($mailer->To, $mailer->Subject, $mailer->Body,  $headers)){
                        \Logger::error("Native Mailer couldn't send mail");
                    } 
                }catch(\Exception $e){
                    \Logger::error($e->getMessage());
                }
            break;
        }

     }  

     private function setMailCredentials(){

        switch(get_class($this->mailer)){
            case "PHPMailer": // PHPMailer
                $this->mailer->Host = $this->mailOptions['mail_server'];

                if($this->mailOptions['protocol'] === "SMTP"){
                     $this->mailer->isSMTP();
                     $this->mailer->SMTPAuth = TRUE;
                }  

                if($this->mailOptions['encryption']){
                     // requires TLS Encryption
                     $this->mailer->SMTPSecure = $this->mailOptions['encryption_type'];
                }       

                $this->mailer->Username = $this->mailOptions['username'];
                $this->mailer->Password = $this->mailOptions['password'];
                $this->mailer->Port = $this->mailOptions['port']; 
            break;
            case "Mailgun\Mailgun": // Mailgun
                $this->mailgunParameters->Host = $this->mailOptions['mail_server'];
                $this->mailgunParameters->Username = $this->mailOptions['username'];
                $this->mailgunParameters->Password = $this->mailOptions['password'];
                $this->mailgunParameters->Port = $this->mailOptions['port'];
            break;  
            case "stdClass":
                if($this->mailer->type == "native"){
                    $this->mailer->Username = $this->mailOptions['username'];
                    $this->mailer->Password = $this->mailOptions['password'];
                    $this->mailer->Port = $this->mailOptions['port'];
                }
            break;  
        }    

        

     }

     public static function setMailParameters(array $from, array $to, array $body, array $params){
          
         $mailer = static::$instance->getMailer(); 
         
         switch (get_class($mailer)) {
            case 'PHPMailer': // PHPMailer
               $mailer->From = $from['email'];
               $mailer->FromName = $from['name'];
               $mailer->addAddress($to['email'], $to['name']);

               //$this->mailer->addCC("<email>");
               //$this->mailer->addBCC("<email>");
               //$this->mailer->addAttachment("<file_path>", "<file_id_text>");

               //$mailer->isHTML($params['html_ok']);
               $mailer->Subject = $params['subject'];
               $mailer->Body = $body['main'];
               //$mailer->AltBody = $body['alt'];

               if(array_key_exists('lang', $params)){
                   $mailer->addLanguage($params['lang']); 
               }
            break;
            case "Mailgun\Mailgun": // Mailgun
                static::$instance->setMailgunParameters('From', $from['email']);
                static::$instance->setMailgunParameters('To', $to['email']);
                static::$instance->setMailgunParameters('Subject', $params['subject']);
                static::$instance->setMailgunParameters('Body', $body['main']);
            break;
            case "stdClass":
               $mailer->From = $from['email'];
               $mailer->FromName = $from['name'];
               $mailer->To = $to['email'];
               $mailer->ToName = $to['name'];

               $mailer->Subject = $params['subject'];
               $mailer->Body = $body['main'];
            break;
         }
              
     }

}

?>