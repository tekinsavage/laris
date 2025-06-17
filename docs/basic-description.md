# Laris CLI Commands — Let's Dive In

Alright, now that Laris is installed and ready to roll, it's time to have some real fun.

You didn't come here just to install stuff and walk away — you came here to *build things faster*, and maybe even *impress yourself a little*.

Laris isn't just another wrapper for `php artisan`. It’s your personal assistant with shortcuts, power tools, and AI magic — all packed in a clean command-line interface.

---

## What’s Next?

We’re going to walk through every command — one by one — like a cooking show, but for code.

No pressure. No mess. Just tasty tools to save your time.

You'll learn:

- How to generate files in a snap  
- How to use AI to skip boring boilerplate  
- How to install modules like a boss  
- And how to look cool doing it (optional)

---

### `laris:ai:config` — Configure Your AI Assistant

The `laris:ai:config` command launches an interactive configuration wizard to set up your preferred AI provider (OpenAI or OpenRouter) for use within Laris CLI.

This command allows you to define:

- **AI Provider** — Choose between `openai` or `openrouter`.
- **Model** — Specify the AI model you want to use (e.g., `deepseek/deepseek-r1-0528-qwen3-8b:free`).
- **API Key** — Securely enter your API key. This is required to connect to the selected provider.
- **Max Tokens** — Set a limit on the maximum tokens per completion to control response length and cost.
- **Default Prompt** — Define a base prompt that guides the AI's behavior for code generation and assistance.

Once completed, the configuration is saved to a `.laris-ai.json` file in the root of your project directory.

#### Example Usage

```bash
~> laris laris:ai:config

🤖 Laris AI Configuration Wizard
This setup will configure your AI provider, API key, and default behavior.
You can change these settings anytime by running this command again.

[?] Select your AI provider: openrouter
[?] Select Ai Model: deepseek/deepseek-r1-0528-qwen3-8b:free
[?] Enter your API key for [openrouter]: *************
[?] Maximum tokens per completion?: 1000
[?] Default prompt prefix for AI assistant?: You are a Laravel assistant that helps generate code...

✅ AI configuration saved successfully!
📄 Path: /your-project/.laris-ai.json
```

---

### `laris:ai:generate:docs` — Generate Documentation from PHP Class Using AI

The `laris:ai:generate:docs` command uses OpenRouter AI to analyze a given PHP class file and generate complete Markdown documentation for it. This is especially useful for creating consistent, high-quality documentation for your Laravel services, controllers, or other classes.

#### Features

- Automatically analyzes the structure and purpose of a PHP class.
- Generates clear, structured Markdown documentation.
- Includes:
  - Overview
  - Purpose
  - Dependencies
  - Method descriptions
  - Example usage (if applicable)
  - Laravel-specific behaviors
- Optionally saves the output as a `.md` file.

#### Prerequisites

Before using this command, make sure you’ve configured your AI settings by running:

```bash
~> laris laris:ai:config
```
##### Usage:
```bash
~> laris laris:ai:generate:docs app/Services/MyService.php
```

---

### `laris:ai:generate:module` — Generate a Full Laravel Module Using AI

The `laris:ai:generate:module` command allows you to quickly scaffold a complete Laravel module using OpenRouter AI. This is ideal for bootstrapping new features with consistent structure and Laravel 10 best practices.

#### What It Generates

When you provide a module name (e.g. `Product`), the AI will generate:

- ✅ **Eloquent Model**
- ✅ **Database Migration**
- ✅ **RESTful Controller** (with route model binding)
- ✅ **Form Request** (for input validation)
- ✅ **API Resource Class**
- ✅ **Service Class** (for business logic)
- ✅ **API Route Definition**

All code is formatted using **PSR-12** and adheres to modern **Laravel 10** conventions.

#### Prerequisites

Make sure your project is a valid Laravel project and that AI settings are configured using:

```bash
~> laris laris:ai:config


What is the name of the module? Product
🧠 Generating Laravel module with AI...
...

✅ AI Generated Code for Product Module

Do you want to save the files manually? [yes]
✅ Module generated and saved to: /your-project/ai-modules/Product.md
```

---

### `laris:ai:generate:readme` — Generate a Professional README.md with AI

The `laris:ai:generate:readme` command allows you to generate a clean, structured, and professional `README.md` file for your Laravel project using OpenRouter AI.

#### What It Includes

The generated README typically contains the following sections:

