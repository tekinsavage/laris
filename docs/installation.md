# Getting Started with Laris

So... I got tired of typing `php artisan make:something` every single time.

Tired of memorizing long commands.  
Tired of aliasing everything.  
Tired of saying “I’ll automate this later.”  

Well, *later* is now — and **Laris** is here to fix it.

---

## What is Laris?

Laris is a supercharged CLI tool built for Laravel developers who want a smarter, faster, and cleaner workflow.  
Think of it as your witty little assistant that gets things done without whining (unlike that one coworker).

You type less. It does more.

---

## Requirements

Before we get rolling, make sure your system has the following:

- PHP 8.2 or higher  
- Composer (installed globally)  
- Laravel 10, 11, or 12  
- Git (for fetching packages)  
- A bit of developer sass (optional but recommended)

---

## Installation

Install Laris globally with Composer. One command. Done.

```bash
composer global require larapire/laris
````

Make sure Composer's global `vendor/bin` directory is in your system PATH. If not, you'll need to add it manually.
(You’ll only cry about it once — then it’s smooth sailing.)

---

## Quick Check

Run this to see if Laris is alive and kicking:

```bash
laris
```

If it talks back — congrats, you’ve got power at your fingertips.

---

## Why Laris?

Because sometimes you just don’t want to:

* Type `php artisan` for the 200th time
* Write yet another boilerplate event or listener
* Manually wire up folders and files
* Think too hard before making a decision
* Be boring

Laris automates all that with a single command — and if you're feeling spicy, it even comes with AI support for generating events, tests, middleware, and more.

---

## AI-Powered Features (Yes, Really)

Laris has commands like:

* `laris ai:make:event`
* `laris ai:test:generate`
* `laris ai:generate:readme`

Just give it a hint, and Laris will code it for you. It’s like pair programming, minus the awkward small talk.

---

## Need More?

Want to install additional modules? Just use:

```bash
laris require something-cool
```

(Seriously, try `laris require app` and see what happens.)

---

## Maintained by

Built with coffee and a growing disdain for repetitive tasks,
by [itarrshia](mailto:itarrshia@gmail.com)

---

Happy hacking!
<div class="nav-links">
    <a href="https://larapire.github.io/laris">&larr; Back to Introduction</a>
    <a href="https://larapire.github.io/laris/basic-description">Basic explanations and basic training &rarr;</a>
</div>
