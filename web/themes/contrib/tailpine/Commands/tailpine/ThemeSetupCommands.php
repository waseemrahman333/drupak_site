<?php

declare(strict_types=1);

namespace Drush\Commands\tailpine;

use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Drush\Drush;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;


/**
 * Class ThemeSetupCommands.
 */
class ThemeSetupCommands extends DrushCommands {

  /**
   * Tailpine theme setup command.
   */
  #[CLI\Command(name: 'tailpine:setup', aliases: ['tailpine'])]
  #[CLI\Usage(name: 'drush tailpine:setup', description: 'Sets up the Tailpine theme requirements.')]
  #[CLI\Bootstrap(level: \Drush\Boot\DrupalBootLevels::FULL)]

  public function createThemeSetup() {
    try {

      $drupalRoot = Drush::bootstrapManager()->getRoot();
      $rootPath = dirname($drupalRoot);

      // Define paths.
      $themePath = $drupalRoot . '/themes/custom/tailpine'; // Path to the theme directory.
      $ddevConfigPath = "$rootPath/.ddev/config.yaml";

      $this->logger()->notice("Setting up Tailpine theme...");
      $this->logger()->notice("Drupal root path: $drupalRoot");
      $this->logger()->notice("Theme path: $themePath");
      $this->logger()->notice("Top root path: $rootPath");

      // Copy files and directories.
      $filesystem = new Filesystem();
      $this->copyFile("$themePath/assets/scaffold/package.json", "$rootPath/package.json", $filesystem);

      // Copy .storybook directory if it doesn't exist.
      $storybookDir = "$rootPath/.storybook";
      if (!$filesystem->exists($storybookDir)) {
        $this->logger()->notice("Copying Storybook scaffold...");
        $filesystem->mirror("$themePath/assets/scaffold/storybook", $storybookDir);
      } else {
        $this->logger()->notice(".storybook directory already exists. Skipping copy.");
      }
      // // Copy optionalConfig directory if it doesn't exist.
      // $optionalConfigDir = "$rootPath/optionalConfig";
      // if (!$filesystem->exists($optionalConfigDir)) {
      //   $this->logger()->notice("Copying optionalConfig scaffold...");
      //   $filesystem->mirror("$themePath/assets/scaffold/optionalConfig", $optionalConfigDir);
      // } else {
      //   $this->logger()->notice("optionalConfig directory already exists. Skipping copy.");
      // }

      // Copy recipes directory if it doesn't exist.
      $optionalConfigDir = "$rootPath/recipes";
      // if (!$filesystem->exists($optionalConfigDir)) {
        $this->logger()->notice("Copying recipes scaffold...");
        $filesystem->mirror("$themePath/assets/scaffold/recipes", $optionalConfigDir);
      // } else {
      //   $this->logger()->notice("recipes directory already exists. Skipping copy.");
      // }

      // Create images directory in the project root if it doesn't exist.
      $imagesDir = "$rootPath/images";
      if (!$filesystem->exists($imagesDir)) {
        $this->logger()->notice("Creating images directory in the project root...");
        $filesystem->mkdir($imagesDir);
      } else {
        $this->logger()->notice("Images directory already exists. Skipping creation.");
      }

      // Add DDEV customizations if .ddev directory exists
      if ($filesystem->exists("$rootPath/.ddev")) {
        $this->logger()->notice("Adding DDEV customizations...");
        $this->addDdevCustomizations($ddevConfigPath, $filesystem);
      }

      // Update development.services.yml
      $this->logger()->notice("Update development.services.yml...");
      $this->updateDevelopmentServicesYml($drupalRoot, $filesystem);

      // Run npm install in the theme directory.
      $this->logger()->notice("Running npm install in the theme directory...");
      $this->runCommand("npm install", $themePath);

      // Enable the Storybook module and set permissions.
      $this->logger()->notice("Enabling Storybook module and setting permissions...");
      $this->enableStorybookModule($rootPath);

      // Run npm install in the root directory.
      $this->logger()->notice("Running npm install in the root directory...");
      $this->runCommand("npm install", $rootPath);

      // Enable the theme and set as a default theme.
      $this->logger()->notice("Enable the theme and set as a default theme...");
      $this->runCommand('drush then tailpine -y; drush config-set system.theme default tailpine -y', $rootPath);
      // Check if Tailpine is default theme
      $default_theme = \Drupal::config('system.theme')->get('default');
      $optionalConfigPath = $rootPath . '/optionalConfig';

      if (is_dir($optionalConfigPath) && $default_theme) {
        $this->logger()->notice("Importing optional config from: $optionalConfigPath");
        $this->runCommand("drush config:import --partial --source=$optionalConfigPath -y", $rootPath);
      } else {
        $this->logger()->warning("Skipping optional config import.");
      }

      // Clear cache.
      $this->logger()->notice("Clearing cache...");
      $this->runCommand("drush cr", $rootPath);

      // Run Storybook.
      $this->logger()->notice("Running Storybook...");
      $this->runCommand("npm run storybook -- --no-open", $rootPath);
    } catch (\Exception $exception) {
      $this->logger()->error($exception->getMessage());
    }
  }