- 📛 **Project Title & Description**
- ⚙️ **Installation Instructions**
- 🔧 **Environment Setup**
- 🚀 **Usage Guide**
- 🎯 **Core Features**
- ✅ **Testing Instructions**
- 📄 **License Information**

All content is written in Markdown and designed for clarity and professionalism.

#### Prerequisites

Before using this command, make sure to:

1. Be inside a valid Laravel project (it checks for the `artisan` file).
2. Configure OpenRouter AI using:

```bash
~> laris laris:ai:generate:readme

Briefly describe your project (optional): A Laravel-based task management API.
📄 Generating README.md using OpenRouter AI...

✅ AI Generated README.md
Do you want to save this as README.md? [yes]
🎉 README.md file has been saved.

```

---

### `laris:ai:test:generate` — Generate PHPUnit Test Class with AI

This command uses OpenRouter AI to generate a fully functional PHPUnit test class for a given Laravel class file.

#### What It Does

- 🧪 Analyzes the PHP class (e.g., Service, Controller, etc.)
- ✅ Generates a PHPUnit-compatible test class
- 🎯 Applies Laravel best practices
- 🔁 Uses Mocks & Dependency Injection where needed

#### Prerequisites

- You must be inside a Laravel project (`artisan` file must exist)
- AI must be configured using:

```bash
~> laris laris:ai:test:generate

-Enter the path to the class (e.g., app/Services/MyService.php)


🧪 Generating test class using OpenRouter AI...
✅ AI Generated Test
Do you want to save this test? [yes]
✅ Test saved to tests/Feature/MyServiceTest.php
```

---


### 🧠 `laris:ai:make:command` — Generate Artisan Command Class

Generate a Laravel Artisan command using OpenRouter AI.

#### ✅ Features

- Uses Laravel 10 conventions
- Follows PSR-12 standards
- Uses Dependency Injection
- Generates full command boilerplate

#### 🔧 Usage

```bash
~> laris laris:ai:make:command

What is the name of the Artisan command class? [CleanupOldPostsCommand]
🧠 Generating Artisan Command using AI...
✅ AI Generated Command Code
Do you want to save this command to app/Console/Commands? [yes]
✅ Command saved to app/Console/Commands/CleanupOldPostsCommand.php
```

---

### 🧠 laris:ai:make:controller — Generate Controller Class

Generate a Laravel controller class with CRUD methods using OpenRouter AI.

#### ✅ Features

-Generates full CRUD methods (index, store, show, update, destroy)
-Uses route model binding
-Uses Form Requests where needed
-PSR-12 compliant and Laravel 10 ready

##### Usage:
```bash
~> laris laris:ai:make:controller

What is the name of the controller? [ExampleController]
🧠 Generating code using OpenRouter AI...
✅ AI Generated Controller Code
Do you want to save this controller to your Laravel project? [yes]
✅ Controller saved to app/Http/Controllers/ExampleController.php

```

### 🧠 `laris:ai:make:event` — Generate Laravel Event Class

Generates a Laravel Event class using OpenRouter AI.

#### ✅ Features

- Compatible with Laravel 10
- Uses PSR-12 code style
- Public properties for event payload
- Ready to be dispatched

#### 🔧 Usage

```bash
~> laris laris:ai:make:event

What is the name of the event? [ExampleEvent]
🧠 Generating event class...
Thinking...
✅ AI Generated Event Code
Save this event? [yes]
✅ Event saved to app/Events/ExampleEvent.php
```

---


Laris CLI includes a set of `laris:ai:make:*` commands that leverage OpenRouter AI to generate clean, PSR-12-compliant Laravel classes — instantly.

### ✨ Available AI Generators

Each command follows a consistent structure and usage pattern, including:

- Checking if the command is being run inside a Laravel project
- Validating the `.laris-ai.json` configuration
- Prompting the user for a class name
- Sending a carefully crafted prompt to OpenRouter AI
- Displaying and optionally saving the AI-generated code

---

## 🔧 AI Class Refactoring with `laris:ai:refactor:class`

The `laris:ai:refactor:class` command allows you to refactor any PHP class file using OpenRouter AI. It analyzes your existing code and returns a cleaner, more maintainable, and production-ready version following Laravel, PSR-12, and SOLID best practices.

### 🛠 Usage

```bash
~> laris laris:ai:refactor:class path/to/YourClass.php

🔍 Refactoring class using AI...
✅ Refactored Class

<?php

namespace App\Services;

class CleanedUpService {
    // ...
}
```

---

## 💾 Project Backup with `laris:backup`

