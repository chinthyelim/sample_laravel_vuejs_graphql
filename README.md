# Sample Laravel Vue Graph QL

Sample Laravel Vue Graph QL

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Cubet Techno Labs](https://cubettech.com)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[Many](https://www.many.co.uk)**
-   **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
-   **[DevSquad](https://devsquad.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[OP.GG](https://op.gg)**
-   **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
-   **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Steps for setting up Sample Laravel Vue Graph QL portal from scratch

At the project path on a terminal run below commands:

1. Run `composer install`
2. Run `yarn`
3. Command to run Sail:
   `sail up -d`
4. Create databse:
   `sail artisan migrate`
5. Add seeders:
   `sail artisan db:seed --class=UserSeeder`
   `sail artisan db:seed --class=CompanySeeder`
6. Set storage path's symbolic link
   `sail artisan storage:link`
7. Run localhost:
   `yarn dev`

## Development References

Steps to install Laravel 10:
https://laravel.com/docs/10.x
https://laravel.com/docs/10.x/sail
https://stackoverflow.com/questions/71950345/php-artisan-makeauth-not-defined-in-laravel-9
https://stackoverflow.com/questions/72112237/getaddrinfo-for-mysql-failed-nodename-nor-servname-provided
https://sadhakbj.medium.com/getting-started-with-vue-3-composition-api-in-typescript-with-laravel-mix-from-scratch-4b55e05df3cf
https://vuetifyjs.com/en/getting-started/installation/

Others:
https://dev.to/hc200ok/an-easy-to-use-vue3-data-table-component-589a
https://hc200ok.github.io/vue3-easy-data-table-doc/features/server-side-paginate-and-sort.html
https://codesandbox.io/s/item-slot-65tc9v?file=/src/main.js
https://stackoverflow.com/questions/22172604/convert-image-from-url-to-base64
https://pqina.nl/blog/convert-an-image-to-a-base64-string-with-javascript/
https://laracasts.com/discuss/channels/laravel/create-image-from-base64-string-laravel
https://laracasts.com/discuss/channels/eloquent/foreignid-or-unsignedbiginteger-for-migration
https://gigahosta.com/docs/r/web-development/How-to-use-Laravel-paginations--and-how-to-paginate-with-ajax-so-your-page-doesn-t-reload---

Extra:
https://codeanddeploy.com/blog/laravel/laravel-8-authentication-login-and-registration-with-username-or-email

## Sample Laravel Vue Graph QL Setup during Development

sail artisan make:migration create_companies_table
sail artisan make:migration create_employees_table
sail artisan make:seeder UserSeeder
sail artisan make:seeder CompanySeeder
sail artisan make:model '\Base\Company'
sail artisan make:model '\Base\Employee'
sail artisan make:controller CompanyController
sail artisan make:controller EmployeeController
sail artisan make:resource CompanyResource
sail artisan make:resource EmployeeResource
sail artisan make:resource CompanyCollection --collection
sail artisan make:resource EmployeeCollection --collection
sail artisan make:request CompanyRequest
sail artisan make:request EmployeeRequest
sail artisan make:mail ComapnyCreated
