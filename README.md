

# 🚀 Service Booking — Quick Start

Welcome to ** Service Booking** — a modular Laravel + Vue (Inertia) admin panel and landing page.


---

## 🛠️ Requirements

- PHP >= 8.x^
- Composer
- Node.js & npm
- MySQL (or other supported database)

---

## ✨ Setup — Step by Step


<strong>1️⃣ Clone the repository</strong>

```powershell
git clone https://github.com/MahfujuRahman/service-booking-system
```


<strong>2️⃣ Install PHP dependencies</strong>

```powershell
composer install
```


<strong>3️⃣ Install Node.js dependencies</strong>

```powershell
npm install
```


<strong>4️⃣ Copy environment file</strong>

```powershell
copy .env.example .env
```


<strong>5️⃣ Generate Laravel app key</strong>

```powershell
php artisan key:generate
```

<strong>6️⃣ Full reload: wipes DB Run migrations and seed database</strong>

```powershell
npm run reload
```

<strong>7️⃣ Start development servers</strong>

```powershell
npm run dev
php artisan serve
```
✨ And Boom Everything ready to work


## 📧 Email (SMTP) setup

Set these values in your `.env` (do NOT commit secrets):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Notes:**
- For Gmail use an App Password (recommended).
- Test: trigger a password reset or use `php artisan tinker` to send a test mail.

---

## 👤 Contributors

> <br>
> <img src="https://github.com/MahfujuRahman.png" alt="alt text" width="50" height="50" > <br> 
> <a href="https://github.com/MahfujuRahman">S. M. Mahfujur Rahman</a> <br>
> Software Engineer ( Tech Park IT )
> <br>
<br>

