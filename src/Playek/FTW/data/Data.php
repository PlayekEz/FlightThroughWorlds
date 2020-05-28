<?php

namespace Playek\FTW\data;

use pocketmine\utils\Config;
use pocketmine\Player;
use Playek\FTW\Main;

class Data 
{
	
	private $data = [];
	private $main;
	
	public function __construct(Main $main){
		$this->main = $main;
	}
	
	public function getConfig():Config {
		return new Config($this->main->getDataFolder() . "data.yml", Config::YAML);
	}
	
	public function load():void {
		$config = $this->getConfig();
		foreach($config->getAll() as $key => $value){
			$this->data[$key] = $value;
		}
	}
	
	public function save():void {
		$config = $this->getConfig();
		foreach($this->data as $key => $value){
			$config->set($key, $value);
		}
		$config->save();
	}
	
	public function canFlyHere(String $world):bool {
		if(isset($this->data[$world])){
			if(isset($this->data[$world])){
				if(is_bool($this->data[$world])){
					return $this->data[$world];
				}
			}
		}
		return false;
	}
	
	public function add(String $world, bool $enabled = true, $sender = "Admin"):bool {
		if(isset($this->data[$world])){
			$this->setError($sender, "This world has been added before");
			return false;
		}
		if(!file_exists($this->main->getServer()->getDataPath() . "worlds/" . $world)){
			$this->setError($sender, "The world does not exist on the server");
			return false;
		}
		$this->data[$world] = $enabled;
		$this->save();
		return true;
	}
	
	public function remove(String $world, $sender = "Admin"){
		$sender = ($sender instanceof Player) ? $sender->getName() : $sender;
		if(!isset($this->data[$world])){
			$this->setError($sender, "The world has not been registered before");
			return false;
		}
		unset($this->data[$world]);
		return true;
	}
	
	public function set(String $world, bool $enabled, $sender = "Admin"){
		if(!isset($this->data[$world])){
			$this->setError($sender, "The world has not been registered before");
			return false;
		}
		$this->data[$world] = $enabled;
		return true;
	}
	
	public function setError($sender, String $error):void {
		$sender = ($sender instanceof Player) ? $sender->getName() : $sender;
		$this->error[$sender] = $error;
	}
	
	public function getError($sender):String {
		$sender = ($sender instanceof Player) ? $sender->getName() : $sender;
		return (isset($this->error[$sender])) ? $this->error[$sender] : "";
	}
}