The `laris:backup` command allows you to easily create a ZIP archive of your Laravel project while excluding unnecessary directories such as `vendor`, `node_modules`, `.git`, and sensitive files like `.env`.

### 🛠 Usage

```bash
~> laris laris:backup
```
#### 📋 What It Does

* Scans the current project directory.
* Excludes the following paths from the archive:

  * `vendor/`
  * `node_modules/`
  * `.git/`
  * `.env`
  * `backups/` (to avoid nested backups)
* Saves the `.zip` archive in the `/backups` directory at the root of your project.
* Filenames are timestamped to avoid overwrites (e.g., `backup_20250617_141230.zip`).

### ✅ Example Output

```
Creating backup...
[OK] Backup created successfully at: /your-project/backups/backup_20250617_141230.zip
```

### 📁 Folder Structure

```
your-project/
├── app/
├── backups/
│   └── backup_20250617_141230.zip
├── vendor/       ← excluded
├── node_modules/ ← excluded
├── .env          ← excluded
└── ...
```

---

This command is especially useful before running destructive operations or before pushing to production. Make sure to version your backups or move them off the server regularly. 🔐

---

## 📦 Composer Control with `laris:composer`

The `laris:composer` command gives you an interactive interface to run common Composer tasks within your Laravel project—without needing to type full commands every time.

### 🛠 Usage

```bash
~> laris laris:composer
```

#### 📋 Available Options

When you run the command, you will be presented with a list of actions:

* `update` – Run `composer update`
* `install` – Run `composer install`
* `require` – Prompt to install a Composer package
* `remove` – Prompt to uninstall a Composer package
* `dump-autoload` – Regenerate the Composer autoload files
* `quit` – Exit the menu

### 🔄 Interactive Mode

Each time you choose an option, the command executes the appropriate Composer command in your Laravel project root and displays the real-time output.

For example, if you choose:

```
> require
Enter package name to require (e.g. guzzlehttp/guzzle):
```

It will automatically run:

```bash
composer require guzzlehttp/guzzle
```

### ⚠️ Requirements

* Must be run inside a Laravel project directory (checks for `artisan`).
* Requires Composer to be installed and accessible in your terminal.

### ✅ Example Session

```
Composer options:
  [0] update
  [1] dump-autoload
  [2] install
  [3] require
  [4] remove
  [5] quit
 > 0

Running: composer update
Loading composer repositories with package information...
...
```

This tool simplifies managing Composer dependencies directly from your Laris CLI workflow. Perfect for CI/CD pipelines, teams, or developers who prefer terminal automation.

---


## ⚙️ Configuration Management with `laris:config`

The `laris:config` command provides an interface to manage project-specific settings using a local `.larisconfig.json` file.

### 🧾 Usage

```bash
~> laris laris:config <action> [key] [value]
````

### 🔧 Available Actions

* `get <key>` – Retrieve the value of a configuration key
* `set <key> <value>` – Set or update a key with a value (supports JSON values)
* `remove <key>` – Delete a key from configuration
* `list` – Display all configuration key-value pairs

### 🗂 Example

```bash
~> laris laris:config set api_url "https://example.com"
~> laris laris:config get api_url
~> laris laris:config remove api_url
~> laris laris:config list
```

> All settings are stored in `.larisconfig.json` in your project root.

---
## 🗄️ Database Management with `laris:db`

The `laris:db` command provides CLI access to database operations using the LarisDB library.

### 🧾 Usage

```bash
~> laris laris:db <action> [table] [--options]
````

### 🔧 Actions

* `list-tables` – List all database tables
* `describe-table <table>` – Show table columns and structure
* `select <table>` – Query records with filters and options
* `insert <table> --data='{"col":"val"}'` – Insert new record(s)
* `update <table> --where="id=1" --data='{"col":"newVal"}'` – Update record(s)
* `delete <table> --where="id=1"` – Delete record(s)
* `generate-fake <table> [--count=10]` – Seed table with fake data

### ⚙️ Options

* `--where="id=1,name!=Ali"` – Filter conditions (supports `=`, `!=`, `<`, `>`, `<=`, `>=`)
* `--order="id DESC"` – Sort results
* `--limit=10` – Limit records (default: 10)
* `--offset=0` – Offset for pagination
* `--fields="id,name"` – Select specific fields
* `--data='{"key":"value"}'` – JSON data for insert/update
* `--count=10` – Fake record count for `generate-fake`

### 🧪 Examples

