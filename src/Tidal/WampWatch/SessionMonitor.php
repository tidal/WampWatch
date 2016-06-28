<?php
namespace Tidal\WampWatch;

/*
 * Copyright 2015 Timo.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */
use Phaim\Server\Wamp\Util;
use Evenement\EventEmitterInterface;

/**
 * Description of SessionMonitor
 *
 * @author Timo
 */
class SessionMonitor  implements 
        MonitorInterface, 
        EventEmitterInterface {
   
    use 
        MonitorTrait {
            start as doStart;
            stop as doStop;
        }

    const SESSION_JOIN_TOPIC    = 'wamp.session.on_join';
    const SESSION_LEAVE_TOPIC   = 'wamp.session.on_leave';
    const SESSION_COUNT_TOPIC   = 'wamp.session.count';
    const SESSION_LIST_TOPIC    = 'wamp.session.list';
    const SESSION_INFO_TOPIC    = 'wamp.session.get';


    protected $sessionIds = [];

    
    public function start() {
        $this->once('list', function(){
            $this->doStart();
        });     
        $this->startSubscriptions();
        $this->retrieveSessionIds();
        return true;
    }
  
    public function stop() {
        $this->stopSubscriptions();
        $this->doStop();
    }
    
    public function getSessionInfo($sessionId, callable $callback) {
        return $this->session->call(self::SESSION_INFO_TOPIC, [$sessionId])->then(
            function ($res)use($callback){
                $this->emit('info', [$res[0]]);
                $callback($res[0]);
            },
            function ($error) {
                $this->emit('error', [$error]);
            }
        );    
    }
    
    public function getSessionIds(callable $callback) {
        if(!count($this->sessionIds)){
            $this->retrieveSessionIds($callback);
        }else{
            $callback($this->sessionIds);
        }
    }
    
    protected function startSubscriptions() { 
        $this->session->subscribe(self::SESSION_JOIN_TOPIC, function($res){
            $sessionInfo = $res[0];
            $sessionId = $sessionInfo['session'];
            if((array_search($sessionId, $this->sessionIds)) === false) {
                $this->sessionIds[] = $sessionId;
                $this->emit('join', [$sessionInfo]);
            }
        });
        $this->session->subscribe(self::SESSION_LEAVE_TOPIC, function($res){
            // @bug : wamp.session.on_leave is bugged as of crossbar.io 0.11.0
            // will provide sessionID when Browser closes/reloads,
            // but not when calling connection.close();
            $sessionId = $res[0];
            if(($key = array_search($sessionId, $this->sessionIds)) !== false) {
                unset($this->sessionIds[$key]);
                $this->emit('leave', [$sessionId]);
            }
        });
    }
    
    protected function stopSubscriptions() {       
        Util::unsubscribe($this->session, self::SESSION_JOIN_TOPIC);
        Util::unsubscribe($this->session, self::SESSION_LEAVE_TOPIC);
    }
    
    
    protected function retrieveSessionIds($callback = null) {
        $this->session->call(self::SESSION_LIST_TOPIC, [])->then(
            function ($res)use($callback){
                $this->sessionIds = $res[0];
                $this->emit('list', [$this->sessionIds]);
                if($callback){
                    $callback($this->sessionIds);
                }
            },
            function ($error) {
                $this->emit('error', [$error]);
            }
        );  
        
    }
    
}
