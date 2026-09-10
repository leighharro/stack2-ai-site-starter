# Stack2 AI-site starter

A minimal HTML, CSS and PHP starter you can upload to Australian hosting. It is a one-page demo plus a contact form that sends through a mailbox on your domain.

This package is meant for sites built with ChatGPT, Claude, Cursor, or similar tools. You do not need WordPress, and you do not need Composer.

## Host this on Stack2

**[Australian hosting for AI-built sites](https://stack2.au/solutions/ai-site)**  
PHP, Laravel, Node, or static HTML, CSS and JS. Sydney servers, free SSL, and engineers on the ticket.

**[Stack2 hosting plans](https://stack2.au/hosting)**  
Plans start from **$15/mo AUD, GST included**. The price you see is the price you pay.

## What you get

- `index.html`, `style.css`, `thank-you.html`, and `assets/` as a small demo site
- `contact.php` using official PHPMailer files under `lib/PHPMailer/src/`
- `config.smtp.php.example` for STARTTLS on port 587
- This README for mailbox setup, upload, DNS, and SSL

PHPMailer is shipped as source files. There is no `vendor/` directory and no Composer step.

## Requirements

- A Stack2 website with PHP 8.1 or newer (PHP 8.5 is available and is a good default)
- A mailbox on your domain for SMTP
- SFTP, Git, or the file manager in the control panel

## 1. Create a mailbox

1. Log in to the Stack2 customer portal and open the website control panel.
2. Open **Email** (sometimes labelled Mailboxes or Email accounts).
3. Create a mailbox such as `hello@yourdomain.com.au` and set a strong password.
4. Note the SMTP details shown for that mailbox. On Stack2 they are usually:
   - Host: `mail.yourdomain.com.au`
   - Port: `587`
   - Encryption: STARTTLS
   - Username: the full mailbox address
   - Password: the password you just set

You can also send a test from webmail to confirm the mailbox works before you wire up the form.

## 2. Fill in the SMTP config

1. Copy the example file:

   ```bash
   cp config.smtp.php.example config.smtp.php
   ```

2. Edit `config.smtp.php` and replace the placeholders:
   - `host`: `mail.example.com.au` becomes `mail.yourdomain.com.au`
   - `port`: leave `587` for STARTTLS
   - `encryption`: leave `tls` for STARTTLS
   - `username` and `password`: your mailbox
   - `from_email` and `to_email`: usually the same mailbox

`config.smtp.php` is listed in `.gitignore`. Do not commit real passwords, and do not invent credentials for screenshots or samples.

## 3. Upload the site

1. Open your website in the Stack2 control panel.
2. Upload the project into the document root (often `public_html`).
3. Include every file in this package **and** your local `config.smtp.php`.
4. Confirm `index.html` and `contact.php` sit in that same folder.
5. In PHP settings, choose 8.1 or newer.

You can upload with the file manager, SFTP, or Git. For Node apps you would use the Enhance Node tools instead. This starter is PHP and static files, so a normal upload is enough.

## 4. Point DNS and turn on SSL

1. In the Stack2 panel, copy the IPv4 address (or the nameservers) for the website.
2. At your registrar, either:
   - point the domain nameservers to Stack2, or
   - set an A record for `@` (and `www` if you use it) to that IP.
3. Wait for DNS to propagate. Stack2's [DNS lookup tool](https://stack2.au/tools) is handy for this.
4. In the panel, issue the free Let's Encrypt SSL certificate and force HTTPS if that option is shown.
5. Visit `https://yourdomain.com.au` and send a test from the contact form.
6. Check the inbox (and junk) for the mailbox you configured.

If the certificate is not live yet, the [SSL checker](https://stack2.au/tools) will show what the public internet sees.

## Support

Stack2 hosting support is by **ticket or email**, handled by the engineers who run the platform. There is no phone queue for hosting.

If the form cannot send, say so in a ticket and include the domain, the SMTP host, and the PHP version. Do not paste the mailbox password into the ticket if you can avoid it. An engineer can reset the mailbox or check the mail logs with you.

## Licence

This starter is MIT licensed. See `LICENSE`.

PHPMailer remains under its own GNU LGPL 2.1 licence. The official licence text is in `lib/PHPMailer/LICENSE`. The files in `lib/PHPMailer/src/` are the upstream PHPMailer 7.1.1 sources.
