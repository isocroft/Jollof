<?php
/*!
 * Vyke Mini Framework (c) 2016
 *
 * {TextStream.php}
 */

class TextStream {

      const EVENT_HEADING = 1; # event: update \n

      const ID_HEADING = 2; # id: 3299029492 \n

      const RETRY_HEADING = 3; # retry: 15000 \n

      private static $instance = NULL;

      private $streamText;

      private $eventDataBlocks;

      private $eventDataBlockSize; // a.k.a number of rows in table


      private function __construct(){

          set_time_limit(0); // removes time limit for script execution

          $this->streamText = '';

          $this->eventDataBlocks = array();

          $this->eventDataBlockSize = 4;

          $this->lineSize = 14;

          $this->maxNoUploadCount = 5;

          $this->columns = array('subject', 'verb', 'object', 'time', 'activity_type');

      }

      public static function createInstance(){

          if(static::$instance == NULL){
               static::$instance = new TextStream();
               return static::$instance;
          }
      }

      public function setForAjax(){

          ignore_user_abort(TRUE); // disbale automatic script exit if user disconnects
      }

      public static function setEvents(AppActivity $activities, array $values){

          $combined = array_combine($this->columns, $values);

          $combined['activity_id'] = Helpers::generateCode();

          return $activities->setOccurence($combined);

      }

      public static function getEvents(AppActivity $activities, $noUpdateCount, $lastEventId){

         $maxCheckCount = 2;

         $events = new \stdClcass();

         $events->noupdate = TRUE; // initially, we assume that there will be no update

          while((!connection_aborted() || !connection_timeout())){

                if(Cache::has('app_activities')){ //

                     static::$instance->setEventDataBlocks(Cache::get('app_activities'));
                }

                if(!static::$instance->isUpdateAvailable()){

                     static::$instance->setEventDataBlocks($activities->getOccurence($this->columns, array('id'=> array('>', $lastEventId)), $this->eventDataBlockSize));
                }

                if(static::$instance->isUpdateAvailable()){

                      $events->noupdate = FALSE;

                      $events->noupdateCount = 0; // reset noupdate count

                      static::$instance->prepareEventPayload();

                      $events->data = static::$instance->getStreamText();

                      break;

                }else{

                      --$maxCheckCount;

                      if($maxCheckCount === 0){

                           static::$instance->includeStreamHeading($lastEventId, self::ID_HEADING);

                           static::$instance->includeStreamHeading('noupdate', self::EVENT_HEADING);

                           if($noUpdateCount >= static::$instance->getMaxNoUpdateCount()){

                              $noUpdateCount = 0;

                              $events->noupdateCount = $noUpdateCount;

                              static::$instance->includeStreamHeading('5000', self::RETRY_HEADING); // retry in 5 seconds

                           }else{

                                $events->noupdateCount = ++$noUpdateCount;

                                $delay = 1000 * ($noUpdateCount + 1);

                                static::$instance->includeStreamHeading("$delay", self::RETRY_HEADING); // retry in 5 seconds

                           }

                           $events->data = static::$instance->getStreamText();

                           break;
                      }

                      sleep(3); // wait for 3 seconds [max -worst case: 6 seconds]
                }
          }

          return $events;

      }

      private function getStreamText(){

          return $this->streamText;
      }

      private function getMaxNoUpdateCount(){

         return $this->maxNoUploadCount;
      }

      private function includeStreamHeading($line, $type = -1){

          $heading = '';

          if(is_string($line)){
              $lineArray = array($line);
          }else{
              throw new \InvalidArgumentException("Cannot not include empty line");
          }

          switch($type){
             case 1:
                $heading = 'event: ';
             break;
             case 3:
                $heading = 'retry: ';
             break;
             case 2:
               $heading = 'id: ';
             break;
             default:
               $heading = 'data: ';
             break;
          }

          foreach($lineArray as $ln){
             $this->streamText .=  $heading . $ln . PHP_EOL;
          }
      }

      public function setEventDataBlocks(array $eventData){

          if(count($eventData) > 0){

                $this->eventDataBlocks[] = $eventData;

           }
      }

      public function isUpdateAvailable(){

            return count($this->eventDataBlocks) > 0;
      }

      public function prepareEventPayload(){

             $this->streamText = '';

             $lines = array();

             $lastEvent = $this->eventDataBlocks[($this->eventDataBlockSize - 1)];

             $this->includeStreamHeading($lastEvent['activity_id'], self::ID_HEADING);

             $this->includeStreamHeading('update', self::EVENT_HEADING);

             for($x = 0; $x < $this->eventDataBlockSize; $x++){

                  $lines[$x] = json_encode($this->eventDataBlocks[$x]);

                  $this->includeStreamHeading(str_split($lnes[$x], $this->lineSize));

             }

      }

}


?>