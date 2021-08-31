<?php

namespace JonyGamesYT9\AntiFrameEdit;

use pocketmine\block\Block;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerInteractEvent;
use function str_replace;

/**
* Class AntiFrameEdit
* @package JonyGamesYT9\AntiFrameEdit
*/
class AntiFrameEdit extends PluginBase implements Listener
{

  /** @var Config $config */
  private Config $config;

  /** @var array $world */
  public $worlds = [];

  /** @var array $items */
  public $items = [];

  /**
  * @return void
  */
  public function onEnable(): void
  {
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    foreach ($this->config->get("worlds") as $world) {
      $this->worlds[] = $world;
    }
    foreach ($this->config->get("prohibited-items") as $item) {
      $this->items[] = $item;
    }
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  /**
  * @return array
  */
  public function getWorlds(): array
  {
    return $this->worlds ?? [];
  }

  /**
  * @return array
  */
  public function getProhibitedItems(): array
  {
    return $this->items ?? [];
  }
  
  public function onInteractFrame(PlayerInteractEvent $event): void 
  {
      $player = 
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
    foreach ($this->getAllWorlds() as $world) {
      foreach ($this->getAllProhibitedItems() as $item) {
        if ($player->getLevel()->getFolderName() === $world) {
          if ($block->getId() === Block::ITEM_FRAME_BLOCK) {
            if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
              if ($player->hasPermission("antiframeedit.place.bypass") or $player->isOp()) {
                return;
              }
              $hand = $player->getInventory()->getItemInHand();
              if ($hand->getId() === (int) $item) {
                $player->sendMessage(str_replace(["&"], ["§"], $this->config->get("prohibited.item.usage")));
                return;
              }
              $event->setCancelled(true);
              $player->sendPopup(str_replace(["&"], ["§"], $this->config->get("no.place.item.frame")));
            } else if ($action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
              if ($player->hasPermission("antiframeedit.remove.bypass") or $player->isOp()) {
                return;
              }
              $hand = $player->getInventory()->getItemInHand();
              if ($hand->getId() === (int) $item) {
                $player->sendMessage(str_replace(["&"], ["§"], $this->config->get("prohibited.item.usage")));
                return;
              }
              $event->setCancelled(true);
              $player->sendPopup(str_replace(["&"], ["§"], $this->config->get("no.remove.item.frame")));
            }
          }
        }
      }
    }
  }
}