<?php

declare(strict_types=1);

namespace Drush\Commands\tailpine;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\AnnotatedCommand\CommandError;
use Drush\Attributes as CLI;
use Drush\Boot\DrupalBootLevels;
use Drush\Commands\DrushCommands;
use Drush\Drush;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

// Drush PHP attributes uses a semi-qualified namespace. Suppress phpcs.
// phpcs:disable Drupal.Classes.FullyQualifiedNamespace.UseStatementMissing

/**
 * Class SubThemeCommands handles tailpine subtheme creation.
 */
class SubThemeCommands extends DrushCommands {

  /**
   * Creates a tailpine sub-theme.
   */
  #[CLI\Command(name: 'tailpine:create', aliases: ['tailpine'])] 
   #[CLI\Argument(name: 'name', description: 'The machine-readable name of your sub-theme.')]
  #[CLI\Bootstrap(level: DrupalBootLevels::FULL)]
  #[CLI\Usage(name: 'drush tailpine:create my-theme', description: 'Creates a tailpine sub-theme called my_theme, using the tailpine_starterkit.')]
  public function createSubTheme(string $name) {
    try {
      $this->copyStarterKit();
      $this->generateTheme($name);
      $this->copyDotFiles($name);
      $this->removeCopiedStarterKit();

      // Success message.
      $this->logger()->success("🚀 Sub-theme '{$name}' created successfully. You may now enable it in the Appearance section of the Drupal administration or by Drush as shown below:");

      $this->printHeading(PHP_EOL . "tailpine DOCUMENTATION");
      $this->logger()->notice("Read the tailpine comprehensive documentation or tl;dr below:");
      $this->printCommand('https://tailpine.studio/introduction/introducing-tailpine');

      $this->printHeading("🟡 STEP 1");
      $this->logger()->notice("Enable and set {$name} as the default theme:");
      $this->printCommand("ddev drush then {$name} -y");
      $this->printCommand("ddev drush config-set system.theme default {$name} -y");

      $this->printHeading("🟡 STEP 2");
      $this->logger()->notice("Go to the root of the {$name} theme:");
      $this->printCommand("cd web/themes/custom/{$name}");

      $this->printHeading("🟡 STEP 3");
      $this->logger()->notice("Install the required node packages:");
      $this->printCommand('npm install');

      $this->printHeading("✅ STEP 4");
      $this->logger()->notice("Run the following command to compile Tailwind CSS, JS and build for other changes:");
      $this->printCommand('npm run build');

      $this->printHeading("✅ STEP 5");
      $this->logger()->notice("Run the following command to compile Tailwind CSS, JS and watch for other changes:");
      $this->printCommand('npm run watch');

      $this->printHeading("✅ STEP 6");
      $this->logger()->notice("Clear the cache and start building 🥷");
      $this->printCommand("drush cr");
    }
    catch (\Exception $exception) {
      $this->logger()->error($exception->getMessage());
    }
  }

  /**
   * Function to print command.
   *
   * @param string $command
   *   Hold command to be print.
   */
  private function printCommand(string $command) {
    $formattedCommand = "<fg=green>$command</>";
    $this->output()->writeln($formattedCommand);
  }

  /**
   * Function to print heading.
   *
   * @param string $heading
   *   Hold heading data to be print.
   */
  private function printHeading(string $heading) {
    $formattedHeading = PHP_EOL . "<options=bold>$heading:</>";
    $this->output()->writeln($formattedHeading);
  }

  /**
   * Function to copy starterkit components.
   */
  private function copyStarterKit() {
    $filesystem = new Filesystem();
    $drupalRoot = Drush::bootstrapManager()->getRoot();
    $source = $drupalRoot . '/themes/contrib/tailpine/src/kits/tailpine_starterkit';
    $destination = $drupalRoot . '/themes/custom/tailpine_starterkit';
    $filesystem->mirror($source, $destination);
  }

