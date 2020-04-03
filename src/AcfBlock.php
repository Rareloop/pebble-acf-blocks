<?php

namespace Rareloop\Lumberjack\AcfBlocks;

use Timber\Timber;
use Stringy\Stringy;

abstract class AcfBlock
{
    protected $block;
    protected $content;
    protected $isPreview;
    protected $postId;

    public function __construct($block, $content = '', $isPreview = false, $postId = null)
    {
        $this->block = $block;
        $this->content = $content;
        $this->isPreview = $isPreview;
        $this->postId = $postId;
    }

    public function template(): string
    {
        $classParts = explode('\\', preg_replace('/^Patterns\\\/', '', static::class));
        array_pop($classParts);

        $classParts = collect($classParts)->map(function ($part) {
            return Stringy::create($part)->dasherize()->__toString();
        })->toArray();

        return implode('/', $classParts);
    }

    public function context(): array
    {
        return [];
    }

    public function render()
    {
        $data = $this->context();

        $data = apply_filters('pebble/acfblock/context', $data, $this->block, $this->content, $this->isPreview, $this->postId);
        $data = apply_filters('pebble/acfblock/context/name=' . static::name(), $data, $this->block, $this->content, $this->isPreview, $this->postId);

        $data = $this->flattenContextToArrays($data);
        echo Timber::compile($this->template(), $data);
    }

    private function flattenContextToArrays(array $context): array
    {
        // Recursively walk the array, when we find something that implements the Arrayable interface
        // flatten it to an array. Because we're passing by reference by updating what the value of
        // $item is will mutate the original data structure passed in.
        array_walk_recursive($context, function (&$item, $key) {
            if ($item instanceof Arrayable || $item instanceof CollectionArrayable) {
                $item = $this->flattenContextToArrays($item->toArray());
            }
        });

        return $context;
    }

    public static function blockConfig(): array
    {
        throw new \Exception('`blockConfig()` must be defined in sub class');
        return [];
    }

    public static function name(): string
    {
        $config = static::blockConfig();

        return $config['name'];
    }

    public static function register()
    {
        acf_register_block_type(array_merge(static::blockConfig(), [
            'render_callback' => function ($block, $content = '', $is_preview = false, $post_id = 0) {
                $block = new static($block, $content, $is_preview, $post_id);

                return $block->render();
            },
        ]));
    }
}
