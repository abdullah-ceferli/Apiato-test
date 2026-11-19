# Şəxsi Task Manager — Apiato 13.x

Tapşırığın məcburi hissələri: WEB qeydiyyat və login, Passport Password Grant, istifadəçiyə aid Task CRUD API, Blade + fetch(), tamamlanma filtri, migration, iki istifadəçili seeder və Postman collection.

## Quraşdırma

PHP 8.2+, Composer və PDO SQLite və ya MySQL lazımdır. Əmrləri `apiato` qovluğunda icra edin.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Mövcud `.env` varsa onu saxlayın. WEB və API üçün serverin document root-u bu layihənin `public` qovluğu olmalıdır. Laragon-da hər iki hostu eyni `public` qovluğuna yönəldin:

```dotenv
APP_URL=http://apiato.test
API_URL=http://api.apiato.test
FRONTEND_URL=http://apiato.test
APP_DEBUG=false
DEBUGBAR_ENABLED=false
SESSION_DRIVER=database
```

SQLite üçün `DB_CONNECTION=sqlite` saxlayın, `DB_DATABASE` təyin etməyin və faylı yaradın:

```powershell
New-Item -ItemType File -Path database/database.sqlite -ErrorAction SilentlyContinue
```

MySQL istifadə edirsinizsə `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` dəyərlərini öz bazanıza uyğun yazın.

```powershell
php artisan config:clear
php artisan migrate
php artisan passport:keys
php artisan passport:client --password --provider=users --name="Task Manager WEB"
```

Mövcud Passport açarları varsa onları saxlayın. Client əmri confidential password client yaradır. Çıxışdakı ID və secret-i yalnız serverdəki `.env` faylına yazın:

```dotenv
CLIENT_WEB_ID=buraya_client_id
CLIENT_WEB_SECRET=buraya_client_secret
```

```powershell
php artisan config:clear
php artisan db:seed --class='App\Containers\AppSection\TaskManagement\Data\Seeders\TaskManagementSeeder_100'
```

Nümunə hesablar: `aysel@example.com` və `murad@example.com`. Hər ikisinin şifrəsi `TaskManager123!` olur. Hər hesab üçün bir tamamlanmış və bir tamamlanmamış tapşırıq yaradılır. Seeder təkrar işlədildikdə həmin qeydləri çoxaltmır.

