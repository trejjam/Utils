<?php

namespace Trejjam\Utils\Helpers;

use Tracy;

class BaseTracyPanel implements Tracy\IBarPanel
{
    protected int $count = 0;

    protected array $cmd = [];

    public string $name;

    public bool $disabled = FALSE;

    protected string $tabTemplate = '';
    protected string $panelTemplate = '';

    public function __construct(AShellExecute $execute, string|null $tabTemplate = NULL, string|null $panelTemplate = NULL)
    {
        $execute->logger[] = [$this, 'logCmd'];
        $this->name = $execute->getLoggerName();

        if (!is_null($tabTemplate)) {
            $this->tabTemplate = $tabTemplate;
        }
        if (!is_null($panelTemplate)) {
            $this->panelTemplate = $panelTemplate;
        }
    }

    public function logCmd(AShellExecute $that, $message, $title, $time)
    {
        if ($this->disabled) {
            return;
        }
        $this->count++;

        $this->cmd[] = [
            'message' => $message,
            'title' => $title,
            'time' => $time,
        ];
    }

    public function getTab()
    {
        $name = $this->name;
        $count = $this->count;

        if (!$count) {
            return '';
        }

        ob_start();
        require $this->tabTemplate;

        return ob_get_clean();
    }


    public function getPanel()
    {
        $this->disabled = TRUE;
        if (!$this->count) {
            return NULL;
        }

        $name = $this->name;
        $count = $this->count;
        $cmd = $this->cmd;

        ob_start();
        require $this->panelTemplate;

        return ob_get_clean();
    }
}
