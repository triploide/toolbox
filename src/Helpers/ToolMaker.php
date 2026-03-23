<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Helpers;

use Symfony\Component\Console\Command\Command;
use Illuminate\Support\Str;
use Triploide\Toolbox\Enums\Tool;

class ToolMaker
{
    private ?string $enviroment;
    private ?string $context;
    private ?string $resource;
    private string $namespaceRoot = 'App';

    private function __construct()
    {
        
    }

    public static function make(): self
    {
        return new self();
    }

    public function enviroment(string $enviroment): self
    {
        $this->enviroment = $enviroment;

        return $this;
    }

    public function context(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function resource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function namespaceRoot(string $namespaceRoot): self
    {
        $this->namespaceRoot = $namespaceRoot;

        return $this;
    }

    /**
     * @param array $tools Tool[]
     */
    public function generate(array $tools): int
    {
        // TODO: refactor
        foreach ($tools as $tool) {
            if ($tool == Tool::ACTION) {
                foreach (['store', 'update', 'delete'] as $action) {
                    if ($stub = $this->getStub($tool->value . '.' . $action)) {
                        $stub = $this->replaceTokens($stub);

                        $this->saveActionFile($stub, $tool, $action);
                    }
                }
            } else {
                if ($stub = $this->getStub($tool->value)) {
                    $stub = $this->replaceTokens($stub);

                    $this->saveFile($stub, $tool);
                }
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $filename
     * @return string|false — The function returns the read data or false on failure.
     */
    public function getStub(string $filename): string|false
    {
        return file_get_contents(__DIR__ . "/../../stubs/$filename.stub");
    }

    /**
     * @param string $stub
     * @return string
     */
    private function replaceTokens(string $stub)
    {
        return Str::of($stub)
            ->replace('{{ namespaceRoot }}', $this->namespaceRoot)
            ->replace('{{ enviroment }}', $this->enviroment)
            ->replace('{{ context }}', $this->context)
            ->replace('{{ resource }}', $this->resource)
            ->__toString()
        ;
    }

    /**
     * @param string $stub
     * @param Tool $tool
     */
    private function saveFile(string $stub, Tool $tool)
    {
        // TODO: refactor
        $path = app_path($tool->folder() . '/' . $this->enviroment . '/' . $this->context); // e.g app/Validators/Api/Customer
        $filename = $this->resource . $tool->name()  . '.php'; // e.g OrderValidator.php
        $uri = "$path/$filename"; // e.g app/Validators/Api/Customer/OrderValidator.php

        if (!file_exists($uri)) {
            if (!is_dir($path)) {
                mkdir($path, 0744, true);
            }
            
            file_put_contents($uri, $stub);
        }
    }

    /**
     * @param string $stub
     * @param Tool $tool
     * @param string $action
     */
    private function saveActionFile(string $stub, Tool $tool, string $action)
    {
        // TODO: refactor
        $path = app_path($tool->folder() . '/' . $this->enviroment . '/' . $this->context); // e.g app/Actions/Api/Customer
        $filename = ucfirst($action) . $this->resource . '.php'; // e.g DeleteOrder.php
        $uri = "$path/$filename"; // e.g app/Actions/Api/Customer/DeleteOrder.php

        if (!file_exists($uri)) {
            if (!is_dir($path)) {
                mkdir($path, 0744, true);
            }
            
            file_put_contents($uri, $stub);
        }
    }
}
