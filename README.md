# Laris: Your Laravel CLI Companion 🚀

![Laris Logo](https://img.shields.io/badge/Laris-CLI%20Tool-blue.svg)  
[![Latest Release](https://img.shields.io/github/v/release/tekinsavage/laris.svg)](https://github.com/tekinsavage/laris/releases)  
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)

---

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Commands](#commands)
- [Contributing](#contributing)
- [License](#license)
- [Acknowledgments](#acknowledgments)

---

## Introduction

Welcome to **Laris**, a blazing-fast and minimal CLI tool designed to streamline your Laravel development. Laris helps you run common Artisan commands with fewer keystrokes and zero clutter. Whether you're serving your application, creating new components, or running migrations, Laris simplifies your workflow.

For the latest releases, check out the [Releases section](https://github.com/tekinsavage/laris/releases).

---

## Features

- **Minimalistic Design**: Focus on what matters without unnecessary distractions.
- **Speed**: Execute commands quickly and efficiently.
- **Common Commands**: Access all the essential Artisan commands with ease.
- **User-Friendly**: Designed for developers of all skill levels.

---

## Installation

To get started with Laris, you need to download and execute the latest version. Visit the [Releases section](https://github.com/tekinsavage/laris/releases) to find the latest version.

### Steps to Install

1. **Download** the latest release from the Releases section.
2. **Extract** the downloaded file.
3. **Execute** the Laris binary in your terminal.

```bash
chmod +x laris
sudo mv laris /usr/local/bin/
```

Now, you can run Laris from anywhere in your terminal.

---

## Usage

Using Laris is straightforward. Simply type `laris` followed by the command you want to execute. For example:

```bash
laris serve
```

This command will start your Laravel application.

---

## Commands

Laris supports a variety of Artisan commands. Here are some of the most common ones:

### 1. Serve

Starts the Laravel development server.

```bash
laris serve
```

### 2. Make

Creates new components like controllers, models, and migrations.

```bash
laris make:model MyModel
```

### 3. Migrate

Runs database migrations.

```bash
laris migrate
```

### 4. Rollback

Rolls back the last database migration.

```bash
laris migrate:rollback
```

### 5. Cache

Clears various caches in your application.

```bash
laris cache:clear
```

### 6. Config

Clears the configuration cache.

```bash
laris config:clear
```

### 7. Route

Clears the route cache.

```bash
laris route:clear
```

### 8. View

Clears the view cache.

```bash
laris view:clear
```

### 9. Optimize

Optimizes the framework for better performance.

```bash
laris optimize
```

### 10. Help

Displays help for Laris commands.

```bash
laris help
```

---

## Contributing

We welcome contributions to Laris! If you'd like to contribute, please follow these steps:

1. **Fork the repository**.
2. **Create a new branch** for your feature or bug fix.
3. **Make your changes** and commit them.
4. **Push your branch** to your fork.
5. **Create a pull request**.

Please ensure your code follows the project's coding standards and includes tests where applicable.

---

## License

Laris is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

---

## Acknowledgments

- Thanks to the Laravel community for their support and contributions.
- Special thanks to the developers who have contributed to this project.

---

Feel free to explore and enhance your Laravel development experience with Laris! For more information, visit the [Releases section](https://github.com/tekinsavage/laris/releases) to download the latest version.