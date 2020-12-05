<?php
declare(strict_types=1);

namespace RKAbdul\executeit;

use pocketmine\block\Block;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\Player;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockPlaceEvent;

use pocketmine\command\ConsoleCommandSender;

class ExecuteIt extends PluginBase implements Listener
{
    public const VERSION = 1;

    private $cfg;

    public function onEnable()
    {
        $this->cfg = $this->getConfig()->getAll();
        if ($this->cfg["version"] < self::VERSION) {
            $this->getLogger()->error("Config Version is outdated! Please delete your current config file!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function blockBreak(BlockBreakEvent $event)
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if ($this->cfg["BlockBreak"]["Enabled"] == true) {
            if ($this->cfg["BlockBreak"]["All-Worlds"] == true || $this->checkWorld($player, "BlockBreak")) {
                if ($this->checkBlock($block, "BlockBreak")) {
                    $commands = $this->getCommands($block->getId(), "BlockBreak");
                    foreach ($commands as $cmd) {
                        Server::getInstance()->dispatchCommand(new ConsoleCommandSender, str_replace("{player}", $player->getName(), $cmd));
                    }
                }
            }
        }
    }
    
    public function playerInteract(PlayerInteractEvent $event)
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if ($this->cfg["BlockInteract"]["Enabled"] == true) {
            if ($this->cfg["BlockInteract"]["All-Worlds"] == true || $this->checkWorld($player, "BlockInteract")) {
                if ($this->checkBlock($block, "BlockInteract")) {
                    $commands = $this->getCommands($block->getId(), "BlockInteract");
                    foreach ($commands as $cmd) {
                        Server::getInstance()->dispatchCommand(new ConsoleCommandSender, str_replace("{player}", $player->getName(), $cmd));
                    }
                }
            }
        }
    }
    
    public function blockPlace(BlockPlaceEvent $event)
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if ($this->cfg["BlockPlace"]["Enabled"] == true) {
            if ($this->cfg["BlockPlace"]["All-Worlds"] == true || $this->checkWorld($player, "BlockPlace")) {
                if ($this->checkBlock($block, "BlockPlace")) {
                    $commands = $this->getCommands($block->getId(), "BlockPlace");
                    foreach ($commands as $cmd) {
                        Server::getInstance()->dispatchCommand(new ConsoleCommandSender, str_replace("{player}", $player->getName(), $cmd));
                    }
                }
            }
        }
    }
    
    public function checkWorld(Player $player, String $eventType): bool
    {
        $world = $player->getLevel()->getName();
        $worlds = $this->cfg[$eventType]["Worlds"];
        foreach ($worlds as $w) {
            if ($w === $world) {
                return true;
            }
        }
        return false;
    }

    public function checkBlock(Block $block, String $eventType): bool
    {
        $id = $block->getId();
        $blocks = $this->cfg[$eventType]["Blocks"];
        foreach ($blocks as $bl) {
            $array = explode(":", $bl);
            if ($id == intval($array[0])) {
                return true;
            }
        }
        return false;
    }
    
    public function getCommands($id, $eventType) {
        $blocks = $this->cfg[$eventType]["Blocks"];
        foreach ($blocks as $bl) {
            $array = explode(":", $bl);
            if ($id == intval($array[0])) {
                unset($array[0]);
                return $array;
            }
        }
    }
}