  /**
   * Adds DDEV customizations to config.yml.
   */
  protected function addDdevCustomizations(string $configPath, Filesystem $filesystem): void {
    $customConfig = <<<YAML

    ###############################################################################
    # Customizations
    ###############################################################################
    nodejs_version: "18"
    webimage_extra_packages:
      - pkg-config
      - libpixman-1-dev
      - libcairo2-dev
      - libpango1.0-dev
      - make
    web_extra_exposed_ports:
      - name: storybook
        container_port: 6006
        http_port: 6007
        https_port: 6006
    web_extra_daemons:
      - name: node.js
        command: "tail -F package.json > /dev/null"
        directory: /var/www/html
    hooks:
      post-start:
        - exec: echo '================================================================================='
        - exec: echo '                                  NOTICE'
        - exec: echo '================================================================================='
        - exec: echo 'The node.js container is ready. You can start storybook by typing:'
        - exec: echo 'ddev yarn storybook'
        - exec: echo
        - exec: echo 'By default it will be available at https://change-me.ddev.site:6006'
        - exec: echo "Use ddev describe to confirm if this doesn't work."
        - exec: echo 'Check the status of startup by running "ddev logs --follow --time"'
        - exec: echo '================================================================================='

    ###############################################################################
    # End of customizations
    ###############################################################################
    YAML;

    // Append the custom configuration if the file exists
    if ($filesystem->exists($configPath)) {
      $currentContent = file_get_contents($configPath);

      // Only append if the custom config isn't already there
      if (strpos($currentContent, '# Customizations') === false) {
        file_put_contents($configPath, $currentContent . $customConfig);
        $this->logger()->notice("Added DDEV customizations to config.yml");
      } else {
        $this->logger()->notice("DDEV customizations already exist in config.yml. Skipping.");
      }
    } else {
      $this->logger()->warning("DDEV config.yml not found at $configPath");
    }
  }

  protected function updateDevelopmentServicesYml(string $web, Filesystem $filesystem): void {
    $devServicesPath = "$web/sites/development.services.yml";

    if (!$filesystem->exists($devServicesPath)) {
      $this->logger()->warning("development.services.yml not found at $devServicesPath");
      return;
    }

    $newParameters = <<<YAML
    parameters:
      http.response.debug_cacheability_headers: true
      storybook.development: true
      cors.config:
        enabled: true
        allowedHeaders: ['*']
        allowedMethods: ['*']
        allowedOrigins: ['*']
        exposedHeaders: false
        maxAge: false
        supportsCredentials: true
    YAML;

    // Read existing content
    $content = file_get_contents($devServicesPath);

    // Replace parameters section using regex
    $pattern = '/parameters:(.*?)(\n\w|\Z)/s';
    $newContent = preg_replace($pattern, $newParameters . "\n\n$2", $content);

    if ($newContent !== null) {
      file_put_contents($devServicesPath, $newContent);
      $this->logger()->notice("Updated parameters in development.services.yml");
    } else {
      $this->logger()->error("Failed to update development.services.yml");
    }
  }

  /**
   * Copies a file from source to destination.
   */
  protected function copyFile(string $source, string $destination, Filesystem $filesystem): void {
    if ($filesystem->exists($source)) {
      $filesystem->copy($source, $destination);
      $this->logger()->notice("Copied $source to $destination.");
    } else {
      $this->logger()->warning("Source file not found: $source");
    }
  }

  /**
   * Runs a shell command in the specified directory.
   */
  protected function runCommand(string $command, string $workingDirectory): void {
    $process = Process::fromShellCommandline($command, $workingDirectory);
    $process->setTimeout(null);
    $process->run(function ($type, $buffer) {
      $this->logger()->notice($buffer);
    });

    if (!$process->isSuccessful()) {
      throw new \RuntimeException("Command failed: {$process->getErrorOutput()}");
    }
  }

  /**
   * Enables the Storybook module and sets permissions.
   */
  protected function enableStorybookModule(string $rootPath): void {
    $drushCommand = file_exists("$rootPath/.ddev") ? 'drush' : "$rootPath/vendor/bin/drush";

    $this->runCommand("$drushCommand en storybook -y", $rootPath);
    $this->runCommand("$drushCommand role:perm:add 'authenticated' 'render storybook stories'", $rootPath);
    $this->runCommand("$drushCommand role:perm:add 'anonymous' 'render storybook stories'", $rootPath);
  }
}
