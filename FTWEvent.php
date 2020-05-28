<?php

namespace Playek\FTW;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;

use Playek\FTW\data\Data;
use Playek\FTW\Main;

class FTWEvent implements Listener {
	
	private $main;
	
	public function __construct(Main $main){
		$this->main = $main;
	}
	
	public function entityChangeLevel(EntityLevelChangeEvent $event):void {
		if(!$event->getEntity() instanceof Player) return;
		if(!$this->main->getData() instanceof Data) return;
		if(!$this->main->getData()->canFlyHere($event->getTarget()->getFolderName())){
			if($event->getEntity()->getGamemode() == 1 or $event->getEntity()->getGamemode() == 3) return;
			if($event->getEntity()->getAllowFlight()){
				$event->getEntity()->setAllowFlight(false);
				$event->getEntity()->setFlying(false);
				$event->getEntity()->sendMessage(TextFormat::YELLOW."Your flight has been deactivated, it is not allowed in this world");
			}
		}
	}
}