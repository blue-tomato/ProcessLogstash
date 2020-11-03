<?php namespace ProcessWire;

class ProcessLogstash extends WireData implements Module, ConfigurableModule
{

	public static function getModuleInfo()
	{
		return array(
			'title' => 'ProcessLogstash',
			'class' => 'ProcessLogstash',
			'version' => 105,
            'summary' => 'Send ProcessWire Logs to Logstash/Kibana',
            'href' => 'https://github.com/blue-tomato/ProcessLogstash/',
			'singular' => true,
			'autoload' => true,
			'requires' => [
				'PHP>=7.0.0',
                'ProcessWire>=3.0.133',
                'InputfieldURL>=1.0.1'
			]
		);
	}

	public function init()
	{
		$this->addHookAfter('WireLog::save', $this, 'sendToLogstash');
    }
    
    private $processLogstashFieldname = 'processlogstash_endpoint';

    public function getModuleConfigInputfields(InputfieldWrapper $wrapper)
	{
        $endpoint = $this->getEndpoint();
        $field = $this->modules->InputfieldURL;
		$field->name = $this->processLogstashFieldname;
        $field->required = true;
		$field->label = __("Logstash HTTP-Input Endpoint");
		$field->value = isset($endpoint) ? $endpoint : '';
        $wrapper->add($field);
		return $wrapper;
    }
    
    private function getEndpoint() {

        $config = Wire::getFuel('config');

		if(isset($config->processLogstash['endpoint'])) {
		    return $config->processLogstash['endpoint'];
		}
        
        return $this->get($this->processLogstashFieldname);
    }

	public function sendToLogstash(HookEvent $event)
	{
        $users = Wire::getFuel('users');
        $input = Wire::getFuel('input');
        $config = Wire::getFuel('config');

		$name = $event->arguments(0);
		$text = $event->arguments(1);
        $options = $event->arguments(2);
        
        $user = $users->getCurrentUser()->name;
        $url = $input->url();

        if($options && isset($options["user"])) $user = $options["user"];
        if($options && isset($options["url"])) $url = $options["url"];
        
        $logData = [
            "timestamp" => date('c'),
            "logType" => $name,
            "user" => $user,
            "url" => $url,
            "text" => $text
        ];

        if(isset($config->processLogstash['env'])) {
            $logData["env"] = $config->processLogstash['env'];
        }
    
        $response = $this->sendData($logData);

    }
    
    private function sendData(array $data)
    {

        $response = null;
        $endpoint = $this->getEndpoint();

        if($endpoint) {
            $config = Wire::getFuel('config');

            $ch = curl_init();
            
            $curlConfig = array(
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'LogType: ' . $data['logType']
                ),
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
            );

            if(isset($config->processLogstash['customHttpHeaders']) && is_array($config->processLogstash['customHttpHeaders'])) {
                $curlConfig[CURLOPT_HTTPHEADER] = array_merge($curlConfig[CURLOPT_HTTPHEADER], $config->processLogstash['customHttpHeaders']);
            }
            
            if(isset($config->processLogstash['proxy'])) {
                $curlConfig[CURLOPT_PROXY] = $config->processLogstash['proxy'];
            }

            curl_setopt_array($ch, $curlConfig);
            
            $response = curl_exec($ch);
        }

        return $response; // TODO: json_decode if needed
    }


}

