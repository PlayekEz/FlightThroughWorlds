<?php

namespace Playek\FTW;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;

use Playek\FTW\data\Data;
use Playek\FTW\command\{FTWCmd, FlyCmd};

class Main extends PluginBase implements Listener 
{
	private static $data;
	public const PERMISSION_FLY = "fly.cmd";
	public const PERMISSION_FTW = "ftw.cmd";
	
	public function onLoad():void {
		self::$data = new Data($this);
	}
	
	public function onEnable():void {
		@mkdir($this->getDataFolder(), 0777);
		
		if($this->getData() instanceof Data){
			$this->getData()->load();
		}
		
		$this->getServer()->getCommandMap()->register(FTWCmd::NAME, new FTWCmd($this));
		$this->getServer()->getCommandMap()->register(FlyCmd::NAME, new FlyCmd($this));
		$this->getServer()->getPluginManager()->registerEvents(new FTWEvent($this), $this);
	}
	
	public function onDisable():void {
		if($this->getData() instanceof Data){
			$this->getData()->save();
		}
	}
	
	public function getData():?Data {
		return self::$data;
	}
}