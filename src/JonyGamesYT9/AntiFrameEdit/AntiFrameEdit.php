<?php

namespace JonyGamesYT9\AntiFrameEdit;

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

  /** @var Config $messages */
  private $messages;

  /** @var string[] $world */
  public static $world;

  /**
  * @return void
  */
  public function onEnable(): void
  {
    $this->saveResource("messages.yml");
    $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  /**
  * @return string
  */
  public function getAllWorlds(): string
  {
    foreach ($this->config->get("worlds") as $worlds) {
      self::$world[] = $worlds;
    }
    return self::$world ?? "";
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
    if ($player->getLevel()->getFolderName() === $this->getAllWorld()) {
      if ($block->getId() === Block::ITEM_FRAME_BLOCK) {
        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
          if ($player->hasPermission("antiframeedit.place.bypass") or $player->isOp()) {
            return;
          }
          $event->setCancelled(true);
          $player->sendPopup(str_replace(["&"], ["§"], $this->config->get("no.place.item.frame")));
        } else if ($action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
          if ($player->hasPermission("antiframeedit.remove.bypass") or $player->isOp()) {
            return;
          }
          $event->setCancelled(true);
          $player->sendPopup(str_replace(["&"], ["§"], $this->config->get("no.remove.item.frame")));
        }
      }
    }
  }
}