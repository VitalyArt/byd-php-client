<?php

declare(strict_types=1);

/**
 * Generate the checked-in Markdown API reference from the public PHP surface.
 *
 * This intentionally uses PHP's own reflection and token parser so the
 * documentation build does not need a second API-description dependency.
 */

const ROOT = __DIR__.'/..';
const OUTPUT = ROOT.'/docs/reference/generated';

spl_autoload_register(static function (string $class): void {
    $prefix = 'Byd\\ApiClient\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $file = ROOT.'/src/'.str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$categories = [
    'clients' => ['Byd\\ApiClient\\BydClient', 'Byd\\ApiClient\\BydWatchClient'],
    'configuration' => 'Byd\\ApiClient\\Config\\',
    'services' => 'Byd\\ApiClient\\Service\\',
    'requests' => 'Byd\\ApiClient\\Dto\\Request\\',
    'responses' => 'Byd\\ApiClient\\Dto\\Response\\',
    'enums' => 'Byd\\ApiClient\\Enum\\',
    'exceptions' => 'Byd\\ApiClient\\Exception\\',
    'values' => 'Byd\\ApiClient\\Value\\',
];

foreach ($categories as $category => $prefix) {
    $directory = OUTPUT.'/'.$category;
    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
        throw new RuntimeException("Unable to create {$directory}");
    }

    foreach (glob($directory.'/*.md') ?: [] as $file) {
        unlink($file);
    }
}

$classes = [];
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOT.'/src', FilesystemIterator::SKIP_DOTS));
foreach ($files as $fileInfo) {
    if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
        continue;
    }
    $file = $fileInfo->getPathname();
    $contents = file_get_contents($file);
    if ($contents === false) {
        continue;
    }
    $tokens = token_get_all($contents);
    $namespace = '';
    $count = count($tokens);
    for ($i = 0; $i < $count; $i++) {
        if (is_array($tokens[$i]) && $tokens[$i][0] === T_NAMESPACE) {
            $namespace = '';
            for ($i++; $i < $count; $i++) {
                if (is_array($tokens[$i]) && in_array($tokens[$i][0], [T_STRING, T_NAME_QUALIFIED], true)) {
                    $namespace .= $tokens[$i][1];
                } elseif ($tokens[$i] === '\\') {
                    $namespace .= '\\';
                } elseif ($tokens[$i] === ';' || $tokens[$i] === '{') {
                    break;
                }
            }
        }
        if (is_array($tokens[$i]) && in_array($tokens[$i][0], [T_CLASS, T_ENUM, T_INTERFACE], true)) {
            $j = $i + 1;
            while ($j < $count && (!is_array($tokens[$j]) || $tokens[$j][0] !== T_STRING)) {
                $j++;
            }
            if ($j < $count) {
                $classes[] = $namespace.'\\'.$tokens[$j][1];
            }
            break;
        }
    }
}

$written = [];
foreach ($categories as $category => $prefix) {
    $members = [];
    foreach ($classes as $class) {
        $matches = is_array($prefix)
            ? in_array($class, $prefix, true)
            : str_starts_with($class, $prefix);
        if (!$matches) {
            continue;
        }
        if (!class_exists($class) && !enum_exists($class)) {
            continue;
        }
        $members[] = enum_exists($class) ? new ReflectionEnum($class) : new ReflectionClass($class);
    }
    usort($members, static fn (ReflectionClass $a, ReflectionClass $b): int => strcmp($a->getName(), $b->getName()));

    $index = "# ".ucfirst($category)."\n\n";
    $index .= "Generated from the public API in `src/`. Run `php tools/generate-reference.php` to refresh this section.\n\n";
    foreach ($members as $reflection) {
        $short = $reflection->getShortName();
        $file = $short.'.md';
        $index .= sprintf("- [`%s`](%s)\n", $short, $file);
        file_put_contents(OUTPUT.'/'.$category.'/'.$file, renderClass($reflection));
        $written[] = OUTPUT.'/'.$category.'/'.$file;
    }
    file_put_contents(OUTPUT.'/'.$category.'/index.md', $index);
}

echo sprintf("Generated %d API reference pages.\n", count($written));

