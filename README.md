# ACF Blocks for Pebble (Lumberjack & Primer)

This package provides a way to create [ACF Blocks](https://www.advancedcustomfields.com/resources/blocks/) that use Twig templates and simultaneously integrate with both Lumberjack and Primer.

## Installation

`composer require rareloop/pebble-acf-blocks`

Once installed, register the Service Provider in config/app.php:

```php
'providers' => [
    ...

    Rareloop\Lumberjack\AcfBlocks\AcfBlocksServiceProvider::class,

    ...
],
```

Copy the example `config/acfblocks.php` file to you theme directory.

## Usage

To create a block, first create a child of the `AcfBlock`. This should sit in the same folder as your Primer component, for example `blocks/my-block`. The name of the class should also be the Upper Camel Case version of the folder name, in our case `MyBlock`.

_Note: Pebble maps the namespace `\Patterns` to the directory `my-theme/resources/patterns`_

```php
<?php

namespace Patterns\Blocks\TestBlock;

use Rareloop\Lumberjack\AcfBlocks\AcfBlock;

class TestBlock extends AcfBlock
{
    /**
     * Provide the data to pass to the template
     *
     * @return array
     */
    public function context(): array
    {
        return [
            'name' => get_field('test_field'),
        ];
    }

    /**
     * Provide the config required to register this block
     * https://www.advancedcustomfields.com/resources/acf_register_block_type/
     *
     * @return array
     */
    public static function blockConfig(): array
    {
        return [
            'name'              => 'raretestblock',
            'title'             => __('Test Block'),
            'description'       => __('A first go with a block.'),
            'category'          => 'formatting',
            'icon'              => 'admin-comments',
            'keywords'          => ['testimonial', 'quote'],
        ];
    }
}
```

The `blockConfig()` function is what ACF uses to register the block with WordPress. For more configuration options please see the [ACF documentation](https://www.advancedcustomfields.com/resources/acf_register_block_type/).

The `context()` function is where you provide the data for your patterns `template.twig` file when used in WordPress. Within this function, all calls to `get_field()` will be scoped to the current Gutenberg block, as is the case with other ACF Blocks.

### Additional Parameters

You have access to the following additional parameters from within your ACF class:

- `$this->content` - The block inner HTML (empty)
- `$this->isPreview` - Whether or not the block is being shown in preview
- `$this->postId` - The ID of the post that the block is attached to