```bash
~> laris laris:db list-tables
~> laris laris:db describe-table users
~> laris laris:db select users --where="active=1" --limit=5
~> laris laris:db insert users --data='{"name":"Ali","email":"ali@example.com"}'
~> laris laris:db update users --where="id=1" --data='{"name":"Ali Updated"}'
~> laris laris:db delete users --where="id=2"
~> laris laris:db generate-fake users --count=20
```

> ℹ️ Ensure your DB credentials are correctly configured in the command.

---

```bash
~> laris laris:deploy
```

### 📦 Deploy Command

The `laris:deploy` command prepares your Laris project for **production deployment**.

It performs the following steps **automatically**, depending on the files present in your project root:

---

#### ✅ Composer (if `composer.json` exists)

```bash
~> composer install --no-dev --optimize-autoloader
```

* Installs only production dependencies
* Optimizes the autoloader

---

#### ✅ NPM (if `package.json` exists)

```bash
~> npm install
~> npm run production
```

* Installs frontend dependencies
* Builds assets for production

---

#### ✅ Laravel Artisan (if `artisan` exists)

```bash
~> php artisan config:cache
~> php artisan route:cache
~> php artisan view:cache
```

* Clears and caches Laravel configurations, routes, and views

---

### 🧪 Example Output

```bash
~> laris laris:deploy

Starting deployment preparation

Running composer install --no-dev --optimize-autoloader ...
> Loading composer repositories...
> Installing dependencies...
> Generating optimized autoload files
Composer dependencies installed

Running npm install ...
> Installing node modules...
npm packages installed

Running npm run production ...
> Compiling assets...
npm build completed

Clearing and caching Laravel config & routes ...
> Configuration cached successfully!
> Routes cached successfully!
> Views cached successfully!

Deployment preparation completed successfully.
```

---


```bash
~> laris laris:docker
```

### 🐳 Docker Command

Interactive Docker management for Laravel projects using **`docker-compose`**.

This command only works if you're inside a **Laravel project** (i.e. `artisan` file exists).

---

### 📋 Available Options

| Option | Description                          |
| ------ | ------------------------------------ |
| build  | Build Docker images                  |
| up     | Start Docker containers (`-d`)       |
| down   | Stop and remove containers           |
| ps     | Show running containers              |
| logs   | Show real-time logs (select service) |
| exec   | Run a command inside a container     |
| quit   | Exit the Docker tool                 |

---

### 🧪 Example Usage

```bash
~> laris laris:docker

Docker options:
  [0] build
  [1] up
  [2] down
  [3] ps
  [4] logs
  [5] exec
  [6] quit
 > ps

Running: docker-compose ps

      Name                    Command               State           Ports         
----------------------------------------------------------------------------------
laravel-app        docker-php-entrypoint php-fpm   Up      9000/tcp
mysql              docker-entrypoint.sh mysqld     Up      3306/tcp
```

---

### 🔁 Logs Example

```bash
> logs
Enter service name for logs (empty for all): laravel-app

Running: docker-compose logs --tail 50 -f laravel-app
laravel-app  | [17-Jun-2025 10:12:03] NOTICE: ready to handle connections
```

---

### ⚙️ Exec Example

```bash
> exec
Enter service name to exec into: laravel-app
Enter command to run inside container: bash

Running: docker-compose exec laravel-app bash
root@container:/var/www#
```

---

### 📌 Notes

* Uses `docker-compose` behind the scenes.
* Output is streamed live using Symfony Process.
* Logs default to last 50 lines and follow (`-f`).
* Useful for local development with containers.

> ⚠️ Make sure `docker-compose.yml` exists and Docker is running.

---


```bash
~> laris laris:docs
```

### 📚 API Documentation Generator

Generates API documentation automatically from **PHPDoc comments** in your Laravel controllers.

---

### 🧩 Options

| Option     | Description                                | Default                  |
| ---------- | ------------------------------------------ | ------------------------ |
| `--path`   | Path to controllers directory              | `./app/Http/Controllers` |
| `--output` | Output file path (Markdown or HTML format) | `./docs/api-docs.md`     |

---

### ✅ Example Usage

```bash
~> laris laris:docs
```

This will:

* Look in `app/Http/Controllers` for controllers.
* Extract PHPDoc comments from each controller and public method.
* Save the result in `docs/api-docs.md`.

---

### 🧪 Custom Path Example

```bash
~> laris laris:docs --path=modules/User/Controllers --output=storage/docs/user-api.md
```

---

### 📄 Example Output (Markdown)

```md
# API Documentation

## Controller: App\Http\Controllers\UserController
### Method: getProfile

```

/\*\*

