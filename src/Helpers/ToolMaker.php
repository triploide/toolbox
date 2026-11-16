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
    private array $map = [];

    private function __construct()
    {
        $this->map = [
            Tool::ACTION->value => [
                'actions' => ['store', 'update', 'destroy'],
                'path' => '{{ folder }}/{{ enviroment }}/{{ context }}/{{ resource }}',
                'filename' => '{{ action }}{{ resource }}.php',
            ],
            Tool::REQUEST->value => [
                'actions' => ['store', 'update'],
                'path' => '{{ folder }}/{{ enviroment }}/{{ context }}/{{ resource }}',
                'filename' => '{{ action }}{{ resource }}Request.php',
            ],
            Tool::RESOURCE->value => [
                'actions' => ['index', 'show'],
                'path' => '{{ folder }}/{{ enviroment }}/{{ context }}/{{ resource }}',
                'filename' => '{{ action }}{{ resource }}Resource.php',
            ],
        ];
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
        foreach ($tools as $tool) {
            $this->generateTool($tool);
        }

        return Command::SUCCESS;
    }

    private function generateTool(Tool $tool): void
    {
        $path = $this->map[$tool->value]['path'] ?? '{{ folder }}/{{ enviroment }}/{{ context }}';
        $filename = $this->map[$tool->value]['filename'] ?? '{{ resource }}{{ name }}.php';
        $actions = $this->map[$tool->value]['actions'] ?? [''];

        foreach ($actions as $action) {
            $stubName = $action === '' ? $tool->value : "{$tool->value}.{$action}";

            if ($stub = $this->getStub($stubName)) {
                $this->saveFile(
                    $this->replaceTokens($stub, $tool, $action),
                    app_path($this->replaceTokens($path, $tool, $action)),
                    ucfirst($this->replaceTokens($filename, $tool, $action)),
                );
            }
        }
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
     * @param Tool $tool
     * @param ?string $action
     * @return string
     */
    private function replaceTokens(string $stub, Tool $tool, ?string $action = ''): string
    {
        return Str::of($stub)
            ->replace('{{ namespaceRoot }}', $this->namespaceRoot)
            ->replace('{{ enviroment }}', $this->enviroment)
            ->replace('{{ context }}', $this->context)
            ->replace('{{ resource }}', $this->resource)
            ->replace('{{ folder }}', $tool->folder())
            ->replace('{{ name }}', $tool->name())
            ->replace('{{ action }}', $action)
            ->__toString()
        ;
    }

    /**
     * @param string $stub
     * @param string $path
     * @param string $filename
     * @return void
     */
    private function saveFile(string $stub, string $path, string $filename): void
    {
        $uri = "$path/$filename";

        if (!file_exists($uri)) {
            if (!is_dir($path)) {
                mkdir($path, 0744, true);
            }
            
            file_put_contents($uri, $stub);
        }
    }
}
