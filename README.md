# KROMA — magazin online de haine (proiect licență)

Platformă e-commerce full-stack: catalog de produse cu variante (mărime/stoc),
coș persistent (inclusiv pentru vizitatori neautentificați), plăți online prin
Stripe, cont de utilizator cu istoric comenzi și wishlist, sistem de recenzii,
căutare full-text, și panou de administrare complet (produse, comenzi,
dashboard cu statistici).

---

## 1. Tech stack

| Categorie | Tehnologie                                        | Observații |
|---|---------------------------------------------------|---|
| Backend framework | Laravel 12 (PHP 8.3+)                             | |
| Reactivitate frontend | Livewire 3 + Volt                                 | componente cu stare pe server, fără API separat/SPA |
| Stilizare | Tailwind CSS v4                                   | CSS-first (`@theme` în `resources/css/app.css`), fără `tailwind.config.js` |
| Bază de date | MySQL (dev/producție) / SQLite în memorie (teste) | |
| Plăți | Stripe (Payment Element + webhook)                | mod test, card `4242 4242 4242 4242` |
| Căutare | Laravel Scout (driver `database`)                 | upgradabil la Meilisearch fără schimbări de cod |
| Cozi de mesaje | Laravel Queue (driver `database`)                 | trimiterea email-ului de confirmare comandă e asincronă |
| Email | Laravel Mail (`log` local / SMTP real la deploy)  | |
| Testare | Pest (peste PHPUnit)                              | 28 teste, `RefreshDatabase` |
| Alte librării JS | Alpine.js (vine cu Livewire)                      | pentru interacțiuni mici de UI (dropdown căutare, formular checkout) |

**De ce acest stack, pe scurt (util pentru capitolul de justificare tehnologică):**
Livewire+Volt elimină nevoia unui API REST/GraphQL separat și a unui frontend
JS decuplat (React/Vue) — logica de business stă într-un singur loc (PHP),
ceea ce reduce suprafața de bug-uri și complexitatea arhitecturală pentru un
proiect de dimensiunea unei licențe, păstrând totuși interactivitate reală
(fără reîncărcare de pagină).

---

## 2. Arhitectură & pattern-uri folosite

```
app/
├── Actions/          PlaceOrder — o singură operațiune de business per clasă,
│                     ușor de testat izolat (nu depinde de HTTP/Livewire)
├── Enums/            OrderStatus, PaymentStatus, UserRole — enum-uri native
│                     PHP 8.1, elimină string-uri "magice" pentru statusuri
├── Events/           OrderPlaced
├── Listeners/        SendOrderConfirmation (implements ShouldQueue → email
│                     trimis pe coadă, nu blochează răspunsul către client)
├── Http/Controllers/ StripeWebhookController — singurul controller clasic,
│                     pentru că webhook-urile nu sunt pagini Livewire
├── Livewire/         CartCounter, SearchBar — componente mici, persistente,
│                     reutilizate în layout (nu sunt "pagini" Volt)
├── Mail/             OrderConfirmationMail
├── Models/           Eloquent, cu relații + accesori (preț în bani → RON)
├── Policies/         (extensibil — gate-ul de admin e în AppServiceProvider)
├── Providers/         AppServiceProvider — Gate::define('access-admin', ...)
└── Services/         CartService, CheckoutService — logică de business
                      reutilizabilă, independentă de Livewire

resources/views/livewire/pages/   Componente Volt = "pagini" (routate direct)
resources/views/components/       Componente Blade reutilizabile (<x-tag>, layout-uri)
```

**Pattern-uri cheie de menționat în capitolul de proiectare:**
- **Service Layer** (`CartService`, `CheckoutService`) — separă logica de
  business de componentele UI; testabilă fără a porni Livewire.
- **Action classes** (`PlaceOrder`) — o operațiune, o clasă, un singur punct
  de intrare (`__invoke`), tranzacție DB completă (`DB::transaction`).
- **Event-driven** — plasarea unei comenzi declanșează `OrderPlaced`, ascultat
  de un listener pus pe coadă — decuplează "creează comanda" de "trimite email".
- **Snapshot pe comandă** — `order_items` copiază numele/prețul produsului la
  momentul cumpărării, ca istoricul comenzilor să rămână corect chiar dacă
  produsul e redenumit/reprețuit ulterior.
- **Soft deletes** pe `products` — un produs șters nu strică istoricul
  comenzilor vechi care îl referențiază.

---

## 3. Baza de date

11 tabele proprii (plus cele implicite Laravel: `users` extins, `sessions`,
`jobs`, `password_reset_tokens`).

