# 🔐 Telegram VPN Decrypt Bot (SlipNet & NetMod)

A powerful Telegram bot for decrypting VPN configurations with a built-in coin system, referral system, and full admin panel.

---

## 🚀 Features

* Decrypt SlipNet configs
* Decrypt NetMod configs
* Coin-based usage system
* Advanced referral system
* Full admin panel
* User management (block/unblock)
* Broadcast & forward messaging
* Unlimited admins
* Unlimited forced join channels
* Advanced statistics system
* Persian (Farsi) bot interface
* Secure and optimized backend

---

## 📁 Project Structure

```bash
.
├── install.php
├── config.php
├── bot.php
│
├── lib/
│   ├── table.php
│   ├── daily.php
│   └── jdf.php
│
├── api/
│   ├── NetMod/
│   └── SlipNet/
```

---

## ⚙️ Installation

### 1. Create Database

Create a MySQL database and keep your credentials:

* DB Name
* Username
* Password

---

### 2. Upload Files

Upload the source code to your server or hosting.

---

### 3. Run Installer

Open this in your browser:

```
http://yourdomain.com/install.php
```

Enter:

* Bot Token
* Admin ID
* Database credentials

The installer will automatically:

* Create database tables
* Set webhook
* Configure the bot

---

### 4. Setup Cron Job

Run this every 1 minute:

```bash
* * * * * php /path/to/lib/daily.php
```

---

## 🧠 System Logic

* Each decrypt costs coins (configurable)
* Each referral gives coins (configurable)
* All settings are manageable via admin panel

---

## 🔐 Security

* Secure bot structure
* Controlled admin access
* Safe API handling

---

## ⚠️ Disclaimer

This project is for educational purposes only.
Use it at your own risk.

---

## ⭐ Support

If you like this project, give it a star on GitHub.
