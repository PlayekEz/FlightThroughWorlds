<?php

namespace Playek\FTW\command;

use pocketmine\Player;
use pocketmine\command\{PluginCommand, CommandSender};
use pocketmine\utils\TextFormat;

use Playek\FTW\data\Data;
use Playek\FTW\Main;

class FlyCmd extends PluginCommand 
{
	
	private $main;
	public const NAME = "fly";
	
	public function __construct(Main $main)
	{
		parent::__construct(self::NAME, $main);
		$this->main = $main;
		$this->setDescription("Fly depends §oFTW");
	}
	
	public function execute(CommandSender $sender, string $labelCommand, array $args):bool {
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED."Use only in-game");
			return false;
		}
		
		$data = $this->main->getData();
		if(!$data instanceof Data) return false;
		if($data->canFlyHere($sender->getLevel()->getFolderName())){
			if(!$sender->hasPermission(Main::PERMISSION_FLY)){
				$sender->sendMessage(TextFormat::RED."You don't have permission to fly");
				return false;
			}
			if(!$sender->getAllowFlight()){
				$sender->setAllowFlight(true);
				$sender->setFlying(true);
				$sender->sendMessage(TextFormat::GREEN."Now you can fly!");
			}else{
				$sender->setAllowFlight(false);
				$sender->setFlying(false);
				$sender->sendMessage(TextFormat::RED."You have deactivated your flight");
			}
			return true;
		}else{
			$sender->sendMessage(TextFormat::RED."Flying in this world is not allowed");
			return false;
		}
		return false;
	}
}