| Tabel | Rol | Coloane / relații cheie |
|---|---|---|
| `users` | conturi | `role` (enum customer/admin), `phone`, `address` |
| `categories` | Women/Men/Accessories | `slug`, `sort_order` |
| `products` | catalog | `category_id`, `price_cents`, `compare_at_price_cents`, `sku`, `is_published`, `is_featured`, soft delete |
| `product_images` | poze produs | `product_id`, `path`, `sort_order`, `is_primary` — 1 rând/poză (nu coloane `image1..4`) |
| `product_variants` | mărime + stoc | `product_id`, `size`, `stock` — unicitate pe (product_id, size) |
| `cart_items` | coș | `user_id` **sau** `session_id` (niciodată ambele) — permite coș pentru vizitatori |
| `orders` | comenzi | `reference` (ex. `KRM-XXXXXXXX`), `status` (enum), `user_id`, `payment_id` |
| `order_items` | linii comandă | *snapshot*: `product_name`, `unit_price_cents`, `size` la momentul cumpărării |
| `payments` | plăți | `stripe_payment_intent_id`, `status` (enum) |
| `reviews` | recenzii | `product_id`, `user_id`, `rating` (1-5) — unicitate pe (product_id, user_id) |
| `wishlists` | favorite | `user_id`, `product_id` — unicitate pe pereche |

**Decizii de design notabile (bune de justificat în scris):**
- Prețurile sunt stocate ca **număr întreg de bani** (`price_cents`), nu
  `float` — evită erorile de rotunjire la totaluri de coș/comandă.
- `product_images` și `product_variants` sunt tabele separate, nu coloane
  fixe pe `products` — un produs poate avea orice număr de poze/mărimi fără
  schimbare de schemă.

*(Poți genera o diagramă ER din schema efectivă cu orice tool — de ex.
`php artisan schema:dump` + un vizualizator, sau direct din MySQL Workbench
conectat la baza locală — pentru diagrama din lucrare.)*

---

## 4. Funcționalități complete

### Vizitator (fără cont)
- Homepage cu produse recomandate, categorii, bandă de promo animată
- Listare pe categorie cu filtrare după mărime
- Pagină produs: galerie, preț (cu badge reducere dacă are `compare_at_price`), recenzii
- Căutare live (Scout) din navbar, pe tot site-ul
- Coș funcțional fără cont (identificat prin `session_id`)
- Pagină 404 personalizată

### Cont
- Înregistrare / autentificare / delogare (Livewire/Volt, nu Breeze default)
- Rate limiting la login (protecție brute-force)
- Resetare parolă (email cu link, flux complet)
- **Coșul de guest se transferă automat la login** (`CartService::mergeGuestCartInto`)
- Istoric comenzi (`/account/orders`) cu status curent
- Wishlist (`/account/wishlist`) — adaugă/scoate produse cu ♡/♥

### Coș & checkout
- Adaugă/modifică cantitate/șterge produse din coș, reactiv (fără reload)
- Checkout cu Stripe (Payment Element) — formular adresă/telefon + plată
- Verificare **server-side** a plății înainte de a crea comanda (nu se are
  încredere doar în callback-ul din browser)
- Webhook Stripe (`payment_intent.succeeded`/`.payment_failed`) ca sursă de
  adevăr asincronă suplimentară
- Email de confirmare comandă (trimis pe coadă)
- Stocul scade automat, per mărime, la plasarea comenzii

### Produs
- Recenzii: adaugă rating + comentariu (o singură recenzie/utilizator/produs)
- Wishlist toggle

### Admin (`/admin/...`, protejat cu Gate `access-admin`)
- Dashboard: venit total, nr. comenzi, produse cu stoc redus, grafic venituri ultimele 7 zile, comenzi recente
- Produse: listă, creare/editare (upload poze, stoc per mărime), publish/hide, ștergere (soft delete)
- Comenzi: listă, schimbare status inline (pending → paid → processing → shipped → delivered / cancelled)

---

## 5. Toate rutele

