<?php

declare(strict_types=1);

namespace Drush\Commands\tailpine;

use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Drush\Drush;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;

/**
 * Drush command to generate .stories.twig files for Storybook.
 */
class GenerateStoriesCommands extends DrushCommands {

  /**
   * The filesystem service.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected Filesystem $filesystem;

  /**
   * The theme path.
   *
   * @var string
   */
  protected string $themePath;

  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->filesystem = new Filesystem();
  }

  /**
   * Generates .stories.twig files for all components or a specific component.
   */
  #[CLI\Command(name: 'generate:stories-twig', aliases: ['gen-stories', 'gst'])]
  #[CLI\Usage(name: 'drush generate:stories-twig', description: 'Generates .stories.twig files for all components in the theme.')]
  #[CLI\Usage(name: 'drush generate:stories-twig --component=button', description: 'Generates .stories.twig file for a specific component.')]
  #[CLI\Option(name: 'theme', description: 'The theme name. Defaults to tailpine.')]
  #[CLI\Option(name: 'component', description: 'Generate stories for a specific component only (e.g., button, badge).')]
  #[CLI\Bootstrap(level: \Drush\Boot\DrupalBootLevels::FULL)]
  public function generateStories(array $options = ['theme' => 'tailpine', 'component' => NULL]) {
    try {
      $themeName = $options['theme'];
      $drupalRoot = Drush::bootstrapManager()->getRoot();
      $this->themePath = "$drupalRoot/themes/custom/$themeName";

      if (!$this->filesystem->exists($this->themePath)) {
        $this->logger()->error("Theme path not found: {$this->themePath}");
        return;
      }

      $componentsPath = "{$this->themePath}/components";
      if (!$this->filesystem->exists($componentsPath)) {
        $this->logger()->error("Components directory not found: {$componentsPath}");
        return;
      }

      // Check if a specific component was requested
      $specificComponent = $options['component'];
      
      if ($specificComponent) {
        $this->logger()->notice("Processing specific component: {$specificComponent}");
        $componentDir = $this->findSpecificComponentDir($componentsPath, $specificComponent);
        
        if (!$componentDir) {
          $this->logger()->error("Component not found: {$specificComponent}");
          return;
        }
        
        $result = $this->processComponent($componentDir, $specificComponent);
        
        if ($result['story_yml_created']) {
          $this->logger()->success("✓ Created story YAML files for: {$specificComponent}");
        }
        if ($result['stories_twig_created']) {
          $this->logger()->success("✓ Created stories.twig file for: {$specificComponent}");
        }
        
        return;
      }

      $this->logger()->notice("Scanning components in: {$componentsPath}");
      $this->logger()->warning("⚠ This will process ALL components. Use --component=name to process only one.");

      // Find all component directories
      $componentDirs = $this->findComponentDirectories($componentsPath);

      $totalComponents = count($componentDirs);
      $processedCount = 0;
      $createdStories = 0;
      $createdStoryYml = 0;

      foreach ($componentDirs as $componentDir) {
        $componentName = basename($componentDir);
        $this->logger()->notice("Processing component: {$componentName}");

        $result = $this->processComponent($componentDir, $componentName);
        
        if ($result['story_yml_created']) {
          $createdStoryYml++;
        }
        if ($result['stories_twig_created']) {
          $createdStories++;
        }
        
        $processedCount++;
      }

      $this->logger()->success("✓ Processed {$processedCount} components");
      $this->logger()->success("✓ Created {$createdStoryYml} default .story.yml files");
      $this->logger()->success("✓ Created {$createdStories} .stories.twig files");

    }
    catch (\Exception $exception) {
      $this->logger()->error($exception->getMessage());
    }
  }

  /**
   * Find all component directories.
   */
  protected function findComponentDirectories(string $componentsPath): array {
    $componentDirs = [];
    $finder = new Finder();
    $finder->files()
      ->in($componentsPath)
      ->name('*.component.yml')
      ->depth('>= 0');

    foreach ($finder as $file) {
      $componentDirs[] = $file->getPath();
    }

    return array_unique($componentDirs);
  }

  /**
   * Find a specific component directory by name.
   */
  protected function findSpecificComponentDir(string $componentsPath, string $componentName): ?string {
    $finder = new Finder();
    
    try {
      $finder->files()
        ->in($componentsPath)
        ->name("$componentName.component.yml")
        ->depth('>= 0');

      foreach ($finder as $file) {
        return $file->getPath();
      }
    }
    catch (\Exception $e) {
      // Component not found
    }

    return null;
  }

  /**
   * Process a single component.
   */
  protected function processComponent(string $componentDir, string $componentName): array {
    $result = [
      'story_yml_created' => false,
      'stories_twig_created' => false,
    ];

    $componentYmlPath = "$componentDir/$componentName.component.yml";
    
    if (!$this->filesystem->exists($componentYmlPath)) {
      $this->logger()->warning("  Component YAML not found for: {$componentName}");
      return $result;
    }

    // Read component definition
    $componentDef = Yaml::parseFile($componentYmlPath);

    // Debug: Check if variants exist
    $hasVariants = isset($componentDef['variants']) && !empty($componentDef['variants']);
    if ($hasVariants) {
      $variantCount = count($componentDef['variants']);
      $this->logger()->notice("  Found {$variantCount} variants in component definition");
    }
    else {
      $this->logger()->notice("  No variants found in component definition");
    }

    // Find existing .story.yml files
    $storyFiles = $this->findStoryFiles($componentDir, $componentName);

    // Determine if we need to create story files
    $needsStoryFiles = false;
    
    if (empty($storyFiles)) {
      $needsStoryFiles = true;
      $this->logger()->notice("  No .story.yml files found.");
    }
    elseif ($hasVariants) {
      // Check if variant-based stories are missing
      $existingStoryNames = array_map(function($file) use ($componentName) {
        return str_replace("$componentName.", '', $file['basename']);
      }, $storyFiles);
      
      $hasDefaultOnly = (count($existingStoryNames) === 1 && in_array('default', $existingStoryNames));
      
      if ($hasDefaultOnly) {
        $this->logger()->notice("  Found only default story, but component has variants. Removing default and creating variant stories...");
        // Remove the default story file
        foreach ($storyFiles as $file) {
          if (strpos($file['filename'], '.default.story.yml') !== false) {
            $this->filesystem->remove($file['path']);
            $this->logger()->notice("  Removed: {$file['filename']}");
          }
        }
        $needsStoryFiles = true;
      }
    }

    // Create story files if needed
    if ($needsStoryFiles) {
      if ($hasVariants) {
        $this->logger()->notice("  Creating stories for {$variantCount} variants...");
        $createdCount = $this->createVariantStoryYmls($componentDir, $componentName, $componentDef);
      }
      else {
        $this->logger()->notice("  Creating default story...");
        $this->createDefaultStoryYml($componentDir, $componentName, $componentDef);
      }
      $storyFiles = $this->findStoryFiles($componentDir, $componentName);
      $result['story_yml_created'] = true;
    }
    else {
      $this->logger()->notice("  Using existing .story.yml files");
    }

    // Generate .stories.twig file (overwrites if exists)
    $storiesTwigPath = "$componentDir/$componentName.stories.twig";
    
    if ($this->filesystem->exists($storiesTwigPath)) {
      $this->logger()->notice("  Overwriting existing .stories.twig file...");
    }
    else {
      $this->logger()->notice("  Creating .stories.twig file...");
    }
    
    $this->createStoriesTwig($componentDir, $componentName, $storyFiles, $componentDef);
    $result['stories_twig_created'] = true;

    return $result;
  }

  /**
   * Find all .story.yml files for a component.
   */
  protected function findStoryFiles(string $componentDir, string $componentName): array {
    $storyFiles = [];
    $finder = new Finder();
    
    try {
      $finder->files()
        ->in($componentDir)
        ->name("$componentName.*.story.yml")
        ->sortByName();

      foreach ($finder as $file) {
        $storyFiles[] = [
          'path' => $file->getRealPath(),
          'basename' => $file->getBasename('.story.yml'),
          'filename' => $file->getFilename(),
        ];
      }
    }
    catch (\Exception $e) {
      // Directory might be empty or inaccessible
    }

    return $storyFiles;
  }

  /**
   * Create .story.yml files for each variant.
   */
  protected function createVariantStoryYmls(string $componentDir, string $componentName, array $componentDef): int {
    $createdCount = 0;
    $variants = $componentDef['variants'] ?? [];

    foreach ($variants as $variantName => $variantDef) {
      $storyData = [
        'name' => $variantDef['title'] ?? ucfirst(str_replace('_', ' ', $variantName)),
        'props' => $this->generateVariantProps($componentDef, $variantName, $variantDef),
      ];

      // Add slots if component has slots
      if (isset($componentDef['slots']) && !empty($componentDef['slots'])) {
        $storyData['slots'] = $this->generateDefaultSlots($componentDef);
      }

      // Sanitize variant name for filename
      $safeVariantName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $variantName));
      $storyYmlPath = "$componentDir/$componentName.$safeVariantName.story.yml";
      $yamlContent = Yaml::dump($storyData, 4, 2);
      
      $this->filesystem->dumpFile($storyYmlPath, $yamlContent);
      $this->logger()->success("    ✓ Created: $componentName.$safeVariantName.story.yml");
      $createdCount++;
    }

    return $createdCount;
  }

  /**
   * Create a default .story.yml file.
   */
  protected function createDefaultStoryYml(string $componentDir, string $componentName, array $componentDef): void {
    $storyData = [
      'name' => 'Default',
      'props' => $this->generateDefaultProps($componentDef),
    ];

    // Add slots if component has slots
    if (isset($componentDef['slots']) && !empty($componentDef['slots'])) {
      $storyData['slots'] = $this->generateDefaultSlots($componentDef);
    }

    $storyYmlPath = "$componentDir/$componentName.default.story.yml";
    $yamlContent = Yaml::dump($storyData, 4, 2);
    
    $this->filesystem->dumpFile($storyYmlPath, $yamlContent);
    $this->logger()->success("  ✓ Created: $componentName.default.story.yml");
  }

  /**
   * Generate props for a specific variant.
   */
  protected function generateVariantProps(array $componentDef, string $variantName, array $variantDef): array {
    // Start with default props
    $props = $this->generateDefaultProps($componentDef);

    // Check if variant definition has explicit props (extended format)
    if (isset($variantDef['props']) && is_array($variantDef['props'])) {
      $props = array_merge($props, $variantDef['props']);
      return $props;
    }

    // Otherwise, intelligently set props based on variant name
    // Parse compound variant names like "large_primary" or "small_outline"
    $parts = explode('_', $variantName);
    
    // Size mapping for common words to enum values
    $sizeMap = [
      'small' => 'sm',
      'medium' => 'md',
      'large' => 'lg',
      'extra' => 'xl',
    ];
    
    // Check for size in variant name
    if (isset($componentDef['props']['properties']['size'])) {
      $validSizes = $componentDef['props']['properties']['size']['enum'] ?? [];
      foreach ($parts as $part) {
        // Direct match
        if (in_array($part, $validSizes)) {
          $props['size'] = $part;
          break;
        }
        // Mapped match (e.g., "large" -> "lg")
        if (isset($sizeMap[$part]) && in_array($sizeMap[$part], $validSizes)) {
          $props['size'] = $sizeMap[$part];
          break;
        }
      }
    }
    
    // Check for variant in variant name
    if (isset($componentDef['props']['properties']['variant'])) {
      $validVariants = $componentDef['props']['properties']['variant']['enum'] ?? [];
      
      // Try to match the full variant name first
      if (in_array($variantName, $validVariants)) {
        $props['variant'] = $variantName;
      }
      else {
        // Check if any part matches a valid variant
        foreach ($parts as $part) {
          if (in_array($part, $validVariants)) {
            $props['variant'] = $part;
            break;
          }
        }
      }
    }

    // Update text prop to match the variant title
    if (isset($props['text']) && isset($variantDef['title'])) {
      $props['text'] = $variantDef['title'];
    }

    return $props;
  }

  /**
   * Generate default props for a component.
   */
  protected function generateDefaultProps(array $componentDef): array {
    $props = [];

    if (!isset($componentDef['props']['properties'])) {
      return $props;
    }

    foreach ($componentDef['props']['properties'] as $propName => $propDef) {
      // Skip attributes and variant
      if (in_array($propName, ['attributes', 'variant', 'badge_attributes'])) {
        continue;
      }

      $props[$propName] = $this->generateDefaultPropValue($propDef);
    }

    return $props;
  }

  /**
   * Generate a default value for a prop.
   */
  protected function generateDefaultPropValue(array $propDef): mixed {
    // If there's a default value, use it
    if (isset($propDef['default'])) {
      return $propDef['default'];
    }

    // If there are examples, use the first one
    if (isset($propDef['examples']) && !empty($propDef['examples'])) {
      return $propDef['examples'][0];
    }

    // If there's an enum, use the first value
    if (isset($propDef['enum']) && !empty($propDef['enum'])) {
      return $propDef['enum'][0];
    }

    // Generate based on type
    $type = $propDef['type'] ?? 'string';
    
    switch ($type) {
      case 'boolean':
        return false;
      case 'number':
      case 'integer':
        return 0;
      case 'array':
        return [];
      case 'string':
      default:
        $title = $propDef['title'] ?? 'Value';
        return $title;
    }
  }

  /**
   * Generate default slots for a component.
   */
  protected function generateDefaultSlots(array $componentDef): array {
    $slots = [];

    if (!isset($componentDef['slots'])) {
      return $slots;
    }

    foreach ($componentDef['slots'] as $slotName => $slotDef) {
      $title = $slotDef['title'] ?? ucfirst(str_replace('_', ' ', $slotName));
      $slots[$slotName] = $title;
    }

    return $slots;
  }

  /**
   * Create a .stories.twig file.
   */
  protected function createStoriesTwig(string $componentDir, string $componentName, array $storyFiles, array $componentDef): void {
    $themeName = basename(dirname(dirname($componentDir)));
    $categoryName = $this->generateCategoryName($componentName, $componentDef);
    
    // Convert hyphens to underscores for Twig identifier (hyphens are not valid in identifiers)
    $storiesIdentifier = str_replace('-', '_', $componentName);
    
    $content = "{% stories $storiesIdentifier with { title: '$categoryName' } %}\n\n";

    $storyIndex = 1;
    foreach ($storyFiles as $storyFile) {
      $storyId = $this->extractStoryId($storyFile['basename'], $componentName);
      $storyData = Yaml::parseFile($storyFile['path']);
      $storyName = $storyData['name'] ?? ucfirst(str_replace('_', ' ', $storyId));
      
      $content .= $this->generateStoryBlock(
        $storyId,
        $storyIndex,
        $storyName,
        $storyData,
        $componentName,
        $themeName,
        $componentDef
      );
      
      $storyIndex++;
    }

    $content .= "{% endstories %}\n";

    $storiesTwigPath = "$componentDir/$componentName.stories.twig";
    $this->filesystem->dumpFile($storiesTwigPath, $content);
    $this->logger()->success("  ✓ Created: $componentName.stories.twig");
  }

  /**
   * Extract story ID from filename.
   */
  protected function extractStoryId(string $basename, string $componentName): string {
    // Remove component name prefix and .story.yml suffix
    $storyId = str_replace("$componentName.", '', $basename);
    return $storyId;
  }

  /**
   * Generate category name for the stories.
   */
  protected function generateCategoryName(string $componentName, array $componentDef): string {
    $name = $componentDef['name'] ?? ucfirst(str_replace('_', ' ', $componentName));
    return "Components/$name";
  }

  /**
   * Generate a single story block.
   */
  protected function generateStoryBlock(
    string $storyId,
    int $index,
    string $storyName,
    array $storyData,
    string $componentName,
    string $themeName,
    array $componentDef
  ): string {
    $content = "{% story $storyId with {\n";
    $content .= "    name: '$index. $storyName',\n";
    $content .= "    args: { \n";

    // Add props to args
    $props = $storyData['props'] ?? [];
    $propsLines = [];
    foreach ($props as $propName => $propValue) {
      if (in_array($propName, ['attributes', 'variant', 'badge_attributes'])) {
        continue;
      }
      $propsLines[] = $this->formatArgLine($propName, $propValue);
    }

    // Add slots to args if they exist and are simple strings
    $slots = $storyData['slots'] ?? [];
    foreach ($slots as $slotName => $slotValue) {
      if (is_string($slotValue)) {
        $propsLines[] = $this->formatArgLine($slotName, $slotValue);
      }
    }

    $content .= implode(",\n", $propsLines);
    $content .= "\n    }\n  } %}\n";

    // Determine if component has slots
    $hasSlots = isset($componentDef['slots']) && !empty($componentDef['slots']);

    // Build the embed
    $embedProps = [];
    foreach ($props as $propName => $propValue) {
      if (in_array($propName, ['attributes', 'variant', 'badge_attributes'])) {
        continue;
      }
      $embedProps[] = "$propName: $propName";
    }

    if (!empty($embedProps)) {
      $content .= "{% embed '$themeName:$componentName' with { \n";
      $content .= "  " . implode(",\n  ", $embedProps) . "\n";
      $content .= "} %}";
    }
    else {
      $content .= "{% embed '$themeName:$componentName' %}";
    }

    // Add slot blocks if component has slots
    if ($hasSlots) {
      $content .= "\n";
      foreach ($slots as $slotName => $slotValue) {
        $content .= "  {% block $slotName %}\n";
        $content .= "    {{ $slotName }}\n";
        $content .= "  {% endblock %}\n";
      }
    }

    $content .= "{% endembed %}\n";
    $content .= "{% endstory %}\n\n";

    return $content;
  }

  /**
   * Format an argument line for the args section.
   */
  protected function formatArgLine(string $name, mixed $value): string {
    $formattedValue = $this->formatValue($value);
    return "      $name: $formattedValue";
  }

  /**
   * Format a value for YAML/Twig output.
   */
  protected function formatValue(mixed $value): string {
    if (is_bool($value)) {
      return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
      return 'null';
    }
    if (is_numeric($value)) {
      return (string) $value;
    }
    if (is_array($value)) {
      return json_encode($value);
    }
    // Escape single quotes in strings
    $escaped = str_replace("'", "\\'", (string) $value);
    return "'$escaped'";
  }

}

