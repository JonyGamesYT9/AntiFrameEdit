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
  public array $worlds = [];

  /** @var array $items */
  public array $items = [];

  /**
  * @return void
  */
  public function onEnable(): void
  {
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    if ($this->config->get("enable-multiworld-support") === true) {
      foreach ($this->config->get("worlds") as $world) {
        $this->worlds[] = $world;
      }
    } else {
      foreach ($this->getServer()->getWorlds() as $world) {
        $this->worlds[] = $world->getFolderName();
      }
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

  /**
  * @param PlayerInteractEvent $event
  * @return void
  */
  public function onInteractFrame(PlayerInteractEvent $event): void
  {
    $player = $event->getPlayer();
    $block = $event->getBlock();
    $action = $event->getAction();
    switch ($action) {
      case PlayerInteractEvent::RIGHT_CLICK_BLOCK:
        foreach ($this->getWorlds() as $world) {
          if ($player->getWorld()->getFolderName() == $world) {
            if ($block->getId() == Block::ITEM_FRAME_BLOCK) {
              if ($this->config->get("only-admin-usage") === true) {
                if ($player->hasPermission("antiframeedit.place.bypass") or $player->isOp()) {
                  return;
                }
              }
              $hand = $player->getInventory()->getItemInHand();
              foreach ($this->getProhibitedItems() as $item) {
                if ($hand->getId() == (int)$item) {
                  $player->sendMessage(str_replace(["&"], ["§"], $this->config->get("prohibited.item.usage")));
                  return;
                }
              }
              $event->setCancelled(true);
              if ($this->config->get("no.place.item.frame") != null) {
                $player->sendPopup(str_replace(["&"], ["§"], $this->config->get("no.place.item.frame")));
              }
            }
          }
        }
        break;
      case PlayerInteractEvent::LEFT_CLICK_BLOCK:
        foreach ($this->getWorlds() as $world) {
          if ($player->getWorld()->getFolderName() == $world) {
            if ($block->getId() == Block::ITEM_FRAME_BLOCK) {
              if ($this->config->get("only-admin-usage") === true) {
                if ($player->hasPermission("antiframeedit.remove.bypass") or $player->isOp()) {
                  return;
                }
              }
              $event->setCancelled(true);
              if ($this->config->get("no.remove.item.frame") != null) {
                $player->sendPopup(str_replace(["&"], ["§"], $this->config->get("no.remove.item.frame")));
              }
            }
          }
        }
        break;
    }
  }
}