Brauzerdə [login](http://apiato.test/login) və ya [qeydiyyat](http://apiato.test/register) səhifəsini açın. CSS və JavaScript birbaşa `public` qovluğundadır, npm build tələb olunmur.

`php artisan serve` ilə `http://127.0.0.1:8000` istifadə edirsinizsə, `.env` daxilində `APP_URL=http://127.0.0.1:8000`, `API_URL=http://127.0.0.1` və `FRONTEND_URL="${APP_URL}"` yazın. API route-un portu Laravel tərəfindən cari sorğudan əlavə edilir. `SESSION_COOKIE=task_manager_session` bu layihənin sessiyasını digər lokal Apiato layihələrindən ayırır. Parametrləri dəyişəndən sonra `php artisan config:clear` icra edib formaları yenidən açın.

Passport cədvəllərinin migration-ları Authentication Container-dədir. Eyni cədvəlləri yenidən yaradan `database/migrations` nüsxələri çıxarılıb; `passport-migrations` yenidən publish edilməməlidir.

## WEB və API

| Method | WEB route | Davranış |
|---|---|---|
| GET / POST | /register | Blade formu / hesab yaratmaq |
| GET / POST | /login | Blade formu / WEB session və Password Grant tokenləri |
| POST | /logout | Session tokenlərini ləğv etmək və sessiyanı bağlamaq |
| GET | /tasks | Siyahı səhifəsi |
| GET | /tasks/create | Yaratma səhifəsi |
| GET | /tasks/{id}/edit | Redaktə səhifəsi |

Task səhifələri `auth:web`, API isə `auth:api` ilə qorunur. WEB formalarında `@csrf` var. Login olmayan istifadəçi task səhifəsindən loginə yönləndirilir.

| Method | API endpoint | Uğurlu status |
|---|---|---|
| GET | /v1/tasks | 200 |
| GET | /v1/tasks/{id} | 200 |
| POST | /v1/tasks | 201 |
| PATCH | /v1/tasks/{id} | 200 |
| DELETE | /v1/tasks/{id} | 204 |

`GET /v1/tasks?is_completed=1` tamamlanmış, `is_completed=0` tamamlanmamış tapşırıqları qaytarır. Parametrsiz sorğu istifadəçinin bütün tapşırıqlarını qaytarır. Task API-si rəqəm ID-lərdən istifadə edir; digər Container-lərin HASH_ID davranışı saxlanılır.

`title` məcburidir və maksimum 150 simvoldur, `description` nullable, `is_completed` boolean və başlanğıcda false-dur. `user_id` daxil olan məlumatdan seçilmir. Yalnız validasiya olunmuş sahələr Action-a ötürülür, əlaqə sahibini `$user->tasks()->create($data)` təyin edir. Başqasına aid ID ilə oxuma, yeniləmə və silmə 404 qaytarır.

Login mövcud `IssueTokenAction` vasitəsilə OAuth serverində `grant_type=password` sorğusu icra edir. Bu, server daxilində işlədiyindən öz serverinə HTTP sorğusu göndərmir. Access token, refresh token və token ID-si server sessiyasında saxlanılır. Blade yalnız access tokeni JavaScript-ə verir; `fetch()` sorğuları `Authorization: Bearer ...` göndərir. Refresh token və client secret HTML-ə verilmir. Task səhifələrində session məlumatlarını göstərə bilən debug toolbar söndürülür.

WEB logout session-dakı token ID-sinə görə access tokeni və ona bağlı refresh tokenləri ləğv edir, WEB sessiyasını etibarsızlaşdırır və CSRF tokenini yeniləyir. Token ID-si saxlandığından access tokenin vaxtı bitəndən sonra da logout işləyir. Token brauzerdə bitərsə istifadəçiyə yenidən daxil olmaq bildirilir.

## Postman ilə qəbul yoxlaması

`postman/TaskManager.postman_collection.json` və `postman/TaskManager.postman_environment.json` fayllarını import edin. Environment-i seçin; `web_url`, `api_url`, `client_id` və `client_secret` dəyərlərini öz mühitinizə uyğun doldurun. Secret-i yalnız lokal Postman environment-də saxlayın.

Collection-ı sıra ilə işlədin. OAuth cavablarından access və refresh tokenlər avtomatik lokal environment-ə yazılır. Birinci hesabın tapşırığı ikinci hesabın tokeni ilə oxunur, dəyişdirilir və silinir: hər üç sorğu 404 olmalıdır. Validation sorğusu 422, tokensiz sorğu 401 qaytarmalıdır.

Refresh sorğusu `POST {{api_url}}/v1/oauth/token` ünvanına aşağıdakı form body-ni göndərir:

```text
grant_type=refresh_token
client_id={{client_id}}
client_secret={{client_secret}}
refresh_token={{refresh_token}}
```

Cavabda yeni access və refresh token alınır. Köhnə refresh token yenidən istifadə edilə bilmir. Collection-dakı logout-dan sonra əvvəlki access token 401, refresh cəhdi 400 qaytarmalıdır.

WEB qovluğundakı sorğular CSRF tokeni və Postman cookie jar ilə adi form login və logout-u da göstərir. Onları sıra ilə işlədin. WEB login → task səhifəsi → Bearer ilə API 200 → WEB logout → eyni Bearer ilə API 401. Brauzerdə də login olub tapşırıq yaratmaq, redaktə etmək, silmək, checkbox ilə vəziyyəti dəyişmək və filtr tətbiq etmək mümkündür. Bu əməliyyatların nəticələri səhifə yenilənmədən göstərilir.

## Sorğunun layihədə yolu

`PATCH /v1/tasks/7` nümunəsi:

1. `TaskManagement/UI/API/Routes/UpdateTask.v1.private.php` route-u `auth:api` ilə Passport tokenini yoxlayır.
2. `UpdateTaskRequest` daxil olan sahələri validasiya edir.
3. `UpdateTaskController` cari istifadəçi, ID və təsdiqlənmiş məlumatı `UpdateTaskAction`-a ötürür.
4. Action əvvəlcə `FindTaskTask` ilə `$user->tasks()->whereKey(7)->firstOrFail()` icra edir.
5. `UpdateTaskTask` tapılmış modeli yeniləyir.
6. `TaskTransformer` cavabın JSON sahələrini hazırlayır, controller 200 qaytarır.
7. JavaScript cavabı oxuyub nəticəni səhifədə göstərir.

WEB controller yalnız Blade view qaytarır. Redaktə məlumatları səhifə açıldıqdan sonra ayrıca GET API sorğusu ilə alınır. Controller-lərdə database sorğusu yazılmır.

## Qısa araşdırma qeydləri

### HTTP, REST və JSON

HTTP method sorğunun nə etmək istədiyini göstərir: GET oxuyur, POST yaradır, PATCH qismən dəyişir, DELETE silir. Header format və autentifikasiya kimi əlavə məlumat daşıyır, body göndərilən məlumatdır. Status code nəticəni bildirir. REST resursları URL ilə ifadə edən yanaşmadır; JSON məlumatı mətn şəklində ötürür. Layihədə `POST /v1/tasks` JSON body qəbul edib 201, səhv başlıq olduqda 422 qaytarır. [MDN HTTP](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/Overview), [MDN REST](https://developer.mozilla.org/en-US/docs/Glossary/REST).

### Apiato və Porto

Container bir funksional sahənin kodunu bir yerə yığır. Action istifadə ssenarisini idarə edir, Task daha kiçik işi yerinə yetirir. Məsələn, `UpdateTaskAction` tapşırığın tapılması və dəyişdirilməsini iki Task ilə əlaqələndirir. Controller sorğunu qəbul edib bu Action-a ötürür. Route faylındakı `.v1.private.php` Apiato üçün API versiyasını və route təsvirini bildirir; autentifikasiya ayrıca middleware ilə tətbiq olunur. [Apiato Actions](https://apiato.io/docs/components/main-components/actions/), [Apiato Tasks](https://apiato.io/docs/components/main-components/tasks/), [Apiato Routes](https://apiato.io/docs/components/main-components/routes/).

### WEB və API autentifikasiyası

Session məlumatları serverdə saxlanılır, brauzerin cookie-si sessiyanı tanıdır. Bearer token isə API sorğusunun Authorization header-ində göndərilir. `auth:web` WEB sessiyasındakı istifadəçini, `auth:api` Passport access tokenini yoxlayır. Layihədə WEB login eyni zamanda Password Grant tokeni alır; yalnız session ilə `/v1/tasks` sorğusu 401 qaytarır. CSRF tokeni cookie ilə göndərilən form sorğusunun həmin sessiyadan gəldiyini yoxlamağa kömək edir; login, register və logout formalarında `@csrf` var. [Laravel Authentication](https://laravel.com/framework/docs/12.x/authentication), [Laravel CSRF](https://laravel.com/framework/docs/12.x/csrf).

### JavaScript: fetch, Promise, async/await, JSON

`fetch()` dərhal nəticə əvəzinə Promise qaytarır. `await` asinxron nəticəni gözləyir; `response.json()` cavabın JSON mətnini JavaScript obyektinə çevirir. HTTP 404 və ya 422 cavabı fetch Promise-ni avtomatik reject etmir, buna görə `response.ok` yoxlanılır. `public/task-manager.js` faylındakı `api()` funksiyası bu yoxlamanı edir. Form göndəriləndə JSON.stringify ilə body hazırlanır, 422 cavabındakı errors form sahələrinin yanında göstərilir. [MDN Fetch](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch).

### OAuth və Passport

Authentication istifadəçinin kim olduğunu yoxlayır; authorization onun hansı resurs üzərində əməliyyat edə bildiyini müəyyənləşdirir. Password Grant istifadəçinin login məlumatları ilə token almağa imkan verir. Client secret confidential tətbiqin özünü tanıdır, access token API-yə giriş, refresh token isə yenidən şifrə göndərmədən token almaq üçündür. Layihədə Passport istifadəçini yoxlayır, owner sorğuları isə başqasının tapşırığına girişi bağlayır. Secret və refresh token serverdə qalır. [Laravel Passport](https://laravel.com/framework/docs/12.x/passport#password-grant-tokens).

### Database əlaqələri

Primary key cədvəldə hər sətri tanıdır. Foreign key başqa cədvəldəki sətrə istinad edir. `users.id` primary key, `tasks.user_id` isə users cədvəlinə foreign key-dir. Bir istifadəçinin çox tapşırığı ola bildiyindən `User::tasks()` hasMany qaytarır; hər tapşırığın sahibi üçün `Task::user()` belongsTo qaytarır. Migration istifadəçi silindikdə tapşırıqlarının da silinməsini database səviyyəsində təmin edir. [Laravel Eloquent Relationships](https://laravel.com/framework/docs/12.x/eloquent-relationships).

### CORS və Same-Origin Policy

Origin protokol, host və portdan ibarətdir. `apiato.test` və `api.apiato.test` fərqli origin-dir. Brauzer cross-origin cavabın JavaScript tərəfindən oxunmasına CORS qaydaları ilə icazə verir; Authorization və JSON header-ləri üçün OPTIONS preflight sorğusu yarana bilər. Layihənin `config/cors.php` faylı FRONTEND_URL origin-inə icazə verir, bootstrap Laravel-in HandleCors middleware-ini saxlayır. API fetch sorğularında cookie göndərilmir, giriş Bearer ilə yoxlanılır. [MDN CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CORS), [MDN Same-origin policy](https://developer.mozilla.org/en-US/docs/Web/Security/Defenses/Same-origin_policy).

## Testlər

```powershell
php vendor/phpunit/phpunit/phpunit app/Containers/AppSection/TaskManagement/Tests/Functional/TaskManagerTest.php
```

Testlər SQLite `:memory:` bazasında işləyir. Mövcud tətbiq bazası silinmir. WEB guest redirect, CSRF, yalnız WEB sessiyasının API üçün kifayət etməməsi, real Password Grant, 401/422/404, CRUD, sahiblik, filtr, refresh, logout və CORS yoxlanılır.

---

<p align="center">
   <a href="https://github.com/apiato/apiato/actions/workflows/tests.yaml">
      <img src="https://img.shields.io/github/actions/workflow/status/apiato/apiato/tests.yaml?label=tests" alt="tests status">
   </a>
   <a href="https://codecov.io/gh/apiato/apiato">
      <img src="https://img.shields.io/codecov/c/github/apiato/apiato?token=siiyEg5AC9" alt="code coverage"/>
   </a>
   <a href="https://packagist.org/packages/apiato/apiato">
      <img src="https://img.shields.io/packagist/v/apiato/apiato" alt="latest stable version">
   </a>
   <br>
   <a href="https://packagist.org/packages/apiato/apiato">
      <img src="https://img.shields.io/packagist/dt/apiato/apiato" alt="total downloads">
   </a>
   <a href="https://github.com/apiato/apiato">
      <img src="https://img.shields.io/github/license/apiato/apiato" alt="license">
   </a>
   <a href="https://discord.gg/ryPcV4KM5k">
      <img src="https://img.shields.io/discord/800815227839053834?logo=discord&label=chat" alt="chat">
   </a>
</p>

<p align="center">
   <img src="https://github.com/apiato/documentation/blob/master/images/apiato.jpg" alt="Apiato Logo"/>
</p>
<h1 align="center">Apiato</h1>
<h3 align="center">Build scalable APIs faster | Powered by PHP and Laravel</h3>

---

## Overview

**Apiato** is a PHP framework built on top of Laravel, specifically designed for creating scalable, testable, API-centric applications. Utilizing the [Porto SAP](https://mahmoudz.github.io/Porto/) architectural pattern, Apiato offers a robust foundation for building complex APIs with flexibility and speed.

### Key Features

- **Code Generators** for faster development and streamlined API creation
- **Documentation Generators** to easily build comprehensive API documentation
- **API Versioning** to maintain backward compatibility across versions
- **OAuth2.0 Authentication** for secure, standardized user authentication
- **Role-Based Access Control** to manage user permissions effectively
- **Pagination Support** for efficient data retrieval and navigation
- **Data Caching** to optimize performance and reduce server load
- **ETag Support** for optimized caching and reduced bandwidth usage
- **Performance Profiler** to identify and improve application bottlenecks
- **Localization** for multilingual support in global applications
- **Social Authentication** with integrations for popular platforms
- **Test Helpers** for building reliable, maintainable tests
- **Multiple Response Formats** to adapt to client needs easily
- **Query Parameters Support** for flexible data querying and filtering
- **Hash ID Support** to secure sensitive IDs in API responses
- **Comprehensive Documentation** for seamless onboarding and usage

For a comprehensive list of features, visit the [Apiato Documentation](https://apiato.io).

---

## Getting Started

To get started with Apiato, please refer to the [Getting Started Guide](https://apiato.io/docs/getting-started/introduction) in the documentation.

---

## Community & Support

Join our [Discord Community](https://discord.gg/ryPcV4KM5k) for free support, discussions, and connecting with other developers using Apiato.

If you find a bug or have a feature request, feel free to [open an issue](https://github.com/apiato/apiato/issues).

---

## Contributing

Thank you for considering contributing to Apiato! Contributions are welcome, whether it's documentation, bug reports, or feature suggestions. Check out our [Contribution Guide](https://apiato.io/docs/contribution-guide) for guidelines on how to get started.

Please adhere to the [Code of Conduct](https://apiato.io/docs/contribution-guide#code-of-conduct) to maintain a welcoming environment for all contributors.

---

## Security

If you discover a security vulnerability, please report it to [Mohammad Alavi](mailto:mohammad.alavi1990@gmail.com). Your report will be addressed promptly.

---

## Project Maintainers

<table>
  <tbody>
     <tr>
        <td align="center" valign="top">
            <img width="125" height="125" src="https://github.com/mahmoudz.png?s=150">
            <br>
            <strong>Mahmoud Zalt</strong>
            <br>
            <a href="https://github.com/Mahmoudz">@mahmoudz</a>
        </td>
         <td align="center" valign="top">
            <img width="125" height="125" src="https://github.com/mohammad-alavi.png?s=150">
            <br>
            <strong>Mohammad Alavi</strong>
            <br>
            <a href="https://github.com/mohammad-alavi">@Mohammad-Alavi</a>
        </td>
          <td align="center" valign="top">
            <img width="125" height="125" src="https://github.com/mderis.png?s=150">
            <br>
            <strong>Moslem Deris</strong>
            <br>
            <a href="https://github.com/mderis">@mderis</a>
          </td>
     </tr>
  </tbody>
</table>


## Contributors

[![Apiato Contributors](https://opencollective.com/apiato/contributors.svg?width=890&button=false&isActive=true)](https://github.com/apiato/apiato/graphs/contributors)


## Sponsors

<!-- Listing Contributors Refference: https://docs.opencollective.com/help/collectives/collective-settings/data-export#contributor-image -->

### Diamond Sponsors

<p align="left">
  <a href="https://smart.sista.ai/?utm_source=docs_apiato&utm_medium=sponsor&utm_campaign=landing_page_content" target="_blank">
    <img src="https://raw.githubusercontent.com/laradock/laradock/master/.github/home-page-images/custom-sponsors/sista-ai-icon.png" height="165px" alt="Sista AI - Plug-and-Play AI Assistant. (www.sista.ai)" style="margin-right: 4em;">
  </a>
  <a href="http://laradock.io/" target="_blank">
    <img src="https://raw.githubusercontent.com/laradock/laradock/master/DOCUMENTATION/static/img/laradock/laradock-icon.png" height="165px" alt="Laradock: Full PHP development environment on Docker.">
  </a>
</p>



### Gold Sponsors

![Gold Sponsors](https://opencollective.com/apiato/tiers/gold-sponsors.svg?avatarHeight=120&width=800&format=svg&button=false)

### Silver Sponsors

![Silver Sponsors](https://opencollective.com/apiato/tiers/silver-sponsors.svg?avatarHeight=90&width=800&format=svg&button=false)

### Bronze Sponsors

![Bronze Sponsors](https://opencollective.com/apiato/tiers/bronze-sponsors.svg?avatarHeight=65&width=800&format=svg&button=false)



## Backers

[![Open Collective backers](https://opencollective.com/apiato/tiers/awesome-backers.svg?width=800&avatarHeight=65&button=false&isActive=true)](https://opencollective.com/apiato#contributors)
[![Open Collective backers](https://opencollective.com/apiato/tiers/donate.svg?width=800&avatarHeight=65&button=false&isActive=true)](https://opencollective.com/apiato#contributors)


### Supports Us

You can support us using any of the methods below:

<b>1:</b> [Open Collective](https://opencollective.com/apiato) (For Sponsorships checkout Open Collective, or emails us at support@apiato.io)

<b>2:</b> [Github Sponsors](https://github.com/sponsors/Mahmoudz)


## License

Apiato is open-sourced software licensed under the [MIT license](https://github.com/apiato/apiato/blob/master/LICENSE).

---

<p align="center">
   <strong>Made with ❤️ by the Apiato community</strong>
</p>

## Acknowledgements
Thanks to [JetBrains](https://www.jetbrains.com) for sponsoring this project with their amazing tools and IDEs.

<img style="width: 300px" src="https://resources.jetbrains.com/storage/products/company/brand/logos/jetbrains.png" alt="JetBrains logo.">