* Get the profile of the authenticated user.
*
* @return \Illuminate\Http\JsonResponse
  \*/

```
```

---

```bash
~> laris laris:git
```

### 🧩 Git Control Panel for Laravel Projects

Interactively manage Git inside your Laravel project.

---

### 🛠️ Available Git Options

| Command  | Description                        |
| -------- | ---------------------------------- |
| `init`   | Initialize a new Git repository    |
| `status` | Show current Git status            |
| `commit` | Stage and commit all changes       |
| `push`   | Push commits to the origin         |
| `remote` | Add a new Git remote URL           |
| `pull`   | Pull latest changes from origin    |
| `log`    | Display concise Git log with graph |
| `quit`   | Exit the interactive Git panel     |

---

### ✅ Example Usage

```bash
~> laris laris:git
```

You'll see an interactive prompt:

```
Git options:
  [init]    Initialize git repository
  [status]  Show git status
  [commit]  Commit changes
  ...
```

You can use arrow keys or type the name of the option.

---

### 💡 Example: Commit Flow

```bash
? Git options > commit
? Enter commit message: Add login form styles

Running: git add .
Running: git commit -m "Add login form styles"
```

---

### 📌 Notes

* Runs only if you're inside a Laravel project (`artisan` file must exist).
* Automates `git add .` before committing.
* Logs are displayed with `--oneline`, `--graph`, and `--all` flags for better readability.
* If a remote is already added, trying to add another may fail — manage with `git remote remove origin` manually if needed.

> 🔒 Safe & quick Git integration for Laravel developers.

---

```bash
~> laris laris:hook
```

### 🔗 Git Hook Manager for Laravel

Easily manage Git hooks (`.git/hooks`) in your Laravel project.

---

### 🛠️ Usage

```bash
laris laris:hook {action} [--name=...] [--script=...]
```

| Action   | Description                                  |
| -------- | -------------------------------------------- |
| `list`   | List all available Git hooks in `.git/hooks` |
| `add`    | Add a Git hook file with a script            |
| `remove` | Remove a Git hook                            |
| `show`   | Display the contents of a specific Git hook  |

---

### 🔧 Options

| Option     | Required for            | Description                              |
| ---------- | ----------------------- | ---------------------------------------- |
| `--name`   | `add`, `remove`, `show` | Name of the hook (e.g. `pre-commit`)     |
| `--script` | `add`                   | Shell command(s) to insert into the hook |

---

### ✅ Examples

#### ➕ Add a `pre-commit` hook

```bash
laris laris:hook add --name=pre-commit --script="php artisan test"
```

> Creates `.git/hooks/pre-commit` with the given script.

---

#### 📄 Show a hook content

```bash
laris laris:hook show --name=pre-commit
```

---

#### ❌ Remove a hook

```bash
laris laris:hook remove --name=pre-commit
```

---

#### 📋 List all hooks

```bash
laris laris:hook list
```

---

### 🧠 Notes

* Hooks are created as executable shell scripts.
* If a hook already exists, it will be overwritten with a warning.
* Only valid inside a Git-enabled project (must have `.git/hooks/`).

> 🚀 Perfect for automating tests, linting, or formatting before commits!

---

```bash
~> laris laris:new
```

### 🧱 Manage Custom Laris Commands

Create or delete your own dynamic Laris CLI commands easily.

---

### 🛠️ Usage

```bash
~> laris laris:new
```

Then follow the interactive prompts to either:

* ✅ **Create a new command**
* ❌ **Delete an existing command**

---

### 🔧 Prompts

| Prompt                     | Purpose                                            |
| -------------------------- | -------------------------------------------------- |
| `Action (create/delete)`   | Choose whether to create or delete a command       |
| `Command name`             | Name like `serve`, `migrate`, etc. (auto-prefixed) |
| `Description` (for create) | Human-readable explanation shown in help output    |
| Confirm (for delete)       | Confirm before deleting files                      |

> ⚠️ Names are automatically prefixed with `laris:` if you don’t include it.

---

### 📁 Generated Files

When creating a command named `laris:serve`, two files are saved under `Laris/Commands/Laris/`:

* `LarisServe.php` – The actual command class
* `LarisServe.txt` – Metadata about the command

---

### 🚫 Protected Commands

These built-in commands cannot be created or deleted:

```
laris:git, laris:docker, laris:composer, laris:db,
laris:npm, laris:config, laris:docs, laris:hook, laris:new
```

Attempting to overwrite/delete them will result in an error.

---

### ✅ Example: Create a custom command