| Metodă | URL | Nume | Acces | Componentă |
|---|---|---|---|---|
| GET | `/` | `home` | public | `pages.home` |
| GET | `/category/{category:slug}` | `category.show` | public | `pages.category` |
| GET | `/products/{product:slug}` | `products.show` | public | `pages.product` |
| GET | `/cart` | `cart.show` | public | `pages.cart` |
| POST | `/stripe/webhook` | `stripe.webhook` | Stripe (fără CSRF) | `StripeWebhookController` |
| GET | `/checkout` | `checkout.show` | auth | `pages.checkout` |
| GET | `/account/orders` | `account.orders` | auth | `pages.account.orders` |
| GET | `/account/wishlist` | `account.wishlist` | auth | `pages.account.wishlist` |
| GET | `/admin/dashboard` | `admin.dashboard` | auth + admin | `pages.admin.dashboard` |
| GET | `/admin/products` | `admin.products.index` | auth + admin | `pages.admin.products` |
| GET | `/admin/products/create` | `admin.products.create` | auth + admin | `pages.admin.product-form` |
| GET | `/admin/products/{product}/edit` | `admin.products.edit` | auth + admin | `pages.admin.product-form` |
| GET | `/admin/orders` | `admin.orders.index` | auth + admin | `pages.admin.orders` |
| GET | `/login` | `login` | guest | `pages.auth.login` |
| GET | `/register` | `register` | guest | `pages.auth.register` |
| GET | `/forgot-password` | `password.request` | guest | `pages.auth.forgot-password` |
| GET | `/reset-password/{token}` | `password.reset` | guest | `pages.auth.reset-password` |
| POST | `/logout` | `logout` | auth | closure simplu |

Accesul admin e controlat printr-un singur `Gate::define('access-admin', ...)`
în `AppServiceProvider`, nu prin verificări de rol împrăștiate în fiecare pagină.

---

## 6. Teste automate (Pest) — 28 teste

Rulare: `php artisan test` (folosește SQLite în memorie, izolat de baza reală — vezi `.env.testing`).

| Fișier | Ce verifică |
|---|---|
| `PagesLoadTest.php` | **Smoke tests** — fiecare rută din tabelul de mai sus răspunde corect (200, 302 sau 403 după caz), inclusiv 404 pentru un produs inexistent. Cel mai valoros fișier: prinde orice pagină stricată instant. |
| `CartServiceTest.php` | Adăugare în coș, incrementare cantitate în loc de duplicare, merge coș guest→user la login, ștergere la cantitate 0 |
| `PlaceOrderTest.php` | Cart → comandă, scădere stoc, golire coș, snapshot preț/nume produs pe `order_items` |
| `AuthTest.php` | Înregistrare, respingere parole nepotrivite, login cu credențiale corecte/greșite |
| `WishlistAndReviewTest.php` | Toggle wishlist, adăugare recenzie, blocare recenzie dublă |

Acesta e material direct pentru capitolul de **testare și validare** din
lucrare: poți include tabelul de mai sus ca "plan de teste", captura de ecran
cu `php artisan test` verde ca dovadă, și explica pentru fiecare grup de teste
ce clasă de bug previne (regresii de rutare, suprvânzare de stoc, integritate
date istorice comandă, etc.).

---

## 7. Instalare rapidă

Detalii complete + troubleshooting în `SETUP.md`. Pe scurt:

```bash
composer create-project laravel/laravel kroma
cd kroma
composer require livewire/livewire livewire/volt laravel/scout stripe/stripe-php
composer require pestphp/pest pestphp/pest-plugin-laravel --dev
php artisan volt:install && php artisan pest:install
npm install
# copiază peste fișierele din acest kit (vezi SETUP.md pentru lista exactă)
php artisan migrate --seed
npm run dev        # terminal separat
php artisan serve
```

Conturi de test create de seeder: `admin@kroma.test` / `password` (admin),
`customer@kroma.test` / `password` (client).

---

## 8. Pentru lucrarea scrisă — sugestie de mapare pe capitole

| Capitol tipic de licență | Ce folosești din proiect |
|---|---|
| Analiza cerințelor | Secțiunea 4 (funcționalități) de mai sus, rescrisă ca cerințe funcționale/nefuncționale |
| Tehnologii utilizate | Secțiunea 1 (tech stack) + justificarea de mai jos |
| Proiectarea sistemului | Secțiunea 2 (arhitectură) + Secțiunea 3 (schema DB) → diagramă ER + diagramă de arhitectură |
| Implementare | Exemple de cod comentate din `app/Actions/PlaceOrder.php` (tranzacție + event), `app/Services/CartService.php` (guest→user merge), `CheckoutService.php` (integrare Stripe) — sunt deja comentate în engleză, gata de citat |
| Testare și validare | Secțiunea 6 direct — tabel + captură `php artisan test` |
| Concluzii / dezvoltări ulterioare | Ce am notat ca "opțional" în `SETUP.md`: CRUD categorii în admin, moderare recenzii, filtre avansate preț/sortare, deploy online, refund din admin conectat la Stripe |

Dacă vrei, pot să-ți scriu și un draft al oricăruia dintre capitolele de mai
sus (mai ales Proiectare/Implementare/Testare, unde ai deja tot materialul
tehnic corect) — spune-mi doar de care ai nevoie primul.
