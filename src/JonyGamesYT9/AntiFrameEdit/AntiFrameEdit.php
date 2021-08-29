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
* @package \JonyGamesYT9\AntiFrameEdit
*/
class AntiFrameEdit extends PluginBase implements Listener
{

  private Config $messages;

  public function onEnable(): void
  {
    $this->saveResource("messages.yml");
    $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  /**
   * @return string[]
   */
  public function getWorlds(): array
  {
    return $this->getConfig()->get("worlds") ?? [];
  }

  /**
   * @return int[]
   */
  public function getProhibitedItems(): array
  {
    $this->getConfig()->get("prohibited-items") ?? [];
  }

  public function onInteractFrame(PlayerInteractEvent $event): void
  {
    $player = $event->getPlayer();
    $block = $event->getBlock();
    $action = $event->getAction();
    foreach ($this->getWorlds() as $world) {
      foreach ($this->getProhibitedItems() as $item) {
        if ($player->getLevel()->getFolderName() == $world) {
          if ($block->getId() == Block::ITEM_FRAME_BLOCK) {
            $hand = $player->getInventory()->getItemInHand();
            if ($action == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
              if ($player->hasPermission("antiframeedit.place.bypass") or $player->isOp()) {
                return;
              }
              if ($hand->getId() == $item) {
                $player->sendMessage(str_replace(["&"], ["§"], $this->messages->get("prohibited.item.usage")));
                return;
              }
              $event->setCancelled(true);
              $player->sendPopup(str_replace(["&"], ["§"], $this->messages->get("no.place.item.frame")));
            } elseif ($action == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
              if ($player->hasPermission("antiframeedit.remove.bypass") or $player->isOp()) {
                return;
              }
              if ($hand->getId() == $item) {
                $player->sendMessage(str_replace(["&"], ["§"], $this->messages->get("prohibited.item.usage")));
                return;
              }
              $event->setCancelled(true);
              $player->sendPopup(str_replace(["&"], ["§"], $this->messages->get("no.remove.item.frame")));
            }
          }
        }
      }
    }
  }
}
