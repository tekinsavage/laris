# 🚀 Laris - The Ultimate Laravel AI-Powered CLI Tool

**Supercharge your Laravel workflow with AI-powered code generation!**  

Laris is a **next-gen CLI tool** for Laravel developers who want to **code smarter, not harder**. Generate Laravel components instantly, scaffold projects lightning-fast, and harness AI to automate boilerplate code.  

🔥 **Built by developers, for developers.**  

## ⚡ Features That Will Blow Your Mind

- **🤖 AI-Powered Code Generation** (Events, Listeners, Middleware, Tests, READMEs)  
- **⚡ Instant Project Scaffolding** (`laris new project-name`)  
- **📦 GitHub Package Installation** (Like Composer, but cooler)  
- **🚀 Built-in Helpers & Smart Traits** (Git, String Manipulation, OpenAI Integration)  
- **💡 Laravel Artisan on Steroids**  

## 🛠️ Installation (It's Stupid Simple)

```bash
composer global require larapire/laris
```

## 🎮 Usage Examples

### 🔥 Create a New Laravel Project
```bash
laris new my-awesome-app
```

### 🤖 Let AI Generate a Laravel Event
```bash
laris laris:ai:make:event OrderShipped
```

### 📦 Install a Package Directly from GitHub
```bash
laris require spatie/laravel-permission
```

### 🧪 Generate a Test via AI
```bash
laris laris:ai:test:generate UserTest
```

## 🏗️ Project Structure (Clean & Modular)

```
laris/
├── ai/                  # AI Magic Lives Here
│   ├── Commands/        # AI Commands (Events, Listeners, Tests...)
│   └── Traits/          # Reusable AI Logic (ChatGPT integration)
├── src/                 # Core CLI Power
│   ├── Commands/        # Base Commands (New, Require, Install...)
│   ├── Contracts/       # Interfaces for Extensibility
│   ├── Helpers/         # Utilities (Git, Str, etc.)
│   └── Providers/       # Laravel Service Registration
├── tests/               # Because We Test Our Stuff
├── .gitignore
├── composer.json
├── LICENSE
└── README.md            # You're here! 😎
```

## 🚀 Why Laris?

✅ **No more repetitive coding** – Let AI handle boilerplate  
✅ **Blazing-fast setup** – From `laris new` to "Hello World" in seconds  
✅ **GitHub package installer** – Skip Composer for direct GitHub integration  
✅ **OpenAI-Powered** – Smarter code suggestions  

## 🤝 Contributing  

**We welcome hackers, tinkerers, and Laravel enthusiasts!**  
Check out [CONTRIBUTING.md](CONTRIBUTING.md) (or submit a PR to create one 😉).  

## 📜 License  

MIT – Do whatever you want, just give credit where it's due.  

---

## 👉 Next Page
[Continue to the description and installation page ➡️](./installation.md)

<p align="center">
  <b>🚀 Powered by Laravel + AI = ❤️</b><br>
  <sub>A project by <a href="https://github.com/larapire">Larapire</a></sub>
</p>