function renderClass(ReflectionClass $reflection): string
{
    $name = $reflection->getName();
    $output = '# `'.$name.'`'."\n\n";
    $output .= summary($reflection->getDocComment())."\n\n";
    $output .= "**Type:** `".($reflection->isEnum() ? 'enum' : 'class')."`  \n";
    $output .= "**Source:** `".str_replace(realpath(ROOT).'/', '', $reflection->getFileName() ?: '')."`\n\n";

    if ($reflection instanceof ReflectionEnum) {
        $output .= "## Cases\n\n| Case | Backed value |\n| --- | --- |\n";
        foreach ($reflection->getCases() as $case) {
            $value = method_exists($case, 'getBackingValue') ? $case->getBackingValue() : null;
            $output .= '| `'.$case->getName().'` | `'.(is_scalar($value) ? (string) $value : '—').'` |'."\n";
        }
        $output .= "\n";
    }

    $properties = array_filter($reflection->getProperties(ReflectionProperty::IS_PUBLIC), static fn (ReflectionProperty $property): bool => !$property->isStatic());
    if ($properties !== []) {
        $output .= "## Public properties\n\n| Property | Type | Default |\n| --- | --- | --- |\n";
        foreach ($properties as $property) {
            $default = $property->hasDefaultValue() ? formatValue($property->getDefaultValue()) : '—';
            $output .= sprintf("| `%s` | `%s` | `%s` |\n", $property->getName(), typeName($property->getType()), $default);
        }
        $output .= "\n";
    }

    $methods = array_filter($reflection->getMethods(ReflectionMethod::IS_PUBLIC), static fn (ReflectionMethod $method): bool => $method->getDeclaringClass()->getName() === $reflection->getName() && !$method->isInternal());
    if ($methods !== []) {
        $output .= "## Public methods\n\n";
        foreach ($methods as $method) {
            $output .= '### `'.$method->getName().'`'."\n\n";
            $output .= summary($method->getDocComment())."\n\n";
            $output .= "```php\n".methodSignature($method)."\n```\n\n";
        }
    }

    return $output;
}

function methodSignature(ReflectionMethod $method): string
{
    $parameters = implode(', ', array_map(static fn (ReflectionParameter $parameter): string => parameterSignature($parameter), $method->getParameters()));
    $visibility = $method->isPublic() ? 'public ' : '';
    $static = $method->isStatic() ? 'static ' : '';

    return sprintf('%s%sfunction %s(%s): %s', $visibility, $static, $method->getName(), $parameters, typeName($method->getReturnType(), $method->getDeclaringClass()));
}

function parameterSignature(ReflectionParameter $parameter): string
{
    $type = typeName($parameter->getType());
    $type = $type === 'mixed' ? '' : $type.' ';
    $reference = $parameter->isPassedByReference() ? '&' : '';
    $variadic = $parameter->isVariadic() ? '...' : '';
    $default = '';
    if ($parameter->isDefaultValueAvailable() && !$parameter->isVariadic()) {
        $default = ' = '.formatValue($parameter->getDefaultValue());
    }

    return $type.$reference.$variadic.'$'.$parameter->getName().$default;
}

function typeName(?ReflectionType $type, ?ReflectionClass $context = null): string
{
    if ($type === null) {
        return 'mixed';
    }
    if ($type instanceof ReflectionNamedType) {
        $name = $type->getName();
        if ($context !== null && $name === 'self') {
            $name = $context->getName();
        }
        if (!$type->isBuiltin()) {
            $name = '\\'.$name;
        }
        return ($type->allowsNull() && $name !== 'null' ? '?' : '').$name;
    }

    return (string) $type;
}

function formatValue(mixed $value): string
{
    if ($value === null) return 'null';
    if ($value === true) return 'true';
    if ($value === false) return 'false';
    if (is_string($value)) return "'".addslashes($value)."'";
    if (is_array($value)) return '[]';
    if ($value instanceof BackedEnum) return $value::class.'::'.$value->name;
    if ($value instanceof UnitEnum) return $value::class.'::'.$value->name;
    if (is_object($value)) return 'new \\'.$value::class.'(...)';
    return (string) $value;
}

function summary(string|false|null $docComment): string
{
    if ($docComment === false || $docComment === null) {
        return '_No PHPDoc description provided._';
    }
    $text = preg_replace('/^\/\*\*|\*\/$/', '', $docComment) ?? $docComment;
    $text = preg_replace('/^\s*\* ?/m', '', $text) ?? $text;
    $text = preg_replace('/^\s*@.*$/m', '', $text) ?? $text;
    $text = trim($text);

    return $text !== '' ? $text : '_No PHPDoc description provided._';
}
