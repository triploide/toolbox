<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Console\Commands;

use Triploide\Toolbox\Helpers\ToolMaker;
use Illuminate\Console\Command;
use Triploide\Toolbox\Contexts\ToolCommandContext;
use Triploide\Toolbox\Enums\Tool;

use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\info;

class MakeTool extends Command
{
    protected $signature = 'make:tool';
    protected $description = 'Create a new tool interactively';
    protected $namespaceRoot = 'App';
    protected ToolCommandContext $context;

    public function __construct()
    {
        parent::__construct();

        $this->context = new ToolCommandContext();
    }

    public function handle()
    {
        // Choose resource
        $this->context->resource = text(
            label: 'Resource name (e.g. "User", "Post", "Order")',
            required: true
        );

        // Choose enviroments
        $this->selectManyOf('enviroments');

        // Choose contexts
        $this->selectManyOf('contexts');

        // Choose tools
        $this->selectManyOf('tools', collect(Tool::cases())->map->name()->toArray());

        // Show summary and confirm creation
        $this->showConfimr();

        // Generate the tools based on the selected options
        $this->generateTools();

        info('Tool created successfully.');
    }

    private function selectManyOf(string $selectable, ?array $options = null): array
    {
        $options ??= config("toolbox.{$selectable}");

        if (count($options) === 0) {
            warning("No {$selectable} defined in config/toolbox.php. Please add at least one {$selectable} to proceed.");
            // Stop the command execution since {$selectable} are required to create a tool
            exit(1);
        }

        if (count($options) === 1) {
            $selectedItems = $options; // Automatically select the only available item
            info("Only one {$selectable} available. Automatically selected: " . $options[0]);
        } else {
            $selectedItems = multiselect(
                label: "Select {$selectable}",
                options: $options,
                required: true
            );
        }

        $this->context->{$selectable} = $selectedItems;

        return $selectedItems;
    }

    private function showConfimr(): void
    {
        info('');
        info('Tool summary');
        info('------------');
        info("Resource: {$this->context->resource}");
        info("Enviroments: " . implode(', ', $this->context->enviroments));
        info("Contexts: " . implode(', ', $this->context->contexts));
        info("Tools: " . implode(', ', $this->context->tools));

        if (! confirm('Create this tool?', default: true)) {
            warning('Tool creation cancelled.');
            exit(0);
        }
    }

    private function generateTools()
    {
        $tools = collect(Tool::cases())
            ->filter(fn (Tool $tool) => in_array($tool->name(), $this->context->tools))
            ->toArray()
        ;

        foreach ($this->context->enviroments as $enviroment) {
            foreach ($this->context->contexts as $context) {
                ToolMaker::make()
                    ->enviroment($enviroment)
                    ->context($context)
                    ->namespaceRoot($this->namespaceRoot)
                    ->resource($this->context->resource)
                    ->generate($tools)
                ;
            }
        }
    }
}