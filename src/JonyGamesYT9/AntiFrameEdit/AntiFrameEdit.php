<?php

namespace JonyGamesYT9\AntiFrameEdit\AntiFrameEdit;

use pocketmine\plugin\PluginBase;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\Config;

/**
 * Class AntiFrameEdit
 * @package JonyGamesYT9\AntiFrameEdit\AntiFrameEdit
 */
class AntiFrameEdit extends PluginBase implements Listener 
{
  
  /** @var Config $config */
  private $config;
  
  /**
   * @return void 
   */
  public function onEnable(): void 
  {
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  
  /**
   * @param PlayerInteractEvent $event
   * @return void 
   */
  public function onInteractFrame(PlayerInteractEvent $event): void 
  {
    $player = $event->getPlayer();
    $block = $event->getBlock();
    $action = $event->getAction();
    if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
      if ($player->hasPermission("antiframeedit.place.bypass") or $player->isOp()) {
        return;
      }
      $event->setCancelled(true);
      $player->sendMessage(str_replace(["&"], ["§"], $this->config->get("no.place.item.frame")));
    } else if ($action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
      if ($player->hasPermission("antiframeedit.remove.bypass") or $player->isOp()) {
        return;
      }
      $event->setCancelled(true);
      $player->sendMessage(str_replace(["&"], ["§"], $this->config->get("no.remove.item.frame")));
    }
  }
}