  /**
   * Temporary function to copy the dot files.
   *
   * @see: https://www.drupal.org/project/drupal/issues/3456699
   */
  private function copyDotFiles(string $themeName) {
    $filesystem = new Filesystem();
    $drupalRoot = Drush::bootstrapManager()->getRoot();
    $source = $drupalRoot . '/themes/contrib/tailpine/src/kits/tailpine_starterkit';
    $destination = $drupalRoot . '/themes/custom/' . $themeName;

    $dotFiles = [
      '.gitignore',
      '.nvmrc',
      '.env.example',
      '.browserslistrc',
      '.stylelintrc.json',
      '.npmrc',
      '.stylelintignore',
      '.gitkeep',
    ];
    foreach ($dotFiles as $file) {
      if (file_exists("$source/$file")) {
        $filesystem->copy("$source/$file", "$destination/$file");
      }
    }
  }

  /**
   * Function to generate theme.
   *
   * @param string $themeName
   *   Holds theme name to generate.
   */
  private function generateTheme(string $themeName) {
    $drupalRoot = Drush::bootstrapManager()->getRoot();
    
    // Get info from the original starterkit info.yml file
    $infoFile = $drupalRoot . '/themes/contrib/tailpine/src/kits/tailpine_starterkit/tailpine_starterkit.info.yml';
    $info = \Drupal\Component\Serialization\Yaml::decode(file_get_contents($infoFile));
    
    // Get the description and version from the info file
    $description = $info['description'] ?? '';
    $version = $info['version'] ?? '1.0.0';
    
    // Replace tailpine_starterkit with the actual theme name in the description
    $description = str_replace('tailpine_starterkit', $themeName, $description);
    
    $process = new Process([
      'php', $drupalRoot . '/core/scripts/drupal', 'generate-theme', 
      '--starterkit', 'tailpine_starterkit', 
      $themeName, 
      '--path', 'themes/custom',
      '--description', $description
    ]);
    $process->run();
    
    if (!$process->isSuccessful()) {
      throw new \RuntimeException($process->getErrorOutput());
    }
    
    // Update the version in the generated theme's info.yml file
$newInfoFile = $drupalRoot . '/themes/custom/' . $themeName . '/' . $themeName . '.info.yml';
$folderName = $drupalRoot . '/themes/custom/' . $themeName . '/Commands/tailpine_starterkit';
$targetFolder = $drupalRoot . '/themes/custom/' . $themeName . '/Commands/' . $themeName;

if (file_exists($newInfoFile)) {
  $newInfo = \Drupal\Component\Serialization\Yaml::decode(file_get_contents($newInfoFile));
  $newInfo['version'] = $version;
  file_put_contents($newInfoFile, \Drupal\Component\Serialization\Yaml::encode($newInfo));
}

// 🟢 Rename the folder if it exists
if (file_exists($folderName)) {
  $filesystem = new Filesystem();

  // Ensure the parent directory exists before renaming
  $parentDir = dirname($targetFolder);
  if (!$filesystem->exists($parentDir)) {
    $filesystem->mkdir($parentDir, 0755);
  }

  // Rename folder
  $filesystem->rename($folderName, $targetFolder, true);

  // Optional logging (if inside a Drush command class)
  $this->logger()->success("✅ Folder renamed: {$folderName} → {$targetFolder}");
}

  }

  /**
   * Function to remove starterkit components.
   */
  private function removeCopiedStarterKit() {
    $filesystem = new Filesystem();
    $drupalRoot = Drush::bootstrapManager()->getRoot();
    $starterkit = $drupalRoot . '/themes/custom/tailpine_starterkit';
    if (is_dir($starterkit)) {
      $filesystem->remove($starterkit);
    }
  }

  /**
   * Function to validate created subtheme.
   *
   * @hook validate tailpine:create
   */
  public function validateCreateSubTheme(CommandData $commandData): ?CommandError {
    $name = $commandData->input()->getArgument('name');
    if (!$this->isValidName($name)) {
      return new CommandError("Invalid theme name: '$name'. Name must be a non-empty string.");
    }
    return NULL;
  }

  /**
   * Function to check for valid name.
   *
   * @param string $name
   *   The subtheme name.
   */
  private function isValidName(string $name): bool {
    return !empty($name);
  }

}