```bash
~> laris laris:new
# Choose: create
# Command name: build-assets
# Description: Compile frontend assets
```

Creates `laris:build-assets` command that prints:

```bash
This is the command laris:build-assets
```

---

### ❌ Example: Delete a command

```bash
laris laris:new
# Choose: delete
# Command name: build-assets
# Confirm: yes
```

---

## `laris:npm` Command

Manage `npm`, `yarn`, or `pnpm` commands via the CLI.

This command detects the package manager (`npm`, `yarn`, or `pnpm`) based on lock files in your project directory and allows you to interact with it directly for common tasks like installing packages, running scripts, updating dependencies, cleaning cache, and more.

---

### 🔧 **Usage**

```bash
~> laris laris:npm <action> [packageOrScripts...] [options]
```

---

### 🧾 **Arguments**

| Argument           | Required | Description                                                                                             |
| ------------------ | -------- | ------------------------------------------------------------------------------------------------------- |
| `action`           | Yes      | One of: `install`, `update`, `run`, `build`, `cache-clean`, `scripts`, `version`, `npx`                 |
| `packageOrScripts` | No       | Package names (for install/update) or script names (for run/build). Can be multiple or comma-separated. |

---

### 🧩 **Options**

| Option       | Description                                                   |
| ------------ | ------------------------------------------------------------- |
| `--save-dev` | Add the installed package(s) as development dependencies      |
| `--save`     | Explicitly save package(s) as production dependencies         |
| `--json`     | (Currently unused) Future support for JSON output for scripts |
| `--force`    | Force clean npm/yarn/pnpm cache                               |

---

### 📦 **Actions**

#### `install`

Install dependencies or specific packages.

```bash
~> laris laris:npm install
~> laris laris:npm install axios vue --save
~> laris laris:npm install jest --save-dev
```

#### `update`

Update all or specific packages.

```bash
~> laris laris:npm update
~> laris laris:npm update vue
```

#### `run`

Run one or more scripts defined in `package.json`.

```bash
~> laris laris:npm run dev
~> laris laris:npm run dev,watch
```

#### `build`

Alias for `run build`.

```bash
~> laris laris:npm build
```

#### `cache-clean`

Clean the cache for the detected package manager.

```bash
~> laris laris:npm cache-clean
~> laris laris:npm cache-clean --force
```

#### `scripts`

List all available scripts in `package.json`.

```bash
~> laris laris:npm scripts
```

#### `version`

Show the current installed version of Node.js and the package manager.

```bash
~> laris laris:npm version
```

#### `npx`

Run any command using `npx`.

```bash
~> laris laris:npm npx create-react-app my-app
```

---

### 🧠 **Smart Features**

* Automatically detects package manager:

  * `yarn.lock` → uses `yarn`
  * `pnpm-lock.yaml` → uses `pnpm`
  * Default → uses `npm`
* Clean and unified interface for managing Node.js dependencies inside Laravel projects
* Developer-friendly output with error handling

---

### 🔍 **Examples**

```bash
# Install Tailwind CSS as dev dependency
~> laris laris:npm install tailwindcss --save-dev

# Run multiple scripts
~> laris laris:npm run dev,watch

# Clean cache forcibly
~> laris laris:npm cache-clean --force

# Check current versions
~> laris laris:npm version
```

---

## `select` Command

Switch between recently used Laravel projects from a stored history.

This command helps developers quickly navigate into different Laravel project directories they've worked on before — similar to a workspace/project switcher.

---

### 🔧 **Usage**

```bash
~> laris select
```

---

### 📌 **Description**

* Lists recently used Laravel projects (from a stored history array).
* Prompts the user to select one by its number.
* Switches the working directory (`chdir`) to the selected project path.

> This command is especially useful when managing multiple Laravel projects locally.

---

### 🧾 **Example Output**

```
[1] blog => /Users/you/Projects/blog
[2] ecommerce => /Users/you/Projects/ecommerce
[3] admin-panel => /Users/you/Projects/admin-panel
Enter number: 2
Switched to: /Users/you/Projects/ecommerce
```

---

### ⚠️ **Notes**

* This command requires an array of previously accessed project paths to be injected during instantiation.
* If the user selects an invalid number (e.g., out of range), an error is displayed.
* The selection is interactive via `STDIN`.

---

### 🧠 **Typical Use Case**

You're using a CLI tool like `laris` with multiple Laravel projects, and you want to switch to one quickly:

```bash
~> laris laris select
```

Then simply type the number of the project to switch context.

