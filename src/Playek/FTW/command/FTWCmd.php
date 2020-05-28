<?php

namespace Playek\FTW\command;

use pocketmine\Player;
use pocketmine\command\{PluginCommand, CommandSender};
use pocketmine\utils\TextFormat;
use pocketmine\level\Level;

use Playek\FTW\data\Data;
use Playek\FTW\Main;

class FTWCmd extends PluginCommand 
{
	
	private $main;
	public const NAME = "ftw";
	
	public function __construct(Main $main)
	{
		parent::__construct(self::NAME, $main);
		$this->main = $main;
		$this->setDescription("Plugin Made By @SoyPlayek");
	}
	
	public function execute(CommandSender $sender, string $labelCommand, array $args):bool {
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED."Use only in-game");
			return false;
		}
		if(!$sender->hasPermission(Main::PERMISSION_FTW)){
			$sender->sendMessage(TextFormat::RED."You do not have permissions to use this command");
			return false;
		}
		if(!empty($args[0])){
			if(strtolower($args[0]) == "add"){ /* /fwt add <world> <on | off> */
				$data = $this->main->getData();
				if($data instanceof Data){
					if(empty($args[1])){
						$sender->sendMessage(TextFormat::RED."Uses: /".self::NAME." add <world> <on | off>");
						return false;
					}
					$world = $args[1];
					$enabled = true;
					if(!empty($args[2])){
						if(strtolower($args[2]) == "off"){
							$enabled = false;
						}else if(strtolower($args[2]) == "on"){
							$enabled = true;
						}else{
							$sender->sendMessage(TextFormat::RED."Invalid format, use ".TextFormat::YELLOW." OFF ".TextFormat::RED."or ".TextFormat::RED."on ".TextFormat::RED."to indicate if you can fly in this world");
							return false;
						}
					}
					if($data->add($world, $enabled, $sender)){
						$sender->sendMessage(TextFormat::YELLOW."Your world has already been successfully added! ".(($enabled) ? TextFormat::GREEN."In this world you can now fly" : TextFormat::RED."In this world you can no longer fly"));
						return true;
					}else{
						$sender->sendMessage(TextFormat::RED.$data->getError($sender));
						return false;
					}
				}
			}else if(strtolower($args[0]) == "remove"){
				$data = $this->main->getData();
				if($data instanceof Data){
					if(empty($args[1])){
						$sender->sendMessage(TextFormat::RED."Uses: /".self::NAME." remove <world>");
						return false;
					}
					if($data->remove($args[1], $sender)){
						$sender->sendMessage(TextFormat::YELLOW."You have removed this world from the database, therefore flight in this world will not be allowed");
						if($this->main->getServer()->getLevelByName($args[1]) instanceof Level){
							foreach($this->main->getServer()->getLevelByName($args[1])->getPlayers() as $player){
								if($player->getAllowFlight()){
									$player->setFlying(false);
									$player->setAllowFlight(false);
									$player->sendMessage(TextFormat::YELLOW."[FTW] Your flight has been deactivated, this world has been removed from the database");
								}
							}
						}
						return true;
					}else{
						$sender->sendMessage(TextFormat::RED.$data->getError($sender));
						return false;
					}
				}
			}else if(strtolower($args[0]) == "help"){
				$lines = [
				"add <world> <on | off>",
				"set <world> <on | off>",
				"remove <world>"];
				$sender->sendMessage(TextFormat::YELLOW."FTW Command Help List: ");
				foreach($lines as $line){
					$sender->sendMessage(TextFormat::GREEN."Uses: ".TextFormat::GRAY."/".$line);
				}
				return true;
			}else if(strtolower($args[0]) == "set"){ /** /ftw set <world> <on | off> */
				$data = $this->main->getData();
				if($data instanceof Data){
					if(empty($args[1]) or empty($args[2])){
						$sender->sendMessage(TextFormat::RED."Uses: /".self::NAME." set <world> <on | off>");
						return false;
					}
					if(strtolower($args[2]) == "on"){
						$enabled = true;
					}else if(strtolower($args[2]) == "off"){
						$enabled = false;
					}else{
						$sender->sendMessage(TextFormat::RED."Invalid format, use ".TextFormat::YELLOW." OFF ".TextFormat::RED."or ".TextFormat::RED."on ".TextFormat::RED."to indicate if you can fly in this world");
						return false;
					}
					if($data->set($args[1], $enabled, $sender)){
						$sender->sendMessage((($enabled) ? TextFormat::GREEN."Flight has been enabled in this world" : TextFormat::RED."Flight in this world has been disabled"));
						if(!$enabled){
							if($this->main->getServer()->getLevelByName($args[1]) instanceof Level){
								foreach($this->main->getServer()->getLevelByName($args[1])->getPlayers() as $player){
									if($player->getAllowFlight()){
										$player->setFlying(false);
										$player->setAllowFlight(false);
										$player->sendMessage(TextFormat::YELLOW."[FTW] Your flight has been deactivated, the configuration of this world has been changed");
									}
								}
							}
						}
						return true;
					}else{
						$sender->sendMessage(TextFormat::RED.$data->getError($sender));
						return false;
					}
				}
			}
		}
		return false;
